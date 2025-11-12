<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\InsightsBudgetWidget;
use App\Filament\Widgets\InsightsCategoryWidget;
use App\Filament\Widgets\InsightsMonthlyWidget;
use App\Filament\Widgets\InsightsStatsWidget;
use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class Insights extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = 'Insights';

    protected static UnitEnum|string|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 98;

    protected string $view = 'filament.pages.insights';

    protected function getHeaderWidgets(): array
    {
        return [
            InsightsStatsWidget::class,
            InsightsCategoryWidget::class,
            InsightsBudgetWidget::class,
            InsightsMonthlyWidget::class,
        ];
    }
}

