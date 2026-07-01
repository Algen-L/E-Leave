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
                <span class="final-label">Tracking Number</span>
                <span class="final-value tracking-number-highlight">{{ $application->tracking_number ?? '---' }}</span>
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
        <div class="layout-content-col">
            <!-- Unified Application Details Card -->
            <div class="dashboard-card animate__animated animate__fadeInUp animate__fast" style="animation-delay: 0.2s;">
                <div class="card-header">
                    <div class="card-icon"><i class="fas fa-clipboard-list"></i></div>
                    <div class="card-title">Leave Application Details</div>
                </div>

                <div class="unified-details-grid">
                    <!-- Left Column: Leave Details & Context -->
                    <div class="space-y-4">
                        <!-- Type of Leave -->
                        <div class="data-row">
                            <span class="data-label">Tracking Number</span>
                            <span class="text-lg font-black text-slate-800 bg-slate-100 px-3 py-1 rounded border border-slate-200 font-mono">
                                {{ $application->tracking_number ?? '---' }}
                            </span>
                        </div>

                        <!-- Type of Leave -->
                        <div class="data-row">
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
                            <div class="data-row">
                                <span class="data-label block mb-3 border-b border-slate-100 pb-2">Context & Location</span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                        @endif

                        @if($application->hr_verified_at)
                        <div class="mt-6 pt-6 border-t border-dashed border-slate-200 animate__animated animate__fadeIn">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-[0.6rem] font-black bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase tracking-wider">
                                    <i class="fas fa-check-circle mr-1"></i> HR Certification Results
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-slate-50/50 p-3 rounded-xl border border-slate-100 hover:bg-white transition-colors">
                                    <span class="data-label !mb-1">Days with Full Pay</span>
                                    <div class="text-sm font-black text-slate-800">{{ number_format($application->days_with_pay ?? 0, 1) }} Days</div>
                                </div>
                                <div class="bg-slate-50/50 p-3 rounded-xl border border-slate-100 hover:bg-white transition-colors">
                                    <span class="data-label !mb-1">Days without Pay</span>
                                    <div class="text-sm font-black text-slate-800">{{ number_format($application->days_without_pay ?? 0, 1) }} Days</div>
                                </div>
                            </div>

                            @if($application->others_remarks)
                            <div class="mt-4">
                                <span class="data-label">Additional Remarks / Others</span>
                                <div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100 text-[0.7rem] text-slate-600 leading-relaxed italic">
                                    "{{ $application->others_remarks }}"
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>

                    <!-- Right Column: Working Days & Commutation -->
                    <div class="card-col-right">
                        <!-- Working Days Section -->
                        <div class="data-row">
                            <span class="data-label block mb-4 border-b border-slate-100 pb-2">Working Days & Dates</span>
                            <div class="flex flex-col gap-3">
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

                        <!-- Commutation Section -->
                        <div class="data-row">
                            <span class="data-label block mb-4 border-b border-slate-100 pb-2">Commutation Request</span>
                            <div class="flex items-center justify-center">
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
            </div>

            <!-- Credit Forecast Card -->
            @php
                $leaveCredit = \App\Models\LeaveCredit::where('user_id', $application->user_id)
                    ->where('leave_type_id', $application->leave_type_id)
                    ->first();
                
                $isApproved = stripos($application->status, 'approved') !== false;
                $isCertified = (bool)$application->hr_verified_at;
                
                $deduction = $isCertified 
                    ? ($application->days_with_pay ?? 0) 
                    : $application->days_applied;

                // FIX: If already approved, the credit has already been subtracted from the DB credits.
                // We must show Previous = DB_Credits + Deduction, and Resulting = DB_Credits.
                if ($isApproved) {
                    $startingBalance = ($leaveCredit->credits ?? 0) + $deduction;
                    $endingBalance = ($leaveCredit->credits ?? 0);
                    $startLabel = "Before Approval";
                    $endLabel = "Post-Approval Balance";
                    $footerNote = "This snapshot shows the deduction result from this approved application.";
                } else {
                    $startingBalance = ($leaveCredit->credits ?? 0);
                    $endingBalance = $startingBalance - $deduction;
                    $startLabel = "Current Balance";
                    $endLabel = "Forecasted";
                    $footerNote = "This forecast shows the remaining balance if this application is approved.";
                }
            @endphp

            @if($leaveCredit)
            <div class="dashboard-card animate__animated animate__fadeInUp animate__fast" style="animation-delay: 0.3s;">
                <div class="card-header">
                    <div class="card-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="card-title">Leave Credit Snapshot</div>
                </div>

                <div class="forecast-container">
                    <div class="forecast-info">
                        <span class="data-label">Credit Type Involved</span>
                        <div class="text-sm font-bold text-slate-700 flex items-center gap-2">
                            <i class="fas fa-shield-alt text-blue-500"></i>
                            {{ $application->leaveType->type_name }} Credit
                        </div>
                    </div>

                    <div class="forecast-visual mt-4">
                        <div class="forecast-visual-row">
                            <!-- Starting Balance -->
                            <div class="balance-pill current">
                                <span class="pill-label">{{ $startLabel }}</span>
                                <div class="pill-value">
                                    {{ format_credit_3_decimal($startingBalance) }}
                                </div>
                            </div>

                            <!-- Deduction Arrow -->
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-{{ $isApproved ? 'check-circle' : 'minus-circle' }} text-{{ $isApproved ? 'emerald' : 'red' }}-500 text-sm md:text-lg"></i>
                                <span class="text-[0.6rem] md:text-[0.7rem] font-black text-{{ $isApproved ? 'emerald' : 'red' }}-600 uppercase tracking-tighter">
                                    -{{ format_credit_3_decimal($deduction) }}
                                </span>
                                <span class="text-[0.5rem] font-bold text-slate-400 uppercase tracking-tighter">
                                    {{ $isCertified ? 'Paid Only' : 'Est. Total' }}
                                </span>
                                <div class="h-px bg-slate-200 w-6 md:w-8 my-1 md:my-2"></div>
                            </div>

                            <!-- Resulting Balance -->
                            <div class="balance-pill final">
                                <span class="pill-label">{{ $endLabel }}</span>
                                <div class="pill-value">
                                    {{ format_credit_3_decimal($endingBalance) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="premium-note-box info animate__animated animate__fadeIn">
                        <div class="premium-note-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="premium-note-content">
                            <span class="premium-note-title">Credit Snapshot Info</span>
                            <p class="premium-note-text">{{ $footerNote }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>


        <div class="layout-sidebar-col">
            <!-- Quick Actions Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <div class="card-icon"><i class="fas fa-bolt"></i></div>
                    <div class="card-title">Quick Actions</div>
                </div>
                <div class="flex flex-col gap-3">
                    <a href="{{ route('user.leave.form6', ['id' => $application->id, 'format' => 'pdf']) }}" target="_blank"
                        class="btn-download-orange !shadow-orange-200/50">
                        <i class="fas fa-file-pdf"></i> Download PDF
                    </a>
                    <div class="premium-note-box info">
                        <div class="premium-note-icon">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="premium-note-content">
                            <span class="premium-note-title">Generation Info</span>
                            <p class="premium-note-text">The downloaded Form 6 will reflect the current stage of approval and verified credits.</p>
                        </div>
                    </div>
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
            </div>        </div>

    </div>

@endsection
on
