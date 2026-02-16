<?php

namespace App\Http\Controllers;

use App\Models\LeaveApplication;
use App\Models\LeaveDetailsForm6;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;

class LeaveController extends Controller
{
    /**
     * Show the application form
     */
    public function showApplyForm()
    {
        $user = Auth::user();
        $leaveTypes = LeaveType::where('is_active', true)->get();
        
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
        
        return view('user.leave.apply', compact('user', 'leaveTypes', 'recommendingOfficers', 'finalApprovers'));
    }

    /**
     * Submit leave application
     */
    public function submitApplication(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'selected_dates' => 'required|string',
            'days_applied' => 'required|numeric|min:0.5',
        ]);

        $user = Auth::user();

        // Validate that the user has configured their approvers
        if (!$user->recommending_officer_id || !$user->approving_officer_id) {
            return back()->with('error', 'Please configure your Recommending and Approving Officers in your Profile before applying.')->withInput();
        }

        try {
            DB::beginTransaction();

            $leaveType = LeaveType::find($request->leave_type_id);

            // Process dates
            $dates = array_filter(explode(',', $request->selected_dates));
            sort($dates);
            $startDate = $dates[0] ?? now();
            $endDate = end($dates) ?: $startDate;

            // 1. Create Application
            $application = LeaveApplication::create([
                'user_id' => $user->id,
                'leave_type_id' => $leaveType->id,
                'date_filing' => now(),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'dates' => $dates,
                'days_applied' => $request->days_applied,
                'commutation' => $request->has('commutation') ? 'Requested' : 'Not Requested',
                'status' => 'Pending HR', // Initial Status
                'recommending_officer_id' => $user->recommending_officer_id,
                'approving_officer_id' => $user->approving_officer_id,
            ]);

            // 2. Create Details (Form 6 specific)
            LeaveDetailsForm6::create([
                'leave_application_id' => $application->id,
                'leave_type_name' => $leaveType->type_name,
                
                'vacation_loc_type' => $request->input('vacation_loc_type'),
                'vacation_loc_details' => $request->input('vacation_loc_details'),
                
                'sick_loc_type' => $request->input('sick_loc_type'),
                'sick_illness' => $request->input('sick_illness'),
                
                'women_illness' => $request->input('women_illness'),
                
                'study_type' => $request->input('study_type'),
                'study_details' => $request->input('study_details'),
                
                'other_purpose' => $request->input('other_purpose'),
            ]);
            
            // Notify HR (using a simple notification for now, can be expanded)
            // \App\Models\Notification::sendToRole('hr', ...); // Placeholder

            DB::commit();

            return redirect()->route('user.leave.history')->with('success', 'Leave application submitted successfully! Pending HR Verification.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error submitting application: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show user's applications
     */
    public function myApplications()
    {
        $user = Auth::user();
        
        $applications = LeaveApplication::with('leaveType')
            ->where('user_id', $user->id)
            ->orderBy('date_filing', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $stats = [
            'total' => $applications->count(),
            'approved' => $applications->where('status', 'Approved')->count(),
            'pending' => $applications->where('status', 'Pending')->count(),
            'disapproved' => $applications->where('status', 'Disapproved')->count(),
        ];

        return view('user.leave.history', compact('applications', 'stats'));
    }

    /**
     * Show single application details
     */
    public function show($id)
    {
        // Fetch application with related data
        $application = LeaveApplication::with(['leaveType', 'details', 'recommendingOfficer', 'approvingOfficer'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('user.leave.show', compact('application'));
    }

    /**
     * Generate Word Form 6
     */
    public function generateForm6($id)
    {
        $application = LeaveApplication::with(['leaveType', 'details', 'user', 'recommendingOfficer', 'approvingOfficer'])->findOrFail($id);
        
        // Check authorization
        if ($application->user_id !== Auth::id() && Auth::user()->role === 'user') {
            abort(403, 'Unauthorized action.');
        }

        $data = $application;
        $details = $application->details;
        $user = $application->user;

        try {
            $templatePath = public_path('assets/WORDFORM-6.docx');
            if (!file_exists($templatePath)) {
                return back()->with('error', 'Word template not found.');
            }

            $templateProcessor = new TemplateProcessor($templatePath);

            // --- HELPER: EXPAND POSITIONS ---
            $expandPos = function ($pos) {
                if (empty($pos)) return '';
                $pos = strtoupper(trim($pos));
                
                $map = [
                    'SGOD CHIEF' => 'CHIEF OF SCHOOL GOVERNANCE OPERATION DIVISION',
                    'CID CHIEF' => 'CHIEF OF CURRICULUM IMPLEMENTATION DIVISION',
                    'AO' => 'ADMINISTRATIVE OFFICER V',
                    'ASDS' => 'ASST. SCHOOLS DIVISION SUPERINTENDENT OFFICER-IN-CHARGE',
                    'SDS' => 'SCHOOLS DIVISION SUPERINTENDENT',
                ];

                return $map[$pos] ?? $pos;
            };

            // --- HELPER: SET VALUES ---
            $setVal = function ($key, $value) use ($templateProcessor) {
                $templateProcessor->setValue($key, $value);
            };

            // --- HELPER: SET IMAGE ---
            $setImage = function($placeholder, $user) use ($templateProcessor) {
                if ($user && $user->esignature && file_exists(public_path($user->esignature))) {
                    try {
                        $templateProcessor->setImageValue($placeholder, [
                            'path' => public_path($user->esignature), 
                            'width' => 100, 
                            'height' => 50, 
                            'ratio' => false
                        ]);
                    } catch (\Exception $e) {
                         // Fallback if image replacement fails
                    }
                } else {
                    $templateProcessor->setValue($placeholder, '');
                }
            };

            // --- 1. PERSONAL INFO ---
            // Use separate fields if available, otherwise fallback to existing full_name split logic (backward compat)
            if (!empty($user->last_name) && !empty($user->first_name)) {
                $lastName = $user->last_name;
                $firstName = $user->first_name;
                $middleName = $user->middle_name ?? '';
                
                // Construct standard format
                $fullNameFormatted = "$lastName, $firstName" . ($middleName ? " " . substr($middleName, 0, 1) . "." : "");
                
                $setVal('NAME', strtoupper($fullNameFormatted));
                $setVal('name', strtoupper($fullNameFormatted));
                
                $setVal('LASTNAME', strtoupper($lastName));
                $setVal('lastname', strtoupper($lastName));

                $setVal('FIRSTNAME', strtoupper($firstName));
                $setVal('firstname', strtoupper($firstName));

                $setVal('MIDNAME', strtoupper($middleName));
                $setVal('midname', strtoupper($middleName));
                
                $setVal('FULLNAME', strtoupper($user->full_name)); 
                // Removed explicit clearing of SIGNAME here to allow setImage to find the placeholder
                $setImage('SIGNAME', $user);
                $setImage('SIG_NAME', $user);

            } else {
                $fullName = strtoupper($user->full_name); 
                
                $parts = explode(' ', $user->full_name);
                $lastName = array_pop($parts);
                $firstName = implode(' ', $parts);
                
                $setVal('NAME', strtoupper("$lastName, $firstName"));
                $setVal('name', strtoupper("$lastName, $firstName"));

                $setVal('LASTNAME', strtoupper($lastName));
                $setVal('lastname', strtoupper($lastName));

                $setVal('FIRSTNAME', strtoupper($firstName));
                $setVal('firstname', strtoupper($firstName));
                
                // Cannot reliably get middle name from exploded string
                $setVal('MIDNAME', '');
                $setVal('midname', '');

                $setVal('FULLNAME', $fullName);
                // Removed explicit clearing of SIGNAME here
                $setImage('SIGNAME', $user);
                $setImage('SIG_NAME', $user);
            }

            $setVal('POSITION', $expandPos($user->position));
            $setVal('position', $expandPos($user->position));

            $setVal('SALARY', $user->salary ?? 'N/A');
            $setVal('salary', $user->salary ?? 'N/A');

            // --- APPROVERS ---
            // Use the actual officers saved in the application
            $recommenderRequest = $application->recommendingOfficer;
            $approverRequest = $application->approvingOfficer;

            // Recommending Officer
            $recName = $recommenderRequest ? $recommenderRequest->full_name : '';
            // If the user has a position defined, use it. Otherwise, use their role expanded.
            $recPos  = $recommenderRequest ? ($recommenderRequest->position ?: $expandPos($recommenderRequest->role)) : '';

            $setVal('RECOMMENDING_NAME', strtoupper($recName));
            $setVal('RECOMMENDING_POSITION', strtoupper($recPos));

            // Final Approver
            $finalName = $approverRequest ? $approverRequest->full_name : '';
            $finalPos  = $approverRequest ? ($approverRequest->position ?: $expandPos($approverRequest->role)) : '';

            $setVal('FINAL_NAME', strtoupper($finalName));
            $setVal('FINAL_POSITION', strtoupper($finalPos));
            
            // --- VERIFIER OF LEAVE CREDITS ---
            // Assuming the current user (HR) generating this form might be the verifier, 
            // OR we should look up the HR who actually verified it in `hr_verified_by` (if we tracked it).
            // For now, let's assume the 'Verifier' is a static Role -> HR.
            $verifier = \App\Models\Signatory::where('position', 'Verifier of Leave Credits')->first();
            $setVal('VOLC_NAME', strtoupper($verifier->name ?? ''));
            $setVal('VOLC_POS', strtoupper($verifier->title ?? ''));

            // --- E-SIGNATURES LOGIC ---
            // (Helper $setImage defined above)


            // 1. HR Verifier Signature (${HRSIGN})
            // Logic: If HR has verified logic (time is set), we try to get the verifier.
            if ($application->hr_verified_at) {
                $hrVerifier = $application->hrVerifier; 
                // If tracked in DB, use it. usage of 'Signatory' model as fallback optional if desired.
                $setImage('HRSIGN', $hrVerifier);
            } else {
                $templateProcessor->setValue('HRSIGN', '');
            }

            // 2. Recommending Officer Signature (${RECOSIGN})
            // Logic: If status is beyond 'Pending Recommending' (meaning Recommended or Approved or Disapproved at high level),
            // BUT usually recommending sig appears if recommended.
            if ($application->recommended_at || ($application->status != 'Pending HR' && $application->status != 'Pending Recommending' && $application->status != 'Disapproved')) {
                 $setImage('RECOSIGN', $recommenderRequest);
            } else {
                $templateProcessor->setValue('RECOSIGN', '');
            }

            // 3. Approving Officer Signature (${APPROVESIGN})
            // Logic: If status is 'Approved'.
            if ($application->status == 'Approved') {
                $setImage('APPROVESIGN', $approverRequest);
            } else {
                $templateProcessor->setValue('APPROVESIGN', '');
            }

            // Clean up old placeholders if they exist in template to be safe
            $oldPlaceholders = ['CIDSIGN', 'SGODSIGN', 'AOSIGN', 'ASDS', 'SDS'];
            foreach ($oldPlaceholders as $ph) {
                try { $templateProcessor->setValue($ph, ''); } catch(\Exception $e){}
            }
            
            // Save file logic below...
                $templateProcessor->setValue('SDS', '');
                // We don't clear ASDS here if it was set by Recommender logic above.

            // 3. HR Signature (${HRSIGN})
            // Logic: If status is not Pending HR, it implies it has been verified.
            // We prioritize the actual verifier stored in the DB, then fall back to the authenticated HR.
            $hrSignUser = null;

            if ($application->status != 'Pending HR') {
                if ($application->hrVerifier) {
                    // Priority 1: The specific HR who verified this record
                    $hrSignUser = $application->hrVerifier;
                } else {
                    /** @var \App\Models\User|null $currentUser */
                    $currentUser = \Illuminate\Support\Facades\Auth::user();
                    
                    if ($currentUser && ($currentUser->isHR() || $currentUser->isHeadHR())) {
                         // Priority 2: The current HR user (fallback for legacy records without verifier_id)
                        $hrSignUser = $currentUser;
                    }
                }
            }

            if ($hrSignUser) {
                $setImage('HRSIGN', $hrSignUser);
            } else {
                $templateProcessor->setValue('HRSIGN', '');
            }


            $setVal('OFFICE', $user->office_station);
            $setVal('office', $user->office_station);

            $setVal('DATE_FILING', $application->date_filing ? \Carbon\Carbon::parse($application->date_filing)->format('m/d/Y') : '');
            $setVal('date_filing', $application->date_filing ? \Carbon\Carbon::parse($application->date_filing)->format('m/d/Y') : '');

            // --- DATES & DAYS ---
            $inclusiveDates = '';
            // Check if 'dates' column is populated and is an array (due to cast)
            if (!empty($application->dates) && is_array($application->dates) && count($application->dates) > 0) {
                // Determine if we should attempt to group ranges or just list them.
                // For simplicity, let's list them properly formatted. 
                // Advanced: Group consecutive dates.
                $sortedDates = $application->dates;
                sort($sortedDates);
                
                $formatted = [];
                foreach ($sortedDates as $d) {
                    $formatted[] = \Carbon\Carbon::parse($d)->format('m/d/Y');
                }
                
                // If many dates, checking for range might be better, but user asked for "multiple dates not range".
                // However, "inclusive dates" field usually expects a summary.
                // If logic detects consecutive block, we could simplify. Use comma for now.
                $inclusiveDates = implode(', ', $formatted);
            } else {
                // Fallback for legacy or range-based
                $inclusiveDates = $application->start_date ? \Carbon\Carbon::parse($application->start_date)->format('m/d/Y') : '';
                if ($application->start_date && $application->end_date && \Carbon\Carbon::parse($application->start_date)->ne(\Carbon\Carbon::parse($application->end_date))) {
                    $inclusiveDates .= ' - ' . \Carbon\Carbon::parse($application->end_date)->format('m/d/Y');
                }
            }

            $setVal('date', $inclusiveDates);
            $setVal('DATE', $inclusiveDates);
            $setVal('inclusive_dates', $inclusiveDates);
            $setVal('INCLUSIVE_DATES', $inclusiveDates);

            $setVal('day', $application->days_applied);
            $setVal('DAY', $application->days_applied);
            $setVal('days_applied', $application->days_applied);
            $setVal('DAYS_APPLIED', $application->days_applied);

            // --- 2. 6.A TYPE OF LEAVE ---
            $typeName = $application->leaveType->type_name;
            $setVal('type_vacation', (stripos($typeName, 'Vacation') !== false) ? '☑' : '☐');
            $setVal('type_mandatory', (stripos($typeName, 'Mandatory') !== false || stripos($typeName, 'Forced') !== false) ? '☑' : '☐');
            $setVal('type_sick', (stripos($typeName, 'Sick') !== false) ? '☑' : '☐');
            $setVal('type_maternity', (stripos($typeName, 'Maternity') !== false) ? '☑' : '☐');
            $setVal('type_paternity', (stripos($typeName, 'Paternity') !== false) ? '☑' : '☐');
            $setVal('type_special_privilege', (stripos($typeName, 'Privilege') !== false) ? '☑' : '☐');
            $setVal('type_solo_parent', (stripos($typeName, 'Solo Parent') !== false) ? '☑' : '☐');
            $setVal('type_study', (stripos($typeName, 'Study') !== false) ? '☑' : '☐');
            $setVal('type_vawc', (stripos($typeName, 'VAWC') !== false) ? '☑' : '☐');
            $setVal('type_rehab', (stripos($typeName, 'Rehabilitation') !== false) ? '☑' : '☐');
            $setVal('type_women', (stripos($typeName, 'Benefits for Women') !== false) ? '☑' : '☐');
            $setVal('type_calamity', (stripos($typeName, 'Calamity') !== false) ? '☑' : '☐');
            $setVal('type_adoption', (stripos($typeName, 'Adoption') !== false) ? '☑' : '☐');
            $setVal('type_others', ($typeName === 'Others') ? '☑' : '☐');

            // --- 3. 6.B DETAILS OF LEAVE ---
            if ($details) {
                // Vacation Loc
                $setVal('detail_vacation_phil', ($details->vacation_loc_type === 'Philippines') ? '☑' : '☐');
                $setVal('detail_vacation_abroad', ($details->vacation_loc_type === 'Abroad') ? '☑' : '☐');
                $setVal('vacation_specify', $details->vacation_loc_details ?? '');

                // Sick Loc
                $setVal('detail_sick_hospital', ($details->sick_loc_type === 'Hospital') ? '☑' : '☐');
                $setVal('sick_hospital_specify', ($details->sick_loc_type === 'Hospital') ? ($details->sick_illness ?? '') : '');
                $setVal('detail_sick_outpatient', ($details->sick_loc_type === 'Out Patient') ? '☑' : '☐');
                $setVal('sick_outpatient_specify', ($details->sick_loc_type === 'Out Patient') ? ($details->sick_illness ?? '') : '');

                // Women
                $setVal('women_specify', $details->women_illness ?? '');

                // Study
                $setVal('detail_study_masters', ($details->study_type === 'Masters') ? '☑' : '☐');
                $setVal('detail_study_bar', ($details->study_type === 'Bar') ? '☑' : '☐');
                $setVal('study_specify', $details->study_details ?? '');
                
                // Other Purpose
                $setVal('other_specify', $details->other_purpose ?? '');
            } else {
                // Clear fields if no details
                $setVal('detail_vacation_phil', '☐');
                $setVal('detail_vacation_abroad', '☐');
                $setVal('vacation_specify', '');
                $setVal('detail_sick_hospital', '☐');
                $setVal('sick_hospital_specify', '');
                $setVal('detail_sick_outpatient', '☐');
                $setVal('sick_outpatient_specify', '');
                $setVal('women_specify', '');
                $setVal('detail_study_masters', '☐');
                $setVal('detail_study_bar', '☐');
                $setVal('study_specify', '');
                $setVal('other_specify', '');
            }

             $setVal('type_monetization', (stripos($typeName, 'Monetization') !== false) ? '☑' : '☐');
             $setVal('type_terminal', (stripos($typeName, 'Terminal') !== false) ? '☑' : '☐');


            // --- 4. 6.C COMMUTATION ---
            $setVal('commutation_yes', ($application->commutation === 'Requested') ? '☑' : '☐');
            $setVal('commutation_no', ($application->commutation === 'Not Requested') ? '☑' : '☐');
            
            // --- 5. 6.D CREDIT COMPUTATION ---
            // Get current credits for VL and SL
            $vlType = LeaveType::where('type_name', 'Vacation Leave')->first();
            $slType = LeaveType::where('type_name', 'Sick Leave')->first();
            
            // Fetch current credit balance
            $vlCredit = 0;
            if ($vlType) {
                $checkVl = \App\Models\LeaveCredit::where('user_id', $user->id)->where('leave_type_id', $vlType->id)->first();
                $vlCredit = $checkVl ? $checkVl->credits : 0;
            }
            
            $slCredit = 0;
            if ($slType) {
                $checkSl = \App\Models\LeaveCredit::where('user_id', $user->id)->where('leave_type_id', $slType->id)->first();
                $slCredit = $checkSl ? $checkSl->credits : 0;
            }
            
            // Logic: Determine deduction based on application
            $appTypeName = $application->leaveType->type_name;
            $daysApplied = $application->days_applied;
            
            $lessVl = 0;
            $lessSl = 0;
            
            // Check if application is VL or SL to apply deduction logic
            // Note: Mandatory/Forced Leave usually deducts from VL too, but strict prompt logic:
            if (stripos($appTypeName, 'Vacation') !== false || stripos($appTypeName, 'Forced') !== false || stripos($appTypeName, 'Mandatory') !== false) {
                 $lessVl = $daysApplied;
            } elseif (stripos($appTypeName, 'Sick') !== false) {
                 $lessSl = $daysApplied;
            }
            
            // Calculate Balances
            $vlBalance = $vlCredit - $lessVl;
            $slBalance = $slCredit - $lessSl;
            
            // Format for display (remove decimal if zero)
            $fmt = function($val) { 
                return (float)$val + 0; 
            };
            
            $setVal('VL_CREDIT', $fmt($vlCredit));
            $setVal('LESSVL_CREDIT', $fmt($lessVl));
            $setVal('VL_BALANCE', $fmt($vlBalance));
            
            $setVal('SL_CREDIT', $fmt($slCredit));
            $setVal('LESSSL_CREDIT', $fmt($lessSl));
            $setVal('SL_BALANCE', $fmt($slBalance));
            
            // Also set total date as of usually current date
            $setVal('DATE_AS_OF', now()->format('F d, Y'));
            
            
            // --- F. DISAPPROVAL REASONS ---
            $recoDisapprove = '';
            $approDisapprove = '';
            
            if ($application->status === 'Disapproved' && $application->rejection_remarks) {
                // Determine who disapproved based on flow state
                // If recommended_at is NULL, it likely failed at Recommending stage (or HR verification, but let's assume Recommending for form purposes)
                if (!$application->recommended_at) {
                    $recoDisapprove = $application->rejection_remarks;
                } else {
                    // If recommended but not approved
                    $approDisapprove = $application->rejection_remarks;
                }
            }
            
            $setVal('reco_disapprove', strtoupper($recoDisapprove));
            $setVal('appro_disapprove', strtoupper($approDisapprove));

            // --- G. APPROVED FOR DETAILS ---
            // Formatted values for check/blank
            $daysWithPay = $application->days_with_pay;
            $daysWithoutPay = $application->days_without_pay;
            $othersRemarks = $application->others_remarks;

            // Helper to format as whole number if integer, or float if needed, but request said "just a whole number"
            // Interpreting as removing .00 and "days" string
            $fmtNum = function($val) {
                return $val ? (string)((float)$val + 0) : '';
            };

            $setVal('DAYWPAY', $fmtNum($daysWithPay));
            $setVal('DAYWOPAY', $fmtNum($daysWithoutPay));
            $setVal('OTHERS', $othersRemarks ? strtoupper($othersRemarks) : '');

            // --- PDF GENERATION LOGIC ---
            if (request('format') === 'pdf') {
                $tempDir = storage_path('app/temp');
                if (!file_exists($tempDir)) {
                    mkdir($tempDir, 0755, true);
                }

                $timestamp = time();
                $baseName = 'Leave_' . $application->id . '_' . $timestamp;
                $tempDocx = $tempDir . '/' . $baseName . '.docx';
                $outputPdf = $tempDir . '/' . $baseName . '.pdf';

                try {
                    // Save DOCX first
                    $templateProcessor->saveAs($tempDocx);

                    // Execute LibreOffice command
                    // Ensure 'soffice' is in your System PATH or defined in .env as LIBREOFFICE_PATH
                    // Example Windows Path: "C:\Program Files\LibreOffice\program\soffice.exe"
                    $sofficePath = env('LIBREOFFICE_PATH', 'soffice'); 
                    
                    // Command: soffice --headless --convert-to pdf --outdir <output_dir> <input_file>
                    $cmd = "\"{$sofficePath}\" --headless --convert-to pdf --outdir \"{$tempDir}\" \"{$tempDocx}\"";
                    
                    exec($cmd, $output, $returnVar);

                    if (file_exists($outputPdf)) {
                        $filename = 'Leave_Form6_' . $lastName . '_' . $application->id . '.pdf';
                        return response()->file($outputPdf, [
                            'Content-Type' => 'application/pdf',
                            'Content-Disposition' => 'inline; filename="' . $filename . '"'
                        ])->deleteFileAfterSend(true);
                    } else {
                        throw new \Exception("PDF generation failed. Output: " . implode("\n", $output));
                    }
                } finally {
                    // Cleanup DOCX (PDF is cleaned up by deleteFileAfterSend if successful, otherwise explicitly here?)
                    // Actually deleteFileAfterSend only works if response is returned. 
                    if (file_exists($tempDocx)) {
                        unlink($tempDocx);
                    }
                }
            }
            
            $filename = 'Leave_Form6_' . $lastName . '_' . $application->id . '.docx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $templateProcessor->saveAs('php://output');
            exit;

        } catch (\Exception $e) {
            return back()->with('error', 'Error generating Word file: ' . $e->getMessage());
        }
    }
}
