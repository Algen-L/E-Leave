<?php

namespace App\Http\Controllers;

use App\Models\LeaveApplication;
use App\Models\User;
use App\Models\Office;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use ZipArchive;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class BulkDownloadController extends Controller
{
    protected $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Download multiple applications as a ZIP of PDFs
     */
    public function downloadZip(Request $request)
    {
        $request->validate([
            'office' => 'nullable|string',
            'user_ids' => 'required|array',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $userIds = $request->input('user_ids');
        $office = $request->input('office');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Query Leave Applications
        $query = LeaveApplication::with(['user', 'leaveType'])
            ->whereIn('user_id', $userIds);

        if ($startDate) {
            $query->whereDate('start_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('end_date', '<=', $endDate);
        }
        if ($office && $office !== 'ALL') {
            $query->whereHas('user', function($q) use ($office) {
                $q->where('office_station', $office);
            });
        }

        /** @var \Illuminate\Database\Eloquent\Collection<\App\Models\LeaveApplication> $applications */
        $applications = $query->get();

        if ($applications->isEmpty()) {
            return back()->with('error', 'No leave applications found matching the selected filters.');
        }

        // --- ZIP GENERATION ---
        $zip = new ZipArchive();
        $zipFileName = 'Bulk_Leave_Applications_' . time() . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Ensure temp directory exists
        if (!File::exists(storage_path('app/temp'))) {
            File::makeDirectory(storage_path('app/temp'), 0755, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Could not create ZIP file.');
        }

        $generatedFiles = [];

        foreach ($applications as $app) {
            /** @var \App\Models\LeaveApplication $app */
            try {
                // Generate PDF using DocumentService
                $result = $this->documentService->generateForm6($app, 'pdf');
                $pdfPath = $result['path'];
                $pdfName = $result['filename'];

                if (File::exists($pdfPath)) {
                    $zip->addFile($pdfPath, $pdfName);
                    $generatedFiles[] = $pdfPath;
                }
            } catch (\Exception $e) {
                // Skip failed ones for now or log them
                continue;
            }
        }

        $zip->close();

        // Cleanup temporary individual PDF files
        // We can't delete them immediately with deleteFileAfterSend on the ZIP,
        // so we manually clean them up.
        foreach ($generatedFiles as $file) {
            if (File::exists($file)) {
                // We keep them until the response is sent? 
                // Actually ZipArchive::addFile adds the file to the zip when closed.
                // After closing, we can safely delete the source PDFs.
                File::delete($file);
            }
        }

        if (!File::exists($zipPath)) {
            return back()->with('error', 'Failed to generate ZIP archive.');
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
