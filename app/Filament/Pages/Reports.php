<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Subscriptions\SubscriptionResource;
use App\Filament\Resources\Transactions\TransactionResource;
use App\Services\PdfReportService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Support\Enums\IconPosition;
use UnitEnum;

class Reports extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'Reports';

    protected static UnitEnum|string|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.pages.reports';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('financialSummary')
                ->label('Financial Summary')
                ->icon('heroicon-o-document-text')
                ->iconPosition(IconPosition::Before)
                ->color('warning')
                ->form([
                    DatePicker::make('start_date')
                        ->label('Start Date')
                        ->default(now()->startOfMonth())
                        ->required()
                        ->native(false),
                    DatePicker::make('end_date')
                        ->label('End Date')
                        ->default(now()->endOfMonth())
                        ->required()
                        ->native(false),
                ])
                ->action(function (array $data, PdfReportService $pdfService) {
                    $startDate = \Carbon\Carbon::parse($data['start_date']);
                    $endDate = \Carbon\Carbon::parse($data['end_date']);

                    return response()->streamDownload(function () use ($pdfService, $startDate, $endDate) {
                        echo $pdfService->generateFinancialSummary($startDate, $endDate)->output();
                    }, 'financial-summary-' . now()->format('Y-m-d') . '.pdf');
                }),

            Action::make('transactionsReport')
                ->label('Transactions')
                ->icon('heroicon-o-arrows-right-left')
                ->iconPosition(IconPosition::Before)
                ->color('primary')
                ->url(fn () => TransactionResource::getUrl('index')),

            Action::make('subscriptionsReport')
                ->label('Subscriptions')
                ->icon('heroicon-o-arrow-path')
                ->iconPosition(IconPosition::Before)
                ->color('success')
                ->url(fn () => SubscriptionResource::getUrl('index')),
        ];
    }
}
