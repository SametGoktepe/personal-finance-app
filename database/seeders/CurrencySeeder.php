<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'TRY',
                'name' => 'Turkish Lira',
                'symbol' => '₺',
                'is_active' => true,
                'is_base' => true,
                'api_enabled' => false,
                'decimal_places' => 2,
                'description' => 'Turkish Lira - Base currency for the application',
            ],
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'is_active' => true,
                'is_base' => false,
                'api_enabled' => true,
                'decimal_places' => 2,
                'description' => 'United States Dollar - Fetched from API',
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'is_active' => true,
                'is_base' => false,
                'api_enabled' => true,
                'decimal_places' => 2,
                'description' => 'European Euro - Fetched from API',
            ],
            [
                'code' => 'GBP',
                'name' => 'British Pound',
                'symbol' => '£',
                'is_active' => true,
                'is_base' => false,
                'api_enabled' => true,
                'decimal_places' => 2,
                'description' => 'British Pound Sterling - Fetched from API',
            ],
            [
                'code' => 'JPY',
                'name' => 'Japanese Yen',
                'symbol' => '¥',
                'is_active' => true,
                'is_base' => false,
                'api_enabled' => true,
                'decimal_places' => 0,
                'description' => 'Japanese Yen - Fetched from API (no decimal places)',
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}

