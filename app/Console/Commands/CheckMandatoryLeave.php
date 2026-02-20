<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveCredit;
use App\Models\LeaveApplication;
use App\Models\ActivityLog;
use Carbon\Carbon;

class CheckMandatoryLeave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:check-mandatory-compliance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'At year-end, check if users complied with default 5-day Mandatory Leave. If not, deduct remaining from VL.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        // This is a year-end task. If not Dec 31, return.
        if ($today->month !== 12 || $today->day !== 31) {
            $this->info('This command is strictly for December 31st.');
            return;
        }

        $this->info('Starting Mandatory Leave Compliance Check...');

        // 1. Get VL Type
        $vlType = LeaveType::where('type_name', 'Vacation Leave')->first();
        if (!$vlType) {
            $this->error('Vacation Leave not found.');
            return;
        }

        $users = User::where('is_active', true)->whereIn('role', ['user', 'hr', 'head_hr'])->get();

        foreach ($users as $user) {
            // Calculate total VL used (including approved Vacation, Mandatory, Forced)
            $totalUsed = LeaveApplication::where('user_id', $user->id)
                ->where('status', 'Approved')
                ->whereHas('leaveType', function ($q) {
                    $q->where('type_name', 'like', '%Vacation%')
                        ->orWhere('type_name', 'like', '%Mandatory%')
                        ->orWhere('type_name', 'like', '%Forced%');
                })
                ->whereYear('start_date', $today->year)
                ->sum('days_with_pay');

            $this->info("User: {$user->full_name} | Used: {$totalUsed}");

            if ($totalUsed < 5) {
                // Determine if user is even subject to forced leave (Must have 10 or more days to be REQUIRED)
                $credit = LeaveCredit::firstOrCreate(
                    ['user_id' => $user->id, 'leave_type_id' => $vlType->id],
                    ['credits' => 0]
                );

                if ($credit->credits < 10) {
                    $this->info("  -> EXEMPT (VL balance {$credit->credits} is less than 10).");
                    continue;
                }

                $daysToDeduct = 5 - $totalUsed;

                if ($credit->credits > 0) {
                    // Only deduct up to remaining balance (cannot go negative on forfeiture)
                    $deduction = min($credit->credits, $daysToDeduct);

                    if ($deduction > 0) {
                        $credit->decrement('credits', $deduction);

                        ActivityLog::logAction(
                            $user->id,
                            'Mandatory Forfeiture',
                            "System deducted {$deduction} days. (Policy: Must use 5 days VL. Used: {$totalUsed})."
                        );

                        $this->info("  -> Deducted {$deduction} days.");
                    }
                }
            }
        }
    }
}
