<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\ExchangeRate;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);

        // Create categories
        $categories = [
            ['name' => 'Salary', 'type' => 'income', 'color' => '#10b981', 'icon' => 'heroicon-o-banknotes'],
            ['name' => 'Freelance', 'type' => 'income', 'color' => '#3b82f6', 'icon' => 'heroicon-o-briefcase'],
            ['name' => 'Food & Dining', 'type' => 'expense', 'color' => '#ef4444', 'icon' => 'heroicon-o-shopping-cart'],
            ['name' => 'Transportation', 'type' => 'expense', 'color' => '#f59e0b', 'icon' => 'heroicon-o-truck'],
            ['name' => 'Entertainment', 'type' => 'expense', 'color' => '#8b5cf6', 'icon' => 'heroicon-o-film'],
            ['name' => 'Utilities', 'type' => 'expense', 'color' => '#06b6d4', 'icon' => 'heroicon-o-light-bulb'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create accounts
        $accounts = [
            [
                'user_id' => $user->id,
                'name' => 'Main Bank Account',
                'type' => 'bank',
                'initial_balance' => 5000,
                'current_balance' => 5000,
                'currency' => 'TRY',
                'color' => '#3b82f6',
            ],
            [
                'user_id' => $user->id,
                'name' => 'Debit Card',
                'type' => 'debit_card',
                'initial_balance' => 1000,
                'current_balance' => 1000,
                'currency' => 'TRY',
                'color' => '#10b981',
            ],
            [
                'user_id' => $user->id,
                'name' => 'Cash Wallet',
                'type' => 'cash',
                'initial_balance' => 500,
                'current_balance' => 500,
                'currency' => 'TRY',
                'color' => '#10b981',
            ],
            [
                'user_id' => $user->id,
                'name' => 'Credit Card',
                'type' => 'credit_card',
                'initial_balance' => 0,
                'current_balance' => 0,
                'currency' => 'TRY',
                'color' => '#ef4444',
            ],
        ];

        foreach ($accounts as $account) {
            Account::create($account);
        }

        // Create sample transactions
        $transactions = [
            [
                'account_id' => 1,
                'category_id' => 1,
                'type' => 'income',
                'amount' => 3000,
                'transaction_date' => now()->subDays(5),
                'description' => 'Monthly salary',
            ],
            [
                'account_id' => 2,
                'category_id' => 3,
                'type' => 'expense',
                'amount' => 150,
                'transaction_date' => now()->subDays(3),
                'description' => 'Grocery shopping',
            ],
            [
                'account_id' => 1,
                'category_id' => 4,
                'type' => 'expense',
                'amount' => 50,
                'transaction_date' => now()->subDays(2),
                'description' => 'Gas station',
            ],
            [
                'account_id' => 2,
                'category_id' => 5,
                'type' => 'expense',
                'amount' => 80,
                'transaction_date' => now()->subDays(1),
                'description' => 'Movie tickets',
            ],
        ];

        foreach ($transactions as $transaction) {
            Transaction::create($transaction);
        }

        // Create budgets
        $budgets = [
            [
                'category_id' => 3,
                'name' => 'Monthly Food Budget',
                'amount' => 500,
                'period' => 'monthly',
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->endOfMonth(),
            ],
            [
                'category_id' => 4,
                'name' => 'Transportation Budget',
                'amount' => 200,
                'period' => 'monthly',
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->endOfMonth(),
            ],
        ];

        foreach ($budgets as $budget) {
            Budget::create($budget);
        }

        // Create exchange rates (TRY is base currency)
        $today = now()->format('Y-m-d');
        $exchangeRates = [
            // TRY to other currencies
            ['from_currency' => 'TRY', 'to_currency' => 'USD', 'rate' => 0.029, 'date' => $today],
            ['from_currency' => 'TRY', 'to_currency' => 'EUR', 'rate' => 0.027, 'date' => $today],
            ['from_currency' => 'TRY', 'to_currency' => 'GBP', 'rate' => 0.023, 'date' => $today],
            ['from_currency' => 'TRY', 'to_currency' => 'JPY', 'rate' => 4.34, 'date' => $today],
            // Other currencies to TRY
            ['from_currency' => 'USD', 'to_currency' => 'TRY', 'rate' => 34.50, 'date' => $today],
            ['from_currency' => 'EUR', 'to_currency' => 'TRY', 'rate' => 37.26, 'date' => $today],
            ['from_currency' => 'GBP', 'to_currency' => 'TRY', 'rate' => 43.83, 'date' => $today],
            ['from_currency' => 'JPY', 'to_currency' => 'TRY', 'rate' => 0.23, 'date' => $today],
        ];

        foreach ($exchangeRates as $rate) {
            ExchangeRate::create($rate);
        }

        // Create subscriptions
        $subscriptions = [
            [
                'category_id' => 5, // Entertainment
                'name' => 'Netflix',
                'description' => 'Premium streaming service subscription',
                'price' => 149.99,
                'currency' => 'TRY',
                'interval' => 'monthly',
                'interval_count' => 1,
                'start_date' => now()->subMonths(3),
                'next_billing_date' => now()->addDays(5),
                'color' => '#E50914',
                'icon' => 'heroicon-o-play',
                'auto_pay' => true,
            ],
            [
                'category_id' => 5, // Entertainment
                'name' => 'Spotify Premium',
                'description' => 'Music streaming service',
                'price' => 54.99,
                'currency' => 'TRY',
                'interval' => 'monthly',
                'interval_count' => 1,
                'start_date' => now()->subMonths(6),
                'next_billing_date' => now()->addDays(12),
                'color' => '#1DB954',
                'icon' => 'heroicon-o-musical-note',
                'auto_pay' => true,
            ],
            [
                'category_id' => 6, // Utilities
                'name' => 'Internet Service',
                'description' => 'Home internet subscription',
                'price' => 350.00,
                'currency' => 'TRY',
                'interval' => 'monthly',
                'interval_count' => 1,
                'start_date' => now()->subYear(),
                'next_billing_date' => now()->addDays(8),
                'color' => '#0EA5E9',
                'icon' => 'heroicon-o-wifi',
                'auto_pay' => false,
            ],
            [
                'category_id' => 5, // Entertainment
                'name' => 'YouTube Premium',
                'description' => 'Ad-free YouTube subscription',
                'price' => 29.99,
                'currency' => 'TRY',
                'interval' => 'monthly',
                'interval_count' => 1,
                'start_date' => now()->subMonths(2),
                'next_billing_date' => now()->addDays(20),
                'color' => '#FF0000',
                'icon' => 'heroicon-o-play-circle',
                'auto_pay' => true,
            ],
        ];

        foreach ($subscriptions as $subscription) {
            Subscription::create($subscription);
        }
    }
}
