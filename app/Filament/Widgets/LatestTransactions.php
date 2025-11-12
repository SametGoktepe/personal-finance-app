<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestTransactions extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()
                    ->with(['account', 'category'])
                    ->latest('transaction_date')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('transaction_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('account.name')
                    ->label('Account')
                    ->searchable(),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color(fn ($record) => $record->category->color ?? 'gray'),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'income' => 'success',
                        'expense' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($record) => number_format($record->getAmountInBaseCurrency(), 2) . ' ₺')
                    ->color(fn ($record): string => $record->type === 'income' ? 'success' : 'danger'),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(30)
                    ->default('—'),
            ])
            ->heading('Latest Transactions');
    }
}
