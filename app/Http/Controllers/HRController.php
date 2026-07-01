<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveCredit;
use App\Models\CompensatoryLeaveCredit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CreditService;
use App\Http\Requests\Leave\UpdateCreditsRequest;
use App\Http\Requests\Leave\AddCtoCreditRequest;
use Illuminate\Support\Facades\Hash; // Keep this for updateProfile
use App\Models\LeaveApplication;
use App\Models\LeaveCreditPolicy;
use App\Models\ActivityLog;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HRController extends Controller
{
    protected $creditService;

    public function __construct(CreditService $creditService)
    {
        $this->creditService = $creditService;
    }

    /**
     * HR Dashboard with analytics
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

        // 4. Recent Activities (Fixed: added missing variable definition)
        $recentActivities = \App\Models\ActivityLog::with('user')
            ->latest()
            ->limit(10)
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
     * List users for credit management
     */
    public function manageCredits(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('gmail', 'like', "%{$search}%")
                    ->orWhere('employee_number', 'like', "%{$search}%");
            });
        })
            ->whereIn('role', ['user', 'immediate_head', 'asds', 'sds', 'sgod_chief', 'cid_chief', 'ao'])
            ->orderBy('last_name')
            ->paginate(15);

        return view('hr.manage_credits.index', compact('users', 'search'));
    }

    /**
     * Edit credits for a user
     */
    public function editCredits(User $user)
    {
        $leaveTypes = LeaveType::where('is_active', true)->get();
        $existingCredits = LeaveCredit::where('user_id', $user->id)->get()->keyBy('leave_type_id');

        $ctoType = LeaveType::where('type_name', 'CTO (Compensatory Time Off)')->first();
        $ctoCredits = collect();
        if ($ctoType) {
            $ctoCredits = CompensatoryLeaveCredit::with('addedBy')
                ->where('user_id', $user->id)
                ->where('status', 'Active')
                ->orderBy('expiration_date', 'asc')
                ->get();
        }

        return view('hr.manage_credits.edit', compact('user', 'leaveTypes', 'existingCredits', 'ctoCredits', 'ctoType'));
    }

    /**
     * Store new COC credit
     */
    public function addCtoCredit(AddCtoCreditRequest $request, User $user)
    {
        try {
            $this->creditService->addCocCredit($user, $request->validated());
            return back()->with('success', 'COC Compensatory Overtime Credit added successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update credits (if not locked)
     */
    public function updateCredits(UpdateCreditsRequest $request, User $user)
    {
        try {
            $this->creditService->updateUserCredits($user, $request->input('credits', []));
            return redirect()->route('hr-staff.manage-credits.edit', $user->id)
                ->with('success', 'Credits updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating credits: ' . $e->getMessage());
        }
    }

    /**
     * HR Profile page
     */
    public function profile()
    {
        $user = Auth::user();

        if (!$user) {
            Auth::logout();
            return redirect()->route('login');
        }

        $recommendingOfficers = User::whereIn('role', ['cid_chief', 'sgod_chief', 'ao', 'asds'])
            ->where('is_active', true)
            ->orderBy('last_name')
            ->get();

        $finalApprovers = User::whereIn('role', ['asds', 'sds'])
            ->where('is_active', true)
            ->orderBy('last_name')
            ->get();

        $departmentHeads = User::where('is_active', true)
            ->where('id', '!=', $user->id)
            ->orderBy('last_name')
            ->get();

        $data = [
            'user' => $user,
            'unreadCount' => Notification::getUnreadCount($user->id),
            'allUsers' => User::where('is_active', true)->where('id', '!=', $user->id)->orderBy('full_name')->get(),
            'recommendingOfficers' => $recommendingOfficers,
            'finalApprovers' => $finalApprovers,
            'departmentHeads' => $departmentHeads,
        ];

        if ($user->isRecordPersonnel()) {
            $data['allOffices'] = \App\Models\Office::orderBy('name')->get();
            $data['subordinateUsers'] = User::where('id', '!=', $user->id)
                ->where('is_active', true)
                ->orderBy('full_name')
                ->get();
        }

        return view('hr.profile', $data);
    }

    /**
     * Update HR profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'first_name' => 'nullable|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:6|confirmed',
            'current_password' => 'required_with:password',
            'office_station' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'salary' => 'nullable|string|max:50',
            'recommending_officer_id' => 'nullable|exists:users,id',
            'approving_officer_id' => 'nullable|exists:users,id',
            'secretary_id' => 'nullable|exists:users,id',
            'department_head_id' => 'nullable',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'employee_number' => 'nullable|string|regex:/^[0-9]{7}$/|unique:users,employee_number,' . Auth::id(),
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $updateData = [];

        if ($request->filled('first_name')) {
            $updateData['first_name'] = $request->first_name;
        }
        if ($request->has('middle_name')) {
            $updateData['middle_name'] = $request->middle_name;
        }
        if ($request->filled('last_name')) {
            $updateData['last_name'] = $request->last_name;
        }
        if ($request->has('office_station')) {
            $updateData['office_station'] = $request->office_station;
        }
        if ($request->has('position')) {
            $updateData['position'] = $request->position;
        }
        if ($request->has('salary')) {
            $updateData['salary'] = $request->salary;
        }
        if ($request->has('recommending_officer_id')) {
            $updateData['recommending_officer_id'] = $request->recommending_officer_id;
        }
        if ($request->has('approving_officer_id')) {
            $updateData['approving_officer_id'] = $request->approving_officer_id;
        }
        if ($request->has('secretary_id')) {
            $updateData['secretary_id'] = $request->secretary_id;
        }
        if ($request->has('department_head_id')) {
            $dhVal = $request->department_head_id;
            if ($dhVal === 'bypass') {
                $updateData['department_head_id'] = null;
                $updateData['is_dept_head'] = true;
            } else {
                if (!empty($dhVal)) {
                    if ($dhVal == Auth::id()) {
                        return redirect()->back()->with('error', 'You cannot assign yourself as your own Department Head.');
                    }
                    if (!\App\Models\User::where('id', $dhVal)->exists()) {
                        return redirect()->back()->with('error', 'Invalid Department Head selected.');
                    }
                    $updateData['department_head_id'] = $dhVal;
                } else {
                    $updateData['department_head_id'] = null;
                }
                $updateData['is_dept_head'] = false;
            }
        }
        if ($request->has('employee_number')) {
            $updateData['employee_number'] = $request->employee_number;
        }

        // Handle password change
        if (!empty($request->password)) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->with('error', 'Current password does not match.');
            }
            $updateData['password'] = Hash::make($request->password);
            
            // Notify Super Admin and other HR
            $hrAndAdmins = User::whereIn('role', ['hr', 'head_hr', 'hr_review_officer', 'super_admin'])
                ->where('is_active', true)
                ->get();
                
            foreach ($hrAndAdmins as $admin) {
                if ($admin->id !== $user->id) {
                    Notification::send($user->id, $admin->id, "HR Personnel {$user->full_name} has updated their account password.");
                }
            }
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete(str_replace('storage/', '', $user->profile_picture));
            }
            $file = $request->file('profile_picture');
            $fileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', $file->getClientOriginalName());
            $path = $file->storeAs('profile_pics', $fileName, 'public');
            $updateData['profile_picture'] = 'storage/' . $path;
        }

        if (!empty($updateData)) {
            $user->update($updateData);
            ActivityLog::logAction($user->id, 'Profile Updated', 'HR profile updated');
        }

        return redirect()->back()->with('success', 'Your profile has been successfully updated.');
    }

    /**
     * Reset CTO balance to zero
     */
    public function resetCto(User $user)
    {
        try {
            $this->creditService->resetCtoBalance($user);
            return back()->with('success', 'CTO balance reset to zero successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error resetting CTO: ' . $e->getMessage());
        }
    }
    /**
     * Delete a specific COC batch
     */
    public function deleteCtoCredit(CompensatoryLeaveCredit $batch)
    {
        try {
            $this->creditService->deleteCocCredit($batch);
            return back()->with('success', 'COC batch deleted successfully and balance adjusted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting COC batch: ' . $e->getMessage());
        }
    }
}
