<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveCredit;
use App\Models\LeaveCreditAuditLog;
use App\Models\LeaveUpdateRequest;
use App\Models\LeaveCreditPolicy;
use App\Models\ActivityLog; // Re-added
use App\Models\Notification; // Re-added
use Illuminate\Http\Request; // Re-added
use Illuminate\Support\Facades\Auth; // Re-added
use Illuminate\Support\Facades\DB; // Re-added
use Illuminate\Support\Facades\Hash; // Re-added

class HRController extends Controller
{
    /**
     * HR Dashboard - redirects to admin dashboard
     */
    public function dashboard()
    {
        return redirect()->route('admin.dashboard');
    }

    /**
     * List users for credit management
     */
    public function manageCredits(Request $request)
    {
        $search = $request->input('search');

        $users = User::where('role', '!=', 'super_admin')
            ->when($search, function ($query, $search) {
                return $query->where('first_name', 'like', "%{$search}%")
                             ->orWhere('last_name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('last_name')
            ->paginate(15);

        return view('hr.manage_credits.index', compact('users'));
    }

    /**
     * Edit credits for a user
     */
    public function editCredits(User $user)
    {
        // Ensure CTO type exists (with Statutoty category so it doesn't mix with Credit/Vacation leaves)
        $ctoType = LeaveType::firstOrCreate(
            ['type_name' => 'Compensatory Time Off'],
            ['description' => 'CTO - Manual Entry', 'category' => 'Statutory', 'is_active' => true]
        );

        $otherTypes = LeaveType::where('id', '!=', $ctoType->id)->get();
        
        $ctoCredits = \App\Models\CompensatoryLeaveCredit::where('user_id', $user->id)
            ->where('status', 'Active')
            ->where('remaining_credits', '>', 0)
            ->orderBy('expiration_date', 'asc')
            ->get();

        // Load existing credits keyed by type ID
        $existingCredits = LeaveCredit::where('user_id', $user->id)
            ->get()
            ->keyBy('leave_type_id');

        return view('hr.manage_credits.edit', compact('user', 'ctoType', 'otherTypes', 'existingCredits', 'ctoCredits'));
    }

    /**
     * Store new CTO credit
     */
    public function addCtoCredit(Request $request, User $user)
    {
        $request->validate([
            'credit_amount' => 'required|numeric|min:0.1',
            'expiration_date' => 'required|date|after:today',
            'remarks' => 'nullable|string|max:255',
        ]);

        $ctoType = LeaveType::firstOrCreate(
            ['type_name' => 'Compensatory Time Off'],
            ['description' => 'CTO - Manual Entry', 'category' => 'Statutory', 'is_active' => true]
        );

        // Check Max Limit (15)
        // Correctly calculate current balance from active batches or LeaveCredit table
        // But since LeaveCredit stores total, we use that.
        $currentTotal = LeaveCredit::where('user_id', $user->id)
            ->where('leave_type_id', $ctoType->id)
            ->value('credits') ?? 0;
            
        if (($currentTotal + $request->credit_amount) > 15) {
            return back()->with('error', "Cannot add credits. Total CTO would exceed the limit of 15. Current: $currentTotal");
        }

        DB::transaction(function () use ($request, $user, $ctoType, $currentTotal) {
            // 1. Create Batch Record
            \App\Models\CompensatoryLeaveCredit::create([
                'user_id' => $user->id,
                'leave_type_id' => $ctoType->id,
                'credits' => $request->credit_amount,
                'remaining_credits' => $request->credit_amount,
                'expiration_date' => $request->expiration_date,
                'remarks' => $request->remarks,
                'status' => 'Active',
            ]);

            // 2. Update Total Balance
            $creditRecord = LeaveCredit::firstOrNew([
                'user_id' => $user->id,
                'leave_type_id' => $ctoType->id
            ]);
            $creditRecord->credits = $currentTotal + $request->credit_amount;
            $creditRecord->is_locked = false; 
            $creditRecord->save();

            // 3. Log
            LeaveCreditAuditLog::create([
                'actor_id' => Auth::id(),
                'target_user_id' => $user->id,
                'action' => 'add_cto',
                'leave_type_name' => 'Compensatory Time Off',
                'previous_value' => $currentTotal,
                'new_value' => $creditRecord->credits,
                'reason' => 'Added CTO batch: ' . $request->credit_amount . ' expiring ' . $request->expiration_date,
            ]);
        });
        
        return back()->with('success', 'Compensatory Time Off added successfully.');
    }

    /**
     * Update credits (if not locked)
     */
    public function updateCredits(Request $request, User $user)
    {
        // 1. Identify which types are being updated (exclude CTO which is handled separately)
        $submittedCredits = $request->input('credits', []); // [type_id => amount]

        DB::beginTransaction();
        try {
            foreach ($submittedCredits as $typeId => $rawAmount) {
                // Normalize input to float, default to 0.0
                $amount = is_numeric($rawAmount) ? (float) $rawAmount : 0.0;

                $type = LeaveType::find($typeId);
                if (!$type) continue;
                
                // Skip CTO in this loop if it accidentally gets here
                if ($type->type_name === 'Compensatory Time Off') continue;

                // Check policy limits
                $policy = LeaveCreditPolicy::where('leave_type_id', $typeId)->first();
                if ($policy && $policy->max_credits > 0 && $amount > $policy->max_credits) {
                    // We can either throw an error or cap it. 
                    // Let's cap it or fail? Failing might be better to alert user.
                    // But to keep it simple, let's just fail this validtion.
                    throw new \Exception("Credit amount for {$type->type_name} exceeds the maximum policy limit of {$policy->max_credits}.");
                }

                // Check existing
                $creditRecord = LeaveCredit::firstOrNew(
                    ['user_id' => $user->id, 'leave_type_id' => $typeId]
                );

                // If locked, skip or error
                // Exception: Head HR can edit locked credits directly
                $currentUser = Auth::user();
                /** @var \App\Models\User $currentUser */
                $isHeadHR = $currentUser->isHeadHR();
                
                if ($creditRecord->exists && $creditRecord->is_locked && !$isHeadHR) {
                    continue; // Skip locked records silently or handle error
                }

                $oldValue = $creditRecord->credits ?? 0;
                // Ensure newValue is not null (default to 0.00)
                $newValue = is_numeric($amount) ? (float) $amount : 0.0;

                // Update
                $creditRecord->credits = $newValue;
                $creditRecord->is_locked = true; // Lock immediately after input
                $creditRecord->save();

                // Audit Log
                LeaveCreditAuditLog::create([
                    'actor_id' => Auth::id(),
                    'target_user_id' => $user->id,
                    'action' => $creditRecord->wasRecentlyCreated ? 'allocate' : 'update',
                    'leave_type_name' => $type->type_name,
                    'previous_value' => $oldValue,
                    'new_value' => $newValue,
                    'reason' => 'Initial credit allocation by HR',
                ]);

                // Also log to main System Activity Log for Super Admin visibility
                ActivityLog::logAction(
                    Auth::id(),
                    'update_credits',
                    "Updated {$type->type_name} credits for {$user->full_name} from {$oldValue} to {$newValue}." . ($isHeadHR ? " (Head HR Override)" : "")
                );
            }
            
            DB::commit();
            return redirect()->route('hr-staff.manage-credits.edit', $user->id)
                ->with('success', 'Credits updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating credits: ' . $e->getMessage());
        }
    }

    /**
     * Request unlock for a specific leave type credit
     */
    public function requestUnlock(Request $request, User $user)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'reason' => 'required|string|max:255',
        ]);

        LeaveUpdateRequest::create([
            'requester_id' => Auth::id(),
            'target_user_id' => $user->id,
            'leave_type_id' => $request->leave_type_id,
            'reason' => $request->reason,
            'status' => 'Pending',
        ]);

        return back()->with('success', 'Unlock request sent to Head HR.');
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
            'full_name' => 'nullable|string|max:100',
            'office_station' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:6|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
