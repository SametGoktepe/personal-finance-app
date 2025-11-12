<?php

namespace App\Filament\Resources\ExchangeRates\Pages;

use App\Filament\Resources\ExchangeRates\ExchangeRateResource;
use App\Services\CurrencyService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListExchangeRates extends ListRecords
{
    protected static string $resource = ExchangeRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('updateFromApi')
                ->label('Update from API')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Update Exchange Rates')
                ->modalDescription('Fetch latest exchange rates from Turkish Central Bank API?')
                ->modalSubmitActionLabel('Update')
                ->action(function (CurrencyService $currencyService) {
                    $results = $currencyService->updateExchangeRates();

                    if ($results['total'] > 0) {
                        Notification::make()
                            ->title('Exchange Rates Updated')
                            ->body("Successfully updated {$results['total']} exchange rates.")
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Update Failed')
                            ->body('Could not fetch exchange rates from API. Check logs for details.')
                            ->danger()
                            ->send();
                    }

                    // Refresh the page to show new rates
                    redirect()->to(ExchangeRateResource::getUrl('index'));
                }),
            CreateAction::make(),
        ];
    }
}
