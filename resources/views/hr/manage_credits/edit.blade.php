@extends('layouts.sdo')

@section('title', 'Allocate Credits')
@section('page-title', 'Allocate User Leave Credits')

@push('styles')
    <style>
        /* Card & Layout */
        .credits-card-container {
            background: white;
            border-radius: 12px;
            border: 1px solid #eef2f6;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .credit-row {
            background: white;
            border-bottom: 1px solid #f1f5f9;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            transition: background-color 0.2s;
        }

        .credit-row:last-child {
            border-bottom: none;
        }

        .credit-row:hover {
            background-color: #f8fafc;
        }

        /* Locked State */
        .locked-bg {
            background-color: #fcfcfc;
        }

        .locked-bg input {
            background-color: #f1f5f9;
            color: #94a3b8;
            cursor: not-allowed;
            border-color: #e2e8f0;
        }

        /* Typography */
        .field-label {
            font-size: 0.95rem;
            font-weight: 600;
            color: #334155;
            min-width: 250px;
            display: flex;
            flex-direction: column;
        }

        .field-sublabel {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 400;
            margin-top: 4px;
        }

        /* Inputs */
        .input-wrapper {
            position: relative;
            width: 160px;
        }

        .field-input {
            width: 100%;
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            color: #1e293b;
            text-align: right;
            transition: all 0.2s;
        }

        .field-input:focus {
            outline: none;
            border-color: #1b4a9a;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        /* Action Link */
        .action-area {
            width: 140px;
            display: flex;
            justify-content: flex-end;
        }

        .btn-request {
            font-size: 0.85rem;
            color: #1b4a9a;
            font-weight: 600;
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .btn-request:hover {
            background-color: #e8f0ff;
            text-decoration: none;
        }

        /* Badges */
        .status-badge {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            letter-spacing: 0.02em;
        }

        .status-locked {
            background: #fef2f2;
            color: #ef4444;
            border: 1px solid #fee2e2;
        }

        /* Compact Table Improvements */
        .statutory-row {
            padding: 8px 24px !important;
        }

        .statutory-row:hover {
            background-color: #f0fdf4 !important;
            border-left: 3px solid #10b981;
        }

        .event-based-pill {
            background: #ecfdf5 !important;
            color: #059669 !important;
            border: 1.5px solid #d1fae5 !important;
            font-size: 10px !important;
            font-weight: 800 !important;
            padding: 4px 12px !important;
            border-radius: 8px !important;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.05);
        }

        .statutory-title {
            font-size: 0.88rem !important;
            font-weight: 700 !important;
            color: #1e293b !important;
        }

        .statutory-desc {
            font-size: 0.72rem !important;
            color: #64748b !important;
            margin-top: 1px !important;
        }

        /* Primary Button */
        .btn-primary {
            background-color: #1b4a9a;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        }

        .btn-primary:hover {
            background-color: #1b4a9a;
            transform: translateY(-1px);
            box-shadow: 0 6px 8px -1px rgba(37, 99, 235, 0.3);
        }

        /* Scrollable Statutory Section */
        .statutory-scroll-container {
            max-height: 420px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f8fafc;
        }

        .statutory-scroll-container::-webkit-scrollbar {
            width: 5px;
        }

        .statutory-scroll-container::-webkit-scrollbar-track {
            background: #f8fafc;
        }

        .statutory-scroll-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 20px;
        }

        .statutory-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* COC Specific Premium Styles */
        .coc-section-card {
            background: white;
            border: 1.5px solid #f1f5f9;
            border-radius: 16px;
            padding: 0 !important;
            margin-top: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .coc-header {
            cursor: pointer;
            user-select: none;
            padding: 14px 20px;
            background: var(--primary-gradient) !important;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s ease;
        }

        .coc-header:hover {
            opacity: 0.95;
        }

        .coc-header-title {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white !important;
        }

        .coc-header-info h3 {
            font-size: 1rem;
            font-weight: 800;
            color: white !important;
            margin: 0;
            line-height: 1.2;
        }

        .coc-header-info p {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0;
        }

        .coc-header i.coc-chevron {
            color: rgba(255, 255, 255, 0.7) !important;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .coc-header i.rotate {
            transform: rotate(180deg);
            color: white !important;
        }

        .coc-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0, 1, 0, 1);
            opacity: 0;
            background: #fdfdfd;
        }

        .coc-content.active {
            max-height: 2000px;
            opacity: 1;
            transition: max-height 0.4s ease-in, opacity 0.3s ease-in;
            padding: 16px;
        }

        .coc-inner-card {
            background: white;
            border-radius: 12px;
            padding: 12px !important;
            border: 1.5px solid #f1f5f9;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            margin-bottom: 12px;
        }

        .coc-inner-card h4 {
            font-size: 11px !important;
            font-weight: 800;
            color: var(--primary) !important;
            text-transform: uppercase;
            margin-bottom: 8px !important;
        }

        .coc-input {
            width: 100%;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            font-size: 0.85rem;
            color: #1e293b;
            background: #f8fafc;
            transition: all 0.2s;
        }

        .coc-input:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(15, 76, 117, 0.1);
        }

        .coc-btn-add {
            width: 100%;
            background: var(--primary-gradient) !important;
            color: white !important;
            padding: 10px;
            border-radius: 10px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            box-shadow: 0 4px 10px rgba(15, 76, 117, 0.2);
        }

        .coc-btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(15, 76, 117, 0.3);
        }

        .coc-btn-reset {
            background: #fef2f2;
            color: #ef4444;
            border: 1.5px solid #fee2e2;
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(239, 68, 68, 0.05);
        }

        .coc-btn-reset:hover {
            background: #ef4444;
            color: white;
            border-color: #ef4444;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
            transform: translateY(-1px);
        }

        .btn-delete-batch {
            width: 26px;
            height: 26px;
            background: #fef2f2;
            color: #ef4444;
            border: 1px solid #fee2e2;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.75rem;
            margin-top: 4px;
        }

        .btn-delete-batch:hover {
            background: #ef4444;
            color: white;
            border-color: #ef4444;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.2);
        }

        /* Premium SweetAlert Styles */
        .premium-swal-popup {
            border-radius: 20px !important;
            padding: 2rem !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        }
        .premium-swal-title {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-weight: 800 !important;
            color: #1e293b !important;
            font-size: 1.5rem !important;
        }
        .premium-swal-confirm {
            border-radius: 12px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            padding: 12px 24px !important;
        }

        .coc-total-badge-bar {
            background: var(--primary) !important;
            color: white !important;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            letter-spacing: 0.05em;
            margin-bottom: 16px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
        }

        .coc-header-row {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            width: 100% !important;
            margin-bottom: 12px !important;
        }

        .coc-header-row h4 {
            margin-bottom: 0 !important;
        }

        .coc-table th {
            font-size: 10px !important;
            padding: 8px 12px !important;
        }

        .coc-table td {
            font-size: 11px !important;
            padding: 8px 12px !important;
        }

        .coc-total-badge {
            background: var(--primary) !important;
            color: white !important;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 900;
        }

        /* Modal Styling (Native CSS) */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: none;
            /* Hidden by default */
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }

        .modal-overlay.open {
            display: flex;
            /* Show flex when open */
        }

        .modal-box {
            background: white;
            border-radius: 16px;
            width: 100%;
            max-width: 450px;
            padding: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transform: scale(0.95);
            transition: transform 0.2s;
        }

        .modal-overlay.open .modal-box {
            transform: scale(1);
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .btn-secondary {
            background-color: white;
            border: 1px solid #cbd5e1;
            color: #64748b;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background-color: #f1f5f9;
            color: #334155;
        }

        .modal-textarea {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 12px;
            font-family: inherit;
            font-size: 0.95rem;
            margin-top: 8px;
            outline: none;
            resize: vertical;
            min-height: 100px;
        }

        .modal-textarea:focus {
            border-color: #1b4a9a;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Header Styling */
        .card-header {
            background: linear-gradient(to right, #f8fafc, #f1f5f9) !important;
            padding: 10px 16px !important;
            border-bottom: 1px solid #e2e8f0 !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            border-top: 1px solid #e2e8f0 !important;
        }

        .header-title {
            font-size: 0.65rem !important;
            font-weight: 900 !important;
            text-transform: uppercase;
            color: #475569 !important;
            letter-spacing: 0.08em;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .header-title i {
            font-size: 0.7rem;
            opacity: 0.4;
        }

        /* Two-Column Page Layout */
        .page-layout-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            align-items: start;
        }

        .layout-column {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        @media (max-width: 1024px) {
            .page-layout-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Profile Header Premium Styling */
        .profile-header-container {
            background: var(--primary-gradient) !important;
            padding: 32px;
            border-radius: 20px;
            margin-bottom: 24px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: var(--saas-shadow-hover);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .profile-header-container::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            filter: blur(60px);
        }

        .profile-badge-btn {
            width: 34px;
            height: 34px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .profile-info-main h2 {
            font-size: 2.2rem;
            font-weight: 800;
            color: white !important;
            letter-spacing: -0.02em;
            margin: 0;
            line-height: 1.1;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .profile-meta-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 8px;
            flex-wrap: wrap;
        }

        .profile-position-pill {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white !important;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .profile-email-meta {
            color: rgba(255, 255, 255, 0.85) !important;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .profile-save-btn {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: var(--primary) !important;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 800;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            transition: all 0.2s ease;
            cursor: pointer;
            z-index: 50;
            white-space: nowrap;
        }

        .profile-save-btn:hover {
            background: white !important;
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            color: var(--primary-light) !important;
        }

        /* RESPONSIVE OVERRIDES */
        @media (max-width: 768px) {
            .profile-header-container {
                flex-direction: column;
                align-items: stretch;
                padding: 24px;
                text-align: center;
            }

            .profile-header-content {
                flex-direction: column;
                align-items: center;
                gap: 16px;
            }

            .profile-info-main h2 {
                font-size: 1.5rem;
                text-align: center;
            }

            .profile-meta-row {
                justify-content: center;
            }

            .profile-save-btn {
                width: 100%;
                justify-content: center;
                margin-top: 10px;
            }

            .credit-row {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
                padding: 16px;
            }

            .field-label {
                min-width: 0;
            }

            .input-wrapper {
                width: 100%;
            }

            .field-input {
                text-align: left;
            }
            
            .action-area {
                width: 100%;
                justify-content: center;
                border-top: 1px dashed #e2e8f0;
                padding-top: 10px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4">
        <div class="profile-header-container">
            <div class="profile-header-content flex items-center gap-6 relative z-10">
                <a href="{{ route('hr-staff.manage-credits') }}" class="profile-badge-btn">
                    <i class="fas fa-arrow-left"></i>
                </a>
                
                <div class="profile-info-main text-left">
                    <h2 class="text-left !mb-0">{{ $user->full_name }}</h2>
                    <div class="profile-meta-row !mt-1 text-left">
                        <span class="profile-position-pill">{{ $user->position ?? 'Personnel' }}</span>
                        <div class="profile-email-meta">
                            <i class="far fa-envelope opacity-60"></i>
                            <span>{{ $user->email }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" form="manageCreditsForm" class="profile-save-btn">
                <i class="fas fa-save"></i>
                <span>SAVE ALL USER CREDITS</span>
            </button>
        </div>

        {{-- Notifications removed as requested to prevent layout interference --}}

        <form action="{{ route('hr-staff.manage-credits.update', $user->id) }}" method="POST" id="manageCreditsForm">
            @csrf
        </form>

        @php
            $typesList = isset($otherTypes) ? $otherTypes : (isset($leaveTypes) ? $leaveTypes : []);
            $typesColl = is_array($typesList) ? collect($typesList) : $typesList;

            $creditLeaves = $typesColl->where('category', 'Credit');
            // Filter out any type containing 'COC' or 'Manual Entry' from the statutory section
            $statutoryLeaves = $typesColl->where('category', '!=', 'Credit')->reject(function($type) {
                $name = strtoupper($type->type_name);
                return str_contains($name, 'COC') || str_contains($name, 'CTO') || str_contains($name, 'MANUAL ENTRY');
            });
            $eventBasedLeaves = ['Maternity Leave', 'Paternity Leave', 'VAWC Leave', 'Adoption Leave', 'Rehabilitation Leave', 'Special Leave Benefits for Women', 'Monetization of Leave Credits', 'Terminal Leave'];
        @endphp

        <div class="page-layout-grid">
            <!-- Left Column: Credit Base & COC -->
            <div class="layout-column">
                <!-- Section A: Credit-Based Leaves -->
                <div class="section-container">
                    {{-- Redundant h3 header removed --}}

                    <div class="credits-card-container !border-t-0 !rounded-t-none overflow-hidden">
                        <div class="card-header">
                            <div class="header-title">
                                <i class="fas fa-layer-group"></i>
                                <span>Credit-Based Leaves</span>
                            </div>
                            <div class="header-title pr-14">
                                <i class="fas fa-calculator"></i>
                                <span>Balance</span>
                            </div>
                        </div>

                        @foreach($creditLeaves as $type)
                            @php
                                $credit = $existingCredits->get($type->id);
                                $currentVal = $credit ? $credit->credits : '';
                            @endphp

                            <div class="credit-row">
                                <div class="field-label min-w-[150px]">
                                    <span class="font-bold text-slate-700">{{ $type->type_name }}</span>
                                </div>

                                <div class="flex items-center gap-4">
                                    <div class="input-wrapper w-[110px]">
                                        <input type="number" step="0.001" min="0" name="credits[{{ $type->id }}]"
                                            value="{{ truncate_credit_for_input($currentVal) }}" form="manageCreditsForm" class="field-input text-right"
                                            placeholder="0.000">
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if(isset($ctoType) && $ctoType)
                            @php
                                $ctoCredit = $existingCredits->get($ctoType->id);
                                $ctoVal = $ctoCredit ? $ctoCredit->credits : 0;
                            @endphp
                            <div class="credit-row bg-slate-50/30 border-t border-slate-100/50">
                                <div class="field-label min-w-[150px]">
                                    <span class="font-bold text-slate-700">{{ $ctoType->type_name }}</span>
                                    <span class="field-sublabel italic">Non-editable balance</span>
                                </div>

                                <div class="flex items-center gap-4">
                                    <div class="input-wrapper w-[110px]">
                                        <div class="field-input bg-[#f8fafc] text-[#94a3b8] border-[#e2e8f0] cursor-not-allowed flex items-center justify-end shadow-sm" style="height: 38px; padding-right: 12px; font-family: inherit;">
                                            {{ format_credit_3_decimal($ctoVal) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Section C: COC Management (Expandable) -->
                @if(isset($ctoType) && $ctoType)
                    <div class="coc-section-card">
                        <div class="coc-header" onclick="toggleCocContent()">
                            <div class="coc-header-title">
                                <i class="fas fa-clock"></i>
                                <div class="coc-header-info">
                                    <h3>COC Management</h3>
                                    <p>Manual Batch Entry</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-down coc-chevron" id="cocChevron"></i>
                        </div>

                        <div class="coc-content" id="cocContent">
                            <div class="space-y-4">
                                <!-- Add Form -->
                                <div class="coc-inner-card">
                                    <h4><i class="fas fa-plus-circle mr-1"></i> Add New Credits</h4>
                                    <form action="{{ route('hr-staff.manage-credits.add-cto', $user->id) }}" method="POST"
                                        class="space-y-3">
                                        @csrf
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="coc-label !text-[10px]">Number of Days</label>
                                                <input type="number" step="0.1" name="credit_amount"
                                                    class="coc-input" required placeholder="0.0">
                                            </div>
                                            <div>
                                                <label class="coc-label !text-[10px]">Expiry</label>
                                                <input type="date" name="expiration_date" class="coc-input"
                                                    required>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="coc-label !text-[10px]">Remarks</label>
                                            <input type="text" name="remarks" class="coc-input"
                                                placeholder="e.g., OT Nov 2023">
                                        </div>
                                        <button type="submit" class="coc-btn-add">
                                            <i class="fas fa-save mr-1"></i> Add COC Credits
                                        </button>
                                    </form>
                                </div>

                                <!-- Active Batches -->
                                <div class="coc-inner-card">
                                    <div class="coc-header-row">
                                        <h4>Active Credit Batches</h4>
                                        <form action="{{ route('hr-staff.manage-credits.reset-cto', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reset the CTO balance to zero? This will expire all active batches.')" class="m-0">
                                            @csrf
                                            <button type="submit" class="coc-btn-reset">
                                                <i class="fas fa-undo"></i> Reset to Zero
                                            </button>
                                        </form>
                                    </div>

                                    <div class="coc-total-badge-bar">
                                        TOTAL: {{ format_credit_3_decimal($ctoCredits->sum('remaining_credits')) }}
                                    </div>
                                    <div class="coc-table-container">
                                        @if($ctoCredits->isEmpty())
                                            <p class="text-[10px] text-gray-400 italic text-center py-4">No active COC credits.</p>
                                        @else
                                            <table class="coc-table">
                                                <tbody>
                                                    @foreach($ctoCredits as $batch)
                                                        <tr class="!border-b border-slate-50">
                                                            <td class="!py-2 !px-0">
                                                                <div class="font-bold text-slate-700">
                                                                    {{ $batch->created_at->format('M d, Y') }}
                                                                </div>
                                                                <div class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">
                                                                    By: {{ $batch->addedBy->full_name ?? 'System' }}
                                                                </div>
                                                            </td>
                                                            <td class="!py-2 !px-0 text-red-500 font-medium whitespace-nowrap">
                                                                <div class="text-[9px] text-slate-400 uppercase font-bold mb-1">Expires</div>
                                                                <i class="fas fa-history mr-1 opacity-50"></i>{{ $batch->expiration_date->format('M d, Y') }}
                                                            </td>
                                                            <td class="!py-2 !px-0 text-right">
                                                                <div class="text-[9px] text-slate-400 uppercase font-bold mb-1">Credits</div>
                                                                <span class="font-black text-indigo-600 text-lg">
                                                                    {{ format_credit_3_decimal($batch->remaining_credits) }}
                                                                </span>
                                                            </td>
                                                            <td class="!py-2 !px-0 text-right w-[50px]">
                                                                <div class="text-[9px] text-slate-400 uppercase font-bold mb-1">Action</div>
                                                                <form action="{{ route('hr-staff.manage-credits.delete-coc', $batch->id) }}" method="POST" 
                                                                    class="delete-coc-form inline m-0"
                                                                    data-credits="{{ format_credit_3_decimal($batch->remaining_credits) }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="button" class="btn-delete-batch delete-coc-btn" title="Delete Batch">
                                                                        <i class="fas fa-trash-alt"></i>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Statutory / Special Leaves -->
            <div class="layout-column">
                <div class="section-container">
                    {{-- Redundant h3 header removed --}}

                    <div class="credits-card-container !border-t-0 !rounded-t-none overflow-hidden">
                        <div class="card-header">
                            <div class="header-title">
                                <i class="fas fa-layer-group"></i>
                                <span>Statutory / Special Leaves</span>
                            </div>
                            <div class="header-title pr-14">
                                <i class="fas fa-calendar-check"></i>
                                <span>Allocation</span>
                            </div>
                        </div>

                        <div class="statutory-scroll-container">
                            @foreach($statutoryLeaves as $type)
                                @php
                                    $credit = $existingCredits->get($type->id);
                                    $currentVal = $credit ? $credit->credits : '';
                                    $isEventBased = in_array($type->type_name, $eventBasedLeaves);
                                @endphp

                                <div class="credit-row statutory-row">
                                    <div class="field-label flex-1">
                                        <div class="statutory-title">{{ $type->type_name }}</div>
                                        <div class="statutory-desc line-clamp-1 italic">
                                            {{ $type->description }}
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <div class="input-wrapper w-[110px]">
                                            @if($isEventBased)
                                                <div class="event-based-pill flex items-center justify-center h-[32px] w-full">
                                                    EVENT-BASED
                                                </div>
                                                <input type="hidden" name="credits[{{ $type->id }}]" value=""
                                                    form="manageCreditsForm">
                                            @else
                                                <input type="number" step="0.001" min="0" name="credits[{{ $type->id }}]"
                                                    value="{{ truncate_credit_for_input($currentVal) }}" form="manageCreditsForm"
                                                    class="field-input h-[32px] text-right font-bold text-sm" placeholder="0.000">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        // Dirty-check functionality
        let isDirty = false;
        const form = document.getElementById('manageCreditsForm');

        if (form) {
            // Track changes in all inputs
            form.querySelectorAll('input, select, textarea').forEach(input => {
                input.addEventListener('change', () => { isDirty = true; });
                input.addEventListener('input', () => { isDirty = true; });
            });

            // Clear flag on submit
            form.addEventListener('submit', () => {
                isDirty = false;
            });
        }

        // Prompt on leave
        window.addEventListener('beforeunload', (e) => {
            if (isDirty) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                return e.returnValue;
            }
        });

        function toggleCocContent() {
            const content = document.getElementById('cocContent');
            const chevron = document.getElementById('cocChevron');
            const header = document.querySelector('.coc-header');

            if (content && chevron && header) {
                content.classList.toggle('active');
                chevron.classList.toggle('rotate');
                header.classList.toggle('active-header');
            }
        }

        // SweetAlert2 for COC Batch Deletion
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-coc-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    const form = this.closest('form');
                    const credits = form.getAttribute('data-credits');
                    
                    Swal.fire({
                        title: 'Are you sure?',
                        text: `You are about to delete this COC batch. This will reduce the user's balance by ${credits} credits.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        background: '#ffffff',
                        borderRadius: '16px',
                        customClass: {
                            popup: 'premium-swal-popup',
                            title: 'premium-swal-title',
                            confirmButton: 'premium-swal-confirm'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
