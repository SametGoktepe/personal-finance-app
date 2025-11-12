<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Subscription')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color(fn ($record) => $record->category->color ?? 'gray')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Price')
                    ->formatStateUsing(fn ($record) => number_format($record->price, 2) . ' ' . $record->currency)
                    ->sortable(),

                TextColumn::make('billing_frequency')
                    ->label('Frequency')
                    ->getStateUsing(fn ($record) =>
                        'Every ' . ($record->interval_count > 1 ? $record->interval_count . ' ' : '') .
                        ucfirst($record->interval) . ($record->interval_count > 1 ? 's' : '')
                    )
                    ->badge()
                    ->color('info'),

                TextColumn::make('next_billing_date')
                    ->label('Next Billing')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->isDueSoon() ? 'warning' : ($record->isOverdue() ? 'danger' : 'success'))
                    ->description(fn ($record) =>
                        $record->isOverdue()
                            ? 'Overdue!'
                            : ($record->isDueSoon() ? 'Due soon!' : abs($record->daysUntilBilling()) . ' days')
                    ),

                ColorColumn::make('color')
                    ->label('Color'),

                IconColumn::make('auto_pay')
                    ->label('Auto Pay')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('transactions_count')
                    ->label('Payments')
                    ->counts('transactions')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('interval')
                    ->options([
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                        'yearly' => 'Yearly',
                    ]),

                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('next_billing_date', 'asc');
    }
}
