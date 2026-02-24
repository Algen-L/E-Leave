<?php

namespace App\Services;

use App\Models\LeaveApplication;
use App\Models\LeaveDetailsForm6;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaveService
{
    /**
     * Get data for apply form
     */
    public function getApplyFormData(User $user)
    {
        $allLeaveTypes = LeaveType::where('is_active', true)->get();

        $standardNames = [
            'Vacation Leave',
            'Mandatory/Forced Leave',
            'Sick Leave',
            'Maternity Leave',
            'Paternity Leave',
            'Special Privilege Leave',
            'Solo Parent Leave',
            'Study Leave',
            'VAWC Leave',
            'Rehabilitation Leave',
            'Special Leave Benefits for Women',
            'Special Emergency (Calamity) Leave',
            'Adoption Leave',
            'Terminal Leave',
            'Monetization of Leave Credits'
        ];

        $standardTypes = $allLeaveTypes->filter(function ($type) use ($standardNames) {
            foreach ($standardNames as $name) {
                if (stripos($type->type_name, $name) !== false)
                    return true;
                if (($name === 'Mandatory/Forced Leave') && (stripos($type->type_name, 'Mandatory') !== false || stripos($type->type_name, 'Forced') !== false))
                    return true;
            }
            return false;
        });

        $otherTypes = $allLeaveTypes->diff($standardTypes)->filter(fn($type) => strtolower($type->type_name) !== 'others');

        return [
            'user' => $user,
            'standardTypes' => $standardTypes,
            'otherTypes' => $otherTypes,
        ];
    }

    /**
     * Submit leave application
     */
    public function submitApplication(User $user, array $data)
    {
        $leaveTypeId = $data['leave_type_id'];
        $othersType = $data['others_type'] ?? null;

        // Resolve Others type
        if (empty($leaveTypeId) && !empty($othersType)) {
            if (is_numeric($othersType)) {
                $leaveTypeId = $othersType;
            } elseif ($othersType === 'COC COMPENSATORY OVERTIME CREDIT') {
                $ctoType = LeaveType::firstOrCreate(['type_name' => 'COC Compensatory Overtime Credit'], ['description' => 'COC - Manual Entry']);
                $leaveTypeId = $ctoType->id;
            } elseif ($othersType === 'Specify') {
                $othersLeave = LeaveType::firstOrCreate(['type_name' => 'Others'], ['description' => 'Other purposes']);
                $leaveTypeId = $othersLeave->id;
            }
        }

        if (!$user->recommending_officer_id || !$user->approving_officer_id) {
            throw new \Exception('Please configure your Recommending and Approving Officers in your Profile before applying.');
        }

        return DB::transaction(function () use ($user, $data, $leaveTypeId) {
            $leaveType = LeaveType::findOrFail($leaveTypeId);
            $daysApplied = $data['days_applied'];

            // Validation Logic
            if ($leaveType->type_name === 'Wellness Leave' && $daysApplied > 3) {
                throw new \Exception('Wellness Leave applications cannot exceed 3 days.');
            }

            if (stripos($leaveType->type_name, 'Mandatory') !== false || stripos($leaveType->type_name, 'Forced') !== false) {
                $used = LeaveApplication::where('user_id', $user->id)
                    ->where('leave_type_id', $leaveType->id)
                    ->whereYear('start_date', now()->year)
                    ->whereIn('status', ['Pending HR', 'Pending Recommending', 'Pending Approval', 'Approved'])
                    ->sum('days_applied');

                if (($used + $daysApplied) > 5) {
                    throw new \Exception('Mandatory/Forced Leave cannot exceed 5 days per year. You have used/applied for ' . $used . ' days.');
                }
            }

            $dates = array_filter(explode(',', $data['selected_dates']));
            sort($dates);
            $startDate = $dates[0] ?? now();
            $endDate = end($dates) ?: $startDate;

            $application = LeaveApplication::create([
                'user_id' => $user->id,
                'leave_type_id' => $leaveTypeId,
                'date_filing' => now(),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'dates' => $dates,
                'days_applied' => $daysApplied,
                'commutation' => isset($data['commutation']) ? 'Requested' : 'Not Requested',
                'status' => 'Pending HR',
                'recommending_officer_id' => $user->recommending_officer_id,
                'approving_officer_id' => $user->approving_officer_id,
            ]);

            $otherPurpose = ($data['others_type'] ?? '') === 'COC COMPENSATORY OVERTIME CREDIT'
                ? 'COC COMPENSATORY OVERTIME CREDIT'
                : ($data['other_purpose'] ?? null);

            LeaveDetailsForm6::create([
                'leave_application_id' => $application->id,
                'leave_type_name' => $leaveType->type_name,
                'vacation_loc_type' => $data['vacation_loc_type'] ?? null,
                'vacation_loc_details' => $data['vacation_loc_details'] ?? null,
                'sick_loc_type' => $data['sick_loc_type'] ?? null,
                'sick_illness' => $data['sick_illness'] ?? null,
                'women_illness' => $data['women_illness'] ?? null,
                'study_type' => $data['study_type'] ?? null,
                'study_details' => $data['study_details'] ?? null,
                'other_purpose' => $otherPurpose,
            ]);

            return $application;
        });
    }

    /**
     * Get user application history and stats
     */
    public function getUserHistory(User $user)
    {
        $applications = LeaveApplication::with('leaveType')
            ->where('user_id', $user->id)
            ->orderBy('date_filing', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $stats = [
            'total' => $applications->count(),
            'approved' => $applications->where('status', 'Approved')->count(),
            'pending' => $applications->whereIn('status', ['Pending HR', 'Pending Recommending', 'Pending Approval', 'Pending'])->count(),
            'disapproved' => $applications->where('status', 'Disapproved')->count(),
        ];

        return compact('applications', 'stats');
    }
}
