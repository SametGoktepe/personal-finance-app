<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Category Name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),

                Select::make('type')
                    ->label('Type')
                    ->options([
                        'income' => 'Income',
                        'expense' => 'Expense',
                    ])
                    ->required()
                    ->default('expense')
                    ->native(false),

                ColorPicker::make('color')
                    ->label('Color')
                    ->default('#6366f1')
                    ->required(),

                TextInput::make('icon')
                    ->label('Icon')
                    ->placeholder('e.g., heroicon-o-shopping-cart')
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
