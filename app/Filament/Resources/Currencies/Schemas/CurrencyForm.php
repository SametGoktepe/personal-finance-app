<?php

namespace App\Filament\Resources\Currencies\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CurrencyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Currency Code')
                    ->required()
                    ->maxLength(3)
                    ->placeholder('e.g., USD, EUR, CHF')
                    ->unique(ignoreRecord: true)
                    ->helperText('3-letter ISO currency code (uppercase)')
                    ->rule('regex:/^[A-Z]{3}$/')
                    ->validationMessages([
                        'regex' => 'Currency code must be 3 uppercase letters.',
                    ]),

                TextInput::make('name')
                    ->label('Currency Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., US Dollar, Euro')
                    ->columnSpan(2),

                TextInput::make('symbol')
                    ->label('Symbol')
                    ->required()
                    ->maxLength(10)
                    ->placeholder('e.g., $, €, £, ₺')
                    ->helperText('Currency symbol'),

                TextInput::make('decimal_places')
                    ->label('Decimal Places')
                    ->numeric()
                    ->default(2)
                    ->minValue(0)
                    ->maxValue(8)
                    ->required()
                    ->helperText('Number of decimal places for this currency'),

                Toggle::make('is_base')
                    ->label('Base Currency')
                    ->default(false)
                    ->inline(false)
                    ->helperText('Only one currency can be base currency'),

                Toggle::make('api_enabled')
                    ->label('API Enabled')
                    ->default(false)
                    ->inline(false)
                    ->helperText('Fetch rates from finans.truncgil.com API'),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->inline(false),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->columnSpanFull()
                    ->placeholder('Additional information about this currency'),
            ])
            ->columns(3);
    }
}
