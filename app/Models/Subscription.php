<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Subscription extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'currency',
        'interval',
        'interval_count',
        'start_date',
        'next_billing_date',
        'end_date',
        'color',
        'icon',
        'auto_pay',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'interval_count' => 'integer',
        'start_date' => 'date',
        'next_billing_date' => 'date',
        'end_date' => 'date',
        'auto_pay' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Calculate next billing date
     */
    public function calculateNextBillingDate(): Carbon
    {
        $current = Carbon::parse($this->next_billing_date);

        return match($this->interval) {
            'daily' => $current->addDays($this->interval_count),
            'weekly' => $current->addWeeks($this->interval_count),
            'monthly' => $current->addMonths($this->interval_count),
            'yearly' => $current->addYears($this->interval_count),
        };
    }

    /**
     * Get days until next billing
     */
    public function daysUntilBilling(): int
    {
        return now()->diffInDays($this->next_billing_date, false);
    }

    /**
     * Check if billing is due soon (within 7 days)
     */
    public function isDueSoon(): bool
    {
        $days = $this->daysUntilBilling();
        return $days >= 0 && $days <= 7;
    }

    /**
     * Check if billing is overdue
     */
    public function isOverdue(): bool
    {
        return $this->daysUntilBilling() < 0;
    }
}
