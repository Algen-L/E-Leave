<?php

namespace App\Services;

use App\Models\FromAlpas;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveCredit;
use App\Models\LeaveCreditAuditLog;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AlpasDeductionService
{
    /**
     * Process deductions for all pending Alpas records
     */
    public function syncAll()
    {
        $pending = FromAlpas::whereRaw('leave_credits > processed_credits')->get();
        $results = [
            'processed' => 0,
            'shortfall' => 0,
            'errors' => 0
        ];

        foreach ($pending as $record) {
            try {
                $status = $this->processRecord($record);
                if ($status === 'success') $results['processed']++;
                elseif ($status === 'shortfall') $results['shortfall']++;
            } catch (\Exception $e) {
                Log::error("Error processing Alpas record #{$record->id}: " . $e->getMessage());
                $results['errors']++;
            }
        }

        return $results;
    }

    /**
     * Process a single Alpas record deduction
     */
    public function processRecord(FromAlpas $record)
    {
        return DB::transaction(function () use ($record) {
            // 1. Find User
            $user = User::where('employee_number', $record->employee_no)->first();
            if (!$user) {
                throw new \Exception("User with employee number {$record->employee_no} not found.");
            }

            // 2. Find Vacation Leave Type
            $vlType = LeaveType::where('type_name', 'Vacation Leave')->first();
            if (!$vlType) {
                throw new \Exception("Vacation Leave type not found in system.");
            }

            // 3. Find/Create Credit Record
            $credit = LeaveCredit::firstOrCreate(
                ['user_id' => $user->id, 'leave_type_id' => $vlType->id],
                ['credits' => 0]
            );

            // 4. Calculate deduction needed
            $targetTotal = (float) $record->leave_credits;
            $alreadyProcessed = (float) $record->processed_credits;
            $toDeduct = $targetTotal - $alreadyProcessed;

            if ($toDeduct <= 0) return 'success';

            $currentBalance = (float) $credit->credits;
            $actualDeduction = min($toDeduct, $currentBalance);
            $shortfall = $toDeduct - $actualDeduction;

            // 5. Apply deduction
            $oldCredits = $currentBalance;
            $newCredits = $currentBalance - $actualDeduction;
            
            $credit->credits = $newCredits;
            $credit->save();

            // 6. Update Alpas record
            $record->processed_credits += $actualDeduction;
            $record->save();

            // 7. Log Audit
            $reason = "Alpas Sync: {$record->source_system} - {$record->source_reference}";
            if ($shortfall > 0) {
                $reason .= " (Shortfall: {$shortfall} credits couldn't be deducted)";
            }

            LeaveCreditAuditLog::create([
                'actor_id' => null, // System action
                'target_user_id' => $user->id,
                'action' => 'deduction',
                'leave_type_name' => 'Vacation Leave',
                'previous_value' => $oldCredits,
                'new_value' => $newCredits,
                'reason' => $reason,
            ]);

            $actorId = Auth::id() ?? User::where('role', 'super_admin')->value('id') ?? 1;

            ActivityLog::logAction($actorId, 'alpas_deduction', "Deducted {$actualDeduction} VL credits from {$user->full_name} via Alpas sync. Shortfall: {$shortfall}");

            return $shortfall > 0 ? 'shortfall' : 'success';
        });
    }
}
