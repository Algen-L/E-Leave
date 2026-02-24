<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeaveCreditPolicy;
use App\Services\CreditService;
use Carbon\Carbon;

class AutoAccrueCredits extends Command
{
    protected $signature = 'leave:auto-accrue-credits';
    protected $description = 'Automatically accrue leave credits based on policies (Run Daily)';

    protected $creditService;

    public function __construct(CreditService $creditService)
    {
        parent::__construct();
        $this->creditService = $creditService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automated leave credit accrual...');

        $policies = LeaveCreditPolicy::with('leaveType')->get();
        $today = Carbon::today();

        foreach ($policies as $policy) {
            $type = $policy->leaveType;
            $this->info("Processing policy for: {$type->type_name}");

            // --- 1. ACCRUAL LOGIC ---
            $shouldAccrue = false;
            if ($policy->accrual_period === 'Monthly' && $today->day === 24) {
                $shouldAccrue = true;
            } elseif ($policy->accrual_period === 'Yearly' && $today->dayOfYear === 1) {
                $shouldAccrue = true;
            }

            if ($shouldAccrue && $policy->accrual_rate > 0) {
                $count = $this->creditService->accrueCredits($policy);
                $this->info(" -> Accrued credits for {$count} users.");
            }

            // --- 2. EXPIRATION LOGIC ---
            $shouldExpire = false;
            if ($policy->expiration_rule === 'Yearly' && $today->month === 12 && $today->day === 31) {
                $shouldExpire = true;
            } elseif ($policy->expiration_rule === 'Monthly' && $today->isLastOfMonth()) {
                $shouldExpire = true;
            } elseif ($policy->expiration_rule === 'SpecificDate' && $policy->expiration_date) {
                if ($today->isSameDay(Carbon::parse($policy->expiration_date))) {
                    $shouldExpire = true;
                }
            }

            if ($shouldExpire) {
                $affected = $this->creditService->expireCredits($policy);
                $this->info(" -> Expired credits for {$affected} users.");
            }
        }

        $this->info('Automation completed successfully.');
    }
}
