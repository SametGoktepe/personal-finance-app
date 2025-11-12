<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InsightsStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        // Calculate monthly totals
        $monthlyIncome = Transaction::where('type', 'income')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $monthlyExpense = Transaction::where('type', 'expense')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $netSavings = $monthlyIncome - $monthlyExpense;

        // Calculate average daily expense
        $daysInMonth = now()->daysInMonth;
        $currentDay = now()->day;
        $avgDailyExpense = $currentDay > 0 ? $monthlyExpense / $currentDay : 0;
        $projectedExpense = $avgDailyExpense * $daysInMonth;

        // Calculate savings rate
        $savingsRate = $monthlyIncome > 0 ? (($monthlyIncome - $monthlyExpense) / $monthlyIncome) * 100 : 0;

        return [
            Stat::make('Monthly Income', '₺' . number_format($monthlyIncome, 2))
                ->description('This month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Monthly Expenses', '₺' . number_format($monthlyExpense, 2))
                ->description('This month')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Net Savings', '₺' . number_format($netSavings, 2))
                ->description('Income - Expenses')
                ->descriptionIcon($netSavings >= 0 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-circle')
                ->color($netSavings >= 0 ? 'success' : 'danger'),

            Stat::make('Savings Rate', number_format($savingsRate, 1) . '%')
                ->description($savingsRate >= 20 ? 'Excellent!' : ($savingsRate >= 10 ? 'Good' : 'Needs improvement'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color($savingsRate >= 20 ? 'success' : ($savingsRate >= 10 ? 'warning' : 'danger')),

            Stat::make('Avg. Daily Expense', '₺' . number_format($avgDailyExpense, 2))
                ->description('Projected: ₺' . number_format($projectedExpense, 2))
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info'),
        ];
    }
}

