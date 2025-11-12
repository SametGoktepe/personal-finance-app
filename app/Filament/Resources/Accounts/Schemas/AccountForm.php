<?php

namespace App\Filament\Resources\Accounts\Schemas;

use App\Models\Currency;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(fn () => Auth::id()),

                TextInput::make('name')
                    ->label('Account Name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),

                Select::make('type')
                    ->label('Account Type')
                    ->options([
                        'bank' => 'Bank Account',
                        'debit_card' => 'Debit Card',
                        'cash' => 'Cash',
                        'credit_card' => 'Credit Card',
                        'investment' => 'Investment',
                        'other' => 'Other',
                    ])
                    ->required()
                    ->default('bank')
                    ->native(false),

                TextInput::make('initial_balance')
                    ->label('Initial Balance')
                    ->numeric()
                    ->prefix('₺')
                    ->default(0)
                    ->required()
                    ->step(0.01),

                TextInput::make('current_balance')
                    ->label('Current Balance')
                    ->numeric()
                    ->prefix('₺')
                    ->default(0)
                    ->required()
                    ->step(0.01),

                Select::make('currency')
                    ->label('Currency')
                    ->options(fn () => Currency::getOptions())
                    ->required()
                    ->default(fn () => Currency::getBase()?->code ?? 'TRY')
                    ->searchable()
                    ->native(false),

                ColorPicker::make('color')
                    ->label('Color')
                    ->default('#3b82f6')
                    ->required(),

                TextInput::make('icon')
                    ->label('Icon')
                    ->placeholder('e.g., heroicon-o-banknotes')
                    ->maxLength(255),

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
