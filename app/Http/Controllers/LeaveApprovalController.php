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
        $application = LeaveApplication::with(['user', 'leaveType', 'details', 'recommendingOfficer', 'approvingOfficer', 'hrVerifier'])->findOrFail($id);

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

        // Check for specific leave types or special conditions like COC Compensatory Overtime Credit
        $isCompensatory = optional($application->details)->other_purpose === 'COC COMPENSATORY OVERTIME CREDIT';
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

        $request->validate([
            'days_with_pay' => 'nullable|numeric|min:0',
            'days_without_pay' => 'nullable|numeric|min:0',
            'others_remarks' => 'nullable|string|max:255',
        ]);

        $application->update([
            'status' => 'Pending Recommending',
            'hr_verified_at' => now(),
            'hr_verifier_id' => Auth::id(),
            'days_with_pay' => $request->days_with_pay,
            'days_without_pay' => $request->days_without_pay,
            'others_remarks' => $request->others_remarks,
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
        $application = LeaveApplication::with(['leaveType', 'user', 'details'])->findOrFail($id);

        if ($application->approving_officer_id != Auth::id()) {
            abort(403);
        }

        // Recommendations are now pre-filled by HR

        try {
            DB::beginTransaction();

            $application->update([
                'status' => 'Approved',
                'approved_at' => now(),
            ]);

            // --- LEAVE CREDIT DEDUCTION LOGIC ---
            $user = $application->user;
            $leaveType = $application->leaveType;
            $daysToDeduct = (float) $application->days_applied;

            // 1. Determine which credit pool to deduct from
            $isVlRelated = false;
            $vlKeywords = ['Vacation', 'Forced', 'Mandatory'];
            foreach ($vlKeywords as $kw) {
                if (stripos($leaveType->type_name, $kw) !== false) {
                    $isVlRelated = true;
                    break;
                }
            }

            $isSick = stripos($leaveType->type_name, 'Sick') !== false;
            $isCTO = (stripos($leaveType->type_name, 'Compensatory') !== false);

            if ($isVlRelated || $isSick || $isCTO) {
                // Find IDs
                $targetTypeName = $isCTO ? 'COC Compensatory Overtime Credit' : ($isSick ? 'Sick Leave' : 'Vacation Leave');
                $targetType = \App\Models\LeaveType::where('type_name', $targetTypeName)->first();

                if ($targetType) {
                    // Update Main Credits Table
                    $credit = \App\Models\LeaveCredit::where('user_id', $user->id)
                        ->where('leave_type_id', $targetType->id)
                        ->first();

                    if ($credit) {
                        $oldCredits = (float) $credit->credits;
                        $credit->credits = max(0, $oldCredits - $daysToDeduct);
                        $credit->save();

                        // Log the deduction
                        \App\Models\LeaveCreditAuditLog::create([
                            'actor_id' => Auth::id(),
                            'target_user_id' => $user->id,
                            'action' => 'deduction',
                            'leave_type_name' => $targetType->type_name,
                            'previous_value' => $oldCredits,
                            'new_value' => $credit->credits,
                            'reason' => 'Leave Approved: ' . $application->id,
                        ]);
                    }

                    // Special Batch Handling for CTO
                    if ($isCTO) {
                        $batches = \App\Models\CompensatoryLeaveCredit::where('user_id', $user->id)
                            ->where('status', 'Active')
                            ->where('remaining_credits', '>', 0)
                            ->orderBy('expiration_date', 'asc')
                            ->get();

                        $remainingToDeduct = $daysToDeduct;
                        foreach ($batches as $batch) {
                            if ($remainingToDeduct <= 0)
                                break;

                            $deductFromBatch = min($remainingToDeduct, (float) $batch->remaining_credits);
                            $batch->remaining_credits -= $deductFromBatch;
                            if ($batch->remaining_credits <= 0) {
                                $batch->status = 'Used';
                            }
                            $batch->save();
                            $remainingToDeduct -= $deductFromBatch;
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('user.leave.approvals')->with('success', 'Application successfully APPROVED and credits updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Critical Error during approval: ' . $e->getMessage());
        }
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
