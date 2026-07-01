<?php

namespace App\Services;

use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveCredit;
use App\Models\LeaveCreditPolicy;
use App\Models\LeaveCreditAuditLog;
use App\Models\CompensatoryLeaveCredit;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CreditService
{
    /**
     * Update regular leave credits for a user
     */
    public function updateUserCredits(User $user, array $submittedCredits)
    {
        return DB::transaction(function () use ($user, $submittedCredits) {
            foreach ($submittedCredits as $typeId => $rawAmount) {
                $amount = is_numeric($rawAmount) ? (float) $rawAmount : 0.0;

                $type = LeaveType::find($typeId);
                if (!$type || $type->type_name === 'COC Compensatory Overtime Credit')
                    continue;

                $policy = LeaveCreditPolicy::where('leave_type_id', $typeId)->first();
                if ($policy && $policy->max_credits > 0 && $amount > $policy->max_credits) {
                    throw new \Exception("Credit amount for {$type->type_name} exceeds the maximum policy limit of {$policy->max_credits}.");
                }

                $creditRecord = LeaveCredit::firstOrNew(['user_id' => $user->id, 'leave_type_id' => $typeId]);
                $oldValue = $creditRecord->credits ?? 0;

                $creditRecord->credits = $amount;
                $creditRecord->save();

                LeaveCreditAuditLog::create([
                    'actor_id' => Auth::id(),
                    'target_user_id' => $user->id,
                    'action' => $creditRecord->wasRecentlyCreated ? 'allocate' : 'update',
                    'leave_type_name' => $type->type_name,
                    'previous_value' => $oldValue,
                    'new_value' => $amount,
                    'reason' => 'Initial credit allocation by HR',
                ]);

                ActivityLog::logAction(
                    Auth::id(),
                    'update_credits',
                    "Updated {$type->type_name} credits for {$user->full_name} from {$oldValue} to {$amount}."
                );
            }
            return true;
        });
    }

    /**
     * Add a batch of COC (Compensatory Overtime Credit)
     */
    public function addCocCredit(User $user, array $data)
    {
        $ctoType = LeaveType::firstOrCreate(
            ['type_name' => 'CTO (Compensatory Time Off)'],
            ['description' => 'COC - Manual Entry', 'category' => 'Statutory', 'is_active' => true]
        );

        $currentTotal = LeaveCredit::where('user_id', $user->id)
            ->where('leave_type_id', $ctoType->id)
            ->value('credits') ?? 0;

        if (($currentTotal + $data['credit_amount']) > 15) {
            throw new \Exception("Cannot add credits. Total COC would exceed the limit of 15. Current: $currentTotal");
        }

        return DB::transaction(function () use ($user, $data, $ctoType, $currentTotal) {
            CompensatoryLeaveCredit::create([
                'user_id' => $user->id,
                'leave_type_id' => $ctoType->id,
                'credits' => $data['credit_amount'],
                'remaining_credits' => $data['credit_amount'],
                'expiration_date' => $data['expiration_date'],
                'remarks' => $data['remarks'] ?? null,
                'status' => 'Active',
                'added_by' => Auth::id(),
            ]);

            $creditRecord = LeaveCredit::firstOrNew([
                'user_id' => $user->id,
                'leave_type_id' => $ctoType->id
            ]);
            $creditRecord->credits = $currentTotal + $data['credit_amount'];
            $creditRecord->is_locked = false;
            $creditRecord->save();

            LeaveCreditAuditLog::create([
                'actor_id' => Auth::id(),
                'target_user_id' => $user->id,
                'action' => 'add_coc',
                'leave_type_name' => 'COC Compensatory Overtime Credit',
                'previous_value' => $currentTotal,
                'new_value' => $creditRecord->credits,
                'reason' => 'Added COC batch: ' . $data['credit_amount'] . ' expiring ' . $data['expiration_date'],
            ]);

            ActivityLog::logAction(
                Auth::id(),
                'add_coc',
                "Added {$data['credit_amount']} COC credits for {$user->full_name}. New Balance: {$creditRecord->credits}"
            );

            return $creditRecord;
        });
    }

    /**
     * Update leave credit policy
     */
    public function updatePolicy(array $data)
    {
        return LeaveCreditPolicy::updateOrCreate(
            ['leave_type_id' => $data['leave_type_id']],
            [
                'accrual_rate' => $data['accrual_rate'],
                'accrual_period' => $data['accrual_period'],
                'expiration_rule' => $data['expiration_rule'],
                'expiration_date' => $data['expiration_date'] ?? null,
                'max_credits' => $data['max_credits'] ?? null,
            ]
        );
    }

    /**
     * Accrue credits for all users based on a policy
     */
    public function accrueCredits(LeaveCreditPolicy $policy)
    {
        $users = User::where('is_active', true)
            ->whereNotIn('role', ['admin', 'super_admin'])
            ->get();

        $count = 0;
        foreach ($users as $user) {
            $credit = LeaveCredit::firstOrCreate(
                ['user_id' => $user->id, 'leave_type_id' => $policy->leave_type_id],
                ['credits' => 0]
            );

            $newBalance = $credit->credits + $policy->accrual_rate;

            if ($policy->max_credits && $newBalance > $policy->max_credits) {
                $newBalance = ($credit->credits < $policy->max_credits) ? $policy->max_credits : $credit->credits;
            }

            if ($newBalance != $credit->credits) {
                $credit->credits = $newBalance;
                $credit->save();

                ActivityLog::logAction(0, 'system_accrual', "System auto-added {$policy->accrual_rate} {$policy->leaveType->type_name} credits to {$user->full_name}.");
                $count++;
            }
        }
        return $count;
    }

    /**
     * Expire credits for all users based on a policy
     */
    public function expireCredits(LeaveCreditPolicy $policy)
    {
        $affected = LeaveCredit::where('leave_type_id', $policy->leave_type_id)
            ->where('credits', '>', 0)
            ->update(['credits' => 0]);

        if ($affected > 0) {
            ActivityLog::logAction(0, 'system_expiration', "System expired/reset {$policy->leaveType->type_name} credits for {$affected} users.");
        }
        return $affected;
    }

    /**
     * Reset CTO (Compensatory Time Off) balance to zero for a user
     */
    public function resetCtoBalance(User $user)
    {
        return DB::transaction(function () use ($user) {
            $ctoType = LeaveType::where('type_name', 'CTO (Compensatory Time Off)')->first();
            if (!$ctoType) return false;

            $creditRecord = LeaveCredit::where('user_id', $user->id)
                ->where('leave_type_id', $ctoType->id)
                ->first();

            $oldValue = $creditRecord ? (float) $creditRecord->credits : 0;

            if ($creditRecord) {
                $creditRecord->credits = 0;
                $creditRecord->save();
            } else {
                // If no record exists, we create one with zero
                LeaveCredit::create([
                    'user_id' => $user->id,
                    'leave_type_id' => $ctoType->id,
                    'credits' => 0
                ]);
            }

            // Also mark all related CompensatoryLeaveCredit as expired/used
            CompensatoryLeaveCredit::where('user_id', $user->id)
                ->where('leave_type_id', $ctoType->id)
                ->where('status', 'Active')
                ->update(['remaining_credits' => 0, 'status' => 'Expired']);

            LeaveCreditAuditLog::create([
                'actor_id' => Auth::id(),
                'target_user_id' => $user->id,
                'action' => 'reset_cto',
                'leave_type_name' => 'CTO (Compensatory Time Off)',
                'previous_value' => $oldValue,
                'new_value' => 0,
                'reason' => 'Manual balance reset to zero by HR',
            ]);

            ActivityLog::logAction(
                Auth::id(),
                'reset_cto',
                "Reset CTO balance for {$user->full_name} from {$oldValue} to 0."
            );

            return true;
        });
    }

    /**
     * Delete a specific COC batch and adjust user balance
     */
    public function deleteCocCredit(CompensatoryLeaveCredit $batch)
    {
        return DB::transaction(function () use ($batch) {
            $user = $batch->user;
            $amountToDelete = (float) $batch->remaining_credits;
            $originalAmount = (float) $batch->credits;

            $ctoType = LeaveType::where('type_name', 'CTO (Compensatory Time Off)')->first();
            if (!$ctoType) throw new \Exception("CTO Leave Type not found.");

            $creditRecord = LeaveCredit::where('user_id', $user->id)
                ->where('leave_type_id', $ctoType->id)
                ->first();

            $oldValue = $creditRecord ? (float) $creditRecord->credits : 0;

            if ($creditRecord) {
                $creditRecord->credits = max(0, $oldValue - $amountToDelete);
                $creditRecord->save();
            }

            $batch->delete();

            LeaveCreditAuditLog::create([
                'actor_id' => Auth::id(),
                'target_user_id' => $user->id,
                'action' => 'delete_coc_batch',
                'leave_type_name' => 'CTO (Compensatory Time Off)',
                'previous_value' => $oldValue,
                'new_value' => $creditRecord ? $creditRecord->credits : 0,
                'reason' => "Deleted COC batch added on {$batch->created_at->format('Y-m-d')}. Original: {$originalAmount}, Remaining: {$amountToDelete}",
            ]);

            ActivityLog::logAction(
                Auth::id(),
                'delete_coc_batch',
                "Deleted COC batch for {$user->full_name}. Deducted {$amountToDelete} remaining credits. New Balance: " . ($creditRecord ? $creditRecord->credits : 0)
            );

            return true;
        });
    }
}
