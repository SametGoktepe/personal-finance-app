<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\Subscription;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PdfReportService
{
    /**
     * Generate financial summary report
     */
    public function generateFinancialSummary(?Carbon $startDate = null, ?Carbon $endDate = null): \Barryvdh\DomPDF\PDF
    {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now()->endOfMonth();

        $data = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => now(),
            'accounts' => Account::where('is_active', true)->get(),
            'total_balance' => Account::where('is_active', true)->sum('current_balance'),
            'income' => Transaction::where('type', 'income')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount'),
            'expense' => Transaction::where('type', 'expense')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount'),
            'transactions_count' => Transaction::whereBetween('transaction_date', [$startDate, $endDate])->count(),
            'categories_expense' => Category::where('type', 'expense')
                ->withCount(['transactions' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('transaction_date', [$startDate, $endDate]);
                }])
                ->withSum(['transactions' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('transaction_date', [$startDate, $endDate]);
                }], 'amount')
                ->having('transactions_sum_amount', '>', 0)
                ->orderByDesc('transactions_sum_amount')
                ->limit(10)
                ->get(),
            'categories_income' => Category::where('type', 'income')
                ->withSum(['transactions' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('transaction_date', [$startDate, $endDate]);
                }], 'amount')
                ->having('transactions_sum_amount', '>', 0)
                ->orderByDesc('transactions_sum_amount')
                ->get(),
        ];

        return Pdf::loadView('pdf.financial-summary', $data)
            ->setPaper('a4', 'portrait');
    }

    /**
     * Generate transactions report
     */
    public function generateTransactionsReport(?Carbon $startDate = null, ?Carbon $endDate = null, ?string $type = null): \Barryvdh\DomPDF\PDF
    {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now()->endOfMonth();

        $query = Transaction::with(['account', 'category', 'subscription'])
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        if ($type) {
            $query->where('type', $type);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->get();

        $data = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => now(),
            'type' => $type,
            'transactions' => $transactions,
            'total_income' => $transactions->where('type', 'income')->sum('amount'),
            'total_expense' => $transactions->where('type', 'expense')->sum('amount'),
            'net_balance' => $transactions->where('type', 'income')->sum('amount') - $transactions->where('type', 'expense')->sum('amount'),
        ];

        return Pdf::loadView('pdf.transactions-report', $data)
            ->setPaper('a4', 'landscape');
    }

    /**
     * Generate subscriptions report
     */
    public function generateSubscriptionsReport(): \Barryvdh\DomPDF\PDF
    {
        $subscriptions = Subscription::with('category')
            ->where('is_active', true)
            ->orderBy('next_billing_date')
            ->get();

        $data = [
            'generated_at' => now(),
            'subscriptions' => $subscriptions,
            'total_monthly_cost' => $subscriptions->where('interval', 'monthly')->sum('price'),
            'total_yearly_cost' => $subscriptions->sum(function ($subscription) {
                return match($subscription->interval) {
                    'daily' => $subscription->price * 365 / $subscription->interval_count,
                    'weekly' => $subscription->price * 52 / $subscription->interval_count,
                    'monthly' => $subscription->price * 12 / $subscription->interval_count,
                    'yearly' => $subscription->price / $subscription->interval_count,
                };
            }),
            'upcoming' => $subscriptions->filter(fn ($s) => $s->daysUntilBilling() >= 0 && $s->daysUntilBilling() <= 14),
            'overdue' => $subscriptions->filter(fn ($s) => $s->isOverdue()),
        ];

        return Pdf::loadView('pdf.subscriptions-report', $data)
            ->setPaper('a4', 'portrait');
    }

    /**
     * Generate account statement
     */
    public function generateAccountStatement(Account $account, ?Carbon $startDate = null, ?Carbon $endDate = null): \Barryvdh\DomPDF\PDF
    {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now()->endOfMonth();

        $transactions = Transaction::where('account_id', $account->id)
            ->with(['category', 'subscription'])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->get();

        $data = [
            'account' => $account,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => now(),
            'transactions' => $transactions,
            'starting_balance' => $account->initial_balance,
            'ending_balance' => $account->current_balance,
            'total_income' => $transactions->where('type', 'income')->sum('amount'),
            'total_expense' => $transactions->where('type', 'expense')->sum('amount'),
        ];

        return Pdf::loadView('pdf.account-statement', $data)
            ->setPaper('a4', 'portrait');
    }

    /**
     * Generate category report
     */
    public function generateCategoryReport(Category $category, ?Carbon $startDate = null, ?Carbon $endDate = null): \Barryvdh\DomPDF\PDF
    {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now()->endOfMonth();

        $transactions = Transaction::where('category_id', $category->id)
            ->with(['account'])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->get();

        $budgets = $category->budgets()
            ->where('is_active', true)
            ->where('start_date', '<=', $endDate)
            ->where(function ($query) use ($startDate) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $startDate);
            })
            ->get();

        $data = [
            'category' => $category,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => now(),
            'transactions' => $transactions,
            'budgets' => $budgets,
            'total_spent' => $transactions->sum('amount'),
            'transaction_count' => $transactions->count(),
        ];

        return Pdf::loadView('pdf.category-report', $data)
            ->setPaper('a4', 'portrait');
    }
}

