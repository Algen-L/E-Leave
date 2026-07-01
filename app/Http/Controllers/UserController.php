<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * User home page
     */
    public function home(Request $request)
    {
        $user = Auth::user();

        // Redirect Higher Roles to their specialized dashboard
        $higherRoles = ['sgod_chief', 'cid_chief', 'ao', 'sds', 'asds'];
        if (in_array($user->role, $higherRoles)) {
            return redirect()->route('user.dashboard');
        }

        // Handle AJAX notification read action
        if ($request->has('action') && $request->action === 'read_notif' && $request->has('notif_id')) {
            $notification = Notification::find($request->notif_id);
            if ($notification && $notification->recipient_id === $user->id) {
                $notification->markAsRead();
            }
            return response()->json(['success' => true]);
        }

        $notifications = \App\Models\Notification::forRecipient($user->id)
            ->with('sender:id,full_name,profile_picture')
            ->latest('created_at')
            ->limit(50)
            ->get();

        // --- SYSTEM PROFILE ALERTS (Virtual) ---
        $systemAlerts = collect();
        if (empty($user->salary)) {
            $systemAlerts->push((object)[
                'id' => 'sys_salary',
                'message' => 'Profile Requirement: Please set your Monthly Salary in the My Profile page.',
                'created_at' => now(),
                'is_read' => false,
                'is_system' => true,
                'icon' => 'fa-coins'
            ]);
        }
        if (empty($user->esignature)) {
            $systemAlerts->push((object)[
                'id' => 'sys_sig',
                'message' => 'Profile Requirement: E-Signature is required for leave applications. Please upload or draw one.',
                'created_at' => now(),
                'is_read' => false,
                'is_system' => true,
                'icon' => 'fa-signature'
            ]);
        }
        if (empty($user->recommending_officer_id)) {
            $systemAlerts->push((object)[
                'id' => 'sys_recom',
                'message' => 'Profile Requirement: Please select your Recommending Officer in My Profile.',
                'created_at' => now(),
                'is_read' => false,
                'is_system' => true,
                'icon' => 'fa-user-tie'
            ]);
        }
        if (empty($user->approving_officer_id)) {
            $systemAlerts->push((object)[
                'id' => 'sys_approv',
                'message' => 'Profile Requirement: Please select your Final Approving Officer in My Profile.',
                'created_at' => now(),
                'is_read' => false,
                'is_system' => true,
                'icon' => 'fa-user-check'
            ]);
        }

        // Merge system alerts at the top
        $unreadCount = $notifications->where('is_read', false)->count() + $systemAlerts->count();
        $notifications = $systemAlerts->merge($notifications);

        // --- FETCH LEAVE CREDITS ---
        $vlType = \App\Models\LeaveType::where('type_name', 'Vacation Leave')->first();
        $slType = \App\Models\LeaveType::where('type_name', 'Sick Leave')->first();
        $ctoType = \App\Models\LeaveType::where('type_name', 'CTO (Compensatory Time Off)')->first();

        $credits = [
            'vl' => \App\Models\LeaveCredit::where('user_id', $user->id)->where('leave_type_id', $vlType->id ?? 0)->value('credits') ?? 0,
            'sl' => \App\Models\LeaveCredit::where('user_id', $user->id)->where('leave_type_id', $slType->id ?? 0)->value('credits') ?? 0,
            'cto' => \App\Models\LeaveCredit::where('user_id', $user->id)->where('leave_type_id', $ctoType->id ?? 0)->value('credits') ?? 0,
        ];

        // --- FETCH LEAVE SUMMARY ---
        $leaveSummary = [
            'pending' => \App\Models\LeaveApplication::where('user_id', $user->id)->whereIn('status', ['Pending', 'Pending HR', 'Pending Recommending', 'Pending Approval'])->count(),
            'approved' => \App\Models\LeaveApplication::where('user_id', $user->id)->where('status', 'Approved')->count(),
            'disapproved' => \App\Models\LeaveApplication::where('user_id', $user->id)->where('status', 'Disapproved')->count(),
            'total' => \App\Models\LeaveApplication::where('user_id', $user->id)->count(),
        ];

        // --- CHECK PROFILE COMPLETION ---
        $profileIncomplete = empty($user->position) ||
            empty($user->salary) ||
            empty($user->recommending_officer_id) ||
            empty($user->approving_officer_id) ||
            empty($user->esignature);

        // --- FETCH DEDUCTION LOGS (Leave Credit Reductions) ---
        $deductionLogs = \App\Models\LeaveCreditAuditLog::with('actor')
            ->where('target_user_id', $user->id)
            ->where('action', 'deduction')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // --- FETCH APPLICATION PROGRESS HISTORY ---
        $applicationHistory = \App\Models\LeaveApplication::with('leaveType')
            ->where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // --- FETCH LEAVE APPLICATIONS FOR CALENDAR (Current Month) ---
        $currentMonthApplications = \App\Models\LeaveApplication::where('user_id', $user->id)
            ->where(function($query) {
                $now = now();
                $query->whereMonth('start_date', $now->month)
                      ->whereYear('start_date', $now->year)
                      ->orWhereMonth('end_date', $now->month)
                      ->whereYear('end_date', $now->year);
            })
            ->get(['id', 'start_date', 'end_date', 'status', 'leave_type_id']);

        return view('user.home', [
            'user' => $user,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'credits' => $credits,
            'leaveSummary' => $leaveSummary,
            'profileIncomplete' => $profileIncomplete,
            'deductionLogs' => $deductionLogs,
            'applicationHistory' => $applicationHistory,
            'calendarApps' => $currentMonthApplications,
        ]);
    }

    /**
     * User profile page
     */
    public function profile()
    {
        $user = Auth::user();
        $notifications = Notification::getUnreadForUser($user->id);

        // Fetch Recommending Officers (CID Chief, SGOD Chief, AO, ASDS)
        $recommendingOfficers = User::whereIn('role', ['cid_chief', 'sgod_chief', 'ao', 'asds'])
            ->where('is_active', true)
            ->orderBy('last_name')
            ->get();

        // Fetch Final Approvers (ASDS, SDS)
        $finalApprovers = User::whereIn('role', ['asds', 'sds'])
            ->where('is_active', true)
            ->orderBy('last_name')
            ->get();

        // Fetch Leave Credits (VL, SL, CTO)
        $vlType = \App\Models\LeaveType::where('type_name', 'Vacation Leave')->first();
        $slType = \App\Models\LeaveType::where('type_name', 'Sick Leave')->first();
        $ctoType = \App\Models\LeaveType::where('type_name', 'CTO (Compensatory Time Off)')->first();

        $credits = [
            'vl' => \App\Models\LeaveCredit::where('user_id', $user->id)->where('leave_type_id', $vlType->id ?? 0)->value('credits') ?? 0,
            'sl' => \App\Models\LeaveCredit::where('user_id', $user->id)->where('leave_type_id', $slType->id ?? 0)->value('credits') ?? 0,
            'cto' => \App\Models\LeaveCredit::where('user_id', $user->id)->where('leave_type_id', $ctoType->id ?? 0)->value('credits') ?? 0,
        ];

        // Fetch subordinate users for Higher Roles OR system users for Record Personnel
        $subordinateUsers = collect();
        $allOffices = collect();

        if ($user->isRecordPersonnel()) {
            $subordinateUsers = User::where('is_active', true)
                ->orderBy('full_name')
                ->get();
            $allOffices = \App\Models\Office::orderBy('name')->get();
        } elseif ($user->isHigherRole()) {
            $category = null;
            if ($user->role === 'sgod_chief') {
                $category = 'SGOD';
            } elseif ($user->role === 'cid_chief') {
                $category = 'CID';
            } elseif (in_array($user->role, ['ao', 'sds', 'asds'])) {
                $category = 'OSDS';
            }

            if ($category) {
                $offices = \App\Models\Office::where('category', $category)->pluck('name')->toArray();
                $subordinateUsers = User::whereIn('office_station', $offices)
                    ->where('id', '!=', $user->id)
                    ->where('is_active', true)
                    ->orderBy('full_name')
                    ->get();
            }
        }

        $allUsers = User::where('role', 'user')
            ->where('is_active', true)
            ->where('id', '!=', $user->id)
            ->orderBy('full_name')
            ->get();

        $departmentHeads = User::where('is_active', true)
            ->where('id', '!=', $user->id)
            ->orderBy('last_name')
            ->get();

        return view('user.profile', [
            'user' => $user,
            'notifications' => $notifications,
            'unreadCount' => $notifications->count(),
            'recommendingOfficers' => $recommendingOfficers,
            'finalApprovers' => $finalApprovers,
            'credits' => $credits,
            'subordinateUsers' => $subordinateUsers,
            'allOffices' => $allOffices,
            'allUsers' => $allUsers,
            'departmentHeads' => $departmentHeads,
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'first_name' => 'nullable|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'office_station' => 'nullable|string|max:100',
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
        if ($request->has('position')) {
            $updateData['position'] = $request->position;
        }
        if ($request->has('salary')) {
            $updateData['salary'] = $request->salary;
        }
        if ($request->has('office_station')) {
            $updateData['office_station'] = $request->office_station;
        }

        if ($request->has('recommending_officer_id')) {
            $updateData['recommending_officer_id'] = $request->recommending_officer_id;
        }
        if ($request->has('approving_officer_id')) $updateData['approving_officer_id'] = $request->approving_officer_id;
        if ($request->has('secretary_id')) $updateData['secretary_id'] = $request->secretary_id;
        
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
        
        if ($request->hasFile('profile_picture')) {
            $updateData['employee_number'] = $request->employee_number;
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete(str_replace('storage/', '', $user->profile_picture));
            }
            $file = $request->file('profile_picture');
            $fileName = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_pics', $fileName, 'public');
            $updateData['profile_picture'] = 'storage/' . $path;
        }

        // Handle E-Signature upload or draw
        $sigMode = $request->input('esignature_mode');

        if ($sigMode === 'draw' && $request->input('esignature_data')) {
            $base64Image = $request->input('esignature_data');

            // Basic validation of base64 string
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image)) {
                $data = substr($base64Image, strpos($base64Image, ',') + 1);
                $data = base64_decode($data);

                if ($user->esignature) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $user->esignature));
                }

                $fileName = 'sign_' . $user->id . '_' . time() . '.png';
                Storage::disk('public')->put('esignatures/' . $fileName, $data);
                $updateData['esignature'] = 'storage/esignatures/' . $fileName;
            }
        } elseif ($request->hasFile('esignature')) {
            $file = $request->file('esignature');

            // Basic validation for PNG
            if (strtolower($file->getClientOriginalExtension()) === 'png') {
                if ($user->esignature) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $user->esignature));
                }
                $fileName = 'sign_' . $user->id . '_' . time() . '.png';
                $path = $file->storeAs('esignatures', $fileName, 'public');
                $updateData['esignature'] = 'storage/' . $path;
            } else {
                return redirect()->back()->with('error', 'Uploaded signature must be a PNG file.');
            }
        }

        if (!empty($updateData)) {
            $user->update($updateData);
            ActivityLog::logAction($user->id, 'Profile Updated', 'User profile updated');
        }

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Update user password with verification
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Current password does not match.');
        }

        // Update password
        $user->update(['password' => Hash::make($request->password)]);

        // Log action
        ActivityLog::logAction($user->id, 'Password Changed', 'User changed password securely via profile');

        // Notify Admins and HR
        $hrAndAdmins = User::whereIn('role', ['hr', 'head_hr', 'super_admin'])
            ->where('is_active', true)
            ->get();
            
        foreach ($hrAndAdmins as $admin) {
            if ($admin->id !== $user->id) {
                Notification::send($user->id, $admin->id, "User {$user->full_name} has updated their account password.");
            }
        }

        return redirect()->back()->with('success', 'Password updated successfully!');
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead(Request $request)
    {
        $request->validate([
            'notification_id' => 'required|integer',
        ]);

        $notification = Notification::find($request->notification_id);

        if ($notification && $notification->recipient_id === Auth::id()) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        Notification::markAllAsReadForUser(Auth::id());
        return response()->json(['success' => true]);
    }

    /**
     * Get notifications (AJAX) - Redirects if direct access
     */
    public function getNotifications(Request $request)
    {
        if (!$request->ajax()) {
            return redirect()->route('user.home');
        }

        $notifications = \App\Models\Notification::forRecipient(Auth::id())
            ->latest('created_at')
            ->limit(50)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->where('is_read', false)->count(),
        ]);
    }

    /**
     * Delete notification
     */
    public function deleteNotification($id)
    {
        $notification = \App\Models\Notification::findOrFail($id);
        
        if ($notification->recipient_id === Auth::id()) {
            $notification->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 403);
    }

    /**
     * Clear all notifications
     */
    public function clearAllNotifications()
    {
        \App\Models\Notification::where('recipient_id', Auth::id())->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Send verification email
     */
    protected function sendVerificationEmail(string $to, string $name, string $token)
    {
        $subject = 'Security Verification Token';
        $body = "Hello {$name},\n\nYour verification code for password change is: {$token}\n\nThis code is valid for 5 minutes.\n\nIf you did not request this, please secure your account.";

        Mail::raw($body, function ($message) use ($to, $subject) {
            $message->to($to)
                ->subject($subject);
        });
    }

    /**
     * Display help page
     */
    public function help()
    {
        return view('user.help');
    }
}
