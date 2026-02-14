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

        return back()->with('success', 'Application verified. Sent to Recommending Officer.');
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

        return back()->with('success', 'Application recommended. Sent to Final Approver.');
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

        $application->update([
            'status' => 'Approved',
            'approved_at' => now(),
        ]);

        // Logic: Deduct leave credits here if not done at verification?
        // Usually final deductions happen on approval.

        return back()->with('success', 'Application successfully APPROVED.');
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

        return back()->with('success', 'Application disapproved.');
    }
}
