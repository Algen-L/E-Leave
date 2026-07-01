<?php

namespace App\Services;

use App\Models\FromDtr;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveCredit;
use App\Models\LeaveCreditAuditLog;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DtrDeductionService
{
    /**
     * Process deductions for all users with pending DTR records
     */
    public function syncAll()
    {
        // Get unique employee numbers from unprocessed records
        $employeeNumbers = FromDtr::where('is_processed', false)
            ->distinct()
            ->pluck('employee_number');

        $results = [
            'users_processed' => 0,
            'credits_deducted' => 0,
            'errors' => 0
        ];

        foreach ($employeeNumbers as $empNo) {
            try {
                $deducted = $this->processUserDeduction($empNo);
                $results['users_processed']++;
                $results['credits_deducted'] += $deducted;
            } catch (\Exception $e) {
                Log::error("Error processing DTR deduction for employee #{$empNo}: " . $e->getMessage());
                $results['errors']++;
            }
        }

        return $results;
    }

    /**
     * Process deduction for a single user
     */
    public function processUserDeduction($employeeNumber)
    {
        return DB::transaction(function () use ($employeeNumber) {
            // 1. Find User
            $user = User::where('employee_number', $employeeNumber)->first();
            if (!$user) {
                throw new \Exception("User with employee number {$employeeNumber} not found.");
            }

            // 2. Get pending records
            /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\FromDtr> $pendingRecords */
            $pendingRecords = FromDtr::where('employee_number', $employeeNumber)
                ->where('is_processed', false)
                ->get();

            if ($pendingRecords->isEmpty()) {
                return 0;
            }

            // 3. Calculate total minutes
            $pendingMinutes = $pendingRecords->sum('total_minutes');
            $totalAvailableMinutes = $user->dtr_minute_balance + $pendingMinutes;

            // 4. Calculate deduction (480 mins = 1 day)
            $daysToDeduct = floor($totalAvailableMinutes / 480);
            $remainingMinutes = $totalAvailableMinutes % 480;

            if ($daysToDeduct <= 0) {
                // Not enough for a full day yet, but we mark records as processed 
                // and move the total to the balance to keep it clean?
                // Actually, let's just update the balance and mark records as processed.
                $user->dtr_minute_balance = $totalAvailableMinutes;
                $user->save();

                foreach ($pendingRecords as $record) {
                    $record->is_processed = true;
                    $record->processed_at = now();
                    $record->save();
                }

                return 0;
            }

            // 5. Find Vacation Leave Type
            $vlType = LeaveType::where('type_name', 'Vacation Leave')->first();
            if (!$vlType) {
                throw new \Exception("Vacation Leave type not found in system.");
            }

            // 6. Find/Create Credit Record
            $credit = LeaveCredit::firstOrCreate(
                ['user_id' => $user->id, 'leave_type_id' => $vlType->id],
                ['credits' => 0]
            );

            // 7. Apply deduction
            $currentBalance = (float) $credit->credits;
            $oldCredits = $currentBalance;
            $newCredits = $currentBalance - $daysToDeduct;

            $credit->credits = $newCredits;
            $credit->save();

            // 8. Update User Balance and mark records as processed
            $user->dtr_minute_balance = (int) $remainingMinutes;
            $user->save();

            foreach ($pendingRecords as $record) {
                $record->is_processed = true;
                $record->processed_at = now();
                $record->save();
            }

            // 9. Log Audit
            $processedDates = $pendingRecords->pluck('date')->map(fn($d) => $d->format('Y-m-d'))->implode(', ');
            $reason = "DTR Accumulation Sync (480m=1d). Minutes: {$pendingMinutes} (+ carry: " . ($totalAvailableMinutes - $pendingMinutes) . "). Dates: {$processedDates}";

            LeaveCreditAuditLog::create([
                'actor_id' => null, // System action
                'target_user_id' => $user->id,
                'action' => 'deduction',
                'leave_type_name' => 'Vacation Leave',
                'previous_value' => $oldCredits,
                'new_value' => $newCredits,
                'reason' => $reason,
            ]);

            $actorId = Auth::id() ?? User::where('role', 'super_admin')->value('id') ?? User::first()->id ?? 1;
            ActivityLog::logAction($actorId, 'dtr_deduction', "Deducted {$daysToDeduct} VL credits from {$user->full_name} via DTR accumulated minutes. Carry-over: {$remainingMinutes}m");

            return $daysToDeduct;
        });
    }
}
