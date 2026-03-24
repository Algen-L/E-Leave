<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveApplication;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;

class PrintLeaveCardController extends Controller
{
    public function print()
    {
        $user = Auth::user();
        $applications = LeaveApplication::with('leaveType')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->orderBy('approved_at', 'asc')
            ->get();

        $templatePath = public_path('assets/LEAVECARD.docx');
        $templateProcessor = new TemplateProcessor($templatePath);
        
        $templateProcessor->setValue('LASTNAME', strtoupper($user->last_name ?? ''));
        $templateProcessor->setValue('FIRSTNAME', strtoupper($user->first_name ?? ''));
        $templateProcessor->setValue('MIDNAME', strtoupper($user->middle_name ?? ''));
        $templateProcessor->setValue('DISTRICT', strtoupper($user->office_station ?? ''));
        $templateProcessor->setValue('name', $user->full_name);
        $templateProcessor->setValue('status', $user->status ?? '');

        if ($applications->count() > 0) {
            $replacements = [];
            foreach ($applications as $app) {
                $typeName = strtolower($app->leaveType->type_name ?? '');
                $isVL = str_contains($typeName, 'vacation') || str_contains($typeName, 'mandatory') || str_contains($typeName, 'force');
                $isSL = str_contains($typeName, 'sick');

                $vauwp = $isVL ? ($app->days_with_pay ?? $app->days_applied) : '';
                $vauwop = $isVL && $app->days_without_pay ? $app->days_without_pay : '';
                $sauwp = $isSL ? ($app->days_with_pay ?? $app->days_applied) : '';
                $sauwop = $isSL && $app->days_without_pay ? $app->days_without_pay : '';

                $audit = \App\Models\LeaveCreditAuditLog::where('target_user_id', $user->id)
                    ->where('reason', 'like', '%Leave Approved: ' . $app->id . '%')
                    ->orderBy('created_at', 'desc')
                    ->first();

                $vbal = '';
                $sbal = '';
                if ($audit) {
                    $at = strtolower($audit->leave_type_name);
                    if (str_contains($at, 'vacation')) $vbal = number_format($audit->new_value, 3);
                    if (str_contains($at, 'sick')) $sbal = number_format($audit->new_value, 3);
                }

                $replacements[] = [
                    'PER' => \Carbon\Carbon::parse($app->start_date)->format('m/d/y') . '-' . \Carbon\Carbon::parse($app->end_date)->format('m/d/y'),
                    'PAR' => $app->leaveType->type_name ?? 'Leave',
                    'VED' => '',
                    'VAUWP' => $vauwp,
                    'VBAL' => $vbal,
                    'VAUWOP' => $vauwop,
                    'SED' => '',
                    'SAUWP' => $sauwp,
                    'SBAL' => $sbal,
                    'SAUWOP' => $sauwop,
                    'DA' => $app->approved_at ? \Carbon\Carbon::parse($app->approved_at)->format('m/d/Y') . ' / Approved' : '',
                ];
            }
            $templateProcessor->cloneRowAndSetValues('PER', $replacements);
        } else {
            $tags = ['PER', 'PAR', 'VED', 'VAUWP', 'VBAL', 'VAUWOP', 'SED', 'SAUWP', 'SBAL', 'SAUWOP', 'DA'];
            foreach ($tags as $t) $templateProcessor->setValue($t, '');
        }

        $fn = 'LeaveCard_' . str_replace(' ', '_', $user->last_name) . '.docx';
        $tp = storage_path('app/public/' . $fn);
        $templateProcessor->saveAs($tp);

        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) mkdir($tempDir, 0755, true);
        $outputPdf = $tempDir . '/' . str_replace('.docx', '.pdf', $fn);
        $sofficePath = config('services.libreoffice_path', 'soffice');
        $cmd = '"' . str_replace('"', '\\"', $sofficePath) . '" --headless --convert-to pdf --outdir "' . str_replace('\\', '/', $tempDir) . '" "' . str_replace('\\', '/', $tp) . '" 2>&1';
        exec($cmd);

        if (file_exists($tp)) unlink($tp);

        if (!file_exists($outputPdf)) {
            return response()->json(['error' => 'PDF conversion failed. Please check LibreOffice installation.'], 500);
        }

        return response()->file($outputPdf, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . str_replace('.docx', '.pdf', $fn) . '"'
        ]);
    }
}
