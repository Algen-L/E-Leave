<?php

namespace App\Http\Controllers;

use App\Models\LeaveApplication;
use App\Models\User;
use App\Models\LeaveCredit;
use App\Models\LeaveType;
use App\Models\LeaveCreditAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;

class HRReportController extends Controller
{
    /**
     * Generate Individual Leave Card report.
     */
    public function leaveCard(Request $request)
    {
        $userId = $request->input('userId');
        $startDate = $request->input('start');
        $endDate = $request->input('end');

        if (!$userId) return response()->json(['error' => 'User ID is required'], 400);

        $targetUser = User::with('office')->findOrFail($userId);
        $currentUser = Auth::user();

        // Security: Higher Roles can only print for users in their office category
        if ($currentUser->isHigherRole()) {
            $category = null;
            if ($currentUser->role === 'sgod_chief') {
                $category = 'SGOD';
            } elseif ($currentUser->role === 'cid_chief') {
                $category = 'CID';
            } elseif (in_array($currentUser->role, ['ao', 'sds', 'asds'])) {
                $category = 'OSDS';
            }

            if ($category && (!$targetUser->office || $targetUser->office->category !== $category)) {
                return response()->json(['error' => 'Unauthorized access to user data outside your office category.'], 403);
            }
        }
        
        $query = LeaveApplication::with('leaveType')
            ->where('user_id', $targetUser->id)
            ->where('status', 'Approved');

        if ($startDate) $query->whereDate('start_date', '>=', $startDate);
        if ($endDate) $query->whereDate('end_date', '<=', $endDate);

        $applications = $query->orderBy('approved_at', 'asc')->get();

        $templatePath = public_path('assets/LEAVECARD.docx');
        if (!file_exists($templatePath)) return response()->json(['error' => 'Template not found'], 404);

        $templateProcessor = new TemplateProcessor($templatePath);
        
        $templateProcessor->setValue('LASTNAME', strtoupper($targetUser->last_name ?? ''));
        $templateProcessor->setValue('FIRSTNAME', strtoupper($targetUser->first_name ?? ''));
        $templateProcessor->setValue('MIDNAME', strtoupper($targetUser->middle_name ?? ''));
        $templateProcessor->setValue('DISTRICT', strtoupper($targetUser->office_station ?? ($targetUser->office->name ?? '')));
        $templateProcessor->setValue('STATUS', strtoupper($targetUser->status ?? 'ACTIVE'));
        $templateProcessor->setValue('DOA', $targetUser->created_at->format('m/d/Y'));

        if ($applications->count() > 0) {
            $replacements = [];
            foreach ($applications as $app) {
                // Determine VL vs SL
                $typeName = strtolower($app->leaveType->type_name ?? '');
                $isVL = str_contains($typeName, 'vacation') || str_contains($typeName, 'mandatory') || str_contains($typeName, 'force');
                $isSL = str_contains($typeName, 'sick');

                $vauwp = $isVL ? ($app->days_with_pay ?? $app->days_applied) : '';
                $vauwop = $isVL && $app->days_without_pay ? $app->days_without_pay : '';
                $sauwp = $isSL ? ($app->days_with_pay ?? $app->days_applied) : '';
                $sauwop = $isSL && $app->days_without_pay ? $app->days_without_pay : '';

                // Find audit log for balance after this specific application
                $audit = LeaveCreditAuditLog::where('target_user_id', $targetUser->id)
                    ->where('reason', 'like', "%Leave Approved: {$app->id}%")
                    ->orderBy('created_at', 'desc')
                    ->first();

                $vbal = ''; $sbal = '';
                if ($audit) {
                    $at = strtolower($audit->leave_type_name);
                    if (str_contains($at, 'vacation')) $vbal = format_credit_3_decimal($audit->new_value);
                    if (str_contains($at, 'sick')) $sbal = format_credit_3_decimal($audit->new_value);
                }

                $replacements[] = [
                    'PER' => Carbon::parse($app->start_date)->format('m/d/y') . '-' . Carbon::parse($app->end_date)->format('m/d/y'),
                    'PAR' => $app->leaveType->type_name ?? 'Leave',
                    'VED' => '', // Earned VL (usually for monthly cards, but here we list applications)
                    'VAUWP' => $vauwp,
                    'VBAL' => $vbal,
                    'VAUWOP' => $vauwop,
                    'SED' => '', // Earned SL
                    'SAUWP' => $sauwp,
                    'SBAL' => $sbal,
                    'SAUWOP' => $sauwop,
                    'DA' => $app->approved_at ? Carbon::parse($app->approved_at)->format('m/d/Y') : '',
                ];
            }
            $templateProcessor->cloneRowAndSetValues('PER', $replacements);
        } else {
            $tags = ['PER', 'PAR', 'VED', 'VAUWP', 'VBAL', 'VAUWOP', 'SED', 'SAUWP', 'SBAL', 'SAUWOP', 'DA'];
            foreach ($tags as $t) $templateProcessor->setValue($t, '');
        }

        return $this->saveAndDownload($templateProcessor, "LeaveCard_{$targetUser->last_name}", false);
    }

