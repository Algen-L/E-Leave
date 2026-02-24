<?php

namespace App\Http\Controllers;

use App\Models\LeaveCreditPolicy;
use App\Models\LeaveType;
use App\Models\LeaveCreditAuditLog;
use Illuminate\Http\Request;
use App\Services\CreditService;
use App\Http\Requests\Leave\UpdatePolicyRequest;

class HeadHRController extends Controller
{
    protected $creditService;

    public function __construct(CreditService $creditService)
    {
        $this->creditService = $creditService;
    }

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
     * Delete a custom leave type (Super Admin only).
     */
    public function destroyLeaveType($id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Unauthorized Action. Only Super Admin can delete leave types.');
        }

        $leaveType = LeaveType::findOrFail($id);

        $systemLeaves = [
            'Vacation Leave',
            'Sick Leave',
            'Mandatory Leave',
            'Forced Leave',
            'COC Compensatory Overtime Credit',
            'Maternity Leave',
            'Paternity Leave',
            'VAWC Leave',
            'Rehabilitation Leave',
            'Special Leave Benefits for Women',
            'Terminal Leave',
            'Adoption Leave',
            'Solo Parent Leave',
            'Special Privilege Leave',
            'Calamity Leave',
            'Monetization of Leave Credits',
            'Wellness Leave'
        ];

        $isSystem = in_array($leaveType->type_name, $systemLeaves) || \Illuminate\Support\Str::contains($leaveType->type_name, ['Mandatory', 'Forced', 'Sick Leave', 'Vacation Leave']);

        if ($isSystem) {
            return back()->with('error', 'Cannot delete system default leave types.');
        }

        LeaveCreditPolicy::where('leave_type_id', $id)->delete();
        $leaveType->delete();

        return back()->with('success', 'Custom leave type deleted successfully.');
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
    public function updatePolicy(UpdatePolicyRequest $request)
    {
        try {
            $this->creditService->updatePolicy($request->validated());
            return back()->with('success', 'Policy updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating policy: ' . $e->getMessage());
        }
    }

    /**
     * Show audit logs
     */
    public function auditLogs(Request $request)
    {
        $filters = [
            'search' => $request->get('search', ''),
            'action' => $request->get('action', ''),
            'date_range' => $request->get('date_range', ''),
        ];

        $query = LeaveCreditAuditLog::with(['actor', 'targetUser']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('actor', function ($q2) use ($search) {
                    $q2->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                })
                    ->orWhereHas('targetUser', function ($q2) use ($search) {
                        $q2->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    })
                    ->orWhere('leave_type_name', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (!empty($filters['date_range'])) {
            $now = now();
            switch ($filters['date_range']) {
                case 'today':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case '7days':
                    $query->where('created_at', '>=', $now->copy()->subDays(7));
                    break;
                case '30days':
                    $query->where('created_at', '>=', $now->copy()->subDays(30));
                    break;
            }
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('head_hr.audit_logs', compact('logs', 'filters'));
    }
}

