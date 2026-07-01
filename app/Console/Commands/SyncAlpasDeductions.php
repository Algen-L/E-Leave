<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncAlpasDeductions extends Command
{
    protected $signature = 'leave:sync-alpas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize deductions from the FromAlpas table (Check for credit changes)';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\AlpasDeductionService $service)
    {
        $this->info('Starting Alpas deduction sync...');
        
        $results = $service->syncAll();

        $this->table(['Category', 'Count'], [
            ['Processed Successfully', $results['processed']],
            ['Shortfall (Logged)', $results['shortfall']],
            ['Errors (See Logs)', $results['errors']],
        ]);

        $this->info('Alpas sync completed.');
    }
}
