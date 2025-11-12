<?php

namespace App\Filament\Resources\ExchangeRates\Schemas;

use App\Models\Currency;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ExchangeRateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('from_currency')
                    ->label('From Currency')
                    ->options(fn () => Currency::getActive())
                    ->required()
                    ->searchable()
                    ->native(false),

                Select::make('to_currency')
                    ->label('To Currency')
                    ->options(fn () => Currency::getActive())
                    ->required()
                    ->searchable()
                    ->native(false),

                TextInput::make('rate')
                    ->label('Exchange Rate')
                    ->numeric()
                    ->required()
                    ->step(0.000001)
                    ->helperText('How much TO currency equals 1 FROM currency'),

                DatePicker::make('date')
                    ->label('Date')
                    ->required()
                    ->default(now())
                    ->native(false),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->inline(false),
            ])
            ->columns(2);
    }
}