    /**
     * Generate Individual Table report.
     */
    public function leaveIndividual(Request $request)
    {
        $userId = $request->input('userId');
        $startDate = $request->input('start');
        $endDate = $request->input('end');
        $status = $request->input('status');

        if (!$userId) return response()->json(['error' => 'User ID is required'], 400);

        $targetUser = User::with('office')->findOrFail($userId);
        $currentUser = Auth::user();

        // Security: Higher Roles can only print for users in their office category
        if ($currentUser->isHigherRole()) {
            $category = null;
            if ($currentUser->role === 'sgod_chief') {
                $category = 'SGOD';
            } elseif ($currentUser->role === 'cid_chief') {
                $category = 'CID';
            } elseif (in_array($currentUser->role, ['ao', 'sds', 'asds'])) {
                $category = 'OSDS';
            }

            if ($category && (!$targetUser->office || $targetUser->office->category !== $category)) {
                return response()->json(['error' => 'Unauthorized access to user data outside your office category.'], 403);
            }
        }
        
        $query = LeaveApplication::with(['leaveType', 'recommendingOfficer', 'approvingOfficer'])
            ->where('user_id', $targetUser->id);

        if ($startDate) $query->whereDate('start_date', '>=', $startDate);
        if ($endDate) $query->whereDate('end_date', '<=', $endDate);
        if ($status && $status !== 'ALL') $query->where('status', $status);

        $applications = $query->orderBy('created_at', 'asc')->get();

        $templatePath = public_path('assets/LeaveIndividualtable.docx');
        if (!file_exists($templatePath)) return response()->json(['error' => 'Template not found'], 404);

        $templateProcessor = new TemplateProcessor($templatePath);
        
        $templateProcessor->setValue('Name', $targetUser->full_name);
        $templateProcessor->setValue('Position', $targetUser->position ?? 'Personnel');
        $templateProcessor->setValue('DateRange', ($startDate ?? 'Start') . ' to ' . ($endDate ?? 'Present'));
        $templateProcessor->setValue('StartDate', $startDate ?? '---');
        $templateProcessor->setValue('EndDate', $endDate ?? '---');

        // Fetch current credits as totals
        $vlType = LeaveType::where('type_name', 'Vacation Leave')->first();
        $slType = LeaveType::where('type_name', 'Sick Leave')->first();
        
        $vlc = LeaveCredit::where('user_id', $targetUser->id)->where('leave_type_id', optional($vlType)->id)->first();
        $slc = LeaveCredit::where('user_id', $targetUser->id)->where('leave_type_id', optional($slType)->id)->first();

        $templateProcessor->setValue('TotalVL', format_credit_3_decimal($vlc->credits ?? 0));
        $templateProcessor->setValue('TotalSL', format_credit_3_decimal($slc->credits ?? 0));
        $templateProcessor->setValue('VLvearned', 'N/A'); // Usually fixed monthly
        $templateProcessor->setValue('SLearned', 'N/A');

        if ($applications->count() > 0) {
            $count = $applications->count();
            $templateProcessor->cloneRow('DOLINPER', $count);

            foreach ($applications as $index => $app) {
                $row = $index + 1;
                $audit = LeaveCreditAuditLog::where('target_user_id', $targetUser->id)
                    ->where('reason', 'like', "%Leave Approved: {$app->id}%")
                    ->orderBy('created_at', 'desc')
                    ->first();

                // Generate vertical dates from inclusive 'dates' array OR range fallback
                $dateTextRun = new \PhpOffice\PhpWord\Element\TextRun();
                $specificDates = $app->dates;
                
                if (is_array($specificDates) && count($specificDates) > 0) {
                    sort($specificDates);
                    foreach ($specificDates as $i => $d) {
                        if ($i > 0) $dateTextRun->addTextBreak();
                        // Use a smaller font size (9pt) to minimize vertical space
                        $dateTextRun->addText(Carbon::parse($d)->format('M d, Y'), ['size' => 9]);
                    }
                } else {
                    $current = Carbon::parse($app->start_date);
                    $end = Carbon::parse($app->end_date);
                    $first = true;
                    while ($current <= $end) {
                        if (!$first) $dateTextRun->addTextBreak();
                        $dateTextRun->addText($current->format('M d, Y'), ['size' => 9]);
                        $current->addDay();
                        $first = false;
                    }
                }

                $templateProcessor->setComplexValue("DOLINPER#$row", $dateTextRun);
                $templateProcessor->setValue("Leavetype#$row", $app->leaveType->type_name ?? 'N/A');
                $templateProcessor->setValue("BBFR#$row", $audit ? format_credit_3_decimal($audit->previous_value) : '---');
                $templateProcessor->setValue("DEDCT#$row", format_credit_3_decimal($app->days_with_pay ?? $app->days_applied));
                $templateProcessor->setValue("BAFTR#$row", $audit ? format_credit_3_decimal($audit->new_value) : '---');
                $templateProcessor->setValue("Action#$row", strtoupper($app->status));
            }
        } else {
            $tags = ['DOLINPER', 'Leavetype', 'BBFR', 'DEDCT', 'BAFTR', 'Action'];
            foreach ($tags as $t) $templateProcessor->setValue($t, '');
        }

        return $this->saveAndDownload($templateProcessor, "IndividualTable_{$targetUser->last_name}");
    }

