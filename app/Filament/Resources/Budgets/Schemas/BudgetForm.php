<?php

namespace App\Filament\Resources\Budgets\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BudgetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Budget Name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),

                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('amount')
                    ->label('Budget Amount')
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->step(0.01)
                    ->columnSpan(2),

                Select::make('period')
                    ->label('Period')
                    ->options([
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                        'yearly' => 'Yearly',
                    ])
                    ->required()
                    ->default('monthly')
                    ->native(false),

                DatePicker::make('start_date')
                    ->label('Start Date')
                    ->required()
                    ->default(now())
                    ->native(false),

                DatePicker::make('end_date')
                    ->label('End Date')
                    ->native(false),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->inline(false),

                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }
}
