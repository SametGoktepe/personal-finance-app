<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class InsightsCategoryWidget extends ChartWidget
{
    protected ?string $heading = 'Top Spending Categories';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $categories = Transaction::query()
            ->select('category_id')
            ->selectRaw('SUM(amount) as total')
            ->with('category')
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Default colors
        $colors = [
            '#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#6366f1',
            '#8b5cf6', '#ec4899', '#14b8a6', '#f97316', '#06b6d4',
        ];

        $labels = [];
        $data = [];
        $backgroundColors = [];

        foreach ($categories as $index => $category) {
            $labels[] = $category->category?->name ?? 'Unknown';
            $data[] = (float) $category->total;
            $backgroundColors[] = $category->category?->color ?? $colors[$index % count($colors)];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Spending Amount (₺)',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}

