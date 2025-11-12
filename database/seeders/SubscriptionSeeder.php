<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
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

