<?php

namespace App\Filament\Resources\ExchangeRates\Tables;

use App\Models\Currency;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExchangeRatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('from_currency')
                    ->label('From')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('to_currency')
                    ->label('To')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('rate')
                    ->label('Rate')
                    ->numeric(decimalPlaces: 6)
                    ->sortable(),

                TextColumn::make('conversion_example')
                    ->label('Example')
                    ->getStateUsing(fn ($record) => '1 ' . $record->from_currency . ' = ' . number_format($record->rate, 6) . ' ' . $record->to_currency),

                TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('from_currency')
                    ->options(fn () => Currency::getActive()),

                SelectFilter::make('to_currency')
                    ->options(fn () => Currency::getActive()),

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
            ->defaultSort('date', 'desc');
    }
}
