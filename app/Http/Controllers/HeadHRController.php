<?php

namespace App\Http\Controllers;

use App\Models\LeaveCreditPolicy;
use App\Models\LeaveType;
use App\Models\LeaveCreditAuditLog;
use App\Models\LeaveApplication;
use App\Models\CompensatoryLeaveCredit;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Services\CreditService;
use App\Http\Requests\Leave\UpdatePolicyRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
    public function dashboard(Request $request)
    {
        $today = Carbon::today();
        $trendRange = $request->get('trend_range', 'month');
        $distRange = $request->get('dist_range', 'month');

        // 1. Metric Cards
        $stats = [
            'active_today' => LeaveApplication::where('status', 'approved')
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->count(),

            'pending_applications' => LeaveApplication::where('status', 'Pending HR')
                ->count(),

            'expiring_coc' => CompensatoryLeaveCredit::where('status', 'Active')
                ->where('remaining_credits', '>', 0)
                ->where('expiration_date', '<=', $today->copy()->addDays(30))
                ->where('expiration_date', '>=', $today)
                ->count(),

            'hoarding_count' => DB::table('leave_credits')
                ->join('leave_credit_policies', 'leave_credits.leave_type_id', '=', 'leave_credit_policies.leave_type_id')
                ->whereRaw('leave_credits.credits >= leave_credit_policies.max_credits')
                ->where('leave_credit_policies.max_credits', '>', 0)
                ->distinct('user_id')
                ->count('user_id'),
        ];

        // 2. Leave Trends (Line Chart)
        $trendsQuery = LeaveApplication::query();
        if ($trendRange === 'week') {
            $monthlyTrends = $trendsQuery->where('created_at', '>=', $today->copy()->subDays(6))
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as date_label, DATE_FORMAT(created_at, '%b %d') as label, count(*) as count")
                ->groupBy('date_label', 'label')
                ->orderBy('date_label', 'asc')
                ->get();
        } elseif ($trendRange === 'year') {
            $monthlyTrends = $trendsQuery->whereYear('created_at', $today->year)
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m-01') as date_label, DATE_FORMAT(created_at, '%b') as label, count(*) as count")
                ->groupBy('date_label', 'label')
                ->orderBy('date_label', 'asc')
                ->get();
        } else { // Default: Month (Current month daily breakdown)
            $monthlyTrends = $trendsQuery->whereMonth('created_at', $today->month)
                ->whereYear('created_at', $today->year)
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as date_label, DATE_FORMAT(created_at, '%d') as label, count(*) as count")
                ->groupBy('date_label', 'label')
                ->orderBy('date_label', 'asc')
                ->get();
        }

        // 3. Leave Distribution (Doughnut Chart) - Include all statuses to show demand
        $distQuery = LeaveApplication::join('leave_types', 'leave_applications.leave_type_id', '=', 'leave_types.id');

        if ($distRange === 'week') {
            $distQuery->where('leave_applications.created_at', '>=', $today->copy()->subDays(7));
        } elseif ($distRange === 'year') {
            $distQuery->whereYear('leave_applications.created_at', $today->year);
        } elseif ($distRange === 'month') {
            $distQuery->whereMonth('leave_applications.created_at', $today->month)
                     ->whereYear('leave_applications.created_at', $today->year);
        }

        $distribution = $distQuery->select('leave_types.type_name as label', DB::raw('count(*) as value'))
            ->groupBy('leave_types.type_name')
            ->get();

        // 4. Recent Activity
        $recentActivities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // 5. On Leave Today (Top 5 for dashboard overview)
        $onLeaveToday = LeaveApplication::with('user', 'leaveType')
            ->where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->limit(5)
            ->get();

        return view('hr.dashboard', compact('stats', 'monthlyTrends', 'distribution', 'recentActivities', 'onLeaveToday', 'trendRange', 'distRange'));
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
            'CTO (Compensatory Time Off)',
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

        $stats = [
            'total' => LeaveCreditAuditLog::count(),
            'allocate' => LeaveCreditAuditLog::where('action', 'allocate')->count(),
            'deduct' => LeaveCreditAuditLog::where('action', 'deduct')->count(),
            'update' => LeaveCreditAuditLog::where('action', 'update')->count(),
        ];

        $logs = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('head_hr.audit_logs', compact('logs', 'filters', 'stats'));
    }
}

