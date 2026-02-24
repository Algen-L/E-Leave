<?php

namespace App\Http\Controllers;

use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LeaveService;
use App\Services\DocumentService;
use App\Http\Requests\Leave\StoreLeaveRequest;

class LeaveController extends Controller
{
    protected $leaveService;
    protected $documentService;

    public function __construct(LeaveService $leaveService, DocumentService $documentService)
    {
        $this->leaveService = $leaveService;
        $this->documentService = $documentService;
    }

    /**
     * Show the application form
     */
    public function showApplyForm()
    {
        $data = $this->leaveService->getApplyFormData(Auth::user());

        // Fetch Officers for the dropdowns (legacy fallback or static lists)
        $recommendingOfficers = User::whereIn('role', ['cid_chief', 'sgod_chief', 'ao', 'asds'])
            ->where('is_active', true)
            ->orderBy('last_name')
            ->get();

        $finalApprovers = User::whereIn('role', ['asds', 'sds'])
            ->where('is_active', true)
            ->orderBy('last_name')
            ->get();

        return view('user.leave.apply', array_merge($data, [
            'recommendingOfficers' => $recommendingOfficers,
            'finalApprovers' => $finalApprovers
        ]));
    }

    /**
     * Submit leave application
     */
    public function submitApplication(StoreLeaveRequest $request)
    {
        try {
            $this->leaveService->submitApplication(Auth::user(), $request->validated());
            return redirect()->route('user.leave.history')->with('success', 'Leave application submitted successfully! Pending HR Verification.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show user's applications
     */
    public function myApplications()
    {
        $data = $this->leaveService->getUserHistory(Auth::user());
        return view('user.leave.history', $data);
    }

    /**
     * Show single application details
     */
    public function show($id)
    {
        $application = LeaveApplication::with(['leaveType', 'details', 'recommendingOfficer', 'approvingOfficer'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('user.leave.show', compact('application'));
    }

    /**
     * Generate Word/PDF Form 6
     */
    public function generateForm6($id)
    {
        try {
            $application = LeaveApplication::findOrFail($id);

            // Authorization check
            if ($application->user_id !== Auth::id() && Auth::user()->role === 'user') {
                abort(403, 'Unauthorized action.');
            }

            $format = request('format', 'docx');
            $result = $this->documentService->generateForm6($application, $format);

            if ($format === 'pdf') {
                return response()->file($result['path'], [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $result['filename'] . '"',
                ])->deleteFileAfterSend(true);
            }

            // For DOCX, we use the TemplateProcessor object directly
            $filename = 'Leave_Form6_' . ($application->user->last_name ?? 'Form') . '_' . $application->id . '.docx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $result->saveAs('php://output');
            exit;

        } catch (\Exception $e) {
            return back()->with('error', 'Error generating file: ' . $e->getMessage());
        }
    }
}
