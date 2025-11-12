<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Services\CurrencyService;
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
                        $baseCurrency = Currency::getBase()?->code ?? 'TRY';

                        if ($currency && $currency !== $baseCurrency && $date) {
                            $currencyService = app(CurrencyService::class);

                            // Try to get live rate first
                            $liveRate = $currencyService->getRateToTRY($currency);

                            // If live rate available, use it
                            if ($liveRate && $liveRate !== 1.0) {
                                $set('exchange_rate', $liveRate);
                            } else {
                                // Fallback to database
                                $rate = ExchangeRate::getRate($currency, $baseCurrency, $date);
                                $set('exchange_rate', $rate);
                            }
                        }
                    }),

                Select::make('currency')
                    ->label('Currency')
                    ->options(fn () => Currency::getOptions())
                    ->required()
                    ->default(fn () => Currency::getBase()?->code ?? 'TRY')
                    ->searchable()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, Get $get) {
                        $amount = $get('amount');
                        $date = $get('transaction_date');
                        $baseCurrency = Currency::getBase()?->code ?? 'TRY';

                        if ($state && $state !== $baseCurrency && $date) {
                            $currencyService = app(CurrencyService::class);

                            // Try to get live rate first
                            $liveRate = $currencyService->getRateToTRY($state);

                            if ($liveRate && $liveRate !== 1.0) {
                                $set('exchange_rate', $liveRate);
                            } else {
                                $rate = ExchangeRate::getRate($state, $baseCurrency, $date);
                                $set('exchange_rate', $rate);
                            }
                        } else {
                            $set('exchange_rate', 1);
                        }
                    }),

                TextInput::make('exchange_rate')
                    ->label(function () {
                        $base = Currency::getBase();
                        return 'Exchange Rate to ' . ($base ? $base->code : 'TRY');
                    })
                    ->numeric()
                    ->step(0.000001)
                    ->default(1)
                    ->disabled(function (Get $get) {
                        $base = Currency::getBase();
                        $baseCode = $base ? $base->code : 'TRY';
                        return $get('currency') === $baseCode;
                    })
                    ->dehydrated()
                    ->helperText(function (Get $get) {
                        $base = Currency::getBase();
                        $baseCurrency = $base ? $base->code : 'TRY';
                        $currentCurrency = $get('currency');
                        $rate = $get('exchange_rate');

                        if ($currentCurrency && $currentCurrency !== $baseCurrency && $rate) {
                            return '1 ' . $currentCurrency . ' = ' . $rate . ' ' . $baseCurrency;
                        }

                        return null;
                    }),

                DatePicker::make('transaction_date')
                    ->label('Transaction Date')
                    ->required()
                    ->default(now())
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, Get $get) {
                        $currency = $get('currency');
                        $baseCurrency = Currency::getBase()?->code ?? 'TRY';

                        if ($currency && $currency !== $baseCurrency && $state) {
                            $currencyService = app(CurrencyService::class);

                            $liveRate = $currencyService->getRateToTRY($currency);

                            if ($liveRate && $liveRate !== 1.0) {
                                $set('exchange_rate', $liveRate);
                            } else {
                                $rate = ExchangeRate::getRate($currency, $baseCurrency, $state);
                                $set('exchange_rate', $rate);
                            }
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
