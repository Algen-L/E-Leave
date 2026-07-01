<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Standalone Printing & Reporting Hub
     */
    public function printHub()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            Auth::logout();
            return redirect()->route('login');
        }

        $allUsers = User::where('is_active', true)
            ->where('id', '!=', $user->id)
            ->orderBy('full_name')
            ->get();

        $data = [
            'user' => $user,
            'unreadCount' => Notification::getUnreadCount($user->id),
            'allUsers' => $allUsers,
            'allOffices' => Office::orderBy('name')->get(),
        ];

        // If the user has administrative/records access, allow bulk filtering
        if ($user->isRecordPersonnel() || $user->role === 'super_admin' || $user->role === 'admin') {
            $data['subordinateUsers'] = $allUsers;
        }

        return view('reports.print_hub', $data);
    }
}
