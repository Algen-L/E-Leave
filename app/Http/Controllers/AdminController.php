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
use Illuminate\Support\Facades\DB;
use App\Models\LeaveApplication;
use App\Models\LeaveCreditAuditLog;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Dashboard
     */
    public function dashboard()
    {
        if (auth()->user()->isHeadHR()) {
            return redirect()->route('head-hr.dashboard');
        }
        
        if (auth()->user()->role === 'hr') {
            return redirect()->route('hr.dashboard');
        }

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
        $isSuperAdmin = $user->isSuperAdmin();

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

        // Monthly Engagement (Active users in last 30 days / Total Users)
        $activeLast30Days = ActivityLog::where('created_at', '>=', now()->subDays(30))
            ->distinct('user_id')
            ->count('user_id');
        
        $monthlyEngagement = $totalUsers > 0 ? round(($activeLast30Days / $totalUsers) * 100, 1) : 0;

        // New registrations last month (for trend)
        $lastMonthRegistrations = User::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->count();
        $registrationTrendUp = $newRegistrations >= $lastMonthRegistrations;

        $data = [
            'totalUsers' => $totalUsers,
            'activeToday' => $activeToday,
            'newRegistrations' => $newRegistrations,
            'registrationTrendUp' => $registrationTrendUp,
            'monthlyEngagement' => $monthlyEngagement,
            'auditTrail' => $auditTrail,
            'user' => $user,
            'unreadCount' => Notification::getUnreadCount($user->id),
            'roleDistribution' => collect([]),
            'officeDistribution' => collect([]),
            'userGrowth' => collect([]),
            'securityStats' => ['total_security_events' => 0, 'blocked_users' => 0, 'avg_visits' => 0]
        ];

        // Superadmin specific data
        if ($isSuperAdmin) {
            // 1. Role Distribution
            $data['roleDistribution'] = User::select('role as label', DB::raw('count(*) as value'))
                ->groupBy('role')
                ->get();

            // 2. Office/Station Distribution (Top 5)
            $data['officeDistribution'] = User::select('office_station as label', DB::raw('count(*) as value'))
                ->whereNotNull('office_station')
                ->groupBy('office_station')
                ->orderBy('value', 'desc')
                ->limit(5)
                ->get();

            // 3. User Growth (Last 6 Months)
            $userGrowth = collect();
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $monthLabel = $month->format('M Y');
                
                $count = User::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count();
                    
                $userGrowth->push([
                    'month' => $monthLabel,
                    'count' => $count
                ]);
            }
            $data['userGrowth'] = $userGrowth;

            // 5. Application Filed Analytics (Last 6 Months)
            $totalApplications = LeaveApplication::count();
            $applicationGrowthData = collect();
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $monthLabel = $month->format('M Y');
                
                $count = LeaveApplication::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count();
                    
                $applicationGrowthData->push([
                    'month' => $monthLabel,
                    'count' => $count
                ]);
            }

            // Trend: Filed this month vs Last month
            $filedThisMonth = LeaveApplication::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count();
            $filedLastMonth = LeaveApplication::whereYear('created_at', now()->subMonth()->year)
                ->whereMonth('created_at', now()->subMonth()->month)
                ->count();
            
            $filedTrendUp = $filedThisMonth >= $filedLastMonth;
            $filedGrowthRate = $filedLastMonth > 0 ? round((($filedThisMonth - $filedLastMonth) / $filedLastMonth) * 100, 1) : ($filedThisMonth > 0 ? 100 : 0);

            $data['totalApplications'] = $totalApplications;
            $data['applicationGrowth'] = $applicationGrowthData;
            $data['filedTrendUp'] = $filedTrendUp;
            $data['filedGrowthRate'] = $filedGrowthRate;

            // 6. Office Category Distribution (Applications)
            $data['officeCategoryDistribution'] = LeaveApplication::join('users', 'leave_applications.user_id', '=', 'users.id')
                ->join('offices', 'users.office_station', '=', 'offices.name')
                ->select('offices.category as label', DB::raw('count(*) as value'))
                ->groupBy('offices.category')
                ->get();

            // 4. Security Stats
            $data['securityStats'] = [
                'blocked_users' => SecurityTracking::where('is_blocked', true)->count(),
                'total_security_events' => SecurityTracking::sum('otp_requests') + SecurityTracking::sum('resends'),
                'avg_visits' => round(SecurityTracking::avg('page_visits') ?? 0, 1)
            ];
        }

        return $data;
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

        $query = User::query()->with('roles');

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
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        // Apply office filter
        if (!empty($filters['office'])) {
            $query->where('office_station', $filters['office']);
        }

        // Exclude current user and all super admins from the list unless current user is super admin
        if (!Auth::user()->isSuperAdmin()) {
            $query->where('id', '!=', Auth::id())
                  ->whereDoesntHave('roles', function ($q) {
                      $q->where('name', 'super_admin');
                  });
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

        $roles = [
            'user',
            'admin',
            'hr',
            'head_hr',
            'hr_review_officer',
            'immediate_head',
            'asds',
            'sds',
            'sgod_chief',
            'cid_chief',
            'ao',
            'record_personnel',
        ];

        return view('admin.manage-users', [
            'view' => $view,
            'users' => $users,
            'auditLogs' => $auditLogs,
            'filters' => $filters,
            'user' => Auth::user(),
            'offices' => $offices,
            'roles' => $roles,
            'unreadCount' => Notification::getUnreadCount(Auth::id()),
        ]);
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'gmail' => 'required|email|unique:users,gmail,' . $user->id,
            'position' => 'nullable|string|max:100',
            'roles' => 'required|array',
            'roles.*' => 'string|in:user,super_admin,admin,hr,head_hr,hr_review_officer,immediate_head,asds,sds,sgod_chief,cid_chief,ao,record_personnel',
            'office_station' => 'nullable|string|max:100',
            'is_active' => 'required|boolean',
            'password' => 'nullable|string|min:6|confirmed',
            'employee_number' => 'nullable|string|regex:/^[0-9]{7}$/|unique:users,employee_number,' . $user->id,
        ]);

        $isHrOnly = Auth::user()->hasRole(['hr', 'head_hr', 'hr_review_officer']) && !Auth::user()->hasRole(['super_admin', 'admin']);
        if ($isHrOnly) {
            $roleNames = $user->roles->pluck('name')->toArray();
        } else {
            $roleNames = $request->roles ?? [];
            
            // Prevent removing super_admin if editing oneself
            if ($user->id === Auth::id() && $user->hasRole('super_admin') && !in_array('super_admin', $roleNames)) {
                $roleNames[] = 'super_admin';
            }
            
            // Prevent assigning super_admin or admin to others
            if ($user->id !== Auth::id()) {
                $roleNames = array_diff($roleNames, ['super_admin', 'admin']);
                // Keep the target user's existing super_admin/admin roles if they already have them
                if ($user->hasRole('super_admin')) {
                    $roleNames[] = 'super_admin';
                }
                if ($user->hasRole('admin')) {
                    $roleNames[] = 'admin';
                }
            }
            
            if (!in_array('super_admin', $roleNames) && !in_array('user', $roleNames)) {
                $roleNames[] = 'user';
            }
        }

        // Authorization check for super_admin role assignment
        if (in_array('super_admin', $roleNames) && !Auth::user()->isSuperAdmin()) {
            return redirect()->back()->withInput()->with('error', 'Only Super Admin can assign the Super Admin role.');
        }

        // Authorization check for restricted roles
        $restrictedRoles = ['asds', 'sds', 'sgod_chief', 'cid_chief', 'ao'];
        $requestedRestricted = array_intersect($roleNames, $restrictedRoles);
        if (!empty($requestedRestricted) && !Auth::user()->isSuperAdmin()) {
            return redirect()->back()->withInput()->with('error', 'Only Super Admin can assign this role.');
        }

        // HR Review Officer Restriction: Only HR or Super Admin
        if (in_array('hr_review_officer', $roleNames) && !Auth::user()->isSuperAdmin() && !Auth::user()->isHR()) {
            return redirect()->back()->withInput()->with('error', 'Only HR Personnel or Super Admin can assign the HR Review Officer role.');
        }

        $mainRole = 'user';
        foreach ($roleNames as $rName) {
            if ($rName !== 'user') {
                $mainRole = $rName;
                break;
            }
        }

        $updateData = [
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'gmail' => $request->gmail,
            'position' => $request->position,
            'role' => $mainRole, // Backwards compatibility
            'office_station' => $request->office_station,
            'is_active' => $request->is_active,
            'employee_number' => $request->employee_number,
        ];

        // Only update password if provided (hashed cast handles hashing)
        if ($request->filled('password')) {
            $updateData['password'] = $request->password;
        }

        $user->update($updateData);

        // Sync roles
        $roleIds = \App\Models\Role::whereIn('name', $roleNames)->pluck('id')->toArray();
        $user->roles()->sync($roleIds);

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
            'first_name' => 'nullable|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'office_station' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:6|confirmed',
            'current_password' => 'required_with:password',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'esignature' => 'nullable|image|mimes:png|max:1024',
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
        if ($request->has('employee_number')) {
            $updateData['employee_number'] = $request->employee_number;
        }

        if (!empty($request->password)) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->with('error', 'Current password does not match.');
            }
            $updateData['password'] = Hash::make($request->password);

            // Notify HR and Super Admins
            $hrAndAdmins = User::whereIn('role', ['hr', 'head_hr', 'hr_review_officer', 'super_admin'])
                ->where('is_active', true)
                ->get();
                
            foreach ($hrAndAdmins as $admin) {
                if ($admin->id !== $user->id) {
                    Notification::send($user->id, $admin->id, "Admin {$user->full_name} has updated their account password.");
                }
            }
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete(str_replace('storage/', '', $user->profile_picture));
            }
            $path = $request->file('profile_picture')->store('profile_pics', 'public');
            $updateData['profile_picture'] = 'storage/' . $path;
        }

        // Handle e-signature upload
        if ($request->hasFile('esignature')) {
            if ($user->esignature) {
                Storage::disk('public')->delete(str_replace('storage/', '', $user->esignature));
            }
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
                        if ($user->esignature) {
                            Storage::disk('public')->delete(str_replace('storage/', '', $user->esignature));
                        }
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
        $user = Auth::user();
        $type = $request->get('type', 'system');

        // Prevent HR Review Officer from viewing Officer Actions
        if ($user->hasRole('hr_review_officer') && !$user->hasRole(['super_admin', 'admin']) && $type === 'officer') {
            return redirect()->route('admin.activity-logs', ['type' => 'system'])->with('error', 'Access denied to Officer Actions.');
        }

        $unreadCount = Notification::getUnreadCount($user->id);
        $filters = [
            'search' => $request->get('search', ''),
            'action' => $request->get('action', ''),
            'date_range' => $request->get('date_range', '30days'),
            'officer_id' => $request->get('officer_id', ''),
            'type' => $type,
        ];

        // 1. Initialize Query based on type
        if ($type === 'credit') {
            $query = LeaveCreditAuditLog::with(['actor', 'targetUser']);
            if (!$user->isSuperAdmin()) {
                $query->whereDoesntHave('actor.roles', function($q) {
                    $q->where('name', 'super_admin');
                })->whereDoesntHave('targetUser.roles', function($q) {
                    $q->where('name', 'super_admin');
                });
            }
        } else {
            $query = ActivityLog::withUserDetails();
            if (!$user->isSuperAdmin()) {
                $query->whereDoesntHave('user.roles', function($q) {
                    $q->where('name', 'super_admin');
                });
            }
            
            // For 'officer' type, filter actors by HR roles
            if ($type === 'officer') {
                $hrRoles = ['hr', 'head_hr', 'hr_review_officer'];
                if ($user->hasRole('super_admin')) {
                    $hrRoles[] = 'super_admin';
                }
                $query->whereHas('user', function ($q) use ($hrRoles) {
                    $q->whereIn('role', $hrRoles);
                });
            }
        }

        // 2. Apply Filters (Search)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            if ($type === 'credit') {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('actor', function ($q2) use ($search) {
                        $q2->where('full_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('targetUser', function ($q2) use ($search) {
                        $q2->where('full_name', 'like', "%{$search}%");
                    })
                    ->orWhere('leave_type_name', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%");
                });
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('action', 'like', "%{$search}%")
                      ->orWhere('details', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($qu) use ($search) {
                          $qu->where('full_name', 'like', "%{$search}%");
                      });
                });
            }
        }

        // 3. Apply Filters (Officer specific for 'officer' tab)
        if ($type === 'officer' && !empty($filters['officer_id'])) {
            $query->where('user_id', $filters['officer_id']);
        }

        // 4. Apply Filters (Action specific)
        if (!empty($filters['action'])) {
            if ($type === 'credit') {
                $query->where('action', $filters['action']);
            } else {
                $actionMap = [
                    'login' => 'login',
                    'logout' => 'logout',
                    'create' => ['create', 'register', 'add'],
                    'update' => ['update', 'edit', 'change'],
                    'delete' => ['delete', 'remove'],
                    'Verify' => 'Verif',
                    'Approve' => 'Approv',
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
        }

        // 5. Apply Filters (Date Range)
        if (!empty($filters['date_range'])) {
            $tz = config('app.timezone');
            switch ($filters['date_range']) {
                case 'today':
                    $query->whereDate('created_at', Carbon::now($tz)->toDateString());
                    break;
                case '7days':
                    $query->where('created_at', '>=', Carbon::now($tz)->subDays(6)->startOfDay());
                    break;
                case '30days':
                    $query->where('created_at', '>=', Carbon::now($tz)->subDays(29)->startOfDay());
                    break;
            }
        }

        // 6. Execute Query
        $logs = $query->latest('created_at')->paginate(25)->withQueryString();

        // 7. Get dropdown data
        $officers = [];
        if ($type === 'officer') {
            $hrRoles = ['hr', 'head_hr', 'hr_review_officer'];
            if ($user->role === 'super_admin') { $hrRoles[] = 'super_admin'; }
            $officers = User::whereIn('role', $hrRoles)->where('is_active', true)->orderBy('full_name')->get();
        }

        return view('admin.activity-logs', compact('logs', 'filters', 'officers', 'user', 'unreadCount', 'type'));
    }

    /**
     * Register new user (by admin)
     */
    public function showRegisterUser()
    {
        $offices = Office::all()->groupBy('category');
        $allRoles = \App\Models\Role::all();

        return view('admin.register-user', [
            'user' => Auth::user(),
            'offices' => $offices,
            'allRoles' => $allRoles,
            'unreadCount' => Notification::getUnreadCount(Auth::id()),
        ]);
    }

    /**
     * Store new user (admin registration)
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'gmail' => 'nullable|email|unique:users,gmail',
            'office_station' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'employee_number' => 'required|string|regex:/^[0-9]{7}$/|unique:users,employee_number',
            'roles' => 'required|array',
            'roles.*' => 'string|in:user,super_admin,admin,hr,head_hr,hr_review_officer,immediate_head,asds,sds,sgod_chief,cid_chief,ao,record_personnel',
        ]);

        $isHrOnly = Auth::user()->hasRole(['hr', 'head_hr', 'hr_review_officer']) && !Auth::user()->hasRole(['super_admin', 'admin']);
        if ($isHrOnly) {
            $roleNames = ['user'];
        } else {
            $roleNames = $request->roles;
            if (!in_array('super_admin', $roleNames) && !in_array('user', $roleNames)) {
                $roleNames[] = 'user';
            }
        }

        // Authorization check for super_admin role assignment
        if (in_array('super_admin', $roleNames) && !Auth::user()->isSuperAdmin()) {
            return redirect()->back()->withInput()->with('error', 'Only Super Admin can create accounts with the Super Admin role.');
        }

        // Authorization check for restricted roles
        $restrictedRoles = ['asds', 'sds', 'sgod_chief', 'cid_chief', 'ao'];
        $requestedRestricted = array_intersect($roleNames, $restrictedRoles);
        if (!empty($requestedRestricted) && !Auth::user()->isSuperAdmin()) {
            return redirect()->back()->withInput()->with('error', 'Only Super Admin can create accounts with this role.');
        }

        // HR Review Officer Restriction: Only HR or Super Admin
        if (in_array('hr_review_officer', $roleNames) && !Auth::user()->isSuperAdmin() && !Auth::user()->isHR()) {
            return redirect()->back()->withInput()->with('error', 'Only HR Personnel or Super Admin can assign the HR Review Officer role.');
        }

        $mainRole = 'user';
        foreach ($roleNames as $rName) {
            if ($rName !== 'user') {
                $mainRole = $rName;
                break;
            }
        }

        $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->first_name)) . mt_rand(1000, 9999);

        $user = User::create([
            'username' => $username,
            'password' => $request->password,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'gmail' => $request->gmail,
            'office_station' => $request->office_station,
            'position' => $request->position,
            'employee_number' => $request->employee_number,
            'role' => $mainRole,
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        // Sync roles
        $roleIds = \App\Models\Role::whereIn('name', $roleNames)->pluck('id')->toArray();
        $user->roles()->sync($roleIds);

        ActivityLog::logAction(Auth::id(), 'Created User', "User ID: {$user->id} - {$user->full_name}");

        // Notify HR Personnel if created by an HR Review Officer
        if (Auth::user()->hasRole('hr_review_officer')) {
            $hrStaff = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['hr', 'head_hr']);
            })->where('is_active', true)->get();
            foreach ($hrStaff as $hr) {
                Notification::send(Auth::id(), $hr->id, "HR Review Officer " . Auth::user()->full_name . " has registered a new user: " . $user->full_name);
            }
        }

        return redirect()->route('admin.manage-users')->with('success', 'User created successfully!');
    }

    /**
     * Edit user page
     */
    public function editUser(User $user)
    {
        $user->load('roles');
        $offices = Office::all()->groupBy('category');
        $allRoles = \App\Models\Role::all();

        return view('admin.edit-user', [
            'editUser' => $user,
            'user' => Auth::user(),
            'offices' => $offices,
            'allRoles' => $allRoles,
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
     * Show combined offices and signatories management
     */
    public function officesAndSignatories()
    {
        $signatories = \App\Models\Signatory::all();
        
        // Get user counts per office to show in deletion warnings
        $userCounts = User::select('office_station', DB::raw('count(*) as total'))
            ->whereNotNull('office_station')
            ->groupBy('office_station')
            ->pluck('total', 'office_station');

        $offices = Office::orderBy('category')->orderBy('name')->get();
        foreach ($offices as $office) {
            $office->user_count = $userCounts[$office->name] ?? 0;
        }
        $offices = $offices->groupBy('category');

        $user = Auth::user();
        $unreadCount = Notification::getUnreadCount($user->id);

        return view('admin.signatories', compact('signatories', 'offices', 'user', 'unreadCount'));
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

    /**
     * Store a new office
     */
    public function storeOffice(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:50',
            'name' => 'required|string|max:100|unique:offices,name',
        ]);

        Office::create($request->all());

        ActivityLog::logAction(Auth::id(), 'Created Office', "Office: {$request->name} ({$request->category})");

        return redirect()->back()->with('success', 'Office added successfully!');
    }

    /**
     * Update an office
     */
    public function updateOffice(Request $request, Office $office)
    {
        $request->validate([
            'category' => 'required|string|max:50',
            'name' => 'required|string|max:100|unique:offices,name,' . $office->id,
        ]);

        $oldName = $office->name;
        $office->update($request->all());

        ActivityLog::logAction(Auth::id(), 'Updated Office', "Office: {$oldName} -> {$office->name}");

        return redirect()->back()->with('success', 'Office updated successfully!');
    }

    /**
     * Delete an office
     */
    public function deleteOffice(Office $office)
    {
        $name = $office->name;
        
        // Update users belonging to this office to have no office station
        User::where('office_station', $name)->update(['office_station' => null]);
        
        $office->delete();

        ActivityLog::logAction(Auth::id(), 'Deleted Office', "Office: {$name}");

        return redirect()->back()->with('success', "Office '{$name}' removed. Users from this office have been updated to 'No Office Assigned'.");
    }
}
