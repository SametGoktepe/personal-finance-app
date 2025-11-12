<?php

namespace App\Filament\Resources\Subscriptions\Pages;

use App\Filament\Resources\Subscriptions\SubscriptionResource;
use App\Services\PdfReportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSubscriptions extends ListRecords
{
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Export Subscriptions Report')
                ->modalDescription('Generate a PDF report of all active subscriptions?')
                ->modalSubmitActionLabel('Export')
                ->action(function (PdfReportService $pdfService) {
                    return response()->streamDownload(function () use ($pdfService) {
                        echo $pdfService->generateSubscriptionsReport()->output();
                    }, 'subscriptions-report-' . now()->format('Y-m-d') . '.pdf');
                }),
            CreateAction::make(),
        ];
    }
}
