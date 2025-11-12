<?php

namespace App\Filament\Widgets;

use App\Models\Budget;
use App\Models\Transaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class InsightsBudgetWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): ?string
    {
        return 'Budget Progress';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Budget::query()
                    ->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where(function ($query) {
                        $query->whereNull('end_date')
                            ->orWhere('end_date', '>=', now());
                    })
                    ->with('category')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Budget Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color(fn ($record) => $record->category?->color ? 'primary' : 'gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Budget Amount')
                    ->money('TRY')
                    ->sortable(),

                Tables\Columns\TextColumn::make('spent')
                    ->label('Spent')
                    ->getStateUsing(function ($record) {
                        return Transaction::where('category_id', $record->category_id)
                            ->where('type', 'expense')
                            ->whereBetween('transaction_date', [
                                $record->start_date,
                                $record->end_date ?? now()
                            ])
                            ->sum('amount');
                    })
                    ->money('TRY')
                    ->color(fn ($record, $state) => $state > $record->amount ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('remaining')
                    ->label('Remaining')
                    ->getStateUsing(function ($record) {
                        $spent = Transaction::where('category_id', $record->category_id)
                            ->where('type', 'expense')
                            ->whereBetween('transaction_date', [
                                $record->start_date,
                                $record->end_date ?? now()
                            ])
                            ->sum('amount');
                        return $record->amount - $spent;
                    })
                    ->money('TRY')
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('progress')
                    ->label('Progress')
                    ->getStateUsing(function ($record) {
                        $spent = Transaction::where('category_id', $record->category_id)
                            ->where('type', 'expense')
                            ->whereBetween('transaction_date', [
                                $record->start_date,
                                $record->end_date ?? now()
                            ])
                            ->sum('amount');
                        $percentage = $record->amount > 0 ? ($spent / $record->amount) * 100 : 0;
                        return number_format(min($percentage, 100), 1) . '%';
                    })
                    ->badge()
                    ->color(function ($record) {
                        $spent = Transaction::where('category_id', $record->category_id)
                            ->where('type', 'expense')
                            ->whereBetween('transaction_date', [
                                $record->start_date,
                                $record->end_date ?? now()
                            ])
                            ->sum('amount');
                        $percentage = $record->amount > 0 ? ($spent / $record->amount) * 100 : 0;

                        if ($percentage >= 100) return 'danger';
                        if ($percentage >= 75) return 'warning';
                        return 'success';
                    }),
            ])
            ->defaultSort('name', 'asc');
    }
}

