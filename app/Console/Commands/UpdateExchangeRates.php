<?php

namespace App\Console\Commands;

use App\Services\CurrencyService;
use Illuminate\Console\Command;

class UpdateExchangeRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rates:update {--force : Force update even if recently updated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update exchange rates from Turkish Central Bank API';

    /**
     * Execute the console command.
     */
    public function handle(CurrencyService $currencyService)
    {
        $this->info('Fetching exchange rates from API...');

        $apiUpdateTime = $currencyService->getApiUpdateTime();
        if ($apiUpdateTime) {
            $this->info('API Last Update: ' . $apiUpdateTime);
        }

        $this->newLine();
        $this->info('Updating exchange rates...');

        $results = $currencyService->updateExchangeRates();

        $this->newLine();

        if (count($results['success']) > 0) {
            $this->info('✓ Successfully updated:');
            foreach ($results['success'] as $message) {
                $this->line('  • ' . $message);
            }
        }

        $this->newLine();

        if (count($results['failed']) > 0) {
            $this->warn('✗ Failed to update:');
            foreach ($results['failed'] as $message) {
                $this->line('  • ' . $message);
            }
        }

        $this->newLine();
        $this->info("Total rates updated: {$results['total']}");

        if ($results['total'] > 0) {
            $this->newLine();
            $this->info('✓ Exchange rates updated successfully!');
            return Command::SUCCESS;
        }

        $this->error('✗ No exchange rates were updated.');
        return Command::FAILURE;
    }
}
