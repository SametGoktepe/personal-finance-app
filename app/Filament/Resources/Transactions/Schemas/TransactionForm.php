<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\ExchangeRate;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('account_id')
                    ->label('Account')
                    ->relationship('account', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpan(2),

                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('subscription_id')
                    ->label('Subscription')
                    ->relationship('subscription', 'name', fn ($query) => $query->where('is_active', true))
                    ->searchable()
                    ->preload()
                    ->helperText('Link this transaction to a subscription payment'),

                Select::make('type')
                    ->label('Type')
                    ->options([
                        'income' => 'Income',
                        'expense' => 'Expense',
                    ])
                    ->required()
                    ->default('expense')
                    ->native(false)
                    ->live(),

                TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->required()
                    ->step(0.01)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set, Get $get) {
                        $currency = $get('currency');
                        $date = $get('transaction_date');
                        if ($currency && $currency !== 'TRY' && $date) {
                            $rate = ExchangeRate::getRate($currency, 'TRY', $date);
                            $set('exchange_rate', $rate);
                        }
                    }),

                Select::make('currency')
                    ->label('Currency')
                    ->options([
                        'TRY' => 'TRY - Turkish Lira',
                        'USD' => 'USD - US Dollar',
                        'EUR' => 'EUR - Euro',
                        'GBP' => 'GBP - British Pound',
                        'JPY' => 'JPY - Japanese Yen',
                    ])
                    ->required()
                    ->default('TRY')
                    ->searchable()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, Get $get) {
                        $amount = $get('amount');
                        $date = $get('transaction_date');
                        if ($state && $state !== 'TRY' && $date) {
                            $rate = ExchangeRate::getRate($state, 'TRY', $date);
                            $set('exchange_rate', $rate);
                        } else {
                            $set('exchange_rate', 1);
                        }
                    }),

                TextInput::make('exchange_rate')
                    ->label('Exchange Rate to TRY')
                    ->numeric()
                    ->step(0.000001)
                    ->default(1)
                    ->disabled(fn (Get $get) => $get('currency') === 'TRY')
                    ->dehydrated()
                    ->helperText(fn (Get $get) => $get('currency') !== 'TRY' ? '1 ' . $get('currency') . ' = ' . $get('exchange_rate') . ' TRY' : null),

                DatePicker::make('transaction_date')
                    ->label('Transaction Date')
                    ->required()
                    ->default(now())
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, Get $get) {
                        $currency = $get('currency');
                        if ($currency && $currency !== 'TRY' && $state) {
                            $rate = ExchangeRate::getRate($currency, 'TRY', $state);
                            $set('exchange_rate', $rate);
                        }
                    }),

                TextInput::make('description')
                    ->label('Description')
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('reference')
                    ->label('Reference Number')
                    ->maxLength(255),

                Toggle::make('is_recurring')
                    ->label('Recurring Transaction')
                    ->default(false)
                    ->inline(false)
                    ->live(),

                Select::make('recurring_interval')
                    ->label('Recurring Interval')
                    ->options([
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                        'yearly' => 'Yearly',
                    ])
                    ->native(false)
                    ->visible(fn ($get) => $get('is_recurring')),

                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }
}
