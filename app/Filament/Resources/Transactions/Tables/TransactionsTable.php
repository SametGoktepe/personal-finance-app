<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('account.name')
                    ->label('Account')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color(fn ($record) => $record->category->color ?? 'gray')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subscription.name')
                    ->label('Subscription')
                    ->badge()
                    ->icon('heroicon-o-arrow-path')
                    ->color(fn ($record) => $record->subscription?->color ?? 'gray')
                    ->default('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'income' => 'success',
                        'expense' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($record) => number_format($record->amount, 2) . ' ' . $record->currency)
                    ->sortable()
                    ->color(fn ($record): string => $record->type === 'income' ? 'success' : 'danger'),

                TextColumn::make('amount_try')
                    ->label('Amount (TRY)')
                    ->money('TRY')
                    ->getStateUsing(fn ($record) => $record->getAmountInBaseCurrency())
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderByRaw('amount * exchange_rate ' . $direction);
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_recurring')
                    ->label('Recurring')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'income' => 'Income',
                        'expense' => 'Expense',
                    ]),

                SelectFilter::make('account_id')
                    ->label('Account')
                    ->relationship('account', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('is_recurring')
                    ->label('Recurring')
                    ->options([
                        1 => 'Yes',
                        0 => 'No',
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
            ->defaultSort('transaction_date', 'desc');
    }
}
