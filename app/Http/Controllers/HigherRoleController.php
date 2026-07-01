<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use App\Models\LeaveApplication;
use App\Models\CompensatoryLeaveCredit;
use App\Models\ActivityLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HigherRoleController extends Controller
{
    /**
     * Dashboard for Higher Roles (SGOD Chief, CID Chief, OSDS Officers)
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        $trendRange = $request->get('trend_range', 'month');
        $distRange = $request->get('dist_range', 'month');

        // 1. Determine Office Category based on Role
        $category = null;
        if ($user->role === 'sgod_chief') {
            $category = 'SGOD';
        } elseif ($user->role === 'cid_chief') {
            $category = 'CID';
        } elseif (in_array($user->role, ['ao', 'sds', 'asds'])) {
            $category = 'OSDS';
        }

        if (!$category) {
            return redirect()->route('user.home');
        }

        // 2. Get Offices in this Category
        $offices = Office::where('category', $category)->pluck('name')->toArray();
        
        // 3. Get User IDs in these Offices
        $officeUserIds = User::whereIn('office_station', $offices)->pluck('id')->toArray();

        // 4. Metric Cards (Filtered by Office)
        $stats = [
            'active_today' => LeaveApplication::whereIn('user_id', $officeUserIds)
                ->where('status', 'Approved')
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->count(),

            'pending_approvals' => LeaveApplication::where(function($query) use ($user) {
                    $query->where(function($q) use ($user) {
                        $q->where('recommending_officer_id', $user->id)
                          ->where('status', 'Pending Recommending');
                    })->orWhere(function($q) use ($user) {
                        $q->where('approving_officer_id', $user->id)
                          ->where('status', 'Pending Approval');
                    });
                })->count(),

            'expiring_coc' => CompensatoryLeaveCredit::whereIn('user_id', $officeUserIds)
                ->where('status', 'Active')
                ->where('remaining_credits', '>', 0)
                ->where('expiration_date', '<=', $today->copy()->addDays(30))
                ->where('expiration_date', '>=', $today)
                ->count(),

            'hoarding_count' => DB::table('leave_credits')
                ->join('leave_credit_policies', 'leave_credits.leave_type_id', '=', 'leave_credit_policies.leave_type_id')
                ->whereIn('leave_credits.user_id', $officeUserIds)
                ->whereRaw('leave_credits.credits >= leave_credit_policies.max_credits')
                ->where('leave_credit_policies.max_credits', '>', 0)
                ->distinct('user_id')
                ->count('user_id'),
        ];

        // 5. Leave Trends (Line Chart - Filtered by Office)
        $trendsQuery = LeaveApplication::whereIn('user_id', $officeUserIds);
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
        } else { // Default: Month
            $monthlyTrends = $trendsQuery->whereMonth('created_at', $today->month)
                ->whereYear('created_at', $today->year)
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as date_label, DATE_FORMAT(created_at, '%d') as label, count(*) as count")
                ->groupBy('date_label', 'label')
                ->orderBy('date_label', 'asc')
                ->get();
        }

        // 6. Leave Distribution (Doughnut Chart - Filtered by Office)
        $distQuery = LeaveApplication::join('leave_types', 'leave_applications.leave_type_id', '=', 'leave_types.id')
            ->whereIn('leave_applications.user_id', $officeUserIds);

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

        // 7. Recent Activities (Filtered by Office Users)
        $recentActivities = ActivityLog::whereIn('user_id', $officeUserIds)
            ->with(['user' => function($q) {
                $q->select('id', 'full_name', 'profile_picture');
            }])
            ->latest()
            ->limit(10)
            ->get();

        // 8. On Leave Today (Filtered by Office)
        $onLeaveToday = LeaveApplication::with(['user', 'leaveType'])
            ->whereIn('user_id', $officeUserIds)
            ->where('status', 'Approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->limit(5)
            ->get();

        // 9. All approved leaves for calendar (Current Year +/- 3 Months buffer)
        $calendarLeaves = LeaveApplication::with(['user:id,full_name', 'leaveType:id,type_name'])
            ->whereIn('user_id', $officeUserIds)
            ->where('status', 'Approved') // Consistent capitalization
            ->where(function($query) use ($today) {
                // Expanded range: current year with padding for year crossovers
                $start = $today->copy()->startOfYear()->subMonths(3);
                $end = $today->copy()->endOfYear()->addMonths(3);
                $query->whereBetween('start_date', [$start, $end])
                      ->orWhereBetween('end_date', [$start, $end])
                      ->orWhere(function($q) use ($start, $end) {
                          $q->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                      });
            })
            ->get(['id', 'user_id', 'leave_type_id', 'start_date', 'end_date', 'status']);

        return view('higher_roles.dashboard', compact(
            'stats', 
            'monthlyTrends', 
            'distribution', 
            'recentActivities', 
            'onLeaveToday', 
            'calendarLeaves',
            'trendRange', 
            'distRange',
            'category'
        ));
    }
}
