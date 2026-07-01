@extends('layouts.sdo')

@section('title', 'Review Application')
@section('page-title', 'Review Application')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/review-leave.css') }}?v={{ time() }}">
@endpush

@section('content')
    @php
        $startDate = \Carbon\Carbon::parse($application->start_date);
        $endDate = \Carbon\Carbon::parse($application->end_date);
        $applicationDates = [];
        if (!empty($application->dates)) {
            $applicationDates = is_array($application->dates) ? $application->dates : explode(',', $application->dates);
        } else {
            $current = $startDate->copy();
            while ($current <= $endDate) {
                $applicationDates[] = $current->format('Y-m-d');
                $current->addDay();
            }
        }
    @endphp

    <x-leave.review-header 
        :application="$application" 
        :backRoute="route('user.leave.approvals', ['tab' => request()->query('tab', 'pending')])" 
    />


    <div class="page-layout">

        <!-- --- LEFT: DOCUMENT VIEW (MODULAR) --- -->
        <div class="flex flex-col gap-6">
            
            <!-- CS Form Header Info (Standalone Card) -->
            <div class="review-section animate__animated animate__backInUp" style="animation-delay: 0.1s;">
                <div class="review-card-body">
                    <div class="doc-header-box">
                        <div class="flex justify-center items-center mb-0">
                            <img src="{{ asset('assets/images/deped_logo.png') }}" class="h-14 w-auto grayscale opacity-40 hover:opacity-100 transition-all duration-500" alt="Logo" onerror="this.style.display='none'">
                        </div>

                        @php
                            $currentStatus = strtolower($application->status);
                            $isRejected = str_contains($currentStatus, 'reject') || str_contains($currentStatus, 'disapprove');
                            
                            $s1 = $application->hr_verified_at ? 'completed' : 'active';
                            
                            $recoComplete = $application->recommended_at;
                            $s2 = $recoComplete ? 'completed' : ($application->hr_verified_at ? 'active' : '');
                            
                            $asdsComplete = $application->asds_approved_at;
                            $s2_5 = $asdsComplete ? 'completed' : ($recoComplete ? 'active' : '');
                            
                            $s3 = $application->approved_at ? 'completed' : 
                                ($application->asds_id 
                                    ? ($asdsComplete ? 'active' : '') 
                                    : ($recoComplete ? 'active' : '')
                                );

                            if ($isRejected) {
                                if (!$application->hr_verified_at) $s1 = 'rejected';
                                else if (!$recoComplete) $s2 = 'rejected';
                                else if ($application->asds_id && !$asdsComplete) $s2_5 = 'rejected';
                                else $s3 = 'rejected';
                            }
                        @endphp

                        <div class="header-workflow-belt mt-12 mb-4 animate__animated animate__fadeInUp">
                            <div class="h-connector"></div>
                            <div class="h-step completed">
                                <div class="h-marker"><i class="fas fa-check"></i></div>
                                <div class="h-step-label">Application Filed</div>
                                <div class="h-step-sub">{{ $application->created_at->format('M d, Y') }}</div>
                            </div>
                            <div class="h-step {{ $s1 }}">
                                <div class="h-marker"><i class="fas fa-check"></i></div>
                                <div class="h-step-label">HR Verification</div>
                                <div class="h-step-sub">
                                    {{ $application->hr_verified_at ? $application->hr_verified_at->format('M d, Y') : ($s1 == 'active' ? 'Under Review' : 'Upcoming') }}
                                </div>
                            </div>
                            <div class="h-step {{ $s2 }}">
                                <div class="h-marker"><i class="fas fa-check"></i></div>
                                <div class="h-step-label">Recommendation</div>
                                <div class="h-step-sub">
                                    {{ $application->recommended_at ? $application->recommended_at->format('M d, Y') : ($s2 == 'active' ? 'Under Review' : 'Upcoming') }}
                                </div>
                            </div>
                            @if($application->asds_id)
                            <div class="h-step {{ $s2_5 }}">
                                <div class="h-marker"><i class="fas fa-check"></i></div>
                                <div class="h-step-label">ASDS Approval</div>
                                <div class="h-step-sub">
                                    {{ $application->asds_approved_at ? $application->asds_approved_at->format('M d, Y') : ($s2_5 == 'active' ? 'Under Review' : 'Upcoming') }}
                                </div>
                            </div>
                            @endif
                            <div class="h-step {{ $s3 }}">
                                <div class="h-marker"><i class="fas fa-check"></i></div>
                                <div class="h-step-label">Final Approval</div>
                                <div class="h-step-sub">
                                    {{ $application->approved_at ? $application->approved_at->format('M d, Y') : ($s3 == 'active' ? 'Under Review' : 'Upcoming') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Group: Applicant Info & Leave Type (Side by Side) -->
            <div class="review-grid-two-col">
                <!-- 1. Office & Personal Details -->
                <div class="review-section animate__animated animate__backInUp" style="animation-delay: 0.2s; margin-bottom: 0;">
                    <div class="review-card-header">
                        <div class="review-card-icon"><i class="fas fa-user-tie"></i></div>
                        <h3>Applicant Information</h3>
                    </div>
                    <div class="review-card-body">
                        <div class="doc-info-row">
                            <span class="doc-info-label">Office:</span>
                            <span class="doc-info-value">DepEd SDO - {{ $application->user->office->name ?? 'N/A' }}</span>
                        </div>
                        <div class="doc-info-row">
                            <span class="doc-info-label">Filing:</span>
                            <span class="doc-info-value">{{ $application->date_filing->format('M d, Y') }}</span>
                        </div>
                        <div class="doc-info-row">
                            <span class="doc-info-label">Name:</span>
                            <span class="doc-info-value uppercase">{{ $application->user->last_name }}, {{ $application->user->first_name }}</span>
                        </div>
                        <div class="doc-info-row border-none">
                            <span class="doc-info-label">Position:</span>
                            <span class="doc-info-value">{{ $application->user->position }}</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Type & Details of Leave -->
                <div class="review-section animate__animated animate__backInUp" style="animation-delay: 0.3s; margin-bottom: 0;">
                    <div class="review-card-header">
                        <div class="review-card-icon"><i class="fas fa-file-signature"></i></div>
                        <h3>Leave Type & Details</h3>
                    </div>
                    <div class="review-card-body">
                        <div class="sub-section-label-premium">6.A Type of Leave to be Availed Of</div>
                        <div class="leave-type-display-premium" style="margin-bottom: 0;">
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
                            <div class="doc-section-divider">6.C Typical Date(s)</div>
                            <div class="date-grid-fancy mt-4">
                                @foreach($applicationDates as $dStr)
                                    @php $d = \Carbon\Carbon::parse($dStr); @endphp
                                    <div class="date-card-fancy">
                                        <span class="df-month">{{ $d->format('M') }}</span>
                                        <span class="df-day">{{ $d->format('d') }}</span>
                                        <span class="df-year">{{ $d->format('Y') }}</span>
                                    </div>
                                @endforeach
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
                    <div class="review-grid-{{ $application->asds_id ? 'four' : 'three' }}-col">
                        <!-- 7.A HR Certification -->
                        <div class="sig-box">
                            <div class="sig-label-top">7.A Cert. of Leave Credits</div>
                            @php
                                $displayVerifier = $application->hrVerifier;
                                if ($displayVerifier && $displayVerifier->role === 'hr_review_officer') {
                                    $displayVerifier = \App\Models\User::whereIn('role', ['head_hr', 'hr'])
                                        ->where('is_active', true)
                                        ->whereNotNull('esignature')
                                        ->first() ?: $displayVerifier;
                                }
                            @endphp
                            <div class="signature-area-premium">
                                @if($application->hr_verified_at)
                                    <div class="mb-2 text-center">
                                        <div class="status-badge-premium certified mb-4">
                                            <i class="fas fa-check-circle"></i> Certified {{ $application->hr_verified_at->format('M d, Y') }}
                                        </div>
                                        @if($displayVerifier && $displayVerifier->esignature)
                                            <div class="mb-2">
                                                <img src="{{ storage_url($displayVerifier->esignature) }}" class="esign-premium" alt="Signature">
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
                                    <div class="font-bold uppercase text-xs text-slate-800 tracking-tight">{{ $displayVerifier->full_name ?? 'Verifying Officer' }}</div>
                                    <div class="text-[0.55rem] text-slate-400 font-black uppercase tracking-widest mt-0.5">{{ $displayVerifier->position ?? 'Administrative Officer' }}</div>
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
                                            <div class="mb-2">
                                                <img src="{{ storage_url($application->recommendingOfficer->esignature) }}" class="esign-premium" alt="Signature">
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
                                    <div class="font-bold uppercase text-xs text-slate-800 tracking-tight">{{ $application->recommendingOfficer->full_name }}</div>
                                    <div class="text-[0.55rem] text-slate-400 font-black uppercase tracking-widest mt-0.5">{{ $application->recommendingOfficer->position }}</div>
                                </div>
                            @endif
                        </div>

                        <!-- 7.B.2 ASDS Approval (If applicable) -->
                        @if($application->asds_id)
                        <div class="sig-box">
                            <div class="sig-label-top">7.B ASDS 1st Approval</div>
                            <div class="signature-area-premium">
                                @if($application->asds_approved_at)
                                    <div class="mb-2 text-center">
                                        <div class="status-badge-premium approved mb-4">
                                            <i class="fas fa-check-double"></i> ASDS Signed {{ $application->asds_approved_at->format('M d, Y') }}
                                        </div>
                                        @if($application->asds && $application->asds->esignature)
                                            <div class="mb-2">
                                                <img src="{{ storage_url($application->asds->esignature) }}" class="esign-premium" alt="Signature">
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="signature-placeholder-premium">
                                        <i class="fas fa-shield-alt"></i>
                                        <span>Awaiting ASDS</span>
                                    </div>
                                @endif
                            </div>
                            @if($application->asds_approved_at)
                                <div class="text-center">
                                    <div class="font-bold uppercase text-xs text-slate-800 tracking-tight">{{ $application->asds->full_name }}</div>
                                    <div class="text-[0.55rem] text-slate-400 font-black uppercase tracking-widest mt-0.5">{{ $application->asds->position }}</div>
                                </div>
                            @endif
                        </div>
                        @endif

                        <!-- 7.C/D Final Approval -->
                        <div class="sig-box bg-slate-50/30 border-slate-200">
                            <div class="sig-label-top">7.C / 7.D Final Executive Approval</div>
                            <div class="signature-area-premium">
                                @if($application->approved_at)
                                    <div class="mb-2 text-center">
                                        <div class="status-badge-premium approved mb-4">
                                            <i class="fas fa-shield-check"></i> Exec Signed {{ $application->approved_at->format('M d, Y') }}
                                        </div>
                                        @if($application->approvingOfficer && $application->approvingOfficer->esignature)
                                            <div class="mb-2">
                                                <img src="{{ storage_url($application->approvingOfficer->esignature) }}" class="esign-premium" alt="Signature">
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    @if($application->hr_verified_at)
                                        <div class="p-3 bg-white/80 rounded-xl border border-slate-200/60 shadow-sm mb-3 w-full">
                                            <div class="space-y-1.5">
                                                <div class="flex justify-between items-center bg-slate-50/50 p-1.5 rounded-lg border border-slate-100/50">
                                                    <span class="text-[0.55rem] font-black text-slate-400 uppercase">With Pay</span>
                                                    <span class="font-black text-blue-600 text-xs">{{ $application->days_with_pay ?? 0 }} <span class="text-[0.5rem]">D</span></span>
                                                </div>
                                                <div class="flex justify-between items-center bg-slate-50/50 p-1.5 rounded-lg border border-slate-100/50">
                                                    <span class="text-[0.55rem] font-black text-slate-400 uppercase">W/O Pay</span>
                                                    <span class="font-black text-slate-700 text-xs">{{ $application->days_without_pay ?? 0 }} <span class="text-[0.5rem]">D</span></span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="signature-placeholder-premium border-slate-200 bg-slate-50/50">
                                        <i class="fas fa-pen-nib"></i>
                                        <span>Pending Signature</span>
                                    </div>
                                @endif
                            </div>
                            @if($application->approved_at)
                                <div class="text-center">
                                    <div class="font-black uppercase text-sm text-blue-950 tracking-tight leading-none mb-1">{{ $application->approvingOfficer->full_name }}</div>
                                    <div class="text-[0.55rem] text-blue-400 font-black uppercase tracking-[0.1em]">{{ $application->approvingOfficer->position }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- --- RIGHT: SIDEBAR --- -->
        <div class="flex flex-col gap-6">

            <!-- Past Date Warning (If applicable) -->
            @if($application->end_date && $application->end_date->isPast() && $application->status !== 'Approved' && $application->status !== 'Rejected')
                <div class="deficit-critical-card mb-6 animate__animated animate__shakeX" style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); border: none; margin-bottom: 24px; box-shadow: 0 10px 20px rgba(239, 68, 68, 0.3); padding: 20px;">
                    <div style="display: flex; gap: 16px; align-items: flex-start;">
                        <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(255, 255, 255, 0.2); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem; flex-shrink: 0;">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        <div style="display: flex; flex-direction: column;">
                            <span style="font-size: 0.65rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255, 255, 255, 0.8); margin-bottom: 4px;">Post-Date Notification</span>
                            <p style="font-size: 0.75rem; font-weight: 700; color: white; margin: 0; line-height: 1.4;">This leave period has already concluded. Please ensure this late verification meets regulatory guidelines.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Credit Analysis Card -->
            <div class="sidebar-modular-card animate__animated animate__fadeInRight" style="animation-delay: 0.6s;">
                <div class="sidebar-modular-header text-blue-900">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600"><i class="fas fa-calculator"></i></div>
                    <span>Credit Analysis</span>
                </div>

                <div class="sidebar-content-modular flex flex-col gap-6">
                    @php
                        $hasInsufficient = false;
                        foreach($displayPools as $pool) {
                            if($pool['balance'] < 0) { $hasInsufficient = true; break; }
                        }
                    @endphp

                    @if($hasInsufficient)
                        <div class="deficit-critical-card mb-12" style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); border: none; margin-bottom: 40px !important; box-shadow: 0 10px 20px rgba(239, 68, 68, 0.3); padding: 20px;">
                            <div style="display: flex; gap: 16px; align-items: flex-start;">
                                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(255, 255, 255, 0.2); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem; flex-shrink: 0;">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-size: 0.65rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255, 255, 255, 0.8); margin-bottom: 4px;">Deficit Warning</span>
                                    <p style="font-size: 0.75rem; font-weight: 700; color: white; margin: 0; line-height: 1.4;">This application will result in negative leave credits if approved.</p>
                                    <p style="font-size: 0.65rem; font-weight: 500; color: rgba(255, 255, 255, 0.9); margin-top: 6px; font-style: italic; line-height: 1.4;">
                                        Note: Negative results in credit may reflect on the salary deduction.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($application->status === 'Approved')
                        <div class="premium-note-box info mb-10 animate__animated animate__fadeIn">
                            <div class="premium-note-icon">
                                <i class="fas fa-history"></i>
                            </div>
                            <div class="premium-note-content">
                                <span class="premium-note-title">Post-Approval Snapshot</span>
                                <p class="premium-note-text">This view shows the credit deduction that occurred upon final approval.</p>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-8">
                        @forelse($displayPools as $pool)
                            <!-- Dynamic Credit Pool -->
                            <div class="credit-pool-card-premium">
                                <div class="credit-pool-label">
                                    <i class="{{ $pool['icon'] }} {{ $pool['color'] }}"></i> {{ $pool['name'] }} Pool
                                </div>
                                <div class="space-y-1">
                                    <div class="credit-row-premium">
                                        <span class="text-[0.7rem] font-bold text-slate-400">{{ $application->status === 'Approved' ? 'Balance Before' : 'Current Balance' }}</span>
                                        <span class="credit-val-dim">{{ (float) $pool['current'] }}</span>
                                    </div>
                                    <div class="credit-row-premium">
                                        <span class="text-[0.7rem] font-bold text-slate-400">{{ $application->status === 'Approved' ? 'Deducted Credits' : 'Less This App' }}</span>
                                        <span class="credit-val-impact">-{{ (float) $pool['less'] }}</span>
                                    </div>
                                    <div class="credit-balance-box">
                                        <span class="text-[0.6rem] font-black text-slate-800 uppercase tracking-tighter">{{ $application->status === 'Approved' ? 'Post-Approval Balance' : 'New Balance' }}</span>
                                        <span class="text-xl font-black {{ $pool['balance'] < 0 ? 'text-red-600' : 'text-green-600' }} font-mono leading-none">{{ (float) $pool['balance'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="premium-note-box empty">
                                <div class="premium-note-icon">
                                    <i class="fas fa-database"></i>
                                </div>
                                <div class="premium-note-content">
                                    <span class="premium-note-title">No Tracked Credits</span>
                                    <p class="premium-note-text">This leave type does not track specific credit balances in the system.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="sidebar-modular-card animate__animated animate__fadeInRight" style="animation-delay: 0.7s;">
                <div class="sidebar-modular-header">
                    <i class="fas fa-bolt"></i>
                    <span>Authority Actions</span>
                </div>

                <div class="sidebar-content-modular authority-actions-wrapper">
                    @php
                        $user = Auth::user();
                        $role = $user->role;
                        $status = $application->status;
                        $canAct = false;
                        $actionType = '';
                        if (in_array($role, ['hr', 'head_hr', 'hr_review_officer', 'super_admin']) && $status === 'Pending HR') $actionType = 'verify';
                        elseif ($application->recommending_officer_id == $user->id && $status === 'Pending Recommending') $actionType = 'recommend';
                        elseif ($application->asds_id == $user->id && $status === 'Pending ASDS Approval') $actionType = 'asds-approve';
                        elseif ($application->approving_officer_id == $user->id && $status === 'Pending Approval') $actionType = 'approve';
                        if ($actionType) $canAct = true;
                    @endphp
                    @if($canAct)
                        @if($actionType === 'verify')
                            <div class="action-form-container">
                                <form action="{{ route('user.leave.verify', $application->id) }}" id="verifyForm" method="POST" class="w-full space-y-4">
                                    @csrf
                                    <div class="bg-slate-50/50 p-6 rounded-3xl border border-slate-100 space-y-5">
                                        <div class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2 mb-2"><i class="fas fa-file-signature text-blue-400"></i> Verification Form</div>
                                        
                                        <div class="verification-date-board-premium">
                                            <div class="flex justify-between items-center mb-4">
                                                <span class="text-[0.6rem] font-black text-slate-500 uppercase tracking-wider">Date Breakdown Selection</span>
                                                <div class="flex gap-2">
                                                    <div class="text-[0.6rem] font-bold px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full" id="paid-total-pill">0.0 Paid</div>
                                                    <div class="text-[0.6rem] font-bold px-2 py-0.5 bg-slate-200 text-slate-600 rounded-full" id="unpaid-total-pill">0.0 Unpaid</div>
                                                </div>
                                            </div>

                                            <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar-premium">
                                                @foreach($applicationDates as $index => $date)
                                                    @php $d = \Carbon\Carbon::parse($date); @endphp
                                                    <div class="date-selector-row-premium">
                                                        <div class="flex flex-col">
                                                            <span class="text-[0.7rem] font-bold text-slate-700 leading-none">{{ $d->format('D, M d') }}</span>
                                                            <span class="text-[0.55rem] text-slate-400 font-bold uppercase mt-0.5">{{ $d->format('Y') }}</span>
                                                        </div>
                                                        <div class="pay-toggle-group-premium">
                                                            <input type="radio" id="p_{{ $index }}" name="date_pay_{{ $index }}" value="1" class="pay-radio-hidden" checked onchange="calculateVerificationTotals()">
                                                            <label for="p_{{ $index }}" class="pay-toggle-btn-premium paid">Paid</label>
                                                            
                                                            <input type="radio" id="u_{{ $index }}" name="date_pay_{{ $index }}" value="0" class="pay-radio-hidden" onchange="calculateVerificationTotals()">
                                                            <label for="u_{{ $index }}" class="pay-toggle-btn-premium unpaid">Unpaid</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <input type="hidden" name="days_with_pay" id="days_with_pay_hidden" value="{{ $application->days_with_pay }}">
                                        <input type="hidden" name="days_without_pay" id="days_without_pay_hidden" value="{{ $application->days_without_pay }}">

                                        <div>
                                            <label class="action-label-premium">Remarks / Internal Notes</label>
                                            <input type="text" name="others_remarks" value="{{ $application->others_remarks }}"
                                                class="action-input-premium shadow-sm" placeholder="Add remarks here...">
                                        </div>
                                    </div>
                                    <button type="button" onclick="triggerActionConfirm('verifyForm', 'Confirm HR Verification', 'Are you sure you want to verify these leave credits and dates? This will finalize the HR certification step.')" class="btn-review-primary shadow-xl shadow-blue-500/10 py-4">
                                        <i class="fas fa-check-double scale-125"></i> <span class="tracking-tight">Verify Application</span>
                                    </button>
                                </form>
                            </div>
                        @elseif($actionType === 'recommend')
                             <div class="action-form-container">
                                <form action="{{ route('user.leave.recommend', $application->id) }}" id="recommendForm" method="POST" class="w-full">
                                    @csrf
                                    <button type="button" onclick="triggerActionConfirm('recommendForm', 'Submit Recommendation', 'Are you sure you want to submit your recommendation for this application?')" class="btn-review-primary shadow-xl shadow-blue-500/10">
                                        <i class="fas fa-award scale-125"></i> <span class="tracking-tight">Submit Recommendation</span>
                                    </button>
                                </form>
                             </div>
                        @elseif($actionType === 'asds-approve')
                             <div class="action-form-container">
                                <form action="{{ route('user.leave.asds-approve', $application->id) }}" id="asdsApproveForm" method="POST" class="w-full">
                                    @csrf
                                    <button type="button" onclick="triggerActionConfirm('asdsApproveForm', 'Grant ASDS Approval', 'Are you sure you want to grant ASDS 1st approval for this application? It will then move to SDS for final approval.')" class="btn-review-primary shadow-xl shadow-blue-500/10">
                                        <i class="fas fa-check-double scale-125"></i> <span class="tracking-tight">Approve (ASDS)</span>
                                    </button>
                                </form>
                             </div>
                        @elseif($actionType === 'approve')
                             <div class="action-form-container">
                                <form action="{{ route('user.leave.approve', $application->id) }}" id="approveForm" method="POST" class="w-full">
                                    @csrf
                                    <button type="button" onclick="triggerActionConfirm('approveForm', 'Grant Final Approval', 'Are you sure you want to grant final approval for this application? This action cannot be undone.')" class="btn-review-primary shadow-xl shadow-green-500/10 !bg-green-600">
                                        <i class="fas fa-signature scale-125"></i> <span class="tracking-tight">Grant Final Approval</span>
                                    </button>
                                </form>
                             </div>
                        @endif

                        <div class="action-form-container">
                            <button id="openRejectModalBtn" type="button" class="btn-review-reject w-full">
                                <i class="fas fa-undo mr-2"></i> Reject & Return
                            </button>
                        </div>
                    @else
                        <div class="premium-note-box empty mt-2">
                            <div class="premium-note-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="premium-note-content">
                                <span class="premium-note-title">No Actions Needed</span>
                                <p class="premium-note-text">There are no pending actions for your role at this stage of the workflow.</p>
                            </div>
                        </div>
                    @endif


                </div>
            </div>

        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="modal-backdrop">
        <div class="modal-content-modular">
            <div class="text-left">
                <h3 class="modal-title-modular">Confirm Rejection</h3>
                <p class="modal-desc-modular">
                    Please provide a clear reason for rejecting or returning this application. This helps the applicant understand what needs correction.
                </p>

                <form action="{{ route('user.leave.reject', $application->id) }}" method="POST" id="rejectForm">
                    @csrf
                    <textarea name="remarks" required class="modal-textarea-modular"
                        placeholder="e.g., Please attach the required medical certificate for sick leave..."></textarea>

                    <div class="modal-footer-modular">
                        <button type="button" id="cancelRejectModalBtn" class="btn-modal-cancel">
                            Cancel
                        </button>
                        <button type="submit" class="btn-modal-confirm">
                            Confirm Rejection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Action Confirmation Modal (Premium Redesign) -->
    <div id="confirmActionModal" class="modal-backdrop">
        <div class="modal-content-modular-premium">
            <div class="modal-header-premium">
                <div class="modal-header-left">
                    <div class="modal-header-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3 class="modal-header-title" id="confirmActionTitle">Confirm Action</h3>
                </div>
                <button type="button" onclick="closeConfirmModal()" class="modal-close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body-premium">
                <p class="modal-body-text" id="confirmActionDesc">
                    Are you sure you want to proceed with this authority action?
                </p>

                <!-- Info Box -->
                <div class="modal-info-box">
                    <div class="flex flex-col gap-1">
                        <span class="modal-info-main" id="confirmActionTracking">---</span>
                        <span class="modal-info-sub" id="confirmActionName">---</span>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer-premium">
                <button type="button" onclick="closeConfirmModal()" class="btn-action-cancel">
                    Cancel
                </button>
                <button type="button" id="finalConfirmBtn" class="btn-action-confirm">
                    <i class="fas fa-check"></i> <span id="confirmActionBtnText">Yes, proceed</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentActionForm = null;

        function triggerActionConfirm(formId, title, message) {
            currentActionForm = document.getElementById(formId);
            document.getElementById('confirmActionTitle').innerText = title;
            document.getElementById('confirmActionDesc').innerText = message;
            
            // Populate tracking and name from existing elements
            const trackingNo = "{{ $application->tracking_no ?? 'UNTRACKED' }}";
            const applicantName = "{{ $application->user->full_name }}";
            document.getElementById('confirmActionTracking').innerText = trackingNo;
            document.getElementById('confirmActionName').innerText = applicantName;

            const confirmModal = document.getElementById('confirmActionModal');
            confirmModal.classList.add('active');

            // Apply specific button text and color
            const finalBtn = document.getElementById('finalConfirmBtn');
            const btnText = document.getElementById('confirmActionBtnText');

            if (formId === 'approveForm') {
                finalBtn.style.backgroundColor = '#10b981'; // Green
                btnText.innerText = "Yes, Approve";
            } else if (formId === 'recommendForm') {
                finalBtn.style.backgroundColor = '#1b4a9a'; // Blue
                btnText.innerText = "Yes, Recommend";
            } else if (formId === 'asdsApproveForm') {
                finalBtn.style.backgroundColor = '#1b4a9a'; // Blue-600
                btnText.innerText = "Yes, ASDS Approve";
            } else {
                finalBtn.style.backgroundColor = '#1b4a9a'; // Blue
                btnText.innerText = "Yes, Verify";
            }
        }

        function closeConfirmModal() {
            document.getElementById('confirmActionModal').classList.remove('active');
        }

        document.getElementById('finalConfirmBtn').addEventListener('click', function() {
            if (currentActionForm) {
                currentActionForm.submit();
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const rejectModal = document.getElementById('rejectModal');
            const rejectBtn = document.getElementById('openRejectModalBtn');
            const cancelBtn = document.getElementById('cancelRejectModalBtn');

            if (rejectBtn) {
                rejectBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    rejectModal.classList.add('active');
                });
            }

            if (cancelBtn) {
                cancelBtn.addEventListener('click', function () {
                    rejectModal.classList.remove('active');
                });
            }

            // Close modal when clicking outside
            rejectModal.addEventListener('click', function (e) {
                if (e.target === rejectModal) {
                    rejectModal.classList.remove('active');
                }
            });

            // Close confirm modal when clicking outside
            const confirmActionModal = document.getElementById('confirmActionModal');
            confirmActionModal.addEventListener('click', function (e) {
                if (e.target === confirmActionModal) {
                    closeConfirmModal();
                }
            });
        });

        function calculateVerificationTotals() {
            const paidRadios = document.querySelectorAll('.pay-radio-hidden[value="1"]:checked');
            const unpaidRadios = document.querySelectorAll('.pay-radio-hidden[value="0"]:checked');
            
            const totalPaid = paidRadios.length;
            const totalUnpaid = unpaidRadios.length;
            
            document.getElementById('days_with_pay_hidden').value = totalPaid;
            document.getElementById('days_without_pay_hidden').value = totalUnpaid;
            
            document.getElementById('paid-total-pill').innerText = totalPaid.toFixed(1) + ' Paid';
            document.getElementById('unpaid-total-pill').innerText = totalUnpaid.toFixed(1) + ' Unpaid';
        }

        // Initialize on load
        window.addEventListener('load', calculateVerificationTotals);
    </script>
@endsection
