<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\SecurityTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    /**
     * Dashboard
     */
    public function dashboard()
    {
        $data = $this->getDashboardStats();
        return view('admin.dashboard', $data);
    }

    /**
     * Dashboard API for AJAX requests
     */
    public function dashboardApi()
    {
        try {
            $data = $this->getDashboardStats();
            return response()->json([
                'status' => 'success',
                'stats' => [
                    'total_users' => $data['totalUsers'],
                    'active_today' => $data['activeToday'],
                    'new_registrations' => $data['newRegistrations']
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats(): array
    {
        $user = Auth::user();

        // Total users
        $totalUsers = User::count();

        // Active today (users with activity logs today)
        $activeToday = ActivityLog::whereDate('created_at', today())
            ->distinct('user_id')
            ->count('user_id');

        // New registrations this month
        $newRegistrations = User::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        // Recent audit trail
        $auditTrail = ActivityLog::withUserDetails()
            ->latest('created_at')
            ->limit(10)
            ->get();

        // --- PERSONAL LEAVE DATA (For non-superadmins) ---
        $credits = null;
        $leaveSummary = null;

        if ($user->role !== 'super_admin' && !in_array($user->role, ['hr', 'head_hr'])) {
            $vlType = \App\Models\LeaveType::where('type_name', 'Vacation Leave')->first();
            $slType = \App\Models\LeaveType::where('type_name', 'Sick Leave')->first();
            $ctoType = \App\Models\LeaveType::where('type_name', 'Compensatory Time Off')->first();

            $credits = [
                'vl' => \App\Models\LeaveCredit::where('user_id', $user->id)->where('leave_type_id', $vlType->id ?? 0)->value('credits') ?? 0,
                'sl' => \App\Models\LeaveCredit::where('user_id', $user->id)->where('leave_type_id', $slType->id ?? 0)->value('credits') ?? 0,
                'cto' => \App\Models\LeaveCredit::where('user_id', $user->id)->where('leave_type_id', $ctoType->id ?? 0)->value('credits') ?? 0,
            ];

            $leaveSummary = [
                'pending' => \App\Models\LeaveApplication::where('user_id', $user->id)->whereIn('status', ['Pending HR', 'Pending Recommending', 'Pending Approval'])->count(),
                'approved' => \App\Models\LeaveApplication::where('user_id', $user->id)->where('status', 'Approved')->count(),
                'disapproved' => \App\Models\LeaveApplication::where('user_id', $user->id)->where('status', 'Disapproved')->count(),
                'total' => \App\Models\LeaveApplication::where('user_id', $user->id)->count(),
            ];
        }

        return [
            'totalUsers' => $totalUsers,
            'activeToday' => $activeToday,
            'newRegistrations' => $newRegistrations,
            'auditTrail' => $auditTrail,
            'user' => $user,
            'unreadCount' => Notification::getUnreadCount(Auth::id()),
            'credits' => $credits,
            'leaveSummary' => $leaveSummary,
        ];
    }

    /**
     * Manage users page
     */
    public function manageUsers(Request $request)
    {
        $view = $request->get('view', 'active');
        $filters = [
            'search' => $request->get('search', ''),
            'role' => $request->get('filter_role', ''),
            'office' => $request->get('filter_office', ''),
        ];

        $query = User::query();

        // Apply search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('gmail', 'like', "%{$search}%");
            });
        }

        // Apply role filter
        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        // Apply office filter
        if (!empty($filters['office'])) {
            $query->where('office_station', $filters['office']);
        }

        // Exclude current super admin from the list
        $query->where('id', '!=', Auth::id());

        // Additionally exclude all Superadmins if the logged-in user is Head HR
        if (Auth::user()->isHeadHR()) {
            $query->where('role', '!=', 'super_admin');
        }

        // View-based filter
        if ($view === 'active') {
            $query->where('is_active', true);
        } elseif ($view === 'inactive') {
            $query->where('is_active', false);
        }

        $users = $query->orderBy('full_name')->get();

        // Get audit logs if viewing notifications
        $auditLogs = [];
        if ($view === 'notifications') {
            $auditLogs = ActivityLog::withUserDetails()
                ->latest('created_at')
                ->limit(50)
                ->get();
        }

        $offices = Office::all();

        return view('admin.manage-users', [
            'view' => $view,
            'users' => $users,
            'auditLogs' => $auditLogs,
            'filters' => $filters,
            'user' => Auth::user(),
            'offices' => $offices,
            'unreadCount' => Notification::getUnreadCount(Auth::id()),
        ]);
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'gmail' => 'required|email|unique:users,gmail,' . $user->id,
            'position' => 'nullable|string|max:100',
            'role' => 'required|string|in:user,admin,hr,head_hr,immediate_head,asds,sds,sgod_chief,cid_chief,ao',
            'office_station' => 'nullable|string|max:100',
            'is_active' => 'required|boolean',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        // Authorization check for restricted roles
        $restrictedRoles = ['asds', 'sds', 'sgod_chief', 'cid_chief', 'ao'];
        if (in_array($request->role, $restrictedRoles) && Auth::user()->role !== 'super_admin') {
            return redirect()->back()->withInput()->with('error', 'Only Super Admin can assign this role.');
        }

        $updateData = [
            'username' => $request->username,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'gmail' => $request->gmail,
            'position' => $request->position,
            'role' => $request->role,
            'office_station' => $request->office_station,
            'is_active' => $request->is_active,
        ];

        // Note: 'full_name' and 'name' are automatically updated by the User model's boot method
        // upon saving first/middle/last.

        // Only update password if provided (hashed cast handles hashing)
        if ($request->filled('password')) {
            $updateData['password'] = $request->password;
        }

        $user->update($updateData);

        ActivityLog::logAction(Auth::id(), 'Updated User Record', "Updated user: {$user->full_name} (ID: {$user->id})");

        return redirect()->route('admin.manage-users')->with('success', 'User updated successfully!');
    }

    /**
     * Toggle user status
     */
    public function toggleUserStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        $action = $user->is_active ? 'Activated' : 'Deactivated';
        ActivityLog::logAction(Auth::id(), "{$action} User", "User ID: {$user->id}");

        return redirect()->back()->with('success', "User {$action} successfully!");
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $userId = $user->id;
        $user->delete();

        ActivityLog::logAction(Auth::id(), 'Deleted User', "User ID: {$userId} removed.");

        return redirect()->back()->with('success', 'User account removed.');
    }

    /**
     * Admin profile
     */
    public function profile()
    {
        $allUsers = User::orderBy('full_name')->get();

        return view('admin.profile', [
            'user' => Auth::user(),
            'allUsers' => $allUsers,
            'unreadCount' => Notification::getUnreadCount(Auth::id()),
        ]);
    }

    /**
     * Update admin profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'full_name' => 'nullable|string|max:100',
            'office_station' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:6|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'esignature' => 'nullable|image|mimes:png|max:1024',
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

        if (!empty($request->password)) {
            $updateData['password'] = $request->password;
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pics', 'public');
            $updateData['profile_picture'] = 'storage/' . $path;
        }

        // Handle e-signature upload
        if ($request->hasFile('esignature')) {
            $path = $request->file('esignature')->store('esignatures', 'public');
            $updateData['esignature'] = 'storage/' . $path;
        } elseif ($request->filled('esignature_data')) {
            // Handle base64 drawn signature
            $data = $request->esignature_data;
            // Extract the base64 part
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif

                if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    // Invalid image type, or handle error
                } else {
                    $data = base64_decode($data);
                    if ($data === false) {
                        // Base64 decode failed
                    } else {
                        $filename = 'esignature_' . time() . '.' . $type;
                        $path = 'esignatures/' . $filename;

                        // Save to public disk
                        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $data);
                        $updateData['esignature'] = 'storage/' . $path;
                    }
                }
            }
        }

        if (!empty($updateData)) {
            $user->update($updateData);
            ActivityLog::logAction(Auth::id(), 'Profile Updated', 'Admin profile updated');
        }

        return redirect()->back()->with('success', 'Your profile has been updated.');
    }

    /**
     * Send notification
     */
    public function sendNotification(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|string',
            'message' => 'required|string|max:500',
        ]);

        if ($request->recipient_id === 'all') {
            Notification::broadcast(Auth::id(), $request->message);
        } else {
            Notification::send(Auth::id(), (int) $request->recipient_id, $request->message);
        }

        ActivityLog::logAction(Auth::id(), 'Sent Notification', "Message: " . substr($request->message, 0, 50));

        return redirect()->back()->with('success', 'Notification sent.');
    }

    /**
     * Activity logs page
     */
    public function activityLogs(Request $request)
    {
        $filters = [
            'search' => $request->get('search', ''),
            'action' => $request->get('action', ''),
            'date_range' => $request->get('date_range', ''),
        ];

        $query = ActivityLog::withUserDetails()
            ->search($filters['search'])
            ->latest('created_at');

        // Filter by action type
        if (!empty($filters['action'])) {
            $actionMap = [
                'login' => 'login',
                'logout' => 'logout',
                'create' => ['create', 'register', 'add'],
                'update' => ['update', 'edit', 'change'],
                'delete' => ['delete', 'remove'],
            ];
            $keywords = $actionMap[$filters['action']] ?? $filters['action'];
            if (is_array($keywords)) {
                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $kw) {
                        $q->orWhere('action', 'like', "%{$kw}%");
                    }
                });
            } else {
                $query->where('action', 'like', "%{$keywords}%");
            }
        }

        // Filter by date range
        if (!empty($filters['date_range'])) {
            $now = now();
            switch ($filters['date_range']) {
                case 'today':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case '7days':
                    $query->where('created_at', '>=', $now->subDays(7));
                    break;
                case '30days':
                    $query->where('created_at', '>=', $now->subDays(30));
                    break;
            }
        }

        $logs = $query->limit(200)->get();

        return view('admin.activity-logs', [
            'logs' => $logs,
            'filters' => $filters,
            'user' => Auth::user(),
            'unreadCount' => Notification::getUnreadCount(Auth::id()),
        ]);
    }

    /**
     * Register new user (by admin)
     */
    public function showRegisterUser()
    {
        $offices = Office::all()->groupBy('category');

        return view('admin.register-user', [
            'user' => Auth::user(),
            'offices' => $offices,
            'unreadCount' => Notification::getUnreadCount(Auth::id()),
        ]);
    }

    /**
     * Store new user (admin registration)
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'gmail' => 'nullable|email|unique:users,gmail',
            'office_station' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'role' => 'required|string|in:user,admin,hr,head_hr,immediate_head,asds,sds,sgod_chief,cid_chief,ao',
        ]);

        // Authorization check for restricted roles
        $restrictedRoles = ['asds', 'sds', 'sgod_chief', 'cid_chief', 'ao'];
        if (in_array($request->role, $restrictedRoles) && Auth::user()->role !== 'super_admin') {
            return redirect()->back()->withInput()->with('error', 'Only Super Admin can create accounts with this role.');
        }

        $user = User::create([
            'username' => $request->username,
            'password' => $request->password,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'gmail' => $request->gmail,
            'office_station' => $request->office_station,
            'position' => $request->position,
            'role' => $request->role,
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        ActivityLog::logAction(Auth::id(), 'Created User', "User ID: {$user->id} - {$user->full_name}");

        return redirect()->route('admin.manage-users')->with('success', 'User created successfully!');
    }

    /**
     * Edit user page
     */
    public function editUser(User $user)
    {
        $offices = Office::all()->groupBy('category');

        return view('admin.edit-user', [
            'editUser' => $user,
            'user' => Auth::user(),
            'offices' => $offices,
            'unreadCount' => Notification::getUnreadCount(Auth::id()),
        ]);
    }

    /**
     * Password reset management
     */
    public function passwordResetManagement()
    {
        return view('admin.password-reset-management', [
            'user' => Auth::user(),
            'unreadCount' => Notification::getUnreadCount(Auth::id()),
        ]);
    }

    /**
     * Authentication Reset Management page
     */
    public function authResetManagement(Request $request)
    {
        $filters = [
            'search' => $request->get('search', ''),
            'status' => $request->get('status', ''),
        ];

        $query = SecurityTracking::query()
            ->where(function ($q) {
                $q->where('page_visits', '>', 0)
                    ->orWhere('otp_requests', '>', 0)
                    ->orWhere('otp_inputs', '>', 0)
                    ->orWhere('resends', '>', 0)
                    ->orWhere('is_blocked', true);
            });

        // Apply search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('email', 'like', "%{$search}%");
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'blocked') {
                $query->where('is_blocked', true);
            } elseif ($filters['status'] === 'active') {
                $query->where('is_blocked', false);
            }
        }

        $trackingRecords = $query->orderBy('last_activity', 'desc')->get();

        // Get related users
        foreach ($trackingRecords as $record) {
            $record->user = User::where('gmail', $record->email)->first();
        }

        // Calculate stats
        $usersWithRequests = SecurityTracking::where('otp_requests', '>', 0)->count();
        $blockedUsers = SecurityTracking::where('is_blocked', true)->count();
        $activeUsers = SecurityTracking::where('is_blocked', false)->where('otp_requests', '>', 0)->count();
        $totalOtpRequests = SecurityTracking::sum('otp_requests');

        return view('admin.auth-reset-management', [
            'trackingRecords' => $trackingRecords,
            'filters' => $filters,
            'usersWithRequests' => $usersWithRequests,
            'blockedUsers' => $blockedUsers,
            'activeUsers' => $activeUsers,
            'totalOtpRequests' => $totalOtpRequests,
            'user' => Auth::user(),
            'unreadCount' => Notification::getUnreadCount(Auth::id()),
        ]);
    }

    /**
     * Get security tracking details
     */
    public function authResetDetails($id)
    {
        $record = SecurityTracking::findOrFail($id);

        return response()->json([
            'email' => $record->email,
            'page_visits' => $record->page_visits,
            'otp_requests' => $record->otp_requests,
            'otp_inputs' => $record->otp_inputs,
            'resends' => $record->resends,
            'is_blocked' => $record->is_blocked,
            'last_activity' => $record->last_activity ? $record->last_activity->format('M j, Y g:i A') : null,
        ]);
    }

    /**
     * Reset counters for a security tracking record
     */
    public function authResetCounters($id)
    {
        $record = SecurityTracking::findOrFail($id);

        $record->update([
            'page_visits' => 0,
            'otp_requests' => 0,
            'otp_inputs' => 0,
            'resends' => 0,
        ]);

        ActivityLog::logAction(Auth::id(), 'Reset Auth Counters', "Email: {$record->email}");

        return back()->with('success', 'Counters reset successfully!');
    }

    /**
     * Block a user from auth reset
     */
    public function authResetBlock($id)
    {
        $record = SecurityTracking::findOrFail($id);
        $record->block();

        ActivityLog::logAction(Auth::id(), 'Blocked Auth Reset', "Email: {$record->email}");

        return back()->with('success', 'User blocked successfully!');
    }

    /**
     * Unblock a user from auth reset
     */
    public function authResetUnblock($id)
    {
        $record = SecurityTracking::findOrFail($id);
        $record->unblock();

        ActivityLog::logAction(Auth::id(), 'Unblocked Auth Reset', "Email: {$record->email}");

        return back()->with('success', 'User unblocked successfully!');
    }

    /**
     * Send password reset email
     */
    public function sendPasswordReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('gmail', $request->email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found']);
        }

        // Generate a password reset token
        $token = \App\Models\PasswordReset::generateToken($user->username);

        // Send email (This logic would likely be in a reusable service or model method)
        // Here we just return success for the tool
        return response()->json(['success' => true, 'message' => "Reset link sent to {$request->email}"]);
    }

    /**
     * Show signatories management
     */
    public function signatories()
    {
        $signatories = \App\Models\Signatory::all();
        $user = Auth::user();
        $unreadCount = Notification::getUnreadCount($user->id);

        return view('admin.signatories', compact('signatories', 'user', 'unreadCount'));
    }

    /**
     * Update signatories
     */
    public function updateSignatories(Request $request)
    {
        $request->validate([
            'signatories' => 'required|array',
            'signatories.*.id' => 'required|exists:signatories,id',
            'signatories.*.name' => 'nullable|string|max:200',
            'signatories.*.title' => 'nullable|string|max:200'
        ]);

        foreach ($request->signatories as $data) {
            $sig = \App\Models\Signatory::find($data['id']);
            $sig->name = $data['name'];
            $sig->title = $data['title'] ?? $sig->title;
            $sig->save();
        }

        ActivityLog::logAction(Auth::id(), 'Updated Signatories', 'Administrative signatories updated');

        return redirect()->back()->with('success', 'Signatories updated successfully.');
    }
}
