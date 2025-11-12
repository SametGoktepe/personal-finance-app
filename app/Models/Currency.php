<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'is_active',
        'is_base',
        'api_enabled',
        'decimal_places',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_base' => 'boolean',
        'api_enabled' => 'boolean',
        'decimal_places' => 'integer',
    ];

    /**
     * Get the base currency
     */
    public static function getBase(): ?Currency
    {
        return self::where('is_base', true)->where('is_active', true)->first();
    }

    /**
     * Get all active currencies
     */
    public static function getActive(): array
    {
        return self::where('is_active', true)
            ->orderBy('is_base', 'desc')
            ->orderBy('code')
            ->pluck('name', 'code')
            ->toArray();
    }

    /**
     * Get API-enabled currencies
     */
    public static function getApiEnabled(): array
    {
        return self::where('api_enabled', true)
            ->where('is_active', true)
            ->pluck('code')
            ->toArray();
    }

    /**
     * Get currency options for forms
     */
    public static function getOptions(): array
    {
        return self::where('is_active', true)
            ->orderBy('is_base', 'desc')
            ->orderBy('code')
            ->get()
            ->mapWithKeys(fn ($currency) => [
                $currency->code => $currency->code . ' - ' . $currency->name . ' (' . $currency->symbol . ')'
            ])
            ->toArray();
    }
}
