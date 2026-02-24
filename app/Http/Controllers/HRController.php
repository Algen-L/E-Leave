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
    public function dashboard()
    {
        $today = Carbon::today();

        // 1. Metric Cards
        $stats = [
            'active_today' => LeaveApplication::where('status', 'approved')
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->count(),

            'pending_applications' => LeaveApplication::where('status', 'pending')
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

        // 2. Monthly Trends (Last 6 Months)
        $monthlyTrends = LeaveApplication::where('created_at', '>=', $today->copy()->subMonths(6))
            ->selectRaw("DATE_FORMAT(created_at, '%b %Y') as month, count(*) as count")
            ->groupBy('month')
            ->orderBy('created_at', 'asc')
            ->get();

        // 3. Leave Type Distribution (Approved Leaves)
        $distribution = LeaveApplication::where('status', 'approved')
            ->join('leave_types', 'leave_applications.leave_type_id', '=', 'leave_types.id')
            ->select('leave_types.type_name as label', DB::raw('count(*) as value'))
            ->groupBy('leave_types.type_name')
            ->get();

        // 4. Recent Activity
        $recentActivities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('hr.dashboard', compact('stats', 'monthlyTrends', 'distribution', 'recentActivities'));
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
                    ->orWhere('employee_number', 'like', "%{$search}%");
            });
        })
            ->where('role', 'user')
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
        $userCredits = LeaveCredit::where('user_id', $user->id)->get()->keyBy('leave_type_id');

        $ctoType = LeaveType::where('type_name', 'COC Compensatory Overtime Credit')->first();
        $ctoBatches = [];
        if ($ctoType) {
            $ctoBatches = CompensatoryLeaveCredit::where('user_id', $user->id)
                ->where('status', 'Active')
                ->orderBy('expiration_date', 'asc')
                ->get();
        }

        return view('hr.manage_credits.edit', compact('user', 'leaveTypes', 'userCredits', 'ctoBatches', 'ctoType'));
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

        return view('hr.profile', [
            'user' => $user,
            'unreadCount' => Notification::getUnreadCount($user->id),
        ]);
    }

    /**
     * Update HR profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,gmail,' . Auth::id(),
            'password' => 'nullable|string|min:8|confirmed',
            'office_station' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'employee_number' => 'nullable|string|regex:/^[0-9]{7}$/|unique:users,employee_number,' . Auth::id(),
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $updateData = [];

        if ($request->filled('full_name')) {
            $updateData['full_name'] = $request->full_name;
        }
        if ($request->has('office_station')) {
            $updateData['office_station'] = $request->office_station;
        }
        if ($request->has('position')) {
            $updateData['position'] = $request->position;
        }
        if ($request->has('employee_number')) {
            $updateData['employee_number'] = $request->employee_number;
        }

        // Handle password change
        if (!empty($request->password)) {
            $updateData['password'] = Hash::make($request->password);
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
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
}
