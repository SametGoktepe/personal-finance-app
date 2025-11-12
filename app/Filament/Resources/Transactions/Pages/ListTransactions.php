<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use App\Services\PdfReportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
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
                    Select::make('type')
                        ->label('Transaction Type')
                        ->options([
                            '' => 'All Transactions',
                            'income' => 'Income Only',
                            'expense' => 'Expense Only',
                        ])
                        ->default('')
                        ->native(false),
                ])
                ->action(function (array $data, PdfReportService $pdfService) {
                    $startDate = $data['start_date'] ? \Carbon\Carbon::parse($data['start_date']) : now()->startOfMonth();
                    $endDate = $data['end_date'] ? \Carbon\Carbon::parse($data['end_date']) : now()->endOfMonth();
                    $type = $data['type'] ?: null;

                    return response()->streamDownload(function () use ($pdfService, $startDate, $endDate, $type) {
                        echo $pdfService->generateTransactionsReport($startDate, $endDate, $type)->output();
                    }, 'transactions-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.pdf');
                }),
            CreateAction::make(),
        ];
    }
}
