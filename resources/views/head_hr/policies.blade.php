@extends('layouts.sdo')

@section('title', 'Leave Policies')
@section('page-title', 'Policies Configuration')

@push('styles')
    <style>
        .premium-header-card {
            animation: fadeInDown 0.6s ease-out;
        }

        .policy-card {
            opacity: 0;
            animation: backInDown 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @foreach(range(1, 15) as $i)
            .policy-card:nth-child({{ $i }}) {
                animation-delay: {{ 0.1 + ($i * 0.05) }}s;
            }
        @endforeach

        @keyframes backInDown {
            0% {
                transform: translateY(-100px) scale(0.7);
                opacity: 0;
            }
            80% {
                transform: translateY(0px) scale(0.7);
                opacity: 0.7;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Modern Layout & Typography */
        .policy-wrapper {
            max-width: 1100px;
            margin: 0 auto;
        }

        .premium-header-card {
            background: var(--primary-gradient);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 24px 32px;
            box-shadow: 0 10px 25px -5px rgba(15, 76, 117, 0.2), 0 8px 10px -6px rgba(15, 76, 117, 0.2);
            margin-bottom: 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .premium-header-card h2, 
        .premium-header-card p {
            color: white !important;
        }

        .premium-header-card p {
            opacity: 0.9;
        }

        /* Policy Card Styling */
        .policy-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #eef2f6;
            margin-bottom: 12px;
            overflow: hidden;
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.04);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
        }

        .policy-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transform: translateY(-5px);
            border-color: #cbd5e1;
        }

        /* Shine effect on hover */
        .policy-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(
                90deg,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.3) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            transform: skewX(-25deg);
            transition: none;
            pointer-events: none;
        }

        .policy-card:hover::after {
            left: 150%;
            transition: all 0.8s ease-in-out;
        }

        .policy-header {
            padding: 12px 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            transition: background 0.3s;
            position: relative;
        }

        .policy-header:hover {
            background: #fcfdfe;
        }

        /* Icon Animation */
        .policy-header .fa-file-contract {
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .policy-card:hover .fa-file-contract {
            transform: scale(1.2) rotate(8deg);
            color: #4f46e5;
        }

        /* Chevron Animation */
        .policy-header .chevron {
            transition: all 0.3s ease;
        }

        .policy-card:hover .chevron:not(.active) {
            transform: translateY(2px);
            color: var(--primary);
        }

        .manage-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: #f8fafc;
            border: 1px solid #eef2f6;
            border-radius: 100px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .policy-card:hover .manage-indicator {
            background: #f5f3ff;
            border-color: #ddd6fe;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.1);
            transform: translateX(-4px);
        }

        .manage-text {
            font-size: 10px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: color 0.3s;
        }

        .policy-card:hover .manage-text {
            color: var(--primary);
        }

        .policy-header.active-header {
            background: #fbfcfe;
            border-bottom: 1px solid #edf2f7;
        }

        .policy-body {
            padding: 20px;
            background: #fff;
            display: none;
            border-top: 1px solid #f1f5f9;
        }

        .policy-body.active {
            display: block;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Settings Grid & Cards */
        .settings-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .setting-card {
            background: #fcfdfe;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            padding: 18px;
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }

        .setting-card.accrual-card {
            background: #f5f8ff;
            border-color: #e0e7ff;
        }

        .setting-card.expiration-card {
            background: #fffaf0;
            border-color: #ffedd5;
        }

        .setting-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .setting-title {
            font-size: 0.7rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Form Controls */
        .lbl {
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 8px;
        }

        .input-std {
            width: 100%;
            padding: 10px 14px;
            font-size: 0.95rem;
            font-weight: 600;
            color: #1e293b;
            background: #fff;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            transition: all 0.2s;
        }

        .input-std:focus {
            outline: none;
            border-color: #1b4a9a;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .help-text {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 6px;
            font-weight: 500;
        }

        /* Action Buttons */
        .btn-premium {
            background: #1b4a9a !important; /* Sidebar blue shade (var--primary) */
            color: white !important;
            padding: 12px 32px !important;
            border-radius: 14px !important;
            font-weight: 800 !important;
            font-size: 0.85rem !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
            box-shadow: 0 4px 12px rgba(15, 76, 117, 0.3) !important;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .btn-premium:hover {
            background: #3b66bc !important; /* primary-light */
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -8px rgba(15, 76, 117, 0.4) !important;
            border-color: rgba(255, 255, 255, 0.2) !important;
        }

        .btn-white-blue {
            background: white !important;
            color: var(--primary) !important;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-white-blue:hover {
            background: #f8fafc !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .btn-ghost-danger {
            padding: 8px 16px;
            color: #ef4444;
            font-size: 0.8rem;
            font-weight: 800;
            border-radius: 10px;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .btn-ghost-danger:hover {
            background: #fef2f2;
            border-color: #fee2e2;
            color: #dc2626;
            transform: translateY(-1px);
        }

        .btn-indigo {
            background: var(--primary-gradient);
            box-shadow: 0 4px 6px -1px rgba(15, 76, 117, 0.2);
        }

        .btn-indigo:hover {
            background: var(--primary-dark);
            box-shadow: 0 10px 15px -3px rgba(15, 76, 117, 0.3);
        }

        /* Status Badges */
        .status-badge {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 8px;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-configured {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #dcfce7;
        }

        .badge-none {
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        .badge-system {
            background: #e8f0ff;
            color: #123166;
            border: 1px solid #dce7ff;
        }

        /* Width Utilities */
        .w-short { max-width: 120px; }
        .w-medium { max-width: 240px; }

        .chevron {
            color: #94a3b8;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .chevron.active {
            transform: rotate(180deg);
            color: var(--primary);
        }

        /* Limit Section */
        .limit-row {
            padding: 12px 16px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        /* Modal Redesign */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .premium-modal {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 32px;
            width: 100%;
            max-width: 520px;
            padding: 40px;
            box-shadow: 
                0 25px 50px -12px rgba(15, 23, 42, 0.25),
                0 0 0 1px rgba(15, 23, 42, 0.05);
            transform: translateY(20px) scale(0.95);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .modal-overlay.active .premium-modal {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        .modal-header-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: var(--primary-gradient);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            box-shadow: 0 8px 16px -4px rgba(79, 70, 229, 0.4);
        }

        .modal-close-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
            cursor: pointer;
        }

        .modal-close-btn:hover {
            background: #fee2e2;
            color: #ef4444;
            border-color: #fecaca;
            transform: rotate(90deg);
        }

        .modal-input-group label {
            font-size: 0.75rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
            display: block;
        }

        .modal-btn-primary {
            background: white !important;
            color: var(--primary) !important;
            padding: 12px 28px;
            border-radius: 14px;
            font-weight: 800;
            font-size: 0.95rem;
            border: 1px solid rgba(15, 76, 117, 0.1);
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .modal-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            background: #f8fafc !important;
        }

        .modal-btn-ghost {
            padding: 12px 24px;
            border-radius: 14px;
            font-weight: 700;
            color: #64748b;
            background: transparent;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
            cursor: pointer;
        }

        .modal-btn-ghost:hover {
            background: #f1f5f9;
            color: #1e293b;
            border-color: #cbd5e1;
        }

        .modal-header-highlight {
            position: relative;
            margin-bottom: 2.5rem;
            padding: 0.5rem 0;
        }

        .modal-header-highlight::before {
            content: '';
            position: absolute;
            left: -40px; /* Aligns with modal padding */
            top: 0;
            bottom: 0;
            width: 5px;
            background: var(--primary-gradient);
            border-radius: 0 4px 4px 0;
            box-shadow: 4px 0 12px rgba(15, 76, 117, 0.2);
        }

        .modal-header-highlight h3 {
            font-size: 1.75rem;
            letter-spacing: -0.02em;
        }

        .modal-header-subtitle {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f0f7ff;
            color: var(--primary);
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 8px;
            border: 1px solid #dce7ff;
        }

        /* Scrollable Container for Policies */
        .policies-scroll-container {
            max-height: 480px; /* Approximately fits 4 compact cards */
            overflow-y: auto;
            padding-right: 8px;
            margin-top: 20px;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .policies-scroll-container::-webkit-scrollbar {
            width: 6px;
        }

        .policies-scroll-container::-webkit-scrollbar-track {
            background: transparent;
        }

        .policies-scroll-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .policies-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endpush

@section('content')
    <div class="policy-wrapper">
        <div class="premium-header-card">
            <div>
                <h2 class="text-2xl font-black tracking-tight">Leave Credit Policies</h2>
                <p class="font-medium mt-1">Configure accumulation and expiration rules for each leave type.</p>
            </div>
            <button onclick="toggleCreateModal()" class="btn-premium btn-white-blue">
                <i class="fas fa-plus-circle text-lg"></i> New Leave Type
            </button>
        </div>

        <!-- Create Leave Type Modal Redesign -->
        <div id="createLeaveModal" class="modal-overlay">
            <div class="premium-modal">
                <div class="modal-header-highlight">
                    <div>
                        <h3 class="text-slate-800 font-black leading-tight">Create New Leave Type</h3>
                        <div class="modal-header-subtitle">
                            <i class="fas fa-cog text-[10px]"></i>
                            Policy Configuration
                        </div>
                    </div>
                </div>

                <form action="{{ route('head-hr.leave-types.store') }}" method="POST">
                    @csrf
                    <div class="modal-input-group mb-6">
                        <label for="type_name">Leave Type Name</label>
                        <input type="text" name="type_name" id="type_name" required 
                               class="input-std" placeholder="e.g., Vacation Leave">
                    </div>
                    <div class="modal-input-group mb-10">
                        <label for="description">Description (Optional)</label>
                        <textarea name="description" id="description" rows="3" 
                                  class="input-std h-auto" style="min-height: 100px; resize: none;" 
                                  placeholder="Briefly describe the purpose of this leave type..."></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-50">
                        <button type="button" onclick="toggleCreateModal()" 
                                class="modal-btn-ghost">Cancel</button>
                        <button type="submit" class="modal-btn-primary">
                            <i class="fas fa-check-circle mr-2"></i> Create Leave Type
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="policies-scroll-container">
            @foreach($types as $type)
                @php 
                            $policy = $policies->get($type->id);
                    $accrualRate = $policy->accrual_rate ?? 0;
                    $accrualPeriod = $policy->accrual_period ?? 'Monthly';
                    $expirationRule = $policy->expiration_rule ?? 'None';
                    $maxCredits = $policy->max_credits ?? '';
                    $isConfigured = $policy ? true : false;

                    $isMandatory = Str::contains($type->type_name, ['Mandatory', 'Forced']);

                    // Define special leave types with no configuration needed
                    $specialLeaves = [
                        'Maternity Leave' => [
                            'details' => '105 days per pregnancy',
                            'reason' => 'This is a statutory benefit granted per pregnancy. It has a fixed duration and does not accrue or expire like regular leave credits.'
                        ],
                        'Paternity Leave' => [
                            'details' => '7 days per childbirth',
                            'reason' => 'This is a statutory benefit granted per childbirth of the spouse. It has a fixed limit per instance and does not require monthly accumulation rules.'
                        ],
                        'VAWC Leave' => [
                            'details' => '10 days per valid case',
                            'reason' => 'Granted to victims of violence against women and children. It is provided per instance based on legal/medical requirements rather than as an accruable pool.'
                        ],
                        'Rehabilitation Leave' => [
                            'details' => 'Up to 6 months',
                            'reason' => 'Granted based on job-related injuries or illnesses. The duration is determined by medical recommendation and approved case-by-case.'
                        ],
                        'Special Leave Benefits for Women' => [
                            'details' => 'Up to 2 months',
                            'reason' => 'Granted for gynecological surgeries. It is a one-time benefit per surgery instance and does not follow annual accrual or carry-over patterns.'
                        ],
                        'Terminal Leave' => [
                            'details' => 'Based on total unused leave credits',
                            'reason' => 'This is the monetization of all accumulated leave credits upon separation from service. Its value is derived from other credits and cannot be configured independently.'
                        ],
                        'Adoption Leave' => [
                            'details' => 'Used per approved adoption',
                            'reason' => 'A statutory benefit for adoptive parents. It is granted per adoption event and is not part of a regularly accruing credit system.'
                        ],
                        'Solo Parent Leave' => [
                            'details' => '7 days per year',
                            'reason' => 'Granted to solo parents under RA 8972. It is a fixed annual entitlement that does not accrue monthly and expires if not used within the year.'
                        ],
                        'Special Privilege Leave' => [
                            'details' => '3 days per year',
                            'reason' => 'A fixed annual entitlement for personal milestones/obligations. It does not accrue monthly and is typically non-cumulative.'
                        ],
                        'Calamity Leave' => [
                            'details' => 'Up to 5 days per year',
                            'reason' => 'Granted during declared state of calamity. It is a fixed annual entitlement tied to Specific events and does not require accrual configuration.'
                        ],
                        'Monetization of Leave Credits' => [
                            'details' => 'Based on request and availability',
                            'reason' => 'This is a financial conversion of existing credits rather than a separate leave pool.'
                        ],
                        'Wellness Leave' => [
                            'details' => 'Fixed per year',
                            'reason' => 'A fixed annual wellness benefit that doesn\'t accrue monthly.'
                        ]
                    ];

                    $specialType = $specialLeaves[$type->type_name] ?? null;

                    $headerClass = 'header-default';
                    if (Str::contains($type->type_name, 'Vacation')) {
                        $headerClass = 'header-vacation';
                    } elseif (Str::contains($type->type_name, 'Sick')) {
                        $headerClass = 'header-sick';
                    } elseif ($isMandatory) {
                        $headerClass = 'header-mandatory';
                    } elseif ($specialType) {
                        $headerClass = 'header-special';
                    } elseif ($type->type_name === 'CTO (Compensatory Time Off)') {
                        $headerClass = 'header-manual';
                    }
                @endphp

                <div class="policy-card">
                    <div class="policy-header {{ $headerClass }} relative" onclick="togglePolicy('{{ $type->id }}')" id="header-{{ $type->id }}">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500 shadow-sm" style="flex-shrink: 0;">
                                <i class="fas fa-file-contract text-sm"></i>
                            </div>
                            <div style="flex: 1;">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-bold text-slate-800 tracking-tight">{{ $type->type_name }}</h3>
                                    @if($specialType)
                                        <span class="bg-indigo-50 text-indigo-600 text-[10px] px-2 py-1 rounded-md font-black uppercase tracking-wider border border-indigo-100">
                                            Special
                                        </span>
                                    @endif
                                </div>
                                <div class="mt-1 flex items-center gap-2">
                                    @if($isMandatory)
                                        <div class="status-badge badge-system py-0.5" style="font-size: 0.65rem;">
                                            <i class="fas fa-shield-alt text-[9px]"></i> System Managed
                                        </div>
                                    @elseif($isConfigured)
                                        <div class="status-badge badge-configured py-0.5" style="font-size: 0.65rem;">
                                            <i class="fas fa-check-circle text-[9px]"></i> Configured
                                        </div>
                                    @else
                                        <div class="status-badge badge-none py-0.5" style="font-size: 0.65rem;">
                                            <i class="fas fa-circle-notch text-[9px]"></i> Not Configured
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="manage-indicator">
                            <span class="manage-text">Click to manage</span>
                            <i class="fas fa-chevron-down chevron text-xs" id="chevron-{{ $type->id }}"></i>
                        </div>
                    </div>

                    <div class="policy-body" id="body-{{ $type->id }}">
                        @if($isMandatory)
                            <div class="bg-blue-50 border border-blue-100 rounded-md p-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-bold text-blue-800">System Managed Policy</h3>
                                        <div class="mt-2 text-sm text-blue-700 space-y-2">
                                            <p>This leave type is automatically managed by the system according to CSC rules.</p>
                                            <ul class="list-disc pl-5 mt-3 space-y-1">
                                                <li>Employees are required to take <strong>5 days</strong> of this leave annually.</li>
                                                <li>These days are deducted from the employee's <strong>Vacation Leave (VL)</strong> credits.</li>
                                                <li>If the employee does not use the full 5 days by the end of the year, the remaining days will be automatically deducted from their VL credits.</li>
                                            </ul>
                                            <div class="mt-4 p-3 bg-white bg-opacity-60 rounded border border-blue-100">
                                                <p class="font-medium text-blue-900"><i class="fas fa-check-circle mr-1"></i> No manual configuration required</p>
                                                <p class="text-xs text-blue-600 mt-1">The system handles validation and year-end processing automatically.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($type->type_name === 'CTO (Compensatory Time Off)')
                            <div class="bg-indigo-50 border border-indigo-100 rounded-md p-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-clock text-indigo-500 mt-1"></i>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-bold text-indigo-800">Manually Managed Credits</h3>
                                        <div class="mt-2 text-sm text-indigo-700 space-y-2">
                                            <p>This leave type is manually managed by HR. Credits are added in batches with specific expiration dates.</p>
                                            <ul class="list-disc pl-5 mt-3 space-y-1">
                                                <li>Credits are added manually via "Manage Credits" for each employee.</li>
                                                <li>Each added batch has its own <strong>expiration date</strong>.</li>
                                                <li>Maximum total credits allowed per employee is <strong>15</strong>.</li>
                                                <li>Credits are consumed based on earliest expiration (FIFO).</li>
                                            </ul>
                                            <div class="mt-4 p-3 bg-white bg-opacity-60 rounded border border-indigo-100">
                                                <p class="font-medium text-indigo-900"><i class="fas fa-check-circle mr-1"></i> Manual Input Only</p>
                                                <p class="text-xs text-indigo-600 mt-1">No automatic accrual. Credits must be explicitly granted.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($specialType)
                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-8">
                                <div class="flex items-start gap-6">
                                    <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0 text-xl">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-slate-800 mb-2">Special Leave Policy</h3>
                                        <p class="text-slate-600 leading-relaxed mb-4">
                                            {{ $specialType['reason'] }}
                                        </p>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                                                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Standard Allocation</div>
                                                <div class="text-slate-800 font-semibold">{{ $specialType['details'] }}</div>
                                            </div>
                                            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                                                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Configuration Status</div>
                                                <div class="text-indigo-600 font-bold flex items-center gap-2">
                                                    <i class="fas fa-check-circle"></i>
                                                    System Default (Fixed)
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-8 pt-6 border-t border-slate-200">
                                            <p class="text-sm text-slate-500 italic">
                                                <i class="fas fa-shield-alt mr-1"></i> 
                                                This leave type follows statutory requirements and does not require periodic accumulation or expiration rules.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <form action="{{ route('head-hr.leave-policies.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="leave_type_id" value="{{ $type->id }}">


                                <!-- Main Grid -->
                                <div class="settings-grid">
                                    <!-- Left: Accrual -->
                                    <div class="setting-card accrual-card">
                                        <div class="setting-title"><i class="fas fa-chart-line text-blue-600 mr-2"></i> Accrual Settings</div>

                                        <div class="mb-4">
                                            <label class="lbl">Credits to Add</label>
                                            <div class="flex items-center gap-3">
                                                <input type="number" step="0.01" name="accrual_rate" value="{{ $accrualRate }}" class="input-std w-short" required>
                                                <span class="text-sm text-slate-500 font-bold">credits</span>
                                            </div>
                                            <div class="help-text">Amount added per cycle.</div>
                                        </div>

                                        <div>
                                            <label class="lbl">Frequency</label>
                                            <select name="accrual_period" class="input-std w-full">
                                                <option value="None" {{ $accrualPeriod == 'None' ? 'selected' : '' }}>None (Manual Only)</option>
                                                <option value="Monthly" {{ $accrualPeriod == 'Monthly' ? 'selected' : '' }}>Monthly (Every 24th)</option>
                                                <option value="Yearly" {{ $accrualPeriod == 'Yearly' ? 'selected' : '' }}>Yearly</option>
                                            </select>
                                            <div class="help-text">How often credits are automatically added.</div>
                                        </div>
                                    </div>

                                    <!-- Right: Expiration -->
                                    <div class="setting-card expiration-card">
                                        <div class="setting-title"><i class="fas fa-hourglass-end text-orange-600 mr-2"></i> Expiration Rules</div>

                                        <div class="mb-4">
                                            <label class="lbl">Expiry Type</label>
                                            <select name="expiration_rule" class="input-std w-full" onchange="toggleDate(this, '{{ $type->id }}')">
                                                <option value="None" {{ $expirationRule == 'None' ? 'selected' : '' }}>No Expiry</option>
                                                <option value="Yearly" {{ $expirationRule == 'Yearly' ? 'selected' : '' }}>Yearly (Reset Jan 1)</option>
                                                <option value="Monthly" {{ $expirationRule == 'Monthly' ? 'selected' : '' }}>End of Month</option>
                                                <option value="SpecificDate" {{ $expirationRule == 'SpecificDate' ? 'selected' : '' }}>Specific Date</option>
                                            </select>
                                            <div class="help-text">When unused credits should be forfeited.</div>
                                        </div>

                                        <div id="date_wrapper_{{ $type->id }}" class="{{ $expirationRule !== 'SpecificDate' ? 'hidden' : '' }}">
                                            <label class="lbl">Expiration Date</label>
                                            <input type="date" name="expiration_date" value="{{ $policy->expiration_date ?? '' }}" class="input-std w-full">
                                        </div>
                                    </div>
                                </div>

                                <!-- Bottom: Limits -->
                                <div class="limit-row mt-4">
                                    <div class="flex flex-col">
                                        <label class="lbl mb-0">Accumulation Limit</label>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Leave empty for unlimited (∞)</p>
                                    </div>
                                    <div class="relative w-short">
                                        <input type="number" step="0.01" name="max_credits" value="{{ $maxCredits }}" 
                                            placeholder="∞" class="input-std text-center font-bold text-base bg-white h-9">
                                    </div>
                                </div>

                                <!-- Actions Refined - Forced Horizontal -->
                                <div class="flex flex-row items-center justify-between mt-8 pt-6 border-t border-slate-50" style="display: flex !important; flex-direction: row !important; align-items: center !important; justify-content: space-between !important; width: 100%;">
                                    <button type="submit" class="btn-premium whitespace-nowrap !px-8">
                                        <i class="fas fa-save text-sm"></i> Save Configuration
                                    </button>

                                    @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->isHeadHR()))
                                        <button type="button" title="Delete Leave Type" 
                                                onclick="confirmDelete('{{ $type->id }}', '{{ addslashes($type->type_name) }}')" 
                                                class="btn-ghost-danger w-auto h-auto flex items-center justify-center rounded-xl bg-slate-50 hover:bg-red-50 py-2.5 px-4 !border-slate-200 hover:!border-red-100 font-black text-[10px] tracking-widest uppercase text-red-500 shadow-sm border-1 border">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                            <span class="ml-2">Remove Type</span>
                                        </button>
                                    @endif
                                </div>
                            </form>
                            
                            @if(auth()->check() && auth()->user()->isSuperAdmin())
                                <form id="delete-form-{{ $type->id }}" action="{{ route('head-hr.leave-types.destroy', $type->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        function togglePolicy(id) {
            const body = document.getElementById('body-' + id);
            const header = document.getElementById('header-' + id);
            const chevron = document.getElementById('chevron-' + id);

            body.classList.toggle('active');
            header.classList.toggle('active-header');
            chevron.classList.toggle('active');
        }

        function toggleDate(select, id) {
            const wrapper = document.getElementById('date_wrapper_' + id);
            if (select.value === 'SpecificDate') {
                wrapper.classList.remove('hidden');
            } else {
                wrapper.classList.add('hidden');
            }
        }

        function toggleCreateModal() {
            const modal = document.getElementById('createLeaveModal');
            modal.classList.toggle('active');
        }

        function confirmDelete(id, name) {
            if (confirm('Are you confirm you want to delete the leave type "' + name + '" ? This action cannot be undone.')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
@endsection
