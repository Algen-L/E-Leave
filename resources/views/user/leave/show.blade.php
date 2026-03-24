@extends('layouts.sdo')

@section('title', 'Application Details')
@section('page-title', 'Application Details')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/view-leave.css') }}?v={{ time() }}">
@endpush

@section('content')

    <!-- --- HEADER CARD --- -->
    <div class="header-card-final animate__animated animate__backInDown animate__fast" style="animation-delay: 0.1s;">
        <!-- Tier 1: Top -->
        <div class="header-tier-top">
            <a href="{{ route('user.leave.history') }}" class="header-arrow-box" title="Back">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div class="header-type-box">
                <span class="type-label">Leave Type</span>
                <span class="type-value">{{ $application->leaveType->type_name }}</span>
            </div>
        </div>

        <!-- Tier 2: Bottom -->
        <div class="header-tier-bottom">
            <div class="header-bottom-item">
                <span class="final-label">Application Reference</span>
                <span class="final-value"># {{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="header-bottom-item">
                <span class="final-label">Application Status</span>
                @php
                    $badgeClass = 'status-pending';
                    if (stripos($application->status, 'approve') !== false && stripos($application->status, 'pending') === false)
                        $badgeClass = 'status-approved';
                    if (stripos($application->status, 'reject') !== false || stripos($application->status, 'disapprove') !== false)
                        $badgeClass = 'status-rejected';
                @endphp
                <div class="dashboard-header-badge {{ $badgeClass }} shadow-sm">
                    <i class="fas fa-circle text-[0.4rem]"></i>
                    {{ $application->status }}
                </div>
            </div>
            <div class="header-bottom-item">
                <span class="final-label">Applied On</span>
                <span class="final-value">{{ $application->created_at->format('M d, Y') }}</span>
            </div>
        </div>
    </div>


    <div class="page-layout">

        <!-- --- LEFT: CONTENT CARDS --- -->
        <div class="flex flex-col gap-5">
            
            <!-- Employee Details Card -->
            <div class="dashboard-card animate__animated animate__backInDown animate__fast" style="animation-delay: 0.2s;">
                <div class="card-header">
                    <div class="card-icon"><i class="fas fa-user-tie"></i></div>
                    <div class="card-title">Employee Details</div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-5 gap-x-12">
                    <div class="data-row">
                        <span class="data-label">Full Name</span>
                        <span class="data-value highlight-value">{{ auth()->user()->full_name }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Office / Department</span>
                        <span class="data-value">DepEd SDO - {{ auth()->user()->office->name ?? 'N/A' }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Position</span>
                        <span class="data-value">{{ auth()->user()->position }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Date of Filing</span>
                        <span class="data-value">{{ $application->date_filing->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Leave Details Card -->
            <div class="dashboard-card animate__animated animate__backInDown animate__fast" style="animation-delay: 0.3s;">
                <div class="card-header">
                    <div class="card-icon"><i class="fas fa-clipboard-check"></i></div>
                    <div class="card-title">Leave Details</div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
                    <!-- Type of Leave -->
                    <div class="data-row md:col-span-2">
                        <span class="data-label">Type of Leave</span>
                        <span class="text-xl font-extrabold text-blue-700 bg-blue-50/50 inline-block px-4 py-2 rounded-lg border border-blue-100">
                            {{ $application->leaveType->type_name }}
                        </span>
                        @if($application->details && $application->details->other_purpose)
                            <div class="mt-3 text-sm font-medium text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-100">
                                <i class="fas fa-info-circle text-slate-400 mr-2"></i> "{{ $application->details->other_purpose }}"
                            </div>
                        @endif
                    </div>

                    <!-- Context / Specifics -->
                    @if($application->details)
                        <div class="data-row md:col-span-2">
                            <span class="data-label block mb-3 border-b border-slate-100 pb-2">Context & Location</span>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($application->details->vacation_loc_type)
                                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                        <div class="content-label mb-1"><i class="fas fa-map-marker-alt text-blue-500 mr-1"></i> Vacation Location</div>
                                        <div class="content-value text-slate-800">{{ $application->details->vacation_loc_type }}</div>
                                        @if($application->details->vacation_loc_details)
                                            <div class="text-xs text-slate-500 italic mt-1">{{ $application->details->vacation_loc_details }}</div>
                                        @endif
                                    </div>
                                @endif
                                @if($application->details->sick_loc_type)
                                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                        <div class="content-label mb-1"><i class="fas fa-hospital text-red-500 mr-1"></i> Sick Leave Context</div>
                                        <div class="content-value text-slate-800">{{ $application->details->sick_loc_type }}</div>
                                        @if($application->details->sick_illness)
                                            <div class="text-xs text-slate-500 italic mt-1">{{ $application->details->sick_illness }}</div>
                                        @endif
                                    </div>
                                @endif
                                @if($application->details->women_illness)
                                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                        <div class="content-label mb-1"><i class="fas fa-notes-medical text-pink-500 mr-1"></i> Special Leave Benefit</div>
                                        <div class="content-value text-slate-800">{{ $application->details->women_illness }}</div>
                                    </div>
                                @endif
                                @if($application->details->study_type)
                                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                        <div class="content-label mb-1"><i class="fas fa-graduation-cap text-indigo-500 mr-1"></i> Study Purpose</div>
                                        <div class="content-value text-slate-800">{{ $application->details->study_type }}</div>
                                        @if($application->details->study_details)
                                            <div class="text-xs text-slate-500 italic mt-1">{{ $application->details->study_details }}</div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                         <div class="data-row md:col-span-2">
                             <div class="empty-placeholder">
                                <i class="fas fa-info-circle"></i> No specific location or purpose context provided.
                             </div>
                         </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Working Days Card -->
                <div class="dashboard-card mb-0">
                    <div class="card-header">
                        <div class="card-icon"><i class="fas fa-calendar-alt"></i></div>
                        <div class="card-title">Working Days & Dates</div>
                    </div>
                    <div class="flex flex-col gap-5">
                        <div class="data-row">
                            <span class="content-label">Total Days Applied</span>
                            <div class="flex items-baseline gap-2">
                                <span class="days-count">{{ $application->days_applied }}</span>
                                <span class="days-unit">{{ Str::plural('Day', $application->days_applied) }}</span>
                            </div>
                        </div>
                        <div class="data-row">
                            <span class="content-label">Inclusive Dates</span>
                            <div class="flex flex-wrap gap-2 mt-2">
                                @if($application->dates && is_array($application->dates))
                                    @foreach($application->dates as $date)
                                        <div class="bg-white border border-blue-100 px-3 py-2 rounded-xl flex items-center gap-2 shadow-sm">
                                            <i class="far fa-calendar-check text-blue-500 text-xs"></i>
                                            <span class="text-sm font-bold text-slate-700">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="bg-white border border-blue-100 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
                                        <i class="far fa-calendar-check text-blue-500"></i>
                                        <span class="dates-range-text font-bold text-slate-700">{{ $application->start_date->format('M d, Y') }} — {{ $application->end_date->format('M d, Y') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Commutation Request Card -->
                <div class="dashboard-card mb-0">
                    <div class="card-header">
                        <div class="card-icon"><i class="fas fa-money-check-alt"></i></div>
                        <div class="card-title">Commutation</div>
                    </div>
                    <div class="flex items-center justify-center h-full min-h-[120px]">
                        @if($application->commutation == 'Requested')
                            <div class="flex flex-col items-center gap-3 bg-blue-50/50 px-8 py-6 rounded-2xl border-2 border-dashed border-blue-200 w-full">
                                <i class="fas fa-check-circle text-blue-600 text-3xl"></i>
                                <span class="status-text-premium text-blue-900">Requested</span>
                            </div>
                        @else
                            <div class="flex flex-col items-center gap-3 bg-slate-50/50 px-8 py-6 rounded-2xl border-2 border-dashed border-slate-200 w-full">
                                <i class="fas fa-minus-circle text-slate-300 text-3xl"></i>
                                <span class="status-text-premium text-slate-400">Not Requested</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>


        <div class="flex flex-col">
            <!-- Quick Actions Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <div class="card-icon"><i class="fas fa-bolt"></i></div>
                    <div class="card-title">Quick Actions</div>
                </div>
                <div class="flex flex-col gap-3">
                    <a href="{{ route('user.leave.form6', ['id' => $application->id, 'format' => 'pdf']) }}" target="_blank"
                        class="btn-download-pdf !shadow-blue-200/50">
                        <i class="fas fa-file-pdf"></i> Download PDF
                    </a>
                    <p class="text-[0.7rem] text-center text-slate-400 font-bold uppercase tracking-wider px-4">
                        Download OS Form No. 6
                    </p>
                </div>
            </div>

            <!-- Approval / Processing Status Timeline -->
            <div class="dashboard-card">
                <div class="card-header">
                    <div class="card-icon"><i class="fas fa-sitemap"></i></div>
                    <div class="card-title">Approval Status</div>
                </div>

                @php
                    $currentStatus = strtolower($application->status);
                    $isRejected = str_contains($currentStatus, 'reject') || str_contains($currentStatus, 'disapprove');
                    
                    $s1 = $application->hr_verified_at ? 'completed' : 'active';
                    $recoComplete = $application->recommended_at;
                    $s2 = $recoComplete ? 'completed' : ($application->hr_verified_at ? 'active' : '');
                    $s3 = $application->approved_at ? 'completed' : ($recoComplete ? 'active' : '');

                    if ($isRejected) {
                        if (!$application->hr_verified_at) $s1 = 'rejected';
                        else if (!$recoComplete) $s2 = 'rejected';
                        else $s3 = 'rejected';
                    }
                @endphp

                <div class="timeline">
                    
                    <!-- Application Filed -->
                    <div class="timeline-step completed">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <div class="step-title">
                                Application Filed
                                <span class="timeline-badge badge-approved"><i class="fas fa-check"></i> Completed</span>
                            </div>
                            <div class="step-desc">
                                Submitted on {{ $application->created_at->format('M d, Y') }} at {{ $application->created_at->format('h:i A') }}
                            </div>
                        </div>
                    </div>

                    <!-- HR Verification -->
                    <div class="timeline-step {{ $s1 }}">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <div class="step-title">
                                HR Certification
                                @if($s1 == 'completed')
                                    <span class="timeline-badge badge-approved"><i class="fas fa-check"></i> Verified</span>
                                @elseif($s1 == 'rejected')
                                    <span class="timeline-badge badge-rejected"><i class="fas fa-times"></i> Rejected</span>
                                @else
                                    <span class="timeline-badge badge-pending"><i class="fas fa-clock"></i> Awaiting</span>
                                @endif
                            </div>
                            <div class="step-desc">
                                @if($application->hr_verified_at)
                                    <span class="font-bold text-slate-700">{{ $application->hrVerifier?->full_name }}</span><br>
                                    Verified on {{ $application->hr_verified_at->format('M d, Y h:i A') }}
                                @else
                                    Pending verification from Human Resources.
                                    @if($s1 == 'rejected' && $application->rejection_remarks)
                                        <div class="mt-2 text-xs text-red-600 italic">"{{ $application->rejection_remarks }}"</div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Recommendation -->
                    <div class="timeline-step {{ $s2 }}">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <div class="step-title">
                                Recommendation
                                @if($s2 == 'completed')
                                    <span class="timeline-badge badge-approved"><i class="fas fa-check"></i> Recommended</span>
                                @elseif($s2 == 'rejected')
                                    <span class="timeline-badge badge-rejected"><i class="fas fa-times"></i> Rejected</span>
                                @elseif($s2 == 'active')
                                    <span class="timeline-badge badge-pending"><i class="fas fa-clock"></i> Awaiting</span>
                                @else
                                    <span class="timeline-badge badge-waiting">Pending HR</span>
                                @endif
                            </div>
                            <div class="step-desc">
                                @if($application->recommended_at)
                                    <span class="font-bold text-slate-700">{{ $application->recommendingOfficer?->full_name }}</span><br>
                                    Recommended on {{ $application->recommended_at->format('M d, Y h:i A') }}
                                @else
                                    Pending signature from recommending officer.
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Final Approval -->
                    <div class="timeline-step {{ $s3 }}">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <div class="step-title">
                                Final Approval
                                @if($s3 == 'completed')
                                    <span class="timeline-badge badge-approved"><i class="fas fa-check"></i> Approved</span>
                                @elseif($s3 == 'rejected')
                                    <span class="timeline-badge badge-rejected"><i class="fas fa-times"></i> Disapproved</span>
                                @elseif($s3 == 'active')
                                    <span class="timeline-badge badge-pending"><i class="fas fa-clock"></i> Awaiting</span>
                                @else
                                    <span class="timeline-badge badge-waiting">Pending Pre-reqs</span>
                                @endif
                            </div>
                            <div class="step-desc">
                                @if($application->approved_at)
                                    <span class="font-bold text-slate-700">{{ $application->approvingOfficer?->full_name }}</span><br>
                                    Approved on {{ $application->approved_at->format('M d, Y h:i A') }}
                                    <div class="mt-2 inline-flex gap-4 p-2 bg-slate-50 border border-slate-100 rounded text-xs font-bold text-slate-700">
                                        <span>With Pay: <span class="text-green-600">{{ $application->days_with_pay ?? 0 }}d</span></span>
                                        <span>Without Pay: <span class="text-slate-500">{{ $application->days_without_pay ?? 0 }}d</span></span>
                                    </div>
                                @else
                                    Pending final decision from approving officer.
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>


            <!-- Activity Log Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <div class="card-icon"><i class="fas fa-history"></i></div>
                    <div class="card-title">Activity Log</div>
                </div>
                <div class="space-y-2">
                    <div class="activity-item">
                        <div class="activity-dot dot-default"></div>
                        <div class="activity-content">
                            <div class="activity-label text-slate-400">Application Submitted</div>
                            <div class="activity-time text-slate-500">{{ $application->created_at->format('M d, Y h:i A') }}</div>
                        </div>
                    </div>

                    @if($application->hr_verified_at)
                    <div class="activity-item">
                        <div class="activity-dot dot-blue"></div>
                        <div class="activity-content">
                            <div class="activity-label text-blue-500">Verified by HR</div>
                            <div class="activity-time">{{ $application->hr_verified_at->format('M d, Y h:i A') }}</div>
                        </div>
                    </div>
                    @endif

                    @if($application->recommended_at)
                    <div class="activity-item">
                        <div class="activity-dot dot-green"></div>
                        <div class="activity-content">
                            <div class="activity-label text-green-500">Recommended</div>
                            <div class="activity-time">{{ $application->recommended_at->format('M d, Y h:i A') }}</div>
                        </div>
                    </div>
                    @endif

                    @if($application->approved_at)
                    <div class="activity-item">
                        <div class="activity-dot dot-green"></div>
                        <div class="activity-content">
                            <div class="activity-label text-green-600">Official Approval</div>
                            <div class="activity-time">{{ $application->approved_at->format('M d, Y h:i A') }}</div>
                        </div>
                    </div>
                    @endif

                    @if($isRejected)
                    <div class="activity-item">
                        <div class="activity-dot dot-red"></div>
                        <div class="activity-content">
                            <div class="activity-label text-red-500">Disapproved</div>
                            <div class="activity-time">Ref: #{{ $application->id }}</div>
                            @if($application->rejection_remarks)
                                <div class="text-xs italic mt-2 text-red-600/70 font-medium bg-red-50/50 p-2 rounded-lg border border-red-100/50">
                                    "{{ $application->rejection_remarks }}"
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

@endsection