<?php

namespace App\Http\Controllers;

use App\Models\LeaveCreditPolicy;
use App\Models\LeaveType;
use App\Models\LeaveCreditAuditLog;
use Illuminate\Http\Request;

class HeadHRController extends Controller
{
    /**
     * Dashboard for Head HR.
     */
    public function dashboard()
    {
        return redirect()->route('head-hr.leave-policies');
    }

    /**
     * Store a new leave type.
     */
    public function storeLeaveType(Request $request)
    {
        $request->validate([
            'type_name' => 'required|string|max:255|unique:leave_types,type_name',
            'description' => 'nullable|string|max:1000',
        ]);

        $leaveType = LeaveType::create([
            'type_name' => $request->type_name,
            'description' => $request->description,
            'is_active' => true,
        ]);

        // Create a default policy entry so it's ready to be configured
        LeaveCreditPolicy::create([
            'leave_type_id' => $leaveType->id,
            'accrual_rate' => 0,
            'accrual_period' => 'None',
            'expiration_rule' => 'None',
        ]);

        return back()->with('success', 'New leave type created successfully.');
    }

    /**
     * Manage leave credit policies.
     */
    public function policies()
    {
        $types = LeaveType::all();
        $policies = LeaveCreditPolicy::with('leaveType')->get()->keyBy('leave_type_id');

        return view('head_hr.policies', compact('types', 'policies'));
    }

    /**
     * Update a leave credit policy.
     */
    public function updatePolicy(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'accrual_rate' => 'required|numeric|min:0',
            'accrual_period' => 'required|in:Monthly,Yearly,None',
            'expiration_rule' => 'required|in:None,Yearly,Monthly,SpecificDate',
            'expiration_date' => 'nullable|required_if:expiration_rule,SpecificDate|date',
            'max_credits' => 'nullable|numeric|min:0',
        ]);

        LeaveCreditPolicy::updateOrCreate(
            ['leave_type_id' => $request->leave_type_id],
            [
                'accrual_rate' => $request->accrual_rate,
                'accrual_period' => $request->accrual_period,
                'expiration_rule' => $request->expiration_rule,
                'expiration_date' => $request->expiration_date,
                'max_credits' => $request->max_credits,
            ]
        );

        return back()->with('success', 'Policy updated successfully.');
    }

    /**
     * View audit logs of HR staff actions.
     */
    public function auditLogs()
    {
        $logs = LeaveCreditAuditLog::with(['actor', 'targetUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Also fetch pending requests
        $requests = \App\Models\LeaveUpdateRequest::with(['requester', 'user']) // Changed targetUser to user
            ->where('status', 'Pending')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('head_hr.audit_logs', compact('logs', 'requests'));
    }

    /**
     * Approve or reject a request
     */
    public function handleRequest(Request $request, $id)
    {
        $status = $request->input('status'); // approved or rejected
        $updRequest = \App\Models\LeaveUpdateRequest::findOrFail($id);

        if ($status === 'approved') {
            $updRequest->status = 'Approved';
            $updRequest->approver_id = \Illuminate\Support\Facades\Auth::id();
            $updRequest->save();

            // Unlock the credit record
            \App\Models\LeaveCredit::where('user_id', $updRequest->target_user_id)
                ->where('leave_type_id', $updRequest->leave_type_id)
                ->update(['is_locked' => false]);

            return back()->with('success', 'Request approved. Credit record unlocked.');
        } else {
            $updRequest->status = 'Rejected';
            $updRequest->approver_id = \Illuminate\Support\Facades\Auth::id();
            $updRequest->save();

            return back()->with('success', 'Request rejected.');
        }
    }
    /**
     * Delete a leave type (Superadmin ONLY)
     */
    public function deleteLeaveType($id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action. Only Superadmins can delete leave types.');
        }

        $type = LeaveType::findOrFail($id);

        // Check if there are existing applications for this leave type
        if (\App\Models\LeaveApplication::where('leave_type_id', $id)->exists()) {
            return back()->with('error', 'Cannot delete "' . $type->type_name . '" because it has existing applications. Consider deactivating it instead.');
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Delete dependents
            LeaveCreditPolicy::where('leave_type_id', $id)->delete();
            \App\Models\LeaveCredit::where('leave_type_id', $id)->delete();

            // Delete the type itself
            $type->delete();

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', 'Leave type "' . $type->type_name . '" deleted successfully.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Error deleting leave type: ' . $e->getMessage());
        }
    }
}

