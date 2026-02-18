<?php

namespace App\Http\Controllers;

use App\Models\LeaveApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveApprovalController extends Controller
{
    /**
     * Display a listing of applications pending the user's action.
     */
    public function index()
    {
        $user = Auth::user();
        $applications = collect();
        $title = 'Pending Approvals';

        // 1. HR Review (Pending HR)
        if (in_array($user->role, ['hr', 'head_hr', 'super_admin'])) {
            $hrPending = LeaveApplication::with(['user', 'leaveType'])
                ->where('status', 'Pending HR')
                ->orderBy('created_at', 'asc')
                ->get();
            
            // Allow HR to see these
            $applications = $applications->merge($hrPending);
        }

        // 2. Recommending Officer Review (Pending Recommending)
        // Check if user is assigned as someone's recommender
        $recommendingPending = LeaveApplication::with(['user', 'leaveType'])
            ->where('recommending_officer_id', $user->id)
            ->where('status', 'Pending Recommending')
            ->orderBy('created_at', 'asc')
            ->get();
        $applications = $applications->merge($recommendingPending);

        // 3. Final Approver Review (Pending Approval)
        $approvalPending = LeaveApplication::with(['user', 'leaveType'])
            ->where('approving_officer_id', $user->id)
            ->where('status', 'Pending Approval')
            ->orderBy('created_at', 'asc')
            ->get();
        $applications = $applications->merge($approvalPending);

        return view('leave.approvals.index', compact('applications', 'title'));
    }

    /**
     * Show application details for approval
     */
    public function show($id)
    {
        $application = LeaveApplication::with(['user', 'leaveType', 'details', 'recommendingOfficer', 'approvingOfficer'])->findOrFail($id);
        
        // Authorization check: User must be involved or HR
        $user = Auth::user();
        $canView = 
            in_array($user->role, ['hr', 'head_hr', 'super_admin']) ||
            $application->recommending_officer_id == $user->id ||
            $application->approving_officer_id == $user->id;

        if (!$canView) {
            abort(403);
        }

        // --- CREDIT CALCULATION LOGIC ---
        $applicant = $application->user;
        $appTypeName = $application->leaveType->type_name ?? '';
        $daysApplied = $application->days_applied;
        
        // Fetch Leave Types for ID lookup
        $vlType = \App\Models\LeaveType::where('type_name', 'Vacation Leave')->first();
        $slType = \App\Models\LeaveType::where('type_name', 'Sick Leave')->first();
        
        // 1. Fetch Current Credits
        $vlCredit = 0;
        if ($vlType) {
            $checkVl = \App\Models\LeaveCredit::where('user_id', $applicant->id)->where('leave_type_id', $vlType->id)->first();
            $vlCredit = $checkVl ? $checkVl->credits : 0;
        }
        
        $slCredit = 0;
        if ($slType) {
            $checkSl = \App\Models\LeaveCredit::where('user_id', $applicant->id)->where('leave_type_id', $slType->id)->first();
            $slCredit = $checkSl ? $checkSl->credits : 0;
        }

        // 2. Determine Deduction
        $lessVl = 0;
        $lessSl = 0;

        // Check for specific leave types or special conditions like Compensatory Time Off
        $isCompensatory = optional($application->details)->other_purpose === 'COMPENSATORY TIME OFF';
        $vlRelatedTypes = ['Vacation', 'Forced', 'Mandatory'];
        
        // Helper to check if type matches any of the keywords
        $isVlRelated = false;
        foreach ($vlRelatedTypes as $keyword) {
             if (stripos($appTypeName, $keyword) !== false) {
                 $isVlRelated = true;
                 break;
             }
        }

        if ($isVlRelated || $isCompensatory) {
             $lessVl = $daysApplied;
        } elseif (stripos($appTypeName, 'Sick') !== false) {
             $lessSl = $daysApplied;
        }
        
        // 3. Calculate Balances
        $vlBalance = $vlCredit - $lessVl;
        $slBalance = $slCredit - $lessSl;
        
        $credits = [
            'vl' => [
                'current' => $vlCredit,
                'less' => $lessVl,
                'balance' => $vlBalance
            ],
            'sl' => [
                'current' => $slCredit,
                'less' => $lessSl,
                'balance' => $slBalance
            ]
        ];

        return view('leave.approvals.show', compact('application', 'credits'));
    }

    /**
     * HR Verification Step
     */
    public function verify(Request $request, $id)
    {
        $application = LeaveApplication::findOrFail($id);
        
        // Ensure user is HR
        if (!in_array(Auth::user()->role, ['hr', 'head_hr', 'super_admin'])) {
            abort(403);
        }

        // Logic to update credits deduction could go here? 
        // For now, simple transition.

        $application->update([
            'status' => 'Pending Recommending',
            'hr_verified_at' => now(),
            'hr_verifier_id' => Auth::id(),
        ]);

        return redirect()->route('user.leave.approvals')->with('success', 'Application verified. Sent to Recommending Officer.');
    }

    /**
     * Recommending Officer Step
     */
    public function recommend(Request $request, $id)
    {
        $application = LeaveApplication::findOrFail($id);
        
        if ($application->recommending_officer_id != Auth::id()) {
            abort(403);
        }

        $application->update([
            'status' => 'Pending Approval',
            'recommended_at' => now(),
        ]);

        return redirect()->route('user.leave.approvals')->with('success', 'Application recommended. Sent to Final Approver.');
    }

    /**
     * Final Approval Step
     */
    public function approve(Request $request, $id)
    {
        $application = LeaveApplication::findOrFail($id);
        
        if ($application->approving_officer_id != Auth::id()) {
            abort(403);
        }

        $request->validate([
            'days_with_pay' => 'nullable|numeric|min:0',
            'days_without_pay' => 'nullable|numeric|min:0',
            'others_remarks' => 'nullable|string|max:255',
        ]);

        $application->update([
            'status' => 'Approved',
            'approved_at' => now(),
            'days_with_pay' => $request->days_with_pay,
            'days_without_pay' => $request->days_without_pay,
            'others_remarks' => $request->others_remarks,
        ]);

        // Logic: Deduct leave credits here if not done at verification?
        // Usually final deductions happen on approval.

        return redirect()->route('user.leave.approvals')->with('success', 'Application successfully APPROVED.');
    }

    /**
     * Disapprove / Reject
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'required|string|max:255',
        ]);

        $application = LeaveApplication::findOrFail($id);
        
        // Authorization check: User must be involved
        $canReject = 
            in_array(Auth::user()->role, ['hr', 'head_hr', 'super_admin']) ||
            $application->recommending_officer_id == Auth::id() ||
            $application->approving_officer_id == Auth::id();

        if (!$canReject) {
            abort(403);
        }

        $application->update([
            'status' => 'Disapproved',
            'rejected_at' => now(),
            'rejection_remarks' => $request->remarks
        ]);

        return redirect()->route('user.leave.approvals')->with('success', 'Application disapproved.');
    }
}
