<?php

namespace App\Http\Controllers;

use App\Models\LeaveApplication;
use Illuminate\Http\Request;

class RecordPersonnelController extends Controller
{
    /**
     * Home Dashboard for Record Personnel
     */
    public function dashboard()
    {
        $stats = [
            'total' => LeaveApplication::count(),
            'approved' => LeaveApplication::where('status', 'Approved')->count(),
            'pending' => LeaveApplication::where('status', 'like', 'Pending%')->count(),
            'disapproved' => LeaveApplication::where('status', 'Disapproved')->count(),
        ];

        return view('record_personnel.home', compact('stats'));
    }

    /**
     * Searchable list of all system-wide leave applications
     */
    public function index(Request $request)
    {
        $query = LeaveApplication::with(['user', 'leaveType'])->orderBy('created_at', 'desc');

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->whereHas('user', function($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                  ->orWhere('last_name', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $applications = $query->paginate(20)->withQueryString();
        
        return view('record_personnel.index', compact('applications'));
    }

    /**
     * View detailed application (Read-Only)
     */
    public function showLeave($id)
    {
        $application = LeaveApplication::with([
            'user', 
            'leaveType', 
            'details', 
            'recommendingOfficer', 
            'approvingOfficer', 
            'hrVerifier'
        ])->findOrFail($id);

        // --- CREDIT CALCULATION LOGIC ---
        $applicant = $application->user;
        $appTypeName = $application->leaveType->type_name ?? '';
        $daysApplied = $application->days_applied;

        $vlType = \App\Models\LeaveType::where('type_name', 'Vacation Leave')->first();
        $slType = \App\Models\LeaveType::where('type_name', 'Sick Leave')->first();

        $vlCredit = 0;
        if ($vlType) {
            $checkVl = \App\Models\LeaveCredit::where('user_id', $applicant->id)->where('leave_type_id', $vlType->id)->first();
            $vlCredit = $checkVl ? $checkVl->credits : 0;
        }

        $slCredit = 0;
        if ($slType) {
            $checkSl = \App\Models\LeaveCredit::where('user_id', $applicant->id)->where('leave_type_id', $slType->id)->first();
            $slCredit = $checkSl ? $checkSl->credits : 0;
        }

        $lessVl = 0;
        $lessSl = 0;

        $isCompensatory = optional($application->details)->other_purpose === 'COC COMPENSATORY OVERTIME CREDIT';
        $vlRelatedTypes = ['Vacation', 'Forced', 'Mandatory'];

        $isVlRelated = false;
        foreach ($vlRelatedTypes as $keyword) {
            if (stripos($appTypeName, $keyword) !== false) {
                $isVlRelated = true;
                break;
            }
        }

        if ($isVlRelated || $isCompensatory) {
            $lessVl = $daysApplied;
        } elseif (stripos($appTypeName, 'Sick') !== false) {
            $lessSl = $daysApplied;
        }

        $credits = [
            'vl' => [
                'current' => $vlCredit,
                'less' => $lessVl,
                'balance' => $vlCredit - $lessVl
            ],
            'sl' => [
                'current' => $slCredit,
                'less' => $lessSl,
                'balance' => $slCredit - $lessSl
            ]
        ];

        return view('record_personnel.show-leave', compact('application', 'credits'));
    }
}
