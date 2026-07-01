<?php

namespace App\Services;

use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\User;
use App\Models\Signatory;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DocumentService
{
    /**
     * Generate Word Form 6 and optionally convert to PDF
     */
    public function generateForm6(LeaveApplication $application, $format = 'docx')
    {
        $application->load(['leaveType', 'details', 'user', 'recommendingOfficer', 'approvingOfficer']);
        $details = $application->details;
        $user = $application->user;

        $templatePath = public_path('assets/WORDFORM-6.docx');
        if (!file_exists($templatePath)) {
            throw new \Exception('Word template not found.');
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // --- HELPER: EXPAND POSITIONS ---
        $expandPos = function ($pos) {
            if (empty($pos))
                return '';
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
        $setImage = function ($placeholder, $user, $raiseSignature = false) use ($templateProcessor) {
            if (!$user || !$user->esignature) {
                $templateProcessor->setValue($placeholder, '');
                return;
            }

            $esignaturePath = storage_path('app/public/' . preg_replace('#^storage/#', '', $user->esignature));
            if (file_exists($esignaturePath)) {
                try {
                    $templateProcessor->setImageValue($placeholder, $this->signatureImageStyle($esignaturePath, $raiseSignature));
                } catch (\Exception $e) {
                    $templateProcessor->setValue($placeholder, '');
                }
            } else {
                $templateProcessor->setValue($placeholder, '');
            }
        };

        // --- 1. PERSONAL INFO ---
        if (!empty($user->last_name) && !empty($user->first_name)) {
            $lastName = $user->last_name;
            $firstName = $user->first_name;
            $middleName = $user->middle_name ?? '';
            $fullNameFormatted = "$lastName, $firstName" . ($middleName ? ' ' . substr($middleName, 0, 1) . '.' : '');

            $setVal('NAME', strtoupper($fullNameFormatted));
            $setVal('name', strtoupper($fullNameFormatted));
            $setVal('LASTNAME', strtoupper($lastName));
            $setVal('lastname', strtoupper($lastName));
            $setVal('FIRSTNAME', strtoupper($firstName));
            $setVal('firstname', strtoupper($firstName));
            $setVal('MIDNAME', strtoupper($middleName));
            $setVal('midname', strtoupper($middleName));
            $setVal('FULLNAME', strtoupper($user->full_name));
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
            $setVal('MIDNAME', '');
            $setVal('midname', '');
            $setVal('FULLNAME', $fullName);
            $setImage('SIGNAME', $user);
            $setImage('SIG_NAME', $user);
        }

        $setVal('POSITION', $expandPos($user->position));
        $setVal('position', $expandPos($user->position));
        $setVal('SALARY', $user->salary ?? 'N/A');
        $setVal('salary', $user->salary ?? 'N/A');

        // --- APPROVERS ---
        $recommenderRequest = $application->recommendingOfficer;
        $approverRequest = $application->approvingOfficer;

        $recName = $recommenderRequest ? $recommenderRequest->full_name : '';
        $recPos = $recommenderRequest ? ($recommenderRequest->position ?: $expandPos($recommenderRequest->role)) : '';
        $setVal('RECOMMENDING_NAME', strtoupper($recName));
        $setVal('RECOMMENDING_POSITION', strtoupper($recPos));

        $finalName = $approverRequest ? $approverRequest->full_name : '';
        $finalPos = $approverRequest ? ($approverRequest->position ?: $expandPos($approverRequest->role)) : '';
        $setVal('FINAL_NAME', strtoupper($finalName));
        $setVal('FINAL_POSITION', strtoupper($finalPos));

        $verifier = Signatory::where('position', 'Verifier of Leave Credits')->first();
        $setVal('VOLC_NAME', strtoupper($verifier->name ?? ''));
        $setVal('VOLC_POS', strtoupper($verifier->title ?? ''));

        // --- E-SIGNATURES LOGIC ---
        if ($application->hr_verified_at) {
            $hrVerifier = $application->hrVerifier;
            $certifyingOfficer = $this->resolveHrCertificationSigner($hrVerifier);

            $setImage('HRSIGN', $certifyingOfficer, true);
            if ($certifyingOfficer) {
                $setVal('VOLC_NAME', strtoupper($certifyingOfficer->full_name));
                $setVal('VOLC_POS', strtoupper($certifyingOfficer->position ?: 'Administrative Officer'));
            }
        } else {
            $templateProcessor->setValue('HRSIGN', '');
        }

        if ($application->recommended_at || ($application->status != 'Pending HR' && $application->status != 'Pending Recommending' && $application->status != 'Disapproved')) {
            $setImage('RECOSIGN', $recommenderRequest, true);
        } else {
            $templateProcessor->setValue('RECOSIGN', '');
        }

        if ($application->status == 'Approved') {
            $setImage('APPROVESIGN', $approverRequest, true);
        } else {
            $templateProcessor->setValue('APPROVESIGN', '');
        }

        foreach (['CIDSIGN', 'SGODSIGN', 'AOSIGN', 'ASDS', 'SDS'] as $ph) {
            try {
                $templateProcessor->setValue($ph, '');
            } catch (\Exception $e) {
            }
        }
        $templateProcessor->setValue('SDS', '');

        $setVal('OFFICE', $user->office_station);
        $setVal('office', $user->office_station);
        $setVal('DATE_FILING', $application->date_filing ? Carbon::parse($application->date_filing)->format('m/d/Y') : '');
        $setVal('date_filing', $application->date_filing ? Carbon::parse($application->date_filing)->format('m/d/Y') : '');

        // --- DATES & DAYS ---
        $inclusiveDates = '';
        if (!empty($application->dates) && is_array($application->dates) && count($application->dates) > 0) {
            $sortedDates = $application->dates;
            sort($sortedDates);
            $formatted = array_map(fn($d) => Carbon::parse($d)->format('m/d/Y'), $sortedDates);
            $inclusiveDates = implode(', ', $formatted);
        } else {
            $inclusiveDates = $application->start_date ? Carbon::parse($application->start_date)->format('m/d/Y') : '';
            if ($application->start_date && $application->end_date && Carbon::parse($application->start_date)->ne(Carbon::parse($application->end_date))) {
                $inclusiveDates .= ' - ' . Carbon::parse($application->end_date)->format('m/d/Y');
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

        // --- 6.A TYPE OF LEAVE ---
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
        $setVal('type_wellness', (stripos($typeName, 'Wellness') !== false) ? '☑' : '☐');

        $isStandard = $this->isStandardLeave($typeName);
        $otherPurpose = $details->other_purpose ?? '';
        
        if (!empty($otherPurpose)) {
            $setVal('type_others', '☑ ' . $otherPurpose);
        } else {
            $setVal('type_others', !$isStandard ? '☑ ' . $typeName : '☐');
        }

        // --- 6.B DETAILS OF LEAVE ---
        if ($details) {
            $setVal('detail_vacation_phil', ($details->vacation_loc_type === 'Philippines') ? '☑' : '☐');
            $setVal('detail_vacation_abroad', ($details->vacation_loc_type === 'Abroad') ? '☑' : '☐');
            $setVal('vacation_specify', $details->vacation_loc_details ?? '');
            $setVal('detail_sick_hospital', ($details->sick_loc_type === 'Hospital') ? '☑' : '☐');
            $setVal('sick_hospital_specify', ($details->sick_loc_type === 'Hospital') ? ($details->sick_illness ?? '') : '');
            $setVal('detail_sick_outpatient', ($details->sick_loc_type === 'Out Patient') ? '☑' : '☐');
            $setVal('sick_outpatient_specify', ($details->sick_loc_type === 'Out Patient') ? ($details->sick_illness ?? '') : '');
            $setVal('women_specify', $details->women_illness ?? '');
            $setVal('detail_study_masters', ($details->study_type === 'Masters') ? '☑' : '☐');
            $setVal('detail_study_bar', ($details->study_type === 'Bar') ? '☑' : '☐');
            $setVal('study_specify', $details->study_details ?? '');

            $otherPurposeText = $details->other_purpose ?? '';
            if (!$isStandard && empty($otherPurposeText))
                $otherPurposeText = $typeName;
            $setVal('other_specify', $otherPurposeText);
        } else {
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
            $setVal('other_specify', !$isStandard ? $typeName : '');
        }

        $setVal('type_monetization', (stripos($typeName, 'Monetization') !== false) ? '☑' : '☐');
        $setVal('type_terminal', (stripos($typeName, 'Terminal') !== false) ? '☑' : '☐');
        $setVal('commutation_yes', ($application->commutation === 'Requested') ? '☑' : '☐');
        $setVal('commutation_no', ($application->commutation === 'Not Requested') ? '☑' : '☐');

        // --- 6.D CREDIT COMPUTATION ---
        $this->setCreditValues($templateProcessor, $user->id, $application);

        $setVal('DATE_AS_OF', now()->format('F d, Y'));

        // --- F. DISAPPROVAL REASONS ---
        $recoDisapprove = '';
        $approDisapprove = '';
        if ($application->status === 'Disapproved' && $application->rejection_remarks) {
            if (!$application->recommended_at)
                $recoDisapprove = $application->rejection_remarks;
            else
                $approDisapprove = $application->rejection_remarks;
        }
        $setVal('reco_disapprove', strtoupper($recoDisapprove));
        $setVal('appro_disapprove', strtoupper($approDisapprove));
        $setVal('forapproval', $application->recommended_at ? '☑' : '☐');
        $setVal('fordisapproval', $recoDisapprove !== '' ? '☑' : '☐');

        // --- G. APPROVED FOR DETAILS ---
        $fmtNum = fn($val) => $val ? (string) ((float) $val + 0) : '';
        $setVal('DAYWPAY', $fmtNum($application->days_with_pay));
        $setVal('DAYWOPAY', $fmtNum($application->days_without_pay));
        $setVal('OTHERS', '');

        $formatString = fn($prefix, $date) => $date ? sprintf("%s %s by:", $prefix, Carbon::parse($date)->format('F d, Y')) : '';
        $setVal('CertifyDate', $formatString("Digitally Verified in this", $application->hr_verified_at));
        $setVal('recoDate', $formatString("Digitally Recommended in this", $application->recommended_at));
        $setVal('ApproDate', $formatString("Digitally Approved in this", $application->approved_at));

        if ($format === 'pdf') {
            return $this->convertToPdf($templateProcessor, $application->id, $user->last_name);
        }

        return $templateProcessor;
    }

    private function isStandardLeave($typeName)
    {
        $standards = ['Vacation', 'Mandatory', 'Forced', 'Sick', 'Maternity', 'Paternity', 'Privilege', 'Solo Parent', 'Study', 'VAWC', 'Rehabilitation', 'Benefits for Women', 'Calamity', 'Adoption'];
        foreach ($standards as $s) {
            if (stripos($typeName, $s) !== false)
                return true;
        }
        return false;
    }

    private function setCreditValues($templateProcessor, $userId, $application)
    {
        $vlType = \App\Models\LeaveType::where('type_name', 'Vacation Leave')->first();
        $slType = \App\Models\LeaveType::where('type_name', 'Sick Leave')->first();

        // Current credits from DB
        $vlCredit = $vlType ? (\App\Models\LeaveCredit::where('user_id', $userId)->where('leave_type_id', $vlType->id)->first()->credits ?? 0) : 0;
        $slCredit = $slType ? (\App\Models\LeaveCredit::where('user_id', $userId)->where('leave_type_id', $slType->id)->first()->credits ?? 0) : 0;

        $lessVl = 0;
        $lessSl = 0;
        $appTypeName = $application->leaveType->type_name;
        $isCompensatory = optional($application->details)->other_purpose === 'COC COMPENSATORY OVERTIME CREDIT';

        // Use certified days if available, otherwise applied days
        $deductionAmount = $application->hr_verified_at 
            ? ($application->days_with_pay ?? 0) 
            : $application->days_applied;

        if (stripos($appTypeName, 'Vacation') !== false || stripos($appTypeName, 'Forced') !== false || stripos($appTypeName, 'Mandatory') !== false || $isCompensatory) {
            $lessVl = $deductionAmount;
        } elseif (stripos($appTypeName, 'Sick') !== false) {
            $lessSl = $deductionAmount;
        }

        // --- FIX: Double Deduction Prevention ---
        // If the application is already APPROVED, the DB credits already have the deduction.
        // We must virtually "add back" the credits to show the balance BEFORE this application.
        $isApproved = stripos($application->status, 'approved') !== false;
        
        if ($isApproved) {
            if ($lessVl > 0) $vlCredit += $lessVl;
            if ($lessSl > 0) $slCredit += $lessSl;
        }

        $fmt = fn($val) => (float) $val + 0;
        $templateProcessor->setValue('VL_CREDIT', $fmt($vlCredit));
        $templateProcessor->setValue('LESSVL_CREDIT', $fmt($lessVl));
        $templateProcessor->setValue('VL_BALANCE', $fmt($vlCredit - $lessVl));
        $templateProcessor->setValue('SL_CREDIT', $fmt($slCredit));
        $templateProcessor->setValue('LESSSL_CREDIT', $fmt($lessSl));
        $templateProcessor->setValue('SL_BALANCE', $fmt($slCredit - $lessSl));
    }

    private function convertToPdf($templateProcessor, $id, $lastName)
    {
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir))
            mkdir($tempDir, 0755, true);

        $timestamp = time();
        $baseName = 'Leave_' . $id . '_' . $timestamp;
        $tempDocx = $tempDir . '/' . $baseName . '.docx';
        $outputPdf = $tempDir . '/' . $baseName . '.pdf';

        $templateProcessor->saveAs($tempDocx);

        $sofficePath = config('services.libreoffice_path', 'soffice');
        $tempDirNorm = str_replace('\\', '/', $tempDir);
        $tempDocxNorm = str_replace('\\', '/', $tempDocx);

        $cmd = '"' . str_replace('"', '\\"', $sofficePath) . '" --headless --convert-to pdf --outdir "' . str_replace('"', '\\"', $tempDirNorm) . '" "' . str_replace('"', '\\"', $tempDocxNorm) . '" 2>&1';
        exec($cmd, $output, $returnVar);

        if (!file_exists($outputPdf)) {
            if (file_exists($tempDocx))
                unlink($tempDocx);
            throw new \Exception('PDF generation failed. Check LibreOffice path.');
        }

        if (file_exists($tempDocx))
            unlink($tempDocx);

        return [
            'path' => $outputPdf,
            'filename' => 'Leave_Form6_' . $lastName . '_' . $id . '.pdf'
        ];
    }

    /**
     * HR personnel may verify records, but Form 6 certification uses the HR Review Officer signature.
     */
    private function resolveHrCertificationSigner(?User $hrVerifier): ?User
    {
        if (!$hrVerifier) {
            return null;
        }

        if ($hrVerifier->role === 'hr_review_officer') {
            return $hrVerifier;
        }

        if (in_array($hrVerifier->role, ['hr', 'head_hr'], true)) {
            return User::where('role', 'hr_review_officer')
                ->where('is_active', true)
                ->whereNotNull('esignature')
                ->orderBy('last_name')
                ->first() ?: $hrVerifier;
        }

        return $hrVerifier;
    }

    /**
     * Fit an e-signature into the Form 6 signature area without changing its aspect ratio.
     */
    private function signatureImageStyle(string $path, bool $raiseSignature = false): array
    {
        $maxWidth = 135;
        $maxHeight = 65;
        $signaturePath = $this->preparedSignatureImagePath($path, $raiseSignature);
        $imageSize = @getimagesize($signaturePath);

        if (!$imageSize || empty($imageSize[0]) || empty($imageSize[1])) {
            return [
                'path' => $signaturePath,
                'width' => $maxWidth,
                'height' => $maxHeight,
                'ratio' => true,
            ];
        }

        [$sourceWidth, $sourceHeight] = $imageSize;
        $scale = min($maxWidth / $sourceWidth, $maxHeight / $sourceHeight);

        return [
            'path' => $signaturePath,
            'width' => max(1, (int) round($sourceWidth * $scale)),
            'height' => max(1, (int) round($sourceHeight * $scale)),
            'ratio' => false,
        ];
    }

    /**
     * Crop blank canvas around signatures. Inline Form 6 signature placeholders
     * balance transparent padding so the visible ink clears both the table and label text.
     */
    private function preparedSignatureImagePath(string $path, bool $raiseSignature): string
    {
        $imageSize = @getimagesize($path);
        if (!$imageSize || empty($imageSize[0]) || empty($imageSize[1])) {
            return $path;
        }

        [$sourceWidth, $sourceHeight, $imageType] = $imageSize;
        $cacheDir = storage_path('app/generated_signatures');
        if (!is_dir($cacheDir) && !mkdir($cacheDir, 0775, true) && !is_dir($cacheDir)) {
            return $path;
        }

        $cacheKey = md5($path . '|' . filemtime($path) . '|signature-placement-v2|' . ($raiseSignature ? 'raised' : 'normal'));
        $cachePath = $cacheDir . DIRECTORY_SEPARATOR . $cacheKey . '.png';
        if (file_exists($cachePath)) {
            return $cachePath;
        }

        $source = $this->createImageResource($path, $imageType);
        if (!$source) {
            return $path;
        }

        imagepalettetotruecolor($source);
        imagealphablending($source, true);
        imagesavealpha($source, true);

        $bounds = $this->detectSignatureInkBounds($source, $sourceWidth, $sourceHeight);
        if (!$bounds) {
            imagedestroy($source);
            return $path;
        }

        [$minX, $minY, $maxX, $maxY] = $bounds;
        $cropPadding = 6;
        $minX = max(0, $minX - $cropPadding);
        $minY = max(0, $minY - $cropPadding);
        $maxX = min($sourceWidth - 1, $maxX + $cropPadding);
        $maxY = min($sourceHeight - 1, $maxY + $cropPadding);

        $cropWidth = max(1, $maxX - $minX + 1);
        $cropHeight = max(1, $maxY - $minY + 1);
        $sidePadding = 8;
        $verticalPadding = $raiseSignature ? max(24, min(58, (int) round($cropHeight * 0.85))) : 8;
        $topPadding = $raiseSignature ? max(10, min(24, (int) round($verticalPadding * 0.45))) : 4;
        $bottomPadding = $verticalPadding - $topPadding;

        $canvasWidth = $cropWidth + ($sidePadding * 2);
        $canvasHeight = $cropHeight + $topPadding + $bottomPadding;
        $canvas = imagecreatetruecolor($canvasWidth, $canvasHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
        imagefilledrectangle($canvas, 0, 0, $canvasWidth, $canvasHeight, $transparent);

        imagecopy($canvas, $source, $sidePadding, $topPadding, $minX, $minY, $cropWidth, $cropHeight);
        $saved = imagepng($canvas, $cachePath);

        imagedestroy($canvas);
        imagedestroy($source);

        return $saved ? $cachePath : $path;
    }

    private function createImageResource(string $path, int $imageType)
    {
        return match ($imageType) {
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_GIF => @imagecreatefromgif($path),
            default => null,
        };
    }

    private function detectSignatureInkBounds($image, int $width, int $height): ?array
    {
        $minX = $width;
        $minY = $height;
        $maxX = -1;
        $maxY = -1;

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgba = imagecolorat($image, $x, $y);
                $alpha = ($rgba & 0x7F000000) >> 24;
                $red = ($rgba >> 16) & 0xFF;
                $green = ($rgba >> 8) & 0xFF;
                $blue = $rgba & 0xFF;

                if ($alpha < 120 && !($red > 245 && $green > 245 && $blue > 245)) {
                    $minX = min($minX, $x);
                    $minY = min($minY, $y);
                    $maxX = max($maxX, $x);
                    $maxY = max($maxY, $y);
                }
            }
        }

        if ($maxX < 0 || $maxY < 0) {
            return null;
        }

        return [$minX, $minY, $maxX, $maxY];
    }
}
