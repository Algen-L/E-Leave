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
            background: #f8faff;
            border: 1px solid #e0e7ff;
            border-radius: 20px;
            padding: 24px;
            margin-top: 24px;
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.05);
        }

        .coc-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 16px;
        }

        @media (max-width: 1024px) {
            .coc-grid {
                grid-template-columns: 1fr;
            }
        }

        .coc-inner-card {
            background: white;
            border-radius: 16px;
            padding: 16px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .coc-input-group {
            margin-bottom: 12px;
        }

        .coc-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-bottom: 8px;
        }

        .coc-input {
            width: 100%;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            font-size: 0.9rem;
            color: #1e293b;
            transition: all 0.2s;
            background: #fcfcfc;
        }

        .coc-input:focus {
            outline: none;
            border-color: #6366f1;
            background: white;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .coc-btn-add {
            width: 100%;
            background: #4f46e5;
            color: white;
            padding: 10px;
            border-radius: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
        }

        .coc-btn-add:hover {
            background: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px -3px rgba(79, 70, 229, 0.3);
        }

        .coc-table-container {
            overflow-y: auto;
            max-height: 280px;
            border-radius: 12px;
            border: 1px solid #f1f5f9;
        }

        .coc-table {
            width: 100%;
            border-collapse: collapse;
        }

        .coc-table th {
            background: #f8fafc;
            padding: 12px 16px;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .coc-table td {
            padding: 10px 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.85rem;
        }

        .coc-table tr:hover {
            background: #fdfdfd;
        }

        .coc-batch-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .coc-batch-date {
            font-weight: 600;
            color: #334155;
        }

        .coc-batch-remarks {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .coc-expiry-badge {
            display: inline-flex;
            padding: 4px 10px;
            background: #fef2f2;
            color: #ef4444;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .coc-remaining {
            font-size: 1.1rem;
            font-weight: 800;
            color: #4f46e5;
        }

        /* Collapsible COC Section */
        .coc-header {
            cursor: pointer;
            user-select: none;
            padding: 16px;
            margin: -24px -24px 0 -24px;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .coc-header:hover {
            background: #f1f5ff;
        }

        .coc-header.active-header {
            border-bottom: 1px solid #eef2ff;
            border-radius: 20px 20px 0 0;
            background: #fcfdff;
        }

        .coc-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0, 1, 0, 1);
            opacity: 0;
        }

        .coc-content.active {
            max-height: 2000px;
            opacity: 1;
            transition: max-height 0.4s ease-in, opacity 0.3s ease-in;
        }

        .coc-chevron {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .coc-chevron.rotate {
            transform: rotate(180deg);
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

        /* Header Styling */
        .card-header {
            background: #f8fafc;
            padding: 12px 24px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-title {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.08em;
            display: flex;
            align-items: center;
            gap: 8px;
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
    </style>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4">
        <div class="bg-gradient-to-r from-slate-100 to-slate-200 p-5 rounded-2xl mb-6 border border-white shadow-sm">
            <div class="flex items-center gap-6">
                <a href="{{ route('hr-staff.manage-credits') }}"
                    class="w-12 h-12 rounded-xl bg-white flex items-center justify-center text-gray-500 hover:text-indigo-600 transition-all hover:shadow-lg transform hover:-translate-x-1">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-full bg-white border-2 border-white shadow-md flex items-center justify-center text-xl font-bold text-indigo-600">
                        {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 tracking-tight">{{ $user->full_name }}</h2>
                        <div class="flex items-center gap-3 mt-1">
                            <span
                                class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-[10px] font-bold rounded uppercase tracking-wider">{{ $user->position ?? 'No Position' }}</span>
                            <span class="text-slate-400 text-sm italic">{{ $user->email }}</span>
                        </div>
                    </div>
                </div>
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

        <form action="{{ route('hr-staff.manage-credits.update', $user->id) }}" method="POST" id="manageCreditsForm">
            @csrf
        </form>

        @php
            $typesList = isset($otherTypes) ? $otherTypes : (isset($leaveTypes) ? $leaveTypes : []);
            $typesColl = is_array($typesList) ? collect($typesList) : $typesList;

            $creditLeaves = $typesColl->where('category', 'Credit');
            $statutoryLeaves = $typesColl->where('category', '!=', 'Credit');
            $eventBasedLeaves = ['Maternity Leave', 'Paternity Leave', 'VAWC Leave', 'Adoption Leave', 'Rehabilitation Leave', 'Special Leave Benefits for Women', 'Monetization of Leave Credits', 'Terminal Leave'];
        @endphp

        <div class="page-layout-grid">
            <!-- Left Column: Credit Base & COC -->
            <div class="layout-column">
                <!-- Section A: Credit-Based Leaves -->
                <div class="section-container">
                    <h3 class="text-xl font-black text-slate-800 mb-3 flex items-center gap-2">
                        <span
                            class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center text-sm shadow-lg shadow-blue-100">
                            <i class="fas fa-coins"></i>
                        </span>
                        Credit-Based Leaves
                    </h3>

                    <div class="credits-card-container">
                        <div class="card-header bg-blue-50/50">
                            <div class="header-title">
                                <i class="fas fa-layer-group text-blue-500 text-sm"></i>
                                <span>Leave Type</span>
                            </div>
                            <div class="header-title pr-14">
                                <i class="fas fa-calculator text-blue-500 text-sm"></i>
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
                                            value="{{ $currentVal }}" form="manageCreditsForm" class="field-input text-right"
                                            placeholder="0.000">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Section C: COC Management (Expandable) -->
                @if(isset($ctoType) && $ctoType)
                    <div class="coc-section-card !mt-0 !p-5 shadow-xl shadow-indigo-50 border-indigo-100">
                        <div class="coc-header active-header flex items-center justify-between !m-0 !p-0 !bg-transparent border-none"
                            onclick="toggleCocContent()">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-200">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-indigo-950 tracking-tight">COC Management</h3>
                                    <p class="text-[10px] text-indigo-400 font-bold uppercase tracking-wider">Manual Batch Entry
                                    </p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-down text-indigo-400 coc-chevron rotate" id="cocChevron"></i>
                        </div>

                        <div class="coc-content active" id="cocContent">
                            <div class="space-y-4 mt-4 pt-4 border-t border-indigo-50">
                                <!-- Add Form -->
                                <div class="coc-inner-card !p-5 !bg-white/50 border border-indigo-100/50">
                                    <h4 class="font-bold text-gray-800 mb-3 text-xs flex items-center gap-2">
                                        <i class="fas fa-plus-circle text-indigo-500"></i> Add New Credits
                                    </h4>
                                    <form action="{{ route('hr-staff.manage-credits.add-cto', $user->id) }}" method="POST"
                                        class="space-y-3">
                                        @csrf
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="coc-label !text-[10px]">Amount</label>
                                                <input type="number" step="0.1" name="credit_amount"
                                                    class="coc-input !py-2 !text-sm" required placeholder="0.0">
                                            </div>
                                            <div>
                                                <label class="coc-label !text-[10px]">Expiry</label>
                                                <input type="date" name="expiration_date" class="coc-input !py-2 !text-sm"
                                                    required>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="coc-label !text-[10px]">Remarks</label>
                                            <input type="text" name="remarks" class="coc-input !py-2 !text-sm"
                                                placeholder="e.g., OT Nov 2023">
                                        </div>
                                        <button type="submit"
                                            class="coc-btn-add !py-2.5 !rounded-xl text-sm shadow-lg shadow-indigo-100 transform active:scale-95 transition-all">
                                            <i class="fas fa-save mr-1"></i> Add COC Credits
                                        </button>
                                    </form>
                                </div>

                                <!-- Active Batches -->
                                <div class="coc-inner-card !p-5">
                                    <div class="flex justify-between items-center mb-4">
                                        <h4 class="font-bold text-gray-800 text-xs">Active Credit Batches</h4>
                                        <div class="px-2 py-1 bg-indigo-600 text-white text-[10px] font-black rounded-lg">
                                            TOTAL: {{ number_format($ctoCredits->sum('remaining_credits'), 2) }}
                                        </div>
                                    </div>
                                    <div class="coc-table-container !max-h-[200px]">
                                        @if($ctoCredits->isEmpty())
                                            <p class="text-[11px] text-gray-400 italic text-center py-6">No active COC credits.</p>
                                        @else
                                            <table class="coc-table !text-[11px]">
                                                <tbody>
                                                    @foreach($ctoCredits as $batch)
                                                        <tr class="!border-b border-slate-50">
                                                            <td class="!py-3 !px-0">
                                                                <div class="font-bold text-slate-700">
                                                                    {{ $batch->created_at->format('M d, Y') }}
                                                                </div>
                                                                <div class="text-[9px] text-slate-400 truncate max-w-[120px]">
                                                                    {{ $batch->remarks ?: 'No remarks' }}
                                                                </div>
                                                            </td>
                                                            <td class="!py-3 !px-0 text-red-500 font-medium whitespace-nowrap">
                                                                <i
                                                                    class="fas fa-history mr-1 opacity-50"></i>{{ $batch->expiration_date->format('M d, Y') }}
                                                            </td>
                                                            <td class="!py-2 !px-0 text-right font-black text-indigo-600 text-xl">
                                                                {{ number_format($batch->remaining_credits, 1) }}
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
                    <h3 class="text-xl font-black text-slate-800 mb-3 flex items-center gap-2">
                        <span
                            class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-sm shadow-lg shadow-emerald-100">
                            <i class="fas fa-file-contract"></i>
                        </span>
                        Statutory / Special Leaves
                    </h3>

                    <div class="credits-card-container">
                        <div class="card-header bg-emerald-50/50">
                            <div class="header-title">
                                <i class="fas fa-layer-group text-emerald-500 text-sm"></i>
                                <span>Leave Type</span>
                            </div>
                            <div class="header-title pr-14">
                                <i class="fas fa-calendar-check text-emerald-500 text-sm"></i>
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

                                <div class="credit-row">
                                    <div class="field-label flex-1">
                                        <div class="font-bold text-slate-700">{{ $type->type_name }}</div>
                                        <div class="text-[10px] text-slate-400 font-medium mt-0.5 line-clamp-1 italic">
                                            {{ $type->description }}
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <div class="input-wrapper w-[110px]">
                                            @if($isEventBased)
                                                <div
                                                    class="field-input bg-slate-50 text-slate-400 !border-dashed !font-bold text-[9px] h-[36px] flex items-center justify-center tracking-tighter shadow-inner">
                                                    EVENT-BASED
                                                </div>
                                                <input type="hidden" name="credits[{{ $type->id }}]" value=""
                                                    form="manageCreditsForm">
                                            @else
                                                <input type="number" step="1" min="0" name="credits[{{ $type->id }}]"
                                                    value="{{ $currentVal }}" form="manageCreditsForm"
                                                    class="field-input h-[36px] text-right font-bold" placeholder="0">
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

        <!-- Sticky Save Button Bar -->
        <div class="flex justify-center mt-8 mb-8">
            <button type="submit" form="manageCreditsForm"
                class="btn-primary px-12 py-4 text-lg font-black rounded-2xl shadow-2xl shadow-indigo-100 hover:shadow-indigo-200 transform hover:-translate-y-1 transition-all active:scale-95 flex items-center gap-3">
                <i class="fas fa-save text-2xl"></i> Save All User Credits
            </button>
        </div>
        <script>
            function toggleCocContent() {
                const content = document.getElementById('cocContent');
                const chevron = document.getElementById('cocChevron');
                const header = document.querySelector('.coc-header');

                content.classList.toggle('active');
                chevron.classList.toggle('rotate');
                header.classList.toggle('active-header');
            }
        </script>
    </div>
@endsection