<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeaveCreditPolicy;
use App\Models\LeaveCredit;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AutoAccrueCredits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:auto-accrue-credits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically accrue leave credits based on policies (Run Daily)';

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
            
            // Monthly accrual: Check if today is the 24th
            if ($policy->accrual_period === 'Monthly' && $today->day === 24) {
                $shouldAccrue = true;
            } elseif ($policy->accrual_period === 'Yearly' && $today->dayOfYear === 1) {
                // Yearly accrual on Jan 1st
                $shouldAccrue = true;
            }
            
            if ($shouldAccrue && $policy->accrual_rate > 0) {
                $this->processAccrual($policy);
            }
            
            // --- 2. EXPIRATION LOGIC ---
            $shouldExpire = false;
            
            if ($policy->expiration_rule === 'Yearly' && $today->month === 12 && $today->day === 31) {
                // End of year expiration
                $shouldExpire = true;
            } elseif ($policy->expiration_rule === 'Monthly' && $today->isLastOfMonth()) {
                // End of month expiration
                $shouldExpire = true;
            } elseif ($policy->expiration_rule === 'SpecificDate' && $policy->expiration_date) {
                // Specific date
                $expDate = Carbon::parse($policy->expiration_date);
                if ($today->isSameDay($expDate)) {
                    $shouldExpire = true;
                }
            }
            
            if ($shouldExpire) {
                $this->processExpiration($policy);
            }
        }
        
        $this->info('Automation completed successfully.');
    }

    /**
     * Add credits to all eligible users
     */
    private function processAccrual($policy)
    {
        // Get all active regular employees (exclude admin/super_admin if needed, usually 'user' role)
        $users = User::where('is_active', true)
            ->whereIn('role', ['user', 'hr', 'head_hr', 'immediate_head']) // Add roles that earn leave
            ->get();
            
        $count = 0;
        
        foreach ($users as $user) {
            // Find or create credit record
            $credit = LeaveCredit::firstOrCreate(
                ['user_id' => $user->id, 'leave_type_id' => $policy->leave_type_id],
                ['credits' => 0]
            );
            
            // Calculate new balance
            $newBalance = $credit->credits + $policy->accrual_rate;
            
            // Cap logic
            if ($policy->max_credits && $newBalance > $policy->max_credits) {
                // If adding credits exceeds cap, set to max allowed
                // Unless existing was ALREADY over cap (e.g. manual override), then don't reduce it, just don't add
                if ($credit->credits < $policy->max_credits) {
                    $newBalance = $policy->max_credits;
                } else {
                    $newBalance = $credit->credits; // No increase
                }
            }
            
            if ($newBalance != $credit->credits) {
                $oldVal = $credit->credits;
                $credit->credits = $newBalance;
                $credit->save();
                
                // Log activity
                ActivityLog::logAction(
                    0, // System user ID usually 0 or specific bot ID
                    'system_accrual', 
                    "System auto-added {$policy->accrual_rate} {$policy->leaveType->type_name} credits to {$user->full_name}.",
                    '127.0.0.1'
                );
                
                $count++;
            }
        }
        
        $this->info(" -> Accrued credits for {$count} users.");
    }

    /**
     * Reset credits based on expiration
     */
    private function processExpiration($policy)
    {
        // Reset logic: usually sets creating to 0, or reduces excess
        // For standard "Yearly Expiration", it means "Forfeited" -> Set to 0
        
        $affected = LeaveCredit::where('leave_type_id', $policy->leave_type_id)
            ->where('credits', '>', 0)
            ->update(['credits' => 0]);
            
        if ($affected > 0) {
            ActivityLog::logAction(
                0, 
                'system_expiration', 
                "System expired/reset {$policy->leaveType->type_name} credits for {$affected} users.",
                '127.0.0.1'
            );
        }
        
        $this->info(" -> Expired credits for {$affected} users.");
    }
}
