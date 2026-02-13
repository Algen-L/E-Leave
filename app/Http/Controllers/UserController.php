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

class UserController extends Controller
{
    /**
     * User home page
     */
    public function home(Request $request)
    {
        $user = Auth::user();

        // Handle AJAX notification read action
        if ($request->has('action') && $request->action === 'read_notif' && $request->has('notif_id')) {
            $notification = Notification::find($request->notif_id);
            if ($notification && $notification->recipient_id === $user->id) {
                $notification->markAsRead();
            }
            return response()->json(['success' => true]);
        }

        $notifications = Notification::getUnreadForUser($user->id);

        return view('user.home', [
            'user' => $user,
            'notifications' => $notifications,
            'unreadCount' => $notifications->count(),
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

        return view('user.profile', [
            'user' => $user,
            'notifications' => $notifications,
            'unreadCount' => $notifications->count(),
            'recommendingOfficers' => $recommendingOfficers,
            'finalApprovers' => $finalApprovers,
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'full_name' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'office_station' => 'nullable|string|max:100',
            'salary' => 'nullable|string|max:50',
            'recommending_officer_id' => 'nullable|exists:users,id',
            'approving_officer_id' => 'nullable|exists:users,id',
            'password' => 'nullable|string|min:6|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        $updateData = [];

        if ($request->filled('full_name')) {
            $updateData['full_name'] = $request->full_name;
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
        if ($request->has('approving_officer_id')) {
            $updateData['approving_officer_id'] = $request->approving_officer_id;
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $fileName = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_pics', $fileName, 'public');
            $updateData['profile_picture'] = 'storage/' . $path;
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
            'password' => 'required|string|min:6|confirmed',
            'token' => 'required|string',
        ]);

        $user = Auth::user();

        // Verify token
        $passwordReset = PasswordReset::verifyToken($user->username, $request->token);

        if (!$passwordReset) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired verification token.'
            ]);
        }

        // Update password
        $user->update(['password' => $request->password]);

        // Delete token
        PasswordReset::deleteForUser($user->username);

        ActivityLog::logAction($user->id, 'Password Changed', 'User changed password via profile');

        return response()->json([
            'status' => 'success',
            'message' => 'Password updated successfully!'
        ]);
    }

    /**
     * Request password change token
     */
    public function requestPasswordToken()
    {
        $user = Auth::user();

        // Check for active token
        $activeToken = PasswordReset::getActiveToken($user->username);

        if ($activeToken) {
            $token = $activeToken->token;
            $message = 'Your active verification token has been re-sent to your Gmail. It will expire in 5 minutes.';
        } else {
            // Generate new token
            $passwordReset = PasswordReset::generateToken($user->username);
            $token = $passwordReset->token;
            $message = 'Verification token sent to your Gmail. Note: The token expires in 5 minutes.';
        }

        try {
            $this->sendVerificationEmail($user->gmail, $user->full_name, $token);

            return response()->json([
                'status' => 'success',
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send verification email.',
            ]);
        }
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
     * Get notifications (AJAX)
     */
    public function getNotifications()
    {
        $notifications = Notification::getUnreadForUser(Auth::id());
        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->count(),
        ]);
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
}
