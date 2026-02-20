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
            padding: 20px 28px;
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
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            text-align: right;
            transition: all 0.2s;
        }

        .field-input:focus {
            outline: none;
            border-color: #3b82f6;
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
            color: #2563eb;
            font-weight: 600;
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .btn-request:hover {
            background-color: #eff6ff;
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

        /* Primary Button */
        .btn-primary {
            background-color: #2563eb;
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
            background-color: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 6px 8px -1px rgba(37, 99, 235, 0.3);
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
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Notice Box */
        .info-notice {
            background: #f0f7ff;
            border-left: 4px solid #3b82f6;
            padding: 24px;
            margin-bottom: 32px;
            border-radius: 0 12px 12px 0;
            display: flex;
            gap: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .info-notice-icon {
            color: #3b82f6;
            font-size: 1.25rem;
            margin-top: 2px;
        }

        .info-notice-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 4px;
            display: block;
        }

        .info-notice-text {
            font-size: 0.875rem;
            color: #1e40af;
            line-height: 1.6;
        }

        /* CTO Section */
        .cto-section {
            background: #f5f3ff;
            border: 1px solid #ddd6fe;
            border-radius: 12px;
            padding: 28px;
            margin-top: 48px;
            margin-bottom: 40px;
        }

        .cto-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 8px;
        }

        .cto-icon-box {
            width: 40px;
            height: 40px;
            background: #e0e7ff;
            color: #20278aff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .cto-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #000000ff;
        }

        .cto-subtitle {
            font-size: 0.85rem;
            color: #000000ff;
            margin-left: 54px;
            margin-bottom: 24px;
            opacity: 0.8;
        }

        .cto-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
        }

        .cto-card {
            background: white;
            border: 1px solid #e0e7ff;
            padding: 24px;
            border-radius: 12px;
            flex: 1;
            min-width: 320px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .cto-card-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: #374151;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f3f4f6;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .cto-form-group {
            margin-bottom: 20px;
        }

        .cto-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 700;
            color: #4b5563;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .cto-input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .cto-input:focus {
            outline: none;
            border-color: #212381ff;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .btn-cto-submit {
            width: 100%;
            background: #0e479cff;
            color: white;
            font-weight: 700;
            padding: 14px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-cto-submit:hover {
            background: #211979ff;
            transform: translateY(-1px);
        }

        /* Generic Utilities */
        .section-heading-custom {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
            margin-top: 40px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-subtext {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 20px;
            padding-left: 44px;
            margin-top: -8px;
        }

        .icon-box-blue {
            background: #dbeafe;
            color: #2563eb;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        .icon-box-emerald {
            background: #dcfce7;
            color: #059669;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-8 flex items-center gap-4">
            <a href="{{ route('hr-staff.manage-credits') }}"
                class="w-10 h-10 rounded-full bg-white border flex items-center justify-center text-gray-500 hover:text-gray-800 transition-colors shadow-sm text-lg">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $user->full_name }}</h2>
                <p class="text-gray-500 text-sm mt-1">{{ $user->email }} • {{ $user->position ?? 'No Position' }}</p>
            </div>
        </div>

        @if(session('success'))
            <div
                class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r shadow-sm flex items-start gap-3">
                <i class="fas fa-check-circle mt-1"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r shadow-sm flex items-start gap-3">
                <i class="fas fa-exclamation-circle mt-1"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <!-- Info rmation Notice -->
        <div class="info-notice">
            <div class="info-notice-icon">
                <i class="fas fa-info-circle"></i>
            </div>
            <div>
                <span class="info-notice-title">Information on Leave Type Visibility</span>
                <p class="info-notice-text">
                    Event-based leave types (such as <span class="font-semibold text-blue-900">Maternity, Paternity, VAWC, Rehabilitation, etc.</span>) are not listed on this page. These types do not follow manual accrual or starting balance logic; instead, they are granted per-instance based on specific childbirth, medical, or legal events.
                </p>
            </div>
        </div>

            <form action="{{ route('hr-staff.manage-credits.update', $user->id) }}" method="POST" id="creditsForm">
                @csrf

                @php
                    // Use otherTypes if available (from controller), otherwise fallback (shouldn't happen with new controller)
                    $typesList = isset($otherTypes) ? $otherTypes : (isset($leaveTypes) ? $leaveTypes : []);
                    $typesColl = is_array($typesList) ? collect($typesList) : $typesList;

                    $creditLeaves = $typesColl->where('category', 'Credit');
                    // Everything else is Statutory/Special (excluding CTO which is handled below)
                    $statutoryLeaves = $typesColl->where('category', '!=', 'Credit');
                @endphp

                <!-- Section A: Credit-Based Leaves -->
            <h3 class="section-heading-custom">
                <span class="icon-box-blue">
                    <i class="fas fa-coins"></i>
                </span>
                Credit-Based Leaves
            </h3>
            <p class="section-subtext">Leaves that accrue monthly (Vacation & Sick Leave).</p>

                <div class="credits-card-container mb-10">
                    <div class="card-header">
                        <div class="header-title">
                            <i class="fas fa-layer-group text-blue-400 text-sm"></i> 
                            <span>Leave Type</span>
                        </div>
                        <div class="header-title pr-14">
                            <i class="fas fa-calculator text-blue-400 text-sm"></i>
                            <span>Current Balance</span>
                        </div>
                    </div>

                    @foreach($creditLeaves as $type)
                        @php
                            $credit = $existingCredits->get($type->id);
                            $dbLocked = $credit && $credit->is_locked;
                            // Locked effectively if DB locked AND not Head HR
                            $isLocked = $dbLocked && !auth()->user()->isHeadHR();
                            $currentVal = $credit ? $credit->credits : '';
                        @endphp

                        <div class="credit-row {{ $isLocked ? 'locked-bg' : '' }}">
                            <div class="field-label">
                                {{ $type->type_name }}
                                @if($isLocked)
                                    <div class="status-badge status-locked mt-1 w-max">
                                        <i class="fas fa-lock text-[10px]"></i> Locked
                                    </div>
                                @elseif($dbLocked)
                                    <div class="status-badge bg-orange-100 text-orange-600 border border-orange-200 mt-1 w-max">
                                        <i class="fas fa-unlock-alt text-[10px]"></i> Unlocked (Head HR)
                                    </div>
                                @else
                                    <span class="field-sublabel">Accrues 1.25/month</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-6">
                                <div class="input-wrapper">
                                    <input type="number" step="0.001" min="0" 
                                        name="credits[{{ $type->id }}]" 
                                        value="{{ $currentVal }}" 
                                        class="field-input" 
                                        placeholder="0.000" 
                                        {{ $isLocked ? 'readonly' : '' }}>
                                </div>

                                <div class="action-area">
                                    @if($isLocked)
                                        <button type="button" onclick="requestUnlock(<?php        echo $type->id; ?>, '<?php        echo addslashes($type->type_name); ?>')" class="btn-request">
                                            <i class="fas fa-key mr-1"></i> Request Unlock
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400">
                                            <i class="fas fa-pen mr-1"></i> Editable
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Section B: Statutory / Special Leaves -->
            <h3 class="section-heading-custom">
                <span class="icon-box-emerald">
                    <i class="fas fa-file-contract"></i>
                </span>
                Statutory / Special Leaves
            </h3>
            <p class="section-subtext">Fixed allocations reset annually (SPL, Solo Parent, Forced Leave).</p>

                <div class="credits-card-container mb-8">
                    <div class="card-header">
                        <div class="header-title">
                            <i class="fas fa-layer-group text-emerald-500 text-sm"></i> 
                            <span>Leave Type</span>
                        </div>
                        <div class="header-title pr-14">
                            <i class="fas fa-calendar-check text-emerald-500 text-sm"></i>
                            <span>Allocation / Limit</span>
                        </div>
                    </div>

                    @foreach($statutoryLeaves as $type)
                        @php
                            $credit = $existingCredits->get($type->id);
                            $dbLocked = $credit && $credit->is_locked;
                            // Locked effectively if DB locked AND not Head HR
                            $isLocked = $dbLocked && !auth()->user()->isHeadHR();
                            $currentVal = $credit ? $credit->credits : '';
                        @endphp

                        <div class="credit-row {{ $isLocked ? 'locked-bg' : '' }}">
                            <div class="field-label">
                                {{ $type->type_name }}
                                @if($isLocked)
                                    <div class="status-badge status-locked mt-1 w-max">
                                        <i class="fas fa-lock text-[10px]"></i> Locked
                                    </div>
                                @elseif($dbLocked)
                                    <div class="status-badge bg-orange-100 text-orange-600 border border-orange-200 mt-1 w-max">
                                        <i class="fas fa-unlock-alt text-[10px]"></i> Unlocked (Head HR)
                                    </div>
                                @else
                                    <span class="field-sublabel">{{ $type->description }}</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-6">
                                <div class="input-wrapper">
                                    <input type="number" step="1" min="0" 
                                        name="credits[{{ $type->id }}]" 
                                        value="{{ $currentVal }}" 
                                        class="field-input" 
                                        placeholder="0" 
                                        {{ $isLocked ? 'readonly' : '' }}>
                                </div>

                                <div class="action-area">
                                     @if($isLocked)
                                        <button type="button" onclick="requestUnlock(<?php        echo $type->id; ?>, '<?php        echo addslashes($type->type_name); ?>')" class="btn-request">
                                            <i class="fas fa-key mr-1"></i> Request Unlock
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400">
                                            <i class="fas fa-pen mr-1"></i> Editable
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>


            </form>

            <!-- Section C: Compensatory Time Off (Manual Entry) -->
        @if(isset($ctoType) && $ctoType)
            <div class="cto-section">
                <div class="cto-header">
                    <div class="cto-icon-box">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="cto-title">Compensatory Over-Time Credit Management</h3>
                </div>
                <p class="cto-subtitle">
                    Manually add COC credits with specific expiration dates. Max total limit: 15 credits.
                </p>
                <div class="cto-grid">
                    <!-- Add Form -->
                    <div class="cto-card">
                        <h4 class="cto-card-title">Add New Credits</h4>
                        <form action="{{ route('hr-staff.manage-credits.add-cto', $user->id) }}" method="POST">
                            @csrf
                            <div class="cto-form-group">
                                <label class="cto-label">Credit Amount (Hours/Days)</label>
                                <input type="number" step="0.1" name="credit_amount" class="cto-input" required placeholder="0.0">
                            </div>
                            <div class="cto-form-group">
                                <label class="cto-label">Expiration Date</label>
                                <input type="date" name="expiration_date" class="cto-input" required>
                            </div>
                            <div class="cto-form-group">
                                <label class="cto-label">Remarks (Optional)</label>
                                <input type="text" name="remarks" class="cto-input" placeholder="Reason for credit...">
                            </div>
                            <button type="submit" class="btn-cto-submit">
                                <i class="fas fa-plus-circle"></i> Add CTO Credits
                            </button>
                        </form>
                    </div>
                    <!-- History Table -->
                    <div class="cto-card">
                         <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #f3f4f6;">
                            <h4 style="margin: 0; font-size: 0.95rem; font-weight: 800; color: #374151; text-transform: uppercase;">Active Credit Batches</h4>
                            <span style="font-size: 0.75rem; background: #e0e7ff; color: #4338ca; padding: 4px 10px; border-radius: 6px; font-weight: 700;">
                                Total: {{ $ctoCredits->sum('remaining_credits') }}
                            </span>
                        </div>
                        @if($ctoCredits->isEmpty())
                            <div style="text-align: center; padding: 40px 0; color: #9ca3af; font-style: italic; font-size: 0.875rem;">No active CTO credits found.</div>
                        @else
                            <div style="max-height: 240px; overflow-y: auto;">
                                <table style="width: 100%; font-size: 0.875rem; border-collapse: collapse;">
                                    <thead style="background: #f9fafb; position: sticky; top: 0;">
                                        <tr>
                                            <th style="padding: 10px; text-align: left; color: #6b7280; font-weight: 600; text-transform: uppercase; font-size: 0.7rem;">Added</th>
                                            <th style="padding: 10px; text-align: left; color: #6b7280; font-weight: 600; text-transform: uppercase; font-size: 0.7rem;">Expires</th>
                                            <th style="padding: 10px; text-align: right; color: #6b7280; font-weight: 600; text-transform: uppercase; font-size: 0.7rem;">Rem.</th>
                                        </tr>
                                    </thead>
                                    <tbody style="border-top: 1px solid #f3f4f6;">
                                        @foreach($ctoCredits as $batch)
                                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                                <td style="padding: 10px; color: #4b5563;">{{ $batch->created_at->format('M d, Y') }}<br><span style="font-size: 0.7rem; color: #9ca3af;">{{ $batch->remarks }}</span></td>
                                                <td style="padding: 10px; color: #ef4444; font-weight: 600;">{{ $batch->expiration_date->format('M d, Y') }}</td>
                                                <td style="padding: 10px; text-align: right; font-weight: 700; color: #111827;">{{ $batch->remaining_credits }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div class="flex justify-end p-6 bg-white rounded-xl border border-gray-100 shadow-sm mt-8">
            <button type="submit" form="creditsForm" class="btn-primary">
                <i class="fas fa-save"></i> Save All Credits
            </button>
        </div>
    </div>

        <!-- Modal -->
        <div id="unlockModal" class="modal-overlay">
            <div class="modal-box">
                <div class="mb-4">
                    <h3 class="modal-title">Request Permission to Edit</h3>
                    <p class="text-gray-500 text-sm">You need approval to modify defaults for <span id="modalTypeName" class="font-bold text-gray-800"></span>.</p>
                </div>

                <form action="{{ route('hr-staff.manage-credits.unlock-request', $user->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="leave_type_id" id="modalTypeId">

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide">Reason for Change</label>
                        <textarea name="reason" rows="3" class="modal-textarea" placeholder="e.g. Correction of typo, policy adjustment, reinstatement..." required></textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-primary" style="padding: 10px 20px; font-size: 0.95rem;">Send Request</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function requestUnlock(id, name) {
                document.getElementById('modalTypeId').value = id;
                document.getElementById('modalTypeName').innerText = name;
                document.getElementById('unlockModal').classList.add('open');
            }
            function closeModal() {
                document.getElementById('unlockModal').classList.remove('open');
            }
        </script>
@endsection