    /**
     * Generate Collective Leave Summary report.
     */
    public function leaveSummary(Request $request)
    {
        $rangeType = $request->input('rangeType');
        $office = $request->input('office');
        $year = $request->input('year');
        $monthFrom = $request->input('monthFrom');
        $monthTo = $request->input('monthTo');

        $currentUser = Auth::user();

        // Security: Higher Roles can only print for their office category
        if ($currentUser->isHigherRole()) {
            if ($currentUser->role === 'sgod_chief') {
                $office = 'SGOD';
            } elseif ($currentUser->role === 'cid_chief') {
                $office = 'CID';
            } elseif (in_array($currentUser->role, ['ao', 'sds', 'asds'])) {
                $office = 'OSDS';
            }
        }

        $query = LeaveApplication::with(['user.office', 'leaveType', 'approvingOfficer'])
            ->where('status', 'Approved');

        if ($office && $office !== 'ALL') {
            $query->whereHas('user', function($q) use ($office) {
                $q->where('office_station', $office)
                  ->orWhereHas('office', fn($o) => $o->where('category', $office));
            });
        }

        if ($rangeType === 'yearly' && $year) {
            $query->whereYear('start_date', $year);
        } elseif ($rangeType === 'monthly' && $monthFrom && $monthTo) {
            $query->whereDate('start_date', '>=', $monthFrom . '-01')
                  ->whereDate('start_date', '<=', Carbon::parse($monthTo)->endOfMonth()->format('Y-m-d'));
        }

        $applications = $query->orderBy('approved_at', 'asc')->get();

        $templatePath = public_path('assets/LeaveSummarytable.docx');
        if (!file_exists($templatePath)) return response()->json(['error' => 'Template not found'], 404);

        $templateProcessor = new TemplateProcessor($templatePath);

        if ($applications->count() > 0) {
            $count = $applications->count();
            $templateProcessor->cloneRow('NAME', $count);

            foreach ($applications as $index => $app) {
                $row = $index + 1;
                // Generate vertical dates from inclusive 'dates' array OR range fallback
                $dateTextRun = new \PhpOffice\PhpWord\Element\TextRun();
                $specificDates = $app->dates;
                
                if (is_array($specificDates) && count($specificDates) > 0) {
                    sort($specificDates);
                    foreach ($specificDates as $i => $d) {
                        if ($i > 0) $dateTextRun->addTextBreak();
                        // Use a smaller font size (9pt) to minimize vertical space
                        $dateTextRun->addText(Carbon::parse($d)->format('M d, Y'), ['size' => 9]);
                    }
                } else {
                    $current = Carbon::parse($app->start_date);
                    $end = Carbon::parse($app->end_date);
                    $first = true;
                    while ($current <= $end) {
                        if (!$first) $dateTextRun->addTextBreak();
                        $dateTextRun->addText($current->format('M d, Y'), ['size' => 9]);
                        $current->addDay();
                        $first = false;
                    }
                }

                $mainOffice = $app->user->office->category ?? '';
                $specificOffice = $app->user->office_station ?: ($app->user->office->name ?? '');
                $officeDisplay = $mainOffice ? ($specificOffice ? "{$mainOffice} - {$specificOffice}" : $mainOffice) : ($specificOffice ?: 'N/A');

                $templateProcessor->setValue("NAME#$row", $app->user->full_name);
                $templateProcessor->setValue("MOFFICE#$row", $officeDisplay);
                $templateProcessor->setValue("LTYPE#$row", $app->leaveType->type_name);
                $templateProcessor->setComplexValue("DATEOFLV#$row", $dateTextRun);
                $templateProcessor->setValue("DATEOFRCRD#$row", $app->created_at->format('M d, Y'));
                $templateProcessor->setValue("DATEOFHR#$row", $app->hr_verified_at ? Carbon::parse($app->hr_verified_at)->format('M d, Y') : '---');
                $templateProcessor->setValue("RECN#$row", $app->recommendingOfficer->full_name ?? '---');
                $templateProcessor->setValue("DATEOFREC#$row", $app->recommended_at ? Carbon::parse($app->recommended_at)->format('M d, Y') : '---');
                $templateProcessor->setValue("APPN#$row", $app->approvingOfficer->full_name ?? '---');
                $templateProcessor->setValue("DATEOFAPP#$row", $app->approved_at ? Carbon::parse($app->approved_at)->format('M d, Y') : '---');
                $templateProcessor->setValue("Remarks#$row", $app->others_remarks ?? '---');
            }
        } else {
            $tags = ['NAME', 'MOFFICE', 'LTYPE', 'DATEOFLV', 'DATEOFRCRD', 'DATEOFHR', 'RECN', 'DATEOFREC', 'APPN', 'DATEOFAPP', 'Remarks'];
            foreach ($tags as $t) $templateProcessor->setValue($t, '');
        }

        return $this->saveAndDownload($templateProcessor, "LeaveSummary_" . ($year ?? 'Report'));
    }

