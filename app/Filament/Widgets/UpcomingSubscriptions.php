<?php

namespace App\Filament\Widgets;

use App\Models\Subscription;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class UpcomingSubscriptions extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Subscription::query()
                    ->where('is_active', true)
                    ->whereDate('next_billing_date', '<=', now()->addDays(14))
                    ->orderBy('next_billing_date', 'asc')
                    ->with(['category'])
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Subscription')
                    ->searchable()
                    ->weight('bold')
                    ->icon(fn ($record) => $record->icon)
                    ->iconColor(fn ($record) => $record->color),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color(fn ($record) => $record->category->color ?? 'gray'),

                TextColumn::make('price')
                    ->label('Amount')
                    ->formatStateUsing(fn ($record) => number_format($record->price, 2) . ' ' . $record->currency)
                    ->color('danger'),

                TextColumn::make('next_billing_date')
                    ->label('Due Date')
                    ->date()
                    ->color(fn ($record) => 
                        $record->isOverdue() ? 'danger' : 
                        ($record->isDueSoon() ? 'warning' : 'success')
                    )
                    ->description(fn ($record) => 
                        $record->isOverdue() 
                            ? '⚠️ Overdue by ' . abs($record->daysUntilBilling()) . ' days!' 
                            : ($record->isDueSoon() 
                                ? '🔔 Due in ' . $record->daysUntilBilling() . ' days' 
                                : 'In ' . $record->daysUntilBilling() . ' days')
                    ),

                TextColumn::make('interval')
                    ->label('Frequency')
                    ->formatStateUsing(fn ($record) => 
                        'Every ' . ($record->interval_count > 1 ? $record->interval_count . ' ' : '') . 
                        ucfirst($record->interval) . ($record->interval_count > 1 ? 's' : '')
                    )
                    ->badge()
                    ->color('info'),

                TextColumn::make('auto_pay')
                    ->label('Payment')
                    ->formatStateUsing(fn ($record) => $record->auto_pay ? '🤖 Auto' : '👤 Manual')
                    ->badge()
                    ->color(fn ($record) => $record->auto_pay ? 'success' : 'gray'),
            ])
            ->heading('Upcoming Subscriptions (Next 14 Days)')
            ->description('Subscriptions due in the next 2 weeks');
    }
}
