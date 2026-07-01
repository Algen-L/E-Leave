<?php

namespace App\Http\Controllers;

use App\Models\LeaveApplication;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveApprovalController extends Controller
{
    /**
     * Display a listing of applications pending the user's action.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->query('tab', 'pending');
        $applications = collect();
        $title = $tab === 'processed' ? 'Processed Approvals' : 'Pending Approvals';

        // Filter Inputs
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');

        // Fetch IDs of users who have designated the current user as their secretary
        $bossIds = \App\Models\User::where('secretary_id', $user->id)->pluck('id')->toArray();

        if ($tab === 'processed') {
            // 1. HR Processed (Verified by HR)
            if (in_array($user->role, ['hr', 'head_hr', 'hr_review_officer', 'super_admin'])) {
                $hrQuery = LeaveApplication::with(['user', 'leaveType'])->whereNotNull('hr_verified_at');
                
                if ($search) $hrQuery->whereHas('user', fn($q) => $q->where('full_name', 'like', "%{$search}%"));
                if ($startDate) $hrQuery->whereDate('date_filing', '>=', $startDate);
                if ($endDate) $hrQuery->whereDate('date_filing', '<=', $endDate);
                if ($status) $hrQuery->where('status', $status);

                $hrProcessed = $hrQuery->orderBy('hr_verified_at', 'desc')->get();
                $applications = $applications->merge($hrProcessed);
            }

            // 2. Recommending Officer Processed
            $recoQuery = LeaveApplication::with(['user', 'leaveType'])
                ->where('recommending_officer_id', $user->id)
                ->whereNotNull('recommended_at');

            if ($search) $recoQuery->whereHas('user', fn($q) => $q->where('full_name', 'like', "%{$search}%"));
            if ($startDate) $recoQuery->whereDate('date_filing', '>=', $startDate);
            if ($endDate) $recoQuery->whereDate('date_filing', '<=', $endDate);
            if ($status) $recoQuery->where('status', $status);

            $recoProcessed = $recoQuery->orderBy('recommended_at', 'desc')->get();
            $applications = $applications->merge($recoProcessed);

            // 3. ASDS Processed
            if ($user->role === 'asds') {
                $asdsQuery = LeaveApplication::with(['user', 'leaveType'])
                    ->where('asds_id', $user->id)
                    ->whereNotNull('asds_approved_at');
                
                if ($search) $asdsQuery->whereHas('user', fn($q) => $q->where('full_name', 'like', "%{$search}%"));
                if ($startDate) $asdsQuery->whereDate('date_filing', '>=', $startDate);
                if ($endDate) $asdsQuery->whereDate('date_filing', '<=', $endDate);
                if ($status) $asdsQuery->where('status', $status);

                $asdsProcessed = $asdsQuery->orderBy('asds_approved_at', 'desc')->get();
                $applications = $applications->merge($asdsProcessed);
            }

            // 4. Approving Officer Processed (Including Delegated for Secretaries)
            $approverIds = array_merge([$user->id], $bossIds);
            $appQuery = LeaveApplication::with(['user', 'leaveType'])
                ->whereIn('approving_officer_id', $approverIds)
                ->whereNotNull('approved_at');

            if ($search) $appQuery->whereHas('user', fn($q) => $q->where('full_name', 'like', "%{$search}%"));
            if ($startDate) $appQuery->whereDate('date_filing', '>=', $startDate);
            if ($endDate) $appQuery->whereDate('date_filing', '<=', $endDate);
            if ($status) $appQuery->where('status', $status);

            $appProcessed = $appQuery->orderBy('approved_at', 'desc')->get();
            $applications = $applications->merge($appProcessed);
            
            // Deduplicate if needed
            $applications = $applications->unique('id');
        } else {
            // Pending Logic
            // 1. HR Review
            if (in_array($user->role, ['hr', 'head_hr', 'hr_review_officer', 'super_admin'])) {
                $hrPending = LeaveApplication::with(['user', 'leaveType'])->where('status', 'Pending HR');
                if ($search) $hrPending->whereHas('user', fn($q) => $q->where('full_name', 'like', "%{$search}%"));
                if ($startDate) $hrPending->whereDate('date_filing', '>=', $startDate);
                if ($endDate) $hrPending->whereDate('date_filing', '<=', $endDate);
                
                $applications = $applications->merge($hrPending->orderBy('created_at', 'asc')->get());
            }

            // 2. Recommending
            $recommendingPending = LeaveApplication::with(['user', 'leaveType'])
                ->where('recommending_officer_id', $user->id)
                ->where('status', 'Pending Recommending');
            if ($search) $recommendingPending->whereHas('user', fn($q) => $q->where('full_name', 'like', "%{$search}%"));
            if ($startDate) $recommendingPending->whereDate('date_filing', '>=', $startDate);
            if ($endDate) $recommendingPending->whereDate('date_filing', '<=', $endDate);
            
            $applications = $applications->merge($recommendingPending->orderBy('created_at', 'asc')->get());

            // 3. ASDS Approval (Middle-stage for > 6 days)
            if ($user->role === 'asds') {
                $asdsPending = LeaveApplication::with(['user', 'leaveType'])
                    ->where('asds_id', $user->id)
                    ->where('status', 'Pending ASDS Approval');
                if ($search) $asdsPending->whereHas('user', fn($q) => $q->where('full_name', 'like', "%{$search}%"));
                if ($startDate) $asdsPending->whereDate('date_filing', '>=', $startDate);
                if ($endDate) $asdsPending->whereDate('date_filing', '<=', $endDate);
                
                $applications = $applications->merge($asdsPending->orderBy('created_at', 'asc')->get());
            }

            // 4. Final Approver (Including Delegated for Secretaries)
            $approverIds = [$user->id];
            $targetApproverIds = array_merge($approverIds, $bossIds);

            $approvalPending = LeaveApplication::with(['user', 'leaveType'])
                ->whereIn('approving_officer_id', $targetApproverIds)
                ->where('status', 'Pending Approval');
            if ($search) $approvalPending->whereHas('user', fn($q) => $q->where('full_name', 'like', "%{$search}%"));
            if ($startDate) $approvalPending->whereDate('date_filing', '>=', $startDate);
            if ($endDate) $approvalPending->whereDate('date_filing', '<=', $endDate);
            
            $applications = $applications->merge($approvalPending->orderBy('created_at', 'asc')->get());
        }

        return view('leave.approvals.index', compact('applications', 'title', 'tab', 'search', 'startDate', 'endDate', 'status'));
    }


    /**
     * Show application details for approval
     */
    public function show($id)
    {
        $application = LeaveApplication::with(['user', 'leaveType', 'details', 'recommendingOfficer', 'approvingOfficer', 'hrVerifier'])->findOrFail($id);

        // Authorization check: User must be involved, HR, or a Secretary of the involved approver
        $user = Auth::user();
        $isSecretaryOfApprover = \App\Models\User::where('id', $application->approving_officer_id)
            ->where('secretary_id', $user->id)
            ->exists();

        $canView =
            in_array($user->role, ['hr', 'head_hr', 'hr_review_officer', 'super_admin']) ||
            $application->recommending_officer_id == $user->id ||
            $application->asds_id == $user->id ||
            $application->approving_officer_id == $user->id ||
            $isSecretaryOfApprover;

        if (!$canView) {
            abort(403);
        }

        // Mark as viewed if current user is the "target" reviewer for this stage
        if (!$application->is_viewed) {
            $isTarget = false;
            if ($application->status === 'Pending HR' && in_array($user->role, ['hr', 'head_hr', 'hr_review_officer', 'super_admin'])) {
                $isTarget = true;
            } elseif ($application->status === 'Pending Recommending' && $application->recommending_officer_id == $user->id) {
                $isTarget = true;
            } elseif ($application->status === 'Pending ASDS Approval' && $application->asds_id == $user->id) {
                $isTarget = true;
            } elseif ($application->status === 'Pending Approval' && ($application->approving_officer_id == $user->id || $isSecretaryOfApprover)) {
                $isTarget = true;
            }

            if ($isTarget) {
                $application->update(['is_viewed' => true]);
            }
        }

        // --- DYNAMIC CREDIT CALCULATION LOGIC ---
        $applicant = $application->user;
        $appType = $application->leaveType;
        $appTypeName = $appType->type_name ?? '';
        $calculationDays = (float) ($application->days_with_pay ?? $application->days_applied);

        // 1. Identify primary and related credit pools
        $displayPools = [];
        
        // Define common categories
        $isVlRelated = (stripos($appTypeName, 'Vacation') !== false || stripos($appTypeName, 'Forced') !== false || stripos($appTypeName, 'Mandatory') !== false);
        $isSlRelated = (stripos($appTypeName, 'Sick') !== false);
        $isWellness = (stripos($appTypeName, 'Wellness') !== false);
        $isCTO = (stripos($appTypeName, 'Compensatory') !== false);

        // Fetch all credits for the user once to match easily
        $allCredits = \App\Models\LeaveCredit::where('user_id', $applicant->id)
            ->with('leaveType')
            ->get();

        // Build display pools based on relevance
        foreach ($allCredits as $credit) {
            $poolTypeName = $credit->leaveType->type_name;
            $showThisPool = false;
            $less = 0;

            if ($isVlRelated && stripos($poolTypeName, 'Vacation') !== false) {
                $showThisPool = true;
                $less = $calculationDays;
            } elseif ($isSlRelated && stripos($poolTypeName, 'Sick') !== false) {
                $showThisPool = true;
                $less = $calculationDays;
            } elseif ($isCTO && stripos($poolTypeName, 'Compensatory') !== false) {
                $showThisPool = true;
                $less = $calculationDays;
            } elseif (stripos($poolTypeName, $appTypeName) !== false || $credit->leave_type_id == $application->leave_type_id) {
                // Exact match or contains the name (e.g., Wellness Leave)
                $showThisPool = true;
                $less = $calculationDays;
            }

            if ($showThisPool) {
                $isApproved = $application->status === 'Approved';
                $currentVal = (float) $credit->credits;
                $balanceVal = $isApproved ? $currentVal : ($currentVal - $less);
                
                // If already approved, the credit in DB already has the deduction.
                // For a "snapshot" view, we show the balance BEFORE this deduction as "current".
                if ($isApproved) $currentVal += $less;

                $displayPools[] = [
                    'name' => $poolTypeName,
                    'current' => $currentVal,
                    'less' => $less,
                    'balance' => $balanceVal,
                    'icon' => $this->getPoolIcon($poolTypeName),
                    'color' => $this->getPoolColor($poolTypeName)
                ];
            }
        }

        // If no credit pool matched (e.g., some special leaves), displayPools will be empty.
        // We'll pass it to the view to handle appropriately.

        return view('leave.approvals.show', compact('application', 'displayPools'));
    }

    /**
     * Helper to get icon for pool
     */
    private function getPoolIcon($name) {
        if (stripos($name, 'Vacation') !== false) return 'fas fa-sun';
        if (stripos($name, 'Sick') !== false) return 'fas fa-briefcase-medical';
        if (stripos($name, 'Wellness') !== false) return 'fas fa-spa';
        if (stripos($name, 'Compensatory') !== false) return 'fas fa-clock';
        return 'fas fa-coins';
    }

    /**
     * Helper to get color class for pool icon
     */
    private function getPoolColor($name) {
        if (stripos($name, 'Vacation') !== false) return 'text-orange-400';
        if (stripos($name, 'Sick') !== false) return 'text-red-400';
        if (stripos($name, 'Wellness') !== false) return 'text-emerald-400';
        if (stripos($name, 'Compensatory') !== false) return 'text-indigo-400';
        return 'text-blue-400';
    }

    /**
     * HR Verification Step
     */
    public function verify(Request $request, $id)
    {
        $application = LeaveApplication::findOrFail($id);

        // Ensure user is HR
        if (!in_array(Auth::user()->role, ['hr', 'head_hr', 'hr_review_officer', 'super_admin'])) {
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
            'is_viewed' => false,
        ]);

        // Notify HR Personnel if verified by an HR Review Officer
        if (Auth::user()->role === 'hr_review_officer') {
            $hrStaff = User::whereIn('role', ['hr', 'head_hr'])->where('is_active', true)->get();
            foreach ($hrStaff as $hr) {
                Notification::send(Auth::id(), $hr->id, "HR Review Officer " . Auth::user()->full_name . " has verified application #" . $application->id . ". Form 6 will use your official signature.");
            }
        }

        \App\Models\ActivityLog::logAction(
            Auth::id(),
            'Verified Application',
            "Verified application #{$application->id} for {$application->user->full_name}. Days with pay: {$request->days_with_pay}"
        );

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
            'status' => $application->asds_id ? 'Pending ASDS Approval' : 'Pending Approval',
            'recommended_at' => now(),
            'is_viewed' => false,
        ]);

        return redirect()->route('user.leave.approvals');
    }

    /**
     * ASDS Approval Step (Middle Step for leaves > 6 days)
     */
    public function asdsApprove(Request $request, $id)
    {
        $application = LeaveApplication::findOrFail($id);

        if ($application->asds_id != Auth::id()) {
            abort(403);
        }

        $application->update([
            'status' => 'Pending Approval', // Now goes to SDS for final
            'asds_approved_at' => now(),
            'is_viewed' => false,
        ]);

        \App\Models\ActivityLog::logAction(
            Auth::id(),
            'ASDS Approved Application',
            "ASDS approved application #{$application->id} for {$application->user->full_name}. Now pending Final SDS Approval."
        );

        return redirect()->route('user.leave.approvals')->with('success', 'Application approved by ASDS. Now pending Final SDS Approval.');
    }

    /**
     * Final Approval Step
     */
    public function approve(Request $request, $id)
    {
        $application = LeaveApplication::with(['leaveType', 'user', 'details'])->findOrFail($id);

        $user = Auth::user();
        $isSecretary = \App\Models\User::where('id', $application->approving_officer_id)
            ->where('secretary_id', $user->id)
            ->exists();

        if ($application->approving_officer_id != $user->id && !$isSecretary) {
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
            
            // Deduct ONLY the days verified as 'With Pay' (fallback to total days if not verified yet)
            $daysToDeduct = (float) ($application->days_with_pay ?? $application->days_applied);

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
                $targetTypeName = $isCTO ? 'CTO (Compensatory Time Off)' : ($isSick ? 'Sick Leave' : 'Vacation Leave');
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

            // --- SYNC TO TODTR TABLE ---
            // If the application has specific dates array (like a comma-separated string or json), we use that.
            // Otherwise we use start_date to end_date.
            $dateRange = is_array($application->dates) 
                ? implode(',', $application->dates) 
                : $application->dates;

            // Fallback to range if dates is empty
            if (empty($dateRange)) {
                $startDateStr = $application->start_date instanceof \Carbon\Carbon ? $application->start_date->format('Y-m-d') : $application->start_date;
                $endDateStr = $application->end_date instanceof \Carbon\Carbon ? $application->end_date->format('Y-m-d') : $application->end_date;
                $dateRange = ($startDateStr == $endDateStr) ? $startDateStr : $startDateStr . ' to ' . $endDateStr;
            }

            DB::table('todtr')->insert([
                'Name' => $user->name ?? $user->full_name,
                'Employee_number' => $user->employee_number ?? '',
                'TypeOfLeave' => $leaveType->type_name ?? 'Unknown',
                'DateOfLeave' => $dateRange,
            ]);

            DB::commit();

            \App\Models\ActivityLog::logAction(
                Auth::id(),
                'Approved Application',
                "Final approval for application #{$application->id} ({$leaveType->type_name}) for {$user->full_name}. Deducted {$daysToDeduct} days."
            );

            return redirect()->route('user.leave.approvals')->with('success', 'Application successfully APPROVED and credits updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Critical Error during approval: ' . $e->getMessage());
        }
    }

    /**
     * Disapprove / Reject
     */
}
