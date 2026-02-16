@extends('layouts.sdo')

@section('title', 'Review Application')
@section('page-title', 'Review Application')

@push('styles')
<style>
    /* Ensure modal shows as overlay */
    #rejectModal {
        display: none; /* Default hidden */
        position: fixed;
        inset: 0;
        z-index: 9999;
        background-color: rgba(0,0,0,0.5);
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    
    /* When active (not hidden) */
    #rejectModal:not(.hidden) {
        display: flex !important;
    }

    /* Modal Content */
    #rejectModal > div {
        background: white;
        width: 100%;
        max-width: 500px;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        position: relative;
        z-index: 10000;
        animation: modal-enter 0.2s ease-out;
    }
    
    @keyframes modal-enter {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }

    /* PAGE LAYOUT GRID */
    .page-layout {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 24px;
        align-items: start;
        margin-top: 20px;
    }
    @media(max-width: 1024px) {
        .page-layout { grid-template-columns: 1fr; }
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
    .v-step:last-child { margin-bottom: 0; }
    .v-step-marker {
        position: absolute;
        left: -27px; /* Adjust based on border/padding */
        top: 0;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #cbd5e1;
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px #cbd5e1;
    }
    .v-step.active .v-step-marker { background: #3b82f6; box-shadow: 0 0 0 1px #3b82f6; }
    .v-step.completed .v-step-marker { background: #22c55e; box-shadow: 0 0 0 1px #22c55e; }
    .v-step.rejected .v-step-marker { background: #ef4444; box-shadow: 0 0 0 1px #ef4444; }

    .v-step-content { }
    .v-step-title { font-size: 0.9rem; font-weight: 600; color: #334155; }
    .v-step-desc { font-size: 0.8rem; color: #64748b; margin-top: 2px; }

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
    .status-pending { background: #fff7ed; color: #c2410c; }
    .status-approved { background: #f0fdf4; color: #15803d; }
    .status-rejected { background: #fef2f2; color: #b91c1c; }

    /* DOCUMENT STYLING */
    .doc-header {
        text-align: center;
        border-bottom: 2px solid #1e293b;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }
    .doc-title { font-weight: 800; text-transform: uppercase; font-size: 1.25rem; color: #0f172a; }
    .doc-subtitle { font-size: 0.9rem; color: #64748b; margin-top: 4px; }
    
    .doc-section { margin-bottom: 30px; }
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
    .doc-label { font-weight: 600; font-size: 0.9rem; color: #64748b; width: 180px; flex-shrink: 0; }
    .doc-label-short { width: 100px; }
    .doc-value { font-weight: 500; font-size: 0.95rem; color: #1e293b; flex-grow: 1; }
    
    /* CREDIT ANALYSIS STYLES */
    .credit-summary-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
        padding: 4px 0;
        border-bottom: 1px dashed #e2e8f0;
    }
    .credit-summary-row:last-child { border-bottom: none; }
    .credit-val { font-family: monospace; font-weight: 600; }
    .text-negative { color: #dc2626; }
    .bg-warning-light { background-color: #fff1f2; border: 1px solid #fecaca; padding: 10px; border-radius: 6px; margin-bottom: 15px; }

</style>
@endpush

@section('content')

<!-- --- HEADER --- -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <div class="flex items-center gap-3">
            <a href="{{ route('user.leave.approvals') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Review Application</h1>
        </div>
        <p class="text-sm text-gray-500 ml-7">Applicant: <span class="font-semibold">{{ $application->user->full_name }}</span></p>
    </div>
    
     <div class="flex items-center gap-3">
        @php
            $badgeClass = 'status-pending';
            if (stripos($application->status, 'approve') !== false && stripos($application->status, 'pending') === false) $badgeClass = 'status-approved';
            if (stripos($application->status, 'reject') !== false || stripos($application->status, 'disapprove') !== false) $badgeClass = 'status-rejected';
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
                    <img src="{{ asset('assets/images/deped_logo.png') }}" class="h-16 w-auto" alt="Logo" onerror="this.style.display='none'">
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
                    <span class="doc-value uppercase">{{ $application->user->last_name }}, {{ $application->user->first_name }} {{ substr($application->user->middle_name ?? '', 0, 1) }}.</span>
                </div>
                <div class="doc-row">
                    <span class="doc-label">Date of Filing:</span>
                    <span class="doc-value">{{ $application->date_filing->format('F d, Y') }}</span>
                </div>
                <div class="doc-row">
                    <span class="doc-label">Position:</span>
                    <span class="doc-value">{{ $application->user->position }}</span>
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
                            <div class="mb-2"><strong>Vacation Location:</strong> {{ $application->details->vacation_loc_type }} - {{ $application->details->vacation_loc_details }}</div>
                        @endif
                        @if($application->details->sick_loc_type)
                            <div class="mb-2"><strong>Sick Leave:</strong> {{ $application->details->sick_loc_type }} - {{ $application->details->sick_illness }}</div>
                        @endif
                        @if($application->details->women_illness)
                            <div class="mb-2"><strong>Special Leave Benefit:</strong> {{ $application->details->women_illness }}</div>
                        @endif
                        @if($application->details->study_type)
                             <div class="mb-2"><strong>Study Leave:</strong> {{ $application->details->study_type }} - {{ $application->details->study_details }}</div>
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
                            {{ $application->start_date->format('M d, Y') }} - {{ $application->end_date->format('M d, Y') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="doc-section">
                <div class="doc-section-title">6.D Commutation</div>
                <div class="pl-4">
                     <span class="font-medium  {{ $application->commutation == 'Requested' ? 'text-blue-700' : 'text-gray-600' }}">
                         <i class="fas {{ $application->commutation == 'Requested' ? 'fa-check-circle' : 'fa-circle' }} mr-2"></i> Requested
                     </span>
                     <span class="font-medium ml-6 {{ $application->commutation == 'Not Requested' ? 'text-blue-700' : 'text-gray-600' }}">
                         <i class="fas {{ $application->commutation == 'Not Requested' ? 'fa-check-circle' : 'fa-circle' }} mr-2"></i> Not Requested
                     </span>
                </div>
            </div>
            
             <!-- Signatories Preview -->
             <div class="mt-8 pt-8 border-t border-gray-200 flex justify-center">
                <div class="text-left group relative w-max mx-auto">
                    {{-- Check if signature exists --}}
                    @if($application->user->esignature)
                        <div class="mb-1 relative h-16 w-full flex items-end justify-start">
                            <img src="{{ asset($application->user->esignature) }}" alt="Signature" class="h-full max-w-[200px] object-contain object-left scale-150 origin-bottom-left -ml-2 mb-2">
                        </div>
                        <div class="font-bold underline uppercase text-sm">{{ $application->user->full_name }}</div>
                    @else
                          <div class="h-16 flex items-end justify-start text-gray-400 text-sm italic mb-1">
                            (No Signature)
                        </div>
                        <div class="font-bold underline uppercase">{{ $application->user->full_name }}</div>
                    @endif
                    <div class="text-xs text-gray-500 mt-1">Signature of Applicant</div>
                </div>
            </div>

        </div>
    </div>
    
    <!-- --- RIGHT: SIDEBAR --- -->
    <div class="flex flex-col">
        
        <!-- Credit Analysis Card (Important for Approvers) -->
        <div class="sidebar-card">
            <div class="sidebar-header"><i class="fas fa-calculator"></i> Credit Analysis</div>
            
            @php
                $vl = $credits['vl'];
                $sl = $credits['sl'];
                $insufficient = ($vl['balance'] < 0 || $sl['balance'] < 0);
            @endphp
            
            @if($insufficient)
                <div class="bg-warning-light text-xs text-red-800">
                    <i class="fas fa-exclamation-triangle mr-1"></i> <strong>Deficit Warning:</strong> This application will result in negative leave credits.
                </div>
            @endif
            
            <div class="mb-4">
                <div class="text-xs font-bold text-gray-500 uppercase mb-2">Vacation Leave</div>
                <div class="credit-summary-row">
                    <span>Current:</span> <span class="credit-val">{{ (float)$vl['current'] }}</span>
                </div>
                 <div class="credit-summary-row">
                    <span>Less This App:</span> <span class="credit-val text-blue-600">{{ (float)$vl['less'] }}</span>
                </div>
                 <div class="credit-summary-row border-t border-gray-300 pt-1 mt-1">
                    <span class="font-bold">Balance:</span> 
                    <span class="credit-val {{ $vl['balance'] < 0 ? 'text-negative' : 'text-green-600' }}">{{ (float)$vl['balance'] }}</span>
                </div>
            </div>

            <div>
                <div class="text-xs font-bold text-gray-500 uppercase mb-2">Sick Leave</div>
                <div class="credit-summary-row">
                    <span>Current:</span> <span class="credit-val">{{ (float)$sl['current'] }}</span>
                </div>
                 <div class="credit-summary-row">
                    <span>Less This App:</span> <span class="credit-val text-blue-600">{{ (float)$sl['less'] }}</span>
                </div>
                 <div class="credit-summary-row border-t border-gray-300 pt-1 mt-1">
                    <span class="font-bold">Balance:</span> 
                    <span class="credit-val {{ $sl['balance'] < 0 ? 'text-negative' : 'text-green-600' }}">{{ (float)$sl['balance'] }}</span>
                </div>
            </div>
        </div>

        <!-- Actions Card -->
        <div class="sidebar-card">
            <div class="sidebar-header"><i class="fas fa-bolt"></i> Actions</div>
            
            @php
                $user = Auth::user();
                $role = $user->role;
                $status = $application->status;
                $canAct = false;
                
                // Determine Action Type
                $actionType = '';
                if(in_array($role, ['hr', 'head_hr', 'super_admin']) && $status === 'Pending HR') $actionType = 'verify';
                elseif($application->recommending_officer_id == $user->id && $status === 'Pending Recommending') $actionType = 'recommend';
                elseif($application->approving_officer_id == $user->id && $status === 'Pending Approval') $actionType = 'approve';
                
                if($actionType) $canAct = true;
            @endphp
            
            <div class="flex flex-col gap-3">
                @if($canAct)
                    @if($actionType === 'verify')
                        <form action="{{ route('user.leave.verify', $application->id) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                                <i class="fas fa-check-circle mr-2"></i> Verify Application
                            </button>
                        </form>
                    @elseif($actionType === 'recommend')
                        <form action="{{ route('user.leave.recommend', $application->id) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                                <i class="fas fa-thumbs-up mr-2"></i> Recommend
                            </button>
                        </form>
                    @elseif($actionType === 'approve')
                        <form action="{{ route('user.leave.approve', $application->id) }}" method="POST" class="w-full">
                            @csrf
                            
                            @if(in_array($role, ['asds', 'sds', 'super_admin'])) {{-- Added super_admin for testing if needed, or strictly asds/sds --}}
                                <div class="mb-4 bg-gray-50 p-3 rounded border border-gray-200 text-sm">
                                    <div class="font-bold text-gray-700 mb-2 text-xs uppercase">7. C Recommendations</div>
                                    
                                    <div class="mb-2">
                                        <label class="block text-gray-600 text-xs mb-1">For approval without pay (Days)</label>
                                        <input type="number" name="days_without_pay" step="0.5" min="0" class="w-full border border-gray-300 rounded text-sm p-1.5 focus:ring-blue-500 focus:border-blue-500" placeholder="Number of days">
                                    </div>
                                     <div class="mb-2">
                                        <label class="block text-gray-600 text-xs mb-1">For approval with pay (Days)</label>
                                        <input type="number" name="days_with_pay" step="0.5" min="0" class="w-full border border-gray-300 rounded text-sm p-1.5 focus:ring-blue-500 focus:border-blue-500" placeholder="Number of days">
                                    </div>
                                     <div class="mb-2">
                                        <label class="block text-gray-600 text-xs mb-1">Others (Specify)</label>
                                        <input type="text" name="others_remarks" class="w-full border border-gray-300 rounded text-sm p-1.5 focus:ring-blue-500 focus:border-blue-500" placeholder="Specify details">
                                    </div>
                                </div>
                            @endif

                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition">
                                <i class="fas fa-signature mr-2"></i> Final Approve
                            </button>
                        </form>
                    @endif
                    
                    <button id="openRejectModalBtn" type="button" class="w-full bg-white border border-red-200 text-red-600 hover:bg-red-50 font-bold py-2 px-4 rounded transition">
                        <i class="fas fa-times-circle mr-2"></i> Reject / Return
                    </button>
                @elseif(in_array($role, ['hr', 'head_hr', 'super_admin']) || $application->recommending_officer_id == $user->id || $application->approving_officer_id == $user->id)
                    <div class="text-sm text-gray-500 text-center italic mb-2">
                        Status: {{ $status }}
                    </div>
                @else
                    <div class="text-sm text-gray-500 text-center italic">
                        No pending actions for you.
                    </div>
                @endif
                
                 <hr class="my-2 border-gray-100">
                 
                 <a href="{{ route('user.leave.form6', ['id' => $application->id, 'format' => 'pdf']) }}" target="_blank" class="text-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                    <i class="fas fa-file-pdf mr-1"></i> Preview PDF
                 </a>
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
                
                $s2 = $recoComplete ? 'completed' : ($application->hr_verified_at ? 'active' : '');
                $s3 = $application->approved_at ? 'completed' : ($recoComplete ? 'active' : '');
                
                if ($isRejected) {
                     if (!$application->hr_verified_at) $s1 = 'rejected';
                     else if (!$recoComplete) $s2 = 'rejected';
                     else $s3 = 'rejected';
                }
            @endphp
            
            <div class="v-stepper">
                <!-- Step 1: Pending (Filed) -->
                 <div class="v-step completed">
                    <div class="v-step-marker"></div>
                    <div class="v-step-content">
                        <div class="v-step-title">Application Filed</div>
                        <div class="v-step-desc">
                            {{ $application->created_at->format('M d, Y') }}
                        </div>
                    </div>
                </div>

                <!-- Step 2: HR Verify -->
                <div class="v-step {{ $s1 }}">
                    <div class="v-step-marker"></div>
                    <div class="v-step-content">
                        <div class="v-step-title">HR Verification</div>
                        <div class="v-step-desc">
                            {{ $application->hr_verified_at ? $application->hr_verified_at->format('M d, Y') : ($s1 == 'active' ? 'Pending Action...' : 'Waiting') }}
                        </div>
                    </div>
                </div>
                
                <!-- Step 3: Recommendation -->
                <div class="v-step {{ $s2 }}">
                    <div class="v-step-marker"></div>
                    <div class="v-step-content">
                        <div class="v-step-title">Recommendation</div>
                         <div class="v-step-desc">
                            {{ $application->recommended_at ? $application->recommended_at->format('M d, Y') : ($s2 == 'active' ? 'Pending Action...' : 'Waiting') }}
                        </div>
                    </div>
                </div>
                
                <!-- Step 4: Approval -->
                <div class="v-step {{ $s3 }}">
                    <div class="v-step-marker"></div>
                    <div class="v-step-content">
                        <div class="v-step-title">Final Approval</div>
                         <div class="v-step-desc">
                            {{ $application->approved_at ? $application->approved_at->format('M d, Y') : ($s3 == 'active' ? 'Pending Action...' : 'Waiting') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden" style="background-color: rgba(0,0,0,0.5); position: fixed; inset: 0; z-index: 9999;">
    <!-- Modal Content -->
    <div style="background-color: white; width: 100%; max-width: 500px; margin: auto; padding: 20px; border-radius: 12px; position: relative; top: 50%; transform: translateY(-50%); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
        <div class="mt-2 text-center sm:text-left">
            <h3 class="text-xl leading-6 font-bold text-gray-900 mb-2" style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; display: block;">Confirm Rejection</h3>
            <p class="text-sm text-gray-500 mb-4" style="color: #6b7280; margin-bottom: 1rem; display: block;">
                Please provide a reason for rejecting or returning this application. This will be visible to the applicant.
            </p>
            
            <form action="{{ route('user.leave.reject', $application->id) }}" method="POST" id="rejectForm">
                @csrf
                <textarea name="remarks" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.875rem; min-height: 100px; display: block;" placeholder="Enter rejection reason here..."></textarea>
                
                <div class="flex gap-3 mt-5 justify-end" style="display: flex; gap: 12px; margin-top: 20px; justify-content: flex-end;">
                    <button type="button" id="cancelRejectModalBtn" style="padding: 8px 16px; background-color: #f3f4f6; color: #374151; font-weight: 600; border-radius: 8px; border: 1px solid #d1d5db; cursor: pointer;">
                        Cancel
                    </button>
                    <button type="submit" style="padding: 8px 16px; background-color: #dc2626; color: white; font-weight: 600; border-radius: 8px; border: none; cursor: pointer; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);">
                        Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Loading overlay script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rejectModal = document.getElementById('rejectModal');
        const rejectBtn = document.getElementById('openRejectModalBtn');
        const cancelBtn = document.getElementById('cancelRejectModalBtn');

        if (rejectBtn) {
            rejectBtn.addEventListener('click', function(e) {
                e.preventDefault();
                rejectModal.classList.remove('hidden');
            });
        }
        
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                rejectModal.classList.add('hidden');
            });
        }
    });
</script>
@endsection

