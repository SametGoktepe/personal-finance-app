<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name', fn ($query) => $query->where('type', 'expense'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpan(2),

                TextInput::make('name')
                    ->label('Subscription Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., Netflix, Spotify, etc.')
                    ->columnSpan(2),

                TextInput::make('price')
                    ->label('Price')
                    ->numeric()
                    ->prefix('₺')
                    ->required()
                    ->step(0.01),

                Select::make('currency')
                    ->label('Currency')
                    ->options([
                        'TRY' => 'TRY - Turkish Lira',
                        'USD' => 'USD - US Dollar',
                        'EUR' => 'EUR - Euro',
                        'GBP' => 'GBP - British Pound',
                    ])
                    ->required()
                    ->default('TRY')
                    ->searchable()
                    ->native(false),

                Select::make('interval')
                    ->label('Billing Interval')
                    ->options([
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                        'yearly' => 'Yearly',
                    ])
                    ->required()
                    ->default('monthly')
                    ->native(false)
                    ->live(),

                TextInput::make('interval_count')
                    ->label('Every')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->required()
                    ->suffix(fn (Get $get) => match($get('interval')) {
                        'daily' => 'day(s)',
                        'weekly' => 'week(s)',
                        'monthly' => 'month(s)',
                        'yearly' => 'year(s)',
                        default => 'period(s)',
                    })
                    ->helperText('Billing frequency'),

                DatePicker::make('start_date')
                    ->label('Start Date')
                    ->required()
                    ->default(now())
                    ->native(false),

                DatePicker::make('next_billing_date')
                    ->label('Next Billing Date')
                    ->required()
                    ->default(now()->addMonth())
                    ->native(false),

                DatePicker::make('end_date')
                    ->label('End Date')
                    ->native(false)
                    ->helperText('Leave empty for ongoing subscription'),

                ColorPicker::make('color')
                    ->label('Color')
                    ->default('#8b5cf6')
                    ->required(),

                TextInput::make('icon')
                    ->label('Icon')
                    ->placeholder('e.g., heroicon-o-play')
                    ->maxLength(255),

                Toggle::make('auto_pay')
                    ->label('Auto Payment')
                    ->default(false)
                    ->inline(false)
                    ->helperText('Automatically create transaction on billing date'),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->inline(false),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }
}
