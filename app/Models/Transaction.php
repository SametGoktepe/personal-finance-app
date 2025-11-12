<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'account_id',
        'category_id',
        'subscription_id',
        'type',
        'amount',
        'currency',
        'exchange_rate',
        'transaction_date',
        'description',
        'notes',
        'reference',
        'is_recurring',
        'recurring_interval',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'transaction_date' => 'date',
        'is_recurring' => 'boolean',
    ];

    /**
     * Get amount in base currency (TRY)
     */
    public function getAmountInBaseCurrency(): float
    {
        if ($this->currency === 'TRY') {
            return (float) $this->amount;
        }

        return (float) $this->amount * (float) $this->exchange_rate;
    }

    /**
     * Get amount in a specific currency
     */
    public function getAmountInCurrency(string $currency): float
    {
        $baseCurrencyAmount = $this->getAmountInBaseCurrency();

        if ($currency === 'TRY') {
            return $baseCurrencyAmount;
        }

        $rate = ExchangeRate::getRate('TRY', $currency, $this->transaction_date);
        return $baseCurrencyAmount * $rate;
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
