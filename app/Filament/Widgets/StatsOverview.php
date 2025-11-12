<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalIncome = Transaction::where('type', 'income')->sum('amount');
        $totalExpense = Transaction::where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpense;
        $totalAccounts = Account::where('is_active', true)->sum('current_balance');

        return [
            Stat::make('Total Balance', '₺' . number_format($totalAccounts, 2))
                ->description('All active accounts')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Total Income', '₺' . number_format($totalIncome, 2))
                ->description('All time income')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Expenses', '₺' . number_format($totalExpense, 2))
                ->description('All time expenses')
                ->descriptionIcon('heroicon-o-arrow-trending-down')
                ->color('danger'),

            Stat::make('Net Balance', '₺' . number_format($netBalance, 2))
                ->description('Income - Expenses')
                ->descriptionIcon('heroicon-o-calculator')
                ->color($netBalance >= 0 ? 'success' : 'danger'),
        ];
    }
}
