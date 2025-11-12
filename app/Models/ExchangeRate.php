<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = [
        'from_currency',
        'to_currency',
        'rate',
        'date',
        'is_active',
    ];

    protected $casts = [
        'rate' => 'decimal:6',
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get exchange rate between two currencies (base currency: TRY)
     */
    public static function getRate(string $from, string $to, $date = null): float
    {
        if ($from === $to) {
            return 1.0;
        }

        $date = $date ?? now()->format('Y-m-d');

        $rate = self::where('from_currency', $from)
            ->where('to_currency', $to)
            ->where('date', '<=', $date)
            ->where('is_active', true)
            ->orderBy('date', 'desc')
            ->first();

        if ($rate) {
            return (float) $rate->rate;
        }

        // Try reverse rate
        $reverseRate = self::where('from_currency', $to)
            ->where('to_currency', $from)
            ->where('date', '<=', $date)
            ->where('is_active', true)
            ->orderBy('date', 'desc')
            ->first();

        if ($reverseRate) {
            return 1 / (float) $reverseRate->rate;
        }

        return 1.0;
    }

    /**
     * Convert amount from one currency to another
     */
    public static function convert(float $amount, string $from, string $to, $date = null): float
    {
        $rate = self::getRate($from, $to, $date);
        return $amount * $rate;
    }
}