    /**
     * Helper to save template and return response (Download or Preview).
     */
    private function saveAndDownload($templateProcessor, $filename, $forceDocx = false)
    {
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) mkdir($tempDir, 0755, true);

        $fileNameDocx = $filename . '_' . time() . '.docx';
        $tempDocx = $tempDir . '/' . $fileNameDocx;
        
        $templateProcessor->saveAs($tempDocx);

        if ($forceDocx) {
            return response()->download($tempDocx)->deleteFileAfterSend(true);
        }

        // Convert to PDF if LibreOffice is available
        $sofficePath = config('services.libreoffice_path', 'soffice');
        $outputPdf = $tempDir . '/' . str_replace('.docx', '.pdf', $fileNameDocx);
        
        $cmd = '"' . str_replace('"', '\\"', $sofficePath) . '" --headless --convert-to pdf --outdir "' . str_replace('\\', '/', $tempDir) . '" "' . str_replace('\\', '/', $tempDocx) . '" 2>&1';
        exec($cmd);

        if (file_exists($outputPdf)) {
            if (file_exists($tempDocx)) unlink($tempDocx);
            // 'inline' sends it to the browser for preview instead of direct download
            return response()->file($outputPdf, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($outputPdf) . '"'
            ])->deleteFileAfterSend(true);
        }

        // Fallback to DOCX download if PDF conversion fails
        return response()->download($tempDocx)->deleteFileAfterSend(true);
    }
}
