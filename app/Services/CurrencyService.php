<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class CurrencyService
{
    protected string $apiUrl = 'https://finans.truncgil.com/v4/today.json';

    /**
     * Fetch and update exchange rates from API
     */
    public function updateExchangeRates(): array
    {
        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->get($this->apiUrl);

            if (!$response->successful()) {
                throw new Exception('Failed to fetch exchange rates from API');
            }

            $data = $response->json();
            $results = [
                'success' => [],
                'failed' => [],
                'total' => 0,
            ];

            $today = now()->toDateString();

            // Get API-enabled currencies from database
            $apiEnabledCurrencies = Currency::getApiEnabled();

            foreach ($apiEnabledCurrencies as $currencyCode) {
                if (!isset($data[$currencyCode])) {
                    $results['failed'][] = $currencyCode . ' - Not found in API response';
                    continue;
                }

                $currencyData = $data[$currencyCode];

                // Get buying rate (more accurate for expenses)
                $buyingRate = $currencyData['Buying'] ?? null;

                if (!$buyingRate) {
                    $results['failed'][] = $currencyCode . ' - No buying rate available';
                    continue;
                }

                try {
                    // Currency to TRY rate
                    $rate1 = ExchangeRate::where('from_currency', $currencyCode)
                        ->where('to_currency', 'TRY')
                        ->where('date', $today)
                        ->first();

                    if ($rate1) {
                        $rate1->update([
                            'rate' => $buyingRate,
                            'is_active' => true,
                        ]);
                    } else {
                        ExchangeRate::create([
                            'from_currency' => $currencyCode,
                            'to_currency' => 'TRY',
                            'rate' => $buyingRate,
                            'date' => $today,
                            'is_active' => true,
                        ]);
                    }

                    // TRY to Currency rate (inverse)
                    $rate2 = ExchangeRate::where('from_currency', 'TRY')
                        ->where('to_currency', $currencyCode)
                        ->where('date', $today)
                        ->first();

                    if ($rate2) {
                        $rate2->update([
                            'rate' => 1 / $buyingRate,
                            'is_active' => true,
                        ]);
                    } else {
                        ExchangeRate::create([
                            'from_currency' => 'TRY',
                            'to_currency' => $currencyCode,
                            'rate' => 1 / $buyingRate,
                            'date' => $today,
                            'is_active' => true,
                        ]);
                    }

                    $results['success'][] = $currencyCode . ' - Updated (1 ' . $currencyCode . ' = ' . number_format($buyingRate, 4) . ' TRY)';
                    $results['total'] += 2; // Both directions

                } catch (Exception $e) {
                    $results['failed'][] = $currencyCode . ' - ' . $e->getMessage();
                    Log::error('Failed to update exchange rate for ' . $currencyCode, [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return $results;

        } catch (Exception $e) {
            Log::error('Failed to fetch exchange rates', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => [],
                'failed' => ['API Error: ' . $e->getMessage()],
                'total' => 0,
            ];
        }
    }

    /**
     * Get current exchange rate for a currency pair
     */
    public function getCurrentRate(string $from, string $to): ?float
    {
        if ($from === $to) {
            return 1.0;
        }

        // Try to get from database first
        $rate = ExchangeRate::getRate($from, $to);

        if ($rate !== 1.0) {
            return $rate;
        }

        // If not found, try to fetch from API
        $this->updateExchangeRates();

        return ExchangeRate::getRate($from, $to);
    }

    /**
     * Get exchange rate for a specific currency to TRY
     */
    public function getRateToTRY(string $currency): ?float
    {
        if ($currency === 'TRY') {
            return 1.0;
        }

        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->get($this->apiUrl);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data[$currency]['Buying'])) {
                    return (float) $data[$currency]['Buying'];
                }
            }
        } catch (Exception $e) {
            Log::warning('Failed to fetch live rate for ' . $currency);
        }

        // Fallback to database
        return ExchangeRate::getRate($currency, 'TRY');
    }

    /**
     * Convert amount from one currency to another
     */
    public function convert(float $amount, string $from, string $to): float
    {
        $rate = $this->getCurrentRate($from, $to);
        return $amount * $rate;
    }

    /**
     * Get all supported currencies
     */
    public function getSupportedCurrencies(): array
    {
        return Currency::getApiEnabled();
    }

    /**
     * Get API update time
     */
    public function getApiUpdateTime(): ?string
    {
        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->get($this->apiUrl);

            if ($response->successful()) {
                $data = $response->json();
                return $data['Update_Date'] ?? null;
            }
        } catch (Exception $e) {
            return null;
        }

        return null;
    }
}

