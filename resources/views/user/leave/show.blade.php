@extends('layouts.sdo')

@section('title', 'Application Details')
@section('page-title', 'Application Details')

@push('styles')
    <style>
        /* PAGE LAYOUT GRID */
        .page-layout {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 24px;
            align-items: start;
            margin-top: 20px;
        }

        @media(max-width: 1024px) {
            .page-layout {
                grid-template-columns: 1fr;
            }
        }

        /* DOCUMENT CARD (LEFT) */
        .document-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            min-height: auto;
            padding: 24px;
            position: relative;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            /* Prevent overflow */
        }

        /* Paper effect */
        .paper-view {
            max-width: 100%;
            margin: 0 auto;
            background: #fff;
        }

        /* SIDEBAR CARDS (RIGHT) */
        .sidebar-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 24px;
            margin-bottom: 24px;
            border: 1px solid #e2e8f0;
        }

        .sidebar-header {
            font-weight: 700;
            font-size: 1rem;
            color: #1e293b;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* STEPPER (VERTICAL) */
        .v-stepper {
            position: relative;
            border-left: 2px solid #e2e8f0;
            margin-left: 10px;
            padding-left: 20px;
            padding-bottom: 10px;
        }

        .v-step {
            position: relative;
            margin-bottom: 24px;
        }

        .v-step:last-child {
            margin-bottom: 0;
        }

        .v-step-marker {
            position: absolute;
            left: -27px;
            /* Adjust based on border/padding */
            top: 0;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #cbd5e1;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px #cbd5e1;
        }

        .v-step.active .v-step-marker {
            background: #3b82f6;
            box-shadow: 0 0 0 1px #3b82f6;
        }

        .v-step.completed .v-step-marker {
            background: #22c55e;
            box-shadow: 0 0 0 1px #22c55e;
        }

        .v-step.rejected .v-step-marker {
            background: #ef4444;
            box-shadow: 0 0 0 1px #ef4444;
        }

        .v-step-content {}

        .v-step-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #334155;
        }

        .v-step-desc {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 2px;
        }

        /* STATUS BADGE */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 16px;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-pending {
            background: #fff7ed;
            color: #c2410c;
        }

        .status-approved {
            background: #f0fdf4;
            color: #15803d;
        }

        .status-rejected {
            background: #fef2f2;
            color: #b91c1c;
        }

        /* DOCUMENT STYLING */
        .doc-header {
            text-align: center;
            border-bottom: 2px solid #1e293b;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .doc-title {
            font-weight: 800;
            text-transform: uppercase;
            font-size: 1.25rem;
            color: #0f172a;
        }

        .doc-subtitle {
            font-size: 0.9rem;
            color: #64748b;
            margin-top: 4px;
        }

        .doc-section {
            margin-bottom: 30px;
        }

        .doc-section-title {
            background: #f1f5f9;
            padding: 8px 12px;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            color: #475569;
            margin-bottom: 12px;
            border-left: 4px solid #3b82f6;
        }

        .doc-row {
            display: flex;
            margin-bottom: 12px;
            border-bottom: 1px dotted #e2e8f0;
            padding-bottom: 4px;
        }

        .doc-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: #64748b;
            width: 180px;
            flex-shrink: 0;
        }

        .doc-value {
            font-weight: 500;
            font-size: 0.95rem;
            color: #1e293b;
            flex-grow: 1;
        }

        .doc-check-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .doc-check-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .doc-check-box {
            width: 16px;
            height: 16px;
            border: 1px solid #94a3b8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endpush

@section('content')

    <!-- --- HEADER --- -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('user.leave.history') }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">View Application</h1>
            </div>
            <p class="text-sm text-gray-500 ml-7">Application ID: #{{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>

        <div class="flex items-center gap-3">
            @php
                $badgeClass = 'status-pending';
                if (stripos($application->status, 'approve') !== false && stripos($application->status, 'pending') === false)
                    $badgeClass = 'status-approved';
                if (stripos($application->status, 'reject') !== false || stripos($application->status, 'disapprove') !== false)
                    $badgeClass = 'status-rejected';
            @endphp
            <span class="status-badge {{ $badgeClass }}">
                <i class="fas fa-circle text-[0.6rem] mr-2"></i> {{ $application->status }}
            </span>
        </div>
    </div>

    <div class="page-layout">

        <!-- --- LEFT: DOCUMENT VIEW --- -->
        <div class="document-card">
            <div class="paper-view">
                <!-- Header -->
                <div class="doc-header">
                    <div class="flex justify-between items-start mb-4">
                        <img src="{{ asset('assets/images/deped_logo.png') }}" class="h-16 w-auto" alt="Logo"
                            onerror="this.style.display='none'">
                        <div class="text-right text-xs text-gray-500">
                            <div class="font-bold">CS Form No. 6</div>
                            <div>Revised 2020</div>
                        </div>
                    </div>
                    <div class="doc-title">Application for Leave</div>
                    <div class="doc-subtitle">Department of Education - Schools Division Office</div>
                </div>

                <!-- 1. Office/Agency -->
                <div class="doc-section">
                    <div class="doc-row">
                        <span class="doc-label">Office/Department:</span>
                        <span class="doc-value">DepEd SDO - {{ $application->user->office->name ?? 'N/A' }}</span>
                    </div>
                    <div class="doc-row">
                        <span class="doc-label">Name:</span>
                        <span class="doc-value uppercase">{{ $application->user->last_name }},
                            {{ $application->user->first_name }}
                            {{ substr($application->user->middle_name ?? '', 0, 1) }}.</span>
                    </div>
                    <div class="doc-row">
                        <span class="doc-label">Date of Filing:</span>
                        <span class="doc-value">{{ $application->date_filing->format('F d, Y') }}</span>
                    </div>
                    <div class="doc-row">
                        <span class="doc-label">Position:</span>
                        <span class="doc-value">{{ $application->user->position }}</span>
                    </div>
                    <div class="doc-row">
                        <span class="doc-label">Salary:</span>
                        <span class="doc-value">{{ $application->user->salary ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- 2. Details of Application -->
                <div class="doc-section">
                    <div class="doc-section-title">6.A Type of Leave to be Availed Of</div>
                    <div class="pl-4">
                        <div class="text-lg font-bold text-gray-800 mb-2">
                            <i class="fas fa-check-square mr-2 text-blue-600"></i> {{ $application->leaveType->type_name }}
                        </div>
                        @if($application->details && $application->details->other_purpose)
                            <div class="text-sm text-gray-600 italic pl-6">
                                Specify: {{ $application->details->other_purpose }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="doc-section">
                    <div class="doc-section-title">6.B Details of Leave</div>
                    <div class="pl-4 text-sm">
                        @if($application->details)
                            @if($application->details->vacation_loc_type)
                                <div class="mb-2"><strong>Vacation Location:</strong> {{ $application->details->vacation_loc_type }}
                                    - {{ $application->details->vacation_loc_details }}</div>
                            @endif
                            @if($application->details->sick_loc_type)
                                <div class="mb-2"><strong>Sick Leave:</strong> {{ $application->details->sick_loc_type }} -
                                    {{ $application->details->sick_illness }}</div>
                            @endif
                            @if($application->details->women_illness)
                                <div class="mb-2"><strong>Special Leave Benefit:</strong> {{ $application->details->women_illness }}
                                </div>
                            @endif
                            @if($application->details->study_type)
                                <div class="mb-2"><strong>Study Leave:</strong> {{ $application->details->study_type }} -
                                    {{ $application->details->study_details }}</div>
                            @endif
                        @else
                            <span class="text-gray-400">No specific details provided.</span>
                        @endif
                    </div>
                </div>

                <div class="doc-section">
                    <div class="doc-section-title">6.C Number of Working Days Applied For</div>
                    <div class="pl-4">
                        <div class="doc-row">
                            <span class="doc-label">Days:</span>
                            <span class="doc-value">{{ $application->days_applied }} Day(s)</span>
                        </div>
                        <div class="doc-row">
                            <span class="doc-label">Inclusive Dates:</span>
                            <span class="doc-value">
                                {{ $application->start_date->format('M d, Y') }} -
                                {{ $application->end_date->format('M d, Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="doc-section">
                    <div class="doc-section-title">6.D Commutation</div>
                    <div class="pl-4">
                        <span
                            class="font-medium  {{ $application->commutation == 'Requested' ? 'text-blue-700' : 'text-gray-600' }}">
                            <i
                                class="fas {{ $application->commutation == 'Requested' ? 'fa-check-circle' : 'fa-circle' }} mr-2"></i>
                            Requested
                        </span>
                        <span
                            class="font-medium ml-6 {{ $application->commutation == 'Not Requested' ? 'text-blue-700' : 'text-gray-600' }}">
                            <i
                                class="fas {{ $application->commutation == 'Not Requested' ? 'fa-check-circle' : 'fa-circle' }} mr-2"></i>
                            Not Requested
                        </span>
                    </div>
                </div>

                <!-- Signatory Section (7.A, 7.B, 7.C) -->
                <div class="mt-12 space-y-8">
                    <!-- Row: HR & Recommendation -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- 7.A HR Certification -->
                        <div class="border border-gray-100 p-4 rounded-lg bg-gray-50/30">
                            <div
                                class="text-[0.65rem] font-bold uppercase text-gray-400 mb-6 border-b border-gray-100 pb-1">
                                7.A Certification of Leave Credits</div>

                            <div class="text-center">
                                @if($application->hr_verified_at)
                                    <div class="text-[0.7rem] text-blue-600 font-bold mb-1 italic">
                                        Digitally Certified on {{ $application->hr_verified_at->format('F d, Y') }}
                                    </div>
                                    @if($application->hrVerifier && $application->hrVerifier->esignature)
                                        <div class="relative h-12 flex items-center justify-center">
                                            <img src="{{ storage_url($application->hrVerifier->esignature) }}" alt="HR Sig"
                                                class="h-full object-contain scale-125">
                                        </div>
                                    @endif
                                    <div class="font-bold underline uppercase text-xs mt-1">
                                        {{ $application->hrVerifier->full_name ?? 'Verifying Officer' }}</div>
                                    <div class="text-[0.65rem] text-gray-500 uppercase">
                                        {{ $application->hrVerifier->position ?? 'Administrative Officer' }}</div>
                                @else
                                    <div class="h-20 flex items-center justify-center text-gray-300 text-[0.65rem] italic">
                                        (Pending HR Verification)
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- 7.B Recommendation -->
                        <div class="border border-gray-100 p-4 rounded-lg bg-gray-50/30">
                            <div
                                class="text-[0.65rem] font-bold uppercase text-gray-400 mb-6 border-b border-gray-100 pb-1">
                                7.B Recommendation</div>

                            <div class="text-center">
                                @if($application->recommended_at)
                                    <div class="text-[0.7rem] text-blue-600 font-bold mb-1 italic">
                                        Digitally Recommended on {{ $application->recommended_at->format('F d, Y') }}
                                    </div>
                                    @if($application->recommendingOfficer && $application->recommendingOfficer->esignature)
                                        <div class="relative h-12 flex items-center justify-center">
                                            <img src="{{ storage_url($application->recommendingOfficer->esignature) }}"
                                                alt="Reco Sig" class="h-full object-contain scale-125">
                                        </div>
                                    @endif
                                    <div class="font-bold underline uppercase text-xs mt-1">
                                        {{ $application->recommendingOfficer->full_name }}</div>
                                    <div class="text-[0.65rem] text-gray-500 uppercase">
                                        {{ $application->recommendingOfficer->position }}</div>
                                @else
                                    <div class="h-20 flex items-center justify-center text-gray-300 text-[0.65rem] italic">
                                        (Pending Recommendation)
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 7.C/D Final Approval -->
                    <div class="border border-gray-100 p-4 rounded-lg bg-gray-50/30">
                        <div class="text-[0.65rem] font-bold uppercase text-gray-400 mb-6 border-b border-gray-100 pb-1">7.C
                            / 7.D Final Approval</div>
                        <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                            <div class="flex-1 w-full text-center md:text-left">
                                @if($application->approved_at)
                                    <div class="inline-block p-3 border-2 border-green-200 bg-green-50 rounded-lg">
                                        <div class="text-green-800 font-bold text-sm mb-1 uppercase">Approved For:</div>
                                        <div class="text-xs text-green-700 space-y-1">
                                            <div><i class="fas fa-check-circle mr-1"></i> {{ $application->days_with_pay ?? 0 }}
                                                Day(s) with pay</div>
                                            <div><i class="fas fa-check-circle mr-1"></i>
                                                {{ $application->days_without_pay ?? 0 }} Day(s) without pay</div>
                                            @if($application->others_remarks)
                                                <div class="mt-1 font-medium italic border-t border-green-100 pt-1">Others:
                                                    {{ $application->others_remarks }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="text-gray-400 text-xs italic">Awaiting final approval details...</div>
                                @endif
                            </div>

                            <div class="flex-1 w-full text-center">
                                @if($application->approved_at)
                                    <div class="text-[0.7rem] text-blue-600 font-bold mb-1 italic">
                                        Digitally Approved on {{ $application->approved_at->format('F d, Y') }}
                                    </div>
                                    @if($application->approvingOfficer && $application->approvingOfficer->esignature)
                                        <div class="relative h-12 flex items-center justify-center">
                                            <img src="{{ storage_url($application->approvingOfficer->esignature) }}" alt="App Sig"
                                                class="h-full object-contain scale-125">
                                        </div>
                                    @endif
                                    <div class="font-bold underline uppercase text-xs mt-1">
                                        {{ $application->approvingOfficer->full_name }}</div>
                                    <div class="text-[0.65rem] text-gray-500 uppercase">
                                        {{ $application->approvingOfficer->position }}</div>
                                @else
                                    <div class="h-20 flex items-center justify-center text-gray-300 text-[0.65rem] italic">
                                        (Pending Final Approval)
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- --- RIGHT: SIDEBAR --- -->
        <div class="flex flex-col">

            <!-- Actions Card -->
            <div class="sidebar-card">
                <div class="sidebar-header"><i class="fas fa-bolt"></i> Actions</div>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('user.leave.form6', ['id' => $application->id, 'format' => 'pdf']) }}" target="_blank"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center transition flex justify-center items-center">
                        <i class="fas fa-download mr-2"></i> Download PDF
                    </a>
                    <p class="text-xs text-center text-gray-500 mt-2">
                        Use the button above to download the official CS Form No. 6.
                    </p>
                </div>
            </div>

            <!-- Progress Tracker -->
            <div class="sidebar-card">
                <div class="sidebar-header"><i class="fas fa-tasks"></i> Progress Tracker</div>

                @php
                    // Logic Reuse
                    $currentStatus = strtolower($application->status);
                    $isRejected = str_contains($currentStatus, 'reject') || str_contains($currentStatus, 'disapprove');

                    $s1 = $application->hr_verified_at ? 'completed' : 'active';
                    $recoComplete = $application->recommended_at;

                    // Logic refinement for recommendation step
                    $s2 = $recoComplete ? 'completed' : ($application->hr_verified_at ? 'active' : '');

                    $s3 = $application->approved_at ? 'completed' : ($recoComplete ? 'active' : '');

                    if ($isRejected) {
                        if (!$application->hr_verified_at)
                            $s1 = 'rejected';
                        else if (!$recoComplete)
                            $s2 = 'rejected';
                        else
                            $s3 = 'rejected';
                    }
                @endphp

                <div class="v-stepper">
                    <!-- Step 1: Pending (Filed) -->
                    <div class="v-step completed">
                        <div class="v-step-marker"></div>
                        <div class="v-step-content">
                            <div class="v-step-title">Application Filed</div>
                            <div class="v-step-desc">
                                {{ $application->created_at->format('M d, Y h:i A') }}
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: HR Verify -->
                    <div class="v-step {{ $s1 }}">
                        <div class="v-step-marker"></div>
                        <div class="v-step-content">
                            <div class="v-step-title">HR Verification</div>
                            <div class="v-step-desc">
                                @if($application->hr_verified_at)
                                    Verified on {{ $application->hr_verified_at->format('M d, Y') }}
                                @else
                                    {{ $s1 == 'active' ? 'Ongoing review...' : 'Pending' }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Recommendation -->
                    <div class="v-step {{ $s2 }}">
                        <div class="v-step-marker"></div>
                        <div class="v-step-content">
                            <div class="v-step-title">Recommendation</div>
                            <div class="v-step-desc">
                                @if($recoComplete)
                                    Date Recom:
                                    {{ $application->recommended_at ? $application->recommended_at->format('M d, Y') : 'N/A' }}
                                @else
                                    {{ $s2 == 'active' ? 'Waiting for recommendation...' : 'Pending' }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Approval -->
                    <div class="v-step {{ $s3 }}">
                        <div class="v-step-marker"></div>
                        <div class="v-step-content">
                            <div class="v-step-title">Final Approval</div>
                            <div class="v-step-desc">
                                @if($application->approved_at)
                                    Approved on {{ $application->approved_at->format('M d, Y') }}
                                @elseif($isRejected && $s3 == 'rejected')
                                    Disapproved
                                @else
                                    {{ $s3 == 'active' ? 'Waiting for final approval...' : 'Pending' }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logs Card -->
            <div class="sidebar-card">
                <div class="sidebar-header"><i class="fas fa-history"></i> Activity Log</div>
                <div class="text-sm text-gray-500">
                    <!-- Simple logs derived from dates -->
                    <div class="mb-3 pl-3 border-l-2 border-gray-200">
                        <div class="font-bold text-gray-700">Application Submitted</div>
                        <div class="text-xs">{{ $application->created_at->format('M d, Y h:i A') }}</div>
                    </div>

                    @if($application->hr_verified_at)
                        <div class="mb-3 pl-3 border-l-2 border-blue-200">
                            <div class="font-bold text-gray-700">Verified by HR</div>
                            <div class="text-xs">{{ $application->hr_verified_at->format('M d, Y h:i A') }}</div>
                        </div>
                    @endif

                    @if($application->recommended_at)
                        <div class="mb-3 pl-3 border-l-2 border-green-200">
                            <div class="font-bold text-gray-700">Recommended</div>
                            <div class="text-xs">{{ $application->recommended_at->format('M d, Y h:i A') }}</div>
                        </div>
                    @endif

                    @if($application->approved_at)
                        <div class="mb-3 pl-3 border-l-2 border-green-500">
                            <div class="font-bold text-green-700">Approved</div>
                            <div class="text-xs">{{ $application->approved_at->format('M d, Y h:i A') }}</div>
                        </div>
                    @endif

                    @if($application->rejected_at)
                        <div class="mb-3 pl-3 border-l-2 border-red-500">
                            <div class="font-bold text-red-700">Disapproved</div>
                            <div class="text-xs">{{ $application->rejected_at->format('M d, Y h:i A') }}</div>
                            @if($application->rejection_remarks)
                                <div class="text-xs italic mt-1 text-red-600">"{{ $application->rejection_remarks }}"</div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

@endsection