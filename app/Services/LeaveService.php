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
            } elseif ($othersType === 'CTO (Compensatory Time Off)') {
                $ctoType = LeaveType::firstOrCreate(['type_name' => 'CTO (Compensatory Time Off)'], ['description' => 'COC - Manual Entry']);
                $leaveTypeId = $ctoType->id;
            } elseif ($othersType === 'Specify') {
                $othersLeave = LeaveType::firstOrCreate(['type_name' => 'Others'], ['description' => 'Other purposes']);
                $leaveTypeId = $othersLeave->id;
            }
        }

        if (!$user->isRecordPersonnel() && (!$user->recommending_officer_id || !$user->approving_officer_id || (!$user->department_head_id && !$user->is_dept_head))) {
            throw new \Exception('Please configure your Department Head, Recommending, and Approving Officers in your Profile before applying.');
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

            // Generate tracking number
            $random = strtoupper(\Illuminate\Support\Str::random(3));
            $yearMonth = now()->format('Ym');
            $count = LeaveApplication::whereYear('date_filing', now()->year)
                ->whereMonth('date_filing', now()->month)
                ->count();
            $series = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            $trackingNumber = "EL{$random}-{$yearMonth}-{$series}";

            $recommendingOfficerId = $user->recommending_officer_id;
            $approvingOfficerId = $user->approving_officer_id;
            $asdsId = null;

            if ($daysApplied > 6) {
                $sds = User::where('role', 'sds')->first();
                $asds = User::where('role', 'asds')->first();
                
                if ($sds) $approvingOfficerId = $sds->id;
                if ($asds) $asdsId = $asds->id;
                // Note: Recommending Officer remains the designation recorded on the User profile
            }

            $application = LeaveApplication::create([
                'user_id' => $user->id,
                'tracking_number' => $trackingNumber,
                'leave_type_id' => $leaveTypeId,
                'date_filing' => now(),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'dates' => $dates,
                'days_applied' => $daysApplied,
                'commutation' => isset($data['commutation']) ? 'Requested' : 'Not Requested',
                'status' => 'Pending HR',
                'recommending_officer_id' => $recommendingOfficerId,
                'approving_officer_id' => $approvingOfficerId,
                'asds_id' => $asdsId,
            ]);

            $otherPurpose = $data['other_purpose'] ?? null;
            if (empty($otherPurpose)) {
                if (($data['others_type'] ?? '') === 'CTO (Compensatory Time Off)') {
                    $otherPurpose = 'CTO (Compensatory Time Off)';
                } elseif (is_numeric($data['others_type'] ?? '')) {
                    $otherPurpose = $leaveType->type_name;
                }
            }

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

            // Notify Department Head of the new application
            if ($user->department_head_id) {
                $dateFilingStr = now()->format('F j, Y');
                $message = "New Application Submitted: {$user->full_name} submitted a Leave Application. Submitted: {$dateFilingStr}. Status: Pending Review.";
                $linkUrl = route('user.leave.show', ['id' => $application->id]);
                \App\Models\Notification::send($user->id, $user->department_head_id, $message, $linkUrl);
            }

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
