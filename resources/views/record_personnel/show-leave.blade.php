@extends('layouts.sdo')

@section('title', 'Application Record Details')
@section('page-title', 'Application Record Details')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/review-leave.css') }}?v={{ time() }}">
@endpush

@section('content')

    <div class="page-header-modular animate__animated animate__fadeInDown">
        <div class="header-left">
            <a href="{{ route('records.dashboard') }}" class="back-btn-premium">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div class="header-titles">
                <h1 class="page-title-premium">Application Record</h1>
                <div class="header-metadata">
                    <div class="meta-capsule-id">
                        <i class="fas fa-fingerprint"></i>
                        <span>ID: #{{ str_pad($application->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="meta-capsule-date">
                        <i class="far fa-calendar-check"></i>
                        <span>Filed: {{ $application->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-right">
            <div class="applicant-profile-mini">
                <div class="applicant-avatar-box">
                    <div class="avatar-circle">
                        {{ substr($application->user->first_name, 0, 1) }}{{ substr($application->user->last_name, 0, 1) }}
                    </div>
                </div>
                <div class="applicant-info-box">
                    <p class="applicant-name-premium text-slate-800 font-bold mb-0 leading-none">{{ $application->user->full_name }}</p>
                    <p class="applicant-role-premium text-slate-400 text-[0.65rem] uppercase font-black tracking-tighter">{{ str_replace('_', ' ', $application->user->role) }}</p>
                </div>
            </div>

            @php
                $statusColor = 'bg-slate-100 text-slate-600';
                $statusIcon = 'fa-clock';
                $stat = strtolower($application->status);
                if (str_contains($stat, 'approve')) { $statusColor = 'bg-green-100 text-green-700'; $statusIcon = 'fa-check-circle'; }
                elseif (str_contains($stat, 'reject') || str_contains($stat, 'disapprove')) { $statusColor = 'bg-red-100 text-red-700'; $statusIcon = 'fa-times-circle'; }
                elseif (str_contains($stat, 'recommend')) { $statusColor = 'bg-blue-100 text-blue-700'; $statusIcon = 'fa-thumbs-up'; }
            @endphp
            
            <div class="header-status-box">
                <span class="status-pill-premium {{ $statusColor }}">
                    <i class="fas {{ $statusIcon }}"></i>
                    {{ $application->status }}
                </span>
            </div>
        </div>
    </div>

    <div class="page-layout">

        <!-- --- LEFT: DOCUMENT VIEW (MODULAR) --- -->
        <div class="flex flex-col gap-6">
            
            <!-- CS Form Header Info (Standalone Card) -->
            <div class="review-section animate__animated animate__backInUp" style="animation-delay: 0.1s;">
                <div class="review-card-body">
                    <div class="doc-header-box">
                        <div class="flex justify-between items-start mb-6">
                            <img src="{{ asset('assets/images/deped_logo.png') }}" class="h-16 w-auto" alt="Logo" onerror="this.style.display='none'">
                            <div class="text-right">
                                <span class="bg-slate-100 text-slate-500 px-3 py-1 rounded-full text-[0.65rem] font-bold uppercase tracking-wider border border-slate-200">CS Form No. 6 (Revised 2020)</span>
                            </div>
                        </div>
                        <h2 class="doc-title-main">Application for Leave</h2>
                        <p class="doc-subtitle-main">Department of Education - Schools Division Office</p>
                    </div>
                </div>
            </div>

            <!-- 1. Office & Personal Details -->
            <div class="review-section animate__animated animate__backInUp" style="animation-delay: 0.2s;">
                <div class="review-card-header">
                    <div class="review-card-icon"><i class="fas fa-user-tie"></i></div>
                    <h3>Applicant Information</h3>
                </div>
                <div class="review-card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-2">
                        <div class="doc-info-row">
                            <span class="doc-info-label">Office:</span>
                            <span class="doc-info-value">DepEd SDO - {{ $application->user->office->name ?? 'N/A' }}</span>
                        </div>
                        <div class="doc-info-row">
                            <span class="doc-info-label">Date of Filing:</span>
                            <span class="doc-info-value">{{ $application->date_filing->format('F d, Y') }}</span>
                        </div>
                        <div class="doc-info-row">
                            <span class="doc-info-label">Full Name:</span>
                            <span class="doc-info-value uppercase">{{ $application->user->last_name }}, {{ $application->user->first_name }} {{ substr($application->user->middle_name ?? '', 0, 1) }}.</span>
                        </div>
                        <div class="doc-info-row">
                            <span class="doc-info-label">Position:</span>
                            <span class="doc-info-value">{{ $application->user->position }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Type & Details of Leave -->
            <div class="review-section animate__animated animate__backInUp" style="animation-delay: 0.3s;">
                <div class="review-card-header">
                    <div class="review-card-icon"><i class="fas fa-file-signature"></i></div>
                    <h3>Leave Type & Details</h3>
                </div>
                <div class="review-card-body">
                    <div class="sub-section-label-premium">6.A Type of Leave to be Availed Of</div>
                    <div class="leave-type-display-premium">
                         @php
                            $typeIcon = 'fa-file-alt';
                            $tn = $application->leaveType->type_name;
                            if (str_contains($tn, 'Vacation')) $typeIcon = 'fa-plane';
                            elseif (str_contains($tn, 'Sick')) $typeIcon = 'fa-notes-medical';
                            elseif (str_contains($tn, 'Maternity') || str_contains($tn, 'Paternity')) $typeIcon = 'fa-baby';
                            elseif (str_contains($tn, 'Wellness')) $typeIcon = 'fa-spa';
                         @endphp
                         <div class="leave-type-icon-box">
                            <i class="fas {{ $typeIcon }}"></i>
                         </div>
                         <div class="flex flex-col">
                             <span class="leave-type-name-premium text-slate-800">{{ $tn }}</span>
                             @if($application->details && $application->details->other_purpose)
                                <div class="mt-1 text-slate-500 font-medium text-xs">
                                    <span class="text-slate-400 mr-1 uppercase text-[0.6rem] font-black">Purpose:</span>
                                    {{ $application->details->other_purpose }}
                                </div>
                            @endif
                         </div>
                    </div>

                </div>
            </div>

            <!-- 3. Duration & Commutation -->
            <div class="review-section animate__animated animate__backInUp" style="animation-delay: 0.4s;">
                <div class="review-card-header">
                    <div class="review-card-icon"><i class="fas fa-calendar-alt"></i></div>
                    <h3>Duration & Commutation</h3>
                </div>
                <div class="review-card-body">
                    <div class="sub-section-label-premium">6.B Details of Leave</div>
                    <div class="leave-details-grid mb-8">
                        @if($application->details)
                            @if($application->details->vacation_loc_type)
                                <div class="detail-item-box-premium vacation">
                                    <span class="detail-item-label-premium text-blue-400">Vacation Location</span>
                                    <div class="detail-item-value-premium">{{ $application->details->vacation_loc_type }}</div>
                                    <div class="text-[0.75rem] text-slate-500 font-medium mt-1">{{ $application->details->vacation_loc_details }}</div>
                                </div>
                            @endif
                            @if($application->details->sick_loc_type)
                                <div class="detail-item-box-premium sick">
                                    <span class="detail-item-label-premium text-red-400">Sick Leave Details</span>
                                    <div class="detail-item-value-premium">{{ $application->details->sick_loc_type }}</div>
                                    <div class="text-[0.75rem] text-slate-500 font-medium mt-1">{{ $application->details->sick_illness }}</div>
                                </div>
                            @endif
                            @if($application->details->women_illness)
                                <div class="detail-item-box-premium special">
                                    <span class="detail-item-label-premium text-pink-400">Special Leave (Women)</span>
                                    <div class="detail-item-value-premium">{{ $application->details->women_illness }}</div>
                                </div>
                            @endif
                            @if($application->details->study_type)
                                <div class="detail-item-box-premium other">
                                    <span class="detail-item-label-premium">Study Details</span>
                                    <div class="detail-item-value-premium">{{ $application->details->study_type }} {{ $application->details->study_details }}</div>
                                </div>
                            @endif
                            @if($application->details->other_type)
                                <div class="detail-item-box-premium other">
                                    <span class="detail-item-label-premium">Other Details</span>
                                    <div class="detail-item-value-premium">{{ $application->details->other_type }} {{ $application->details->other_details }}</div>
                                </div>
                            @endif
                            @if($application->details->commutation)
                                <div class="detail-item-box-premium other">
                                    <span class="detail-item-label-premium">Commutation</span>
                                    <div class="detail-item-value-premium">{{ $application->details->commutation }}</div>
                                </div>
                            @endif
                        @else
                            <div class="text-slate-400 italic text-sm py-4">No specific details provided.</div>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <div class="doc-section-divider">6.C Duration</div>
                            <div class="doc-info-row border-none">
                                <span class="doc-info-label w-24">Working Days:</span>
                                <span class="doc-info-value text-xl text-blue-600 font-black">{{ $application->days_applied }} Day(s)</span>
                            </div>
                            <div class="doc-info-row border-none">
                                <span class="doc-info-label w-24">Dates:</span>
                                <span class="doc-info-value">{{ $application->start_date->format('M d, Y') }} - {{ $application->end_date->format('M d, Y') }}</span>
                            </div>
                        </div>
                        <div>
                            <div class="doc-section-divider">6.D Commutation</div>
                            <div class="flex flex-col gap-3 mt-4">
                                <div class="flex items-center gap-3 p-3 rounded-xl border {{ $application->commutation == 'Requested' ? 'bg-blue-50 border-blue-200 text-blue-700' : 'bg-slate-50 border-slate-100 text-slate-400' }}">
                                    <i class="fas {{ $application->commutation == 'Requested' ? 'fa-check-circle' : 'fa-circle opacity-20' }} text-lg"></i>
                                    <span class="font-bold uppercase text-xs">Requested</span>
                                </div>
                                <div class="flex items-center gap-3 p-3 rounded-xl border {{ $application->commutation == 'Not Requested' ? 'bg-blue-50 border-blue-100 text-blue-700' : 'bg-slate-50 border-slate-100 text-slate-400' }}">
                                    <i class="fas {{ $application->commutation == 'Not Requested' ? 'fa-check-circle' : 'fa-circle opacity-20' }} text-lg"></i>
                                    <span class="font-bold uppercase text-xs">Not Requested</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Verification & Approvals -->
            <div class="review-section animate__animated animate__backInUp" style="animation-delay: 0.5s;">
                <div class="review-card-header">
                    <div class="review-card-icon"><i class="fas fa-stamp"></i></div>
                    <h3>Certification & Approvals</h3>
                </div>
                <div class="review-card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <!-- 7.A HR Certification -->
                        <div class="sig-box">
                            <div class="sig-label-top">7.A Cert. of Leave Credits</div>
                            <div class="signature-area-premium">
                                @if($application->hr_verified_at)
                                    <div class="mb-2 text-center">
                                        <div class="status-badge-premium certified mb-4">
                                            <i class="fas fa-check-circle"></i> Certified {{ $application->hr_verified_at->format('M d, Y') }}
                                        </div>
                                        @if($application->hrVerifier && $application->hrVerifier->esignature)
                                            <div class="h-16 mb-2">
                                                <img src="{{ storage_url($application->hrVerifier->esignature) }}" class="h-full mx-auto object-contain">
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="signature-placeholder-premium">
                                        <i class="fas fa-clock"></i>
                                        <span>Awaiting HR Certification</span>
                                    </div>
                                @endif
                            </div>
                            @if($application->hr_verified_at)
                                <div class="text-center">
                                    <div class="font-bold uppercase text-sm text-slate-800 tracking-tight">{{ $application->hrVerifier->full_name ?? 'Verifying Officer' }}</div>
                                    <div class="text-[0.6rem] text-slate-400 font-black uppercase tracking-widest mt-0.5">{{ $application->hrVerifier->position ?? 'Administrative Officer' }}</div>
                                </div>
                            @endif
                        </div>

                        <!-- 7.B Recommendation -->
                        <div class="sig-box">
                            <div class="sig-label-top">7.B Recommendation</div>
                            <div class="signature-area-premium">
                                @if($application->recommended_at)
                                    <div class="mb-2 text-center">
                                        <div class="status-badge-premium recommended mb-4">
                                            <i class="fas fa-thumbs-up"></i> Recommended {{ $application->recommended_at->format('M d, Y') }}
                                        </div>
                                        @if($application->recommendingOfficer && $application->recommendingOfficer->esignature)
                                            <div class="h-16 mb-2">
                                                <img src="{{ storage_url($application->recommendingOfficer->esignature) }}" class="h-full mx-auto object-contain">
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="signature-placeholder-premium">
                                        <i class="fas fa-user-clock"></i>
                                        <span>Awaiting Recommendation</span>
                                    </div>
                                @endif
                            </div>
                            @if($application->recommended_at)
                                <div class="text-center">
                                    <div class="font-bold uppercase text-sm text-slate-800 tracking-tight">{{ $application->recommendingOfficer->full_name }}</div>
                                    <div class="text-[0.6rem] text-slate-400 font-black uppercase tracking-widest mt-0.5">{{ $application->recommendingOfficer->position }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 7.C/D Final Approval -->
                    <div class="sig-box bg-slate-50/30 border-slate-200">
                        <div class="sig-label-top">7.C / 7.D Final Executive Approval</div>
                        <div class="final-approval-container-premium">
                            <div class="w-full">
                                @if($application->approved_at)
                                    <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm">
                                        <div class="text-blue-900 font-black text-[0.65rem] mb-4 uppercase flex items-center gap-2">
                                            <i class="fas fa-check-double text-blue-500"></i> Approved Leave Allocation
                                        </div>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center bg-slate-50 p-3 rounded-xl border border-slate-100">
                                                <span class="text-[0.7rem] font-black text-slate-400 uppercase">With Pay</span>
                                                <span class="font-black text-blue-600 text-lg">{{ $application->days_with_pay ?? 0 }} <span class="text-[0.6rem]">DAYS</span></span>
                                            </div>
                                            <div class="flex justify-between items-center bg-slate-50 p-3 rounded-xl border border-slate-100">
                                                <span class="text-[0.7rem] font-black text-slate-400 uppercase">Without Pay</span>
                                                <span class="font-black text-slate-700 text-lg">{{ $application->days_without_pay ?? 0 }} <span class="text-[0.6rem]">DAYS</span></span>
                                            </div>
                                            @if($application->others_remarks)
                                                <div class="p-3 bg-blue-50/30 border border-blue-100/50 rounded-xl mt-4">
                                                    <div class="text-[0.6rem] font-black text-blue-400 uppercase mb-1">Remarks</div>
                                                    <div class="text-[0.75rem] text-blue-900 font-medium leading-relaxed">{{ $application->others_remarks }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                     <div class="bg-white/70 border-2 border-dashed border-slate-200 rounded-3xl p-8 text-center">
                                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-300">
                                            <i class="fas fa-hourglass-half text-2xl"></i>
                                        </div>
                                        <div class="text-slate-400 text-[0.65rem] font-black uppercase tracking-widest leading-relaxed">
                                            Awaiting final executive decision<br>
                                            <span class="text-slate-300 font-bold lowercase">Decision will appear here once approved</span>
                                        </div>
                                     </div>
                                @endif
                            </div>

                            <div class="w-full text-center py-4">
                                <div class="signature-area-premium">
                                    @if($application->approved_at)
                                        <div class="status-badge-premium approved mb-6">
                                            <i class="fas fa-shield-check"></i> Exec Signed {{ $application->approved_at->format('M d, Y') }}
                                        </div>
                                        @if($application->approvingOfficer && $application->approvingOfficer->esignature)
                                            <div class="h-20 mb-4">
                                                <img src="{{ storage_url($application->approvingOfficer->esignature) }}" class="h-full mx-auto object-contain scale-125">
                                            </div>
                                        @endif
                                        <div class="font-black uppercase text-lg text-blue-950 tracking-tight leading-none mb-1">{{ $application->approvingOfficer->full_name }}</div>
                                        <div class="text-[0.6rem] text-blue-400 font-black uppercase tracking-[0.2em]">{{ $application->approvingOfficer->position }}</div>
                                    @else
                                        <div class="signature-placeholder-premium border-slate-200 bg-slate-50/50">
                                            <i class="fas fa-pen-nib"></i>
                                            <span>Pending Executive Signature</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- --- RIGHT: SIDEBAR --- -->
        <div class="flex flex-col gap-6">

            <!-- Export Card -->
            <div class="sidebar-modular-card animate__animated animate__fadeInRight" style="animation-delay: 0.6s;">
                <div class="sidebar-modular-header text-slate-800">
                    <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center text-orange-500"><i class="fas fa-print"></i></div>
                    <span>Export & Print</span>
                </div>
                
                <div class="flex flex-col gap-4">
                    <div class="w-full">
                        <a href="{{ route('user.leave.form6', ['id' => $application->id, 'format' => 'pdf']) }}" target="_blank"
                            class="preview-form6-card-premium">
                            <div class="preview-icon-box">
                                <i class="fas fa-file-pdf text-xl text-red-500"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[0.7rem] font-black text-slate-700 uppercase tracking-tighter leading-none">Download Form 6 Data</span>
                            </div>
                            <div class="ml-auto text-slate-300">
                                <i class="fas fa-external-link-alt text-xs"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Credit Analysis Card -->
            <div class="sidebar-modular-card animate__animated animate__fadeInRight" style="animation-delay: 0.7s;">
                <div class="sidebar-modular-header text-blue-900">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600"><i class="fas fa-calculator"></i></div>
                    <span>Credit Analysis</span>
                </div>

                @php
                    $vl = $credits['vl'];
                    $sl = $credits['sl'];
                    $insufficient = ($vl['balance'] < 0 || $sl['balance'] < 0);
                @endphp

                @if($insufficient)
                    <div class="bg-red-50 border border-red-100 p-4 rounded-xl mb-6 flex gap-3">
                        <i class="fas fa-exclamation-triangle text-red-500 mt-1 text-sm"></i>
                        <div class="text-[0.7rem] text-red-800 leading-relaxed font-bold">
                            <span class="uppercase block mb-1">Deficit Warning</span>
                            This application will result in negative leave credits.
                        </div>
                    </div>
                @endif

                <div class="space-y-8">
                    <!-- VL Pool -->
                    <div class="credit-pool-card-premium">
                        <div class="credit-pool-label">
                            <i class="fas fa-sun text-orange-400"></i> Vacation Leave Pool
                        </div>
                        <div class="space-y-1">
                            <div class="credit-row-premium">
                                <span class="text-[0.7rem] font-bold text-slate-400">Current Balance</span>
                                <span class="credit-val-dim">{{ (float) $vl['current'] }}</span>
                            </div>
                            <div class="credit-row-premium">
                                <span class="text-[0.7rem] font-bold text-slate-400">Less This App</span>
                                <span class="credit-val-impact">-{{ (float) $vl['less'] }}</span>
                            </div>
                            <div class="credit-balance-box">
                                <span class="text-[0.6rem] font-black text-slate-800 uppercase tracking-tighter">New Balance</span>
                                <span class="text-xl font-black {{ $vl['balance'] < 0 ? 'text-red-600' : 'text-green-600' }} font-mono leading-none">{{ (float) $vl['balance'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- SL Pool -->
                    <div class="credit-pool-card-premium">
                        <div class="credit-pool-label">
                            <i class="fas fa-briefcase-medical text-red-400"></i> Sick Leave Pool
                        </div>
                        <div class="space-y-1">
                            <div class="credit-row-premium">
                                <span class="text-[0.7rem] font-bold text-slate-400">Current Balance</span>
                                <span class="credit-val-dim">{{ (float) $sl['current'] }}</span>
                            </div>
                            <div class="credit-row-premium">
                                <span class="text-[0.7rem] font-bold text-slate-400">Less This App</span>
                                <span class="credit-val-impact">-{{ (float) $sl['less'] }}</span>
                            </div>
                            <div class="credit-balance-box">
                                <span class="text-[0.6rem] font-black text-slate-800 uppercase tracking-tighter">New Balance</span>
                                <span class="text-xl font-black {{ $sl['balance'] < 0 ? 'text-red-600' : 'text-green-600' }} font-mono leading-none">{{ (float) $sl['balance'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Tracker -->
            <div class="sidebar-modular-card animate__animated animate__fadeInRight" style="animation-delay: 0.8s;">
                <div class="sidebar-modular-header text-slate-800">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500"><i class="fas fa-route"></i></div>
                    <span>Workflow Journey</span>
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

                <div class="v-stepper-modular">
                    <div class="v-step-modular completed">
                        <div class="v-marker"></div>
                        <div class="v-step-content">
                            <div class="text-[0.8rem] font-bold text-slate-800 uppercase tracking-tight">Application Filed</div>
                            <div class="text-[0.6rem] font-black text-slate-400 uppercase tracking-tighter">{{ $application->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>

                    <div class="v-step-modular {{ $s1 }}">
                        <div class="v-marker"></div>
                        <div class="v-step-content">
                            <div class="text-[0.8rem] font-bold text-slate-800 uppercase tracking-tight">HR Verification</div>
                            <div class="text-[0.6rem] font-black text-slate-400 uppercase tracking-tighter">
                                {{ $application->hr_verified_at ? $application->hr_verified_at->format('M d, Y') : ($s1 == 'active' ? 'Under Review...' : 'Upcoming') }}
                            </div>
                        </div>
                    </div>

                    <div class="v-step-modular {{ $s2 }}">
                        <div class="v-marker"></div>
                        <div class="v-step-content">
                            <div class="text-[0.8rem] font-bold text-slate-800 uppercase tracking-tight">Recommendation</div>
                            <div class="text-[0.6rem] font-black text-slate-400 uppercase tracking-tighter">
                                {{ $application->recommended_at ? $application->recommended_at->format('M d, Y') : ($s2 == 'active' ? 'Under Review...' : 'Upcoming') }}
                            </div>
                        </div>
                    </div>

                    <div class="v-step-modular {{ $s3 }}">
                        <div class="v-marker"></div>
                        <div class="v-step-content">
                            <div class="text-[0.8rem] font-bold text-slate-800 uppercase tracking-tight">Executive Approval</div>
                            <div class="text-[0.6rem] font-black text-slate-400 uppercase tracking-tighter">
                                {{ $application->approved_at ? $application->approved_at->format('M d, Y') : ($s3 == 'active' ? 'Under Review...' : 'Upcoming') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
