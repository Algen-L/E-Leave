@extends('layouts.sdo')

@section('title', 'Leave Policies')
@section('page-title', 'Policies Configuration')

@push('styles')
    <style>
        /* Modern Layout & Typography */
        .policy-wrapper {
            max-width: 1100px;
            margin: 0 auto;
        }

        .premium-header-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid white;
            border-radius: 20px;
            padding: 24px 32px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Policy Card Styling */
        .policy-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #eef2f6;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .policy-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .policy-header {
            padding: 20px 28px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            transition: all 0.2s;
        }

        .policy-header:hover {
            background: #f8fafc;
        }

        .policy-header.active-header {
            background: #fcfdff;
            border-bottom: 1px solid #f1f5f9;
        }

        .policy-body {
            padding: 32px;
            background: #fff;
            display: none;
        }

        .policy-body.active {
            display: block;
            animation: slideDown 0.4s ease-out;
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
            gap: 24px;
            margin-bottom: 24px;
        }

        .setting-card {
            background: #f8fafc;
            border: 1px solid #eef2f6;
            border-radius: 16px;
            padding: 24px;
            transition: all 0.2s;
        }

        .setting-card:hover {
            background: #f1f5f9;
            border-color: #e2e8f0;
        }

        .setting-title {
            font-size: 0.75rem;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
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
            border-color: #3b82f6;
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
            background: #1e293b;
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-premium:hover {
            background: #0f172a;
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .btn-indigo {
            background: #4f46e5;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }

        .btn-indigo:hover {
            background: #4338ca;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
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
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #dbeafe;
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
            color: #4f46e5;
        }

        /* Limit Section */
        .limit-card {
            background: #fff;
            border: 2px dashed #e2e8f0;
            border-radius: 20px;
            padding: 24px;
            text-align: center;
            margin-bottom: 24px;
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
            background: white;
            border-radius: 24px;
            width: 100%;
            max-width: 500px;
            padding: 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform: scale(0.95);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .modal-overlay.active .premium-modal {
            transform: scale(1);
            opacity: 1;
        }
    </style>
@endpush

@section('content')
    <div class="policy-wrapper">
        <div class="premium-header-card">
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Leave Credit Policies</h2>
                <p class="text-slate-500 font-medium mt-1">Configure accumulation and expiration rules for each leave type.</p>
            </div>
            <button onclick="toggleCreateModal()" class="btn-premium btn-indigo">
                <i class="fas fa-plus-circle text-lg"></i> New Leave Type
            </button>
        </div>

        <!-- Create Leave Type Modal Redesign -->
        <div id="createLeaveModal" class="modal-overlay">
            <div class="premium-modal">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-100">
                            <i class="fas fa-plus"></i>
                        </div>
                        <h3 class="text-xl font-black text-slate-800">Create New Leave Type</h3>
                    </div>
                    <button onclick="toggleCreateModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form action="{{ route('head-hr.leave-types.store') }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="lbl" for="type_name">Leave Type Name</label>
                        <input type="text" name="type_name" id="type_name" required 
                               class="input-std" placeholder="e.g., Vacation Leave">
                    </div>
                    <div class="mb-8">
                        <label class="lbl" for="description">Description (Optional)</label>
                        <textarea name="description" id="description" rows="3" 
                                  class="input-std h-auto" placeholder="Briefly describe the purpose of this leave type..."></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-3">
                        <button type="button" onclick="toggleCreateModal()" 
                                class="px-6 py-3 text-slate-500 font-bold hover:bg-slate-50 rounded-xl transition-all">Cancel</button>
                        <button type="submit" class="btn-premium btn-indigo px-8">Create Leave Type</button>
                    </div>
                </form>
            </div>
        </div>

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
            @endphp

            <div class="policy-card">
                <div class="policy-header" onclick="togglePolicy('{{ $type->id }}')" id="header-{{ $type->id }}">
                    <div class="flex items-center gap-5">
                        <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500 shadow-sm">
                            <i class="fas fa-file-contract text-lg"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-3">
                                 <h3 class="text-lg font-black text-slate-800 tracking-tight">{{ $type->type_name }}</h3>
                                 @if($specialType)
                                    <span class="bg-indigo-50 text-indigo-600 text-[10px] px-2 py-1 rounded-md font-black uppercase tracking-wider border border-indigo-100">
                                        Special
                                    </span>
                                 @endif
                            </div>
                            <div class="mt-1.5 flex items-center gap-2">
                                @if($isMandatory)
                                    <div class="status-badge badge-system">
                                        <i class="fas fa-shield-alt text-[10px]"></i> System Managed
                                    </div>
                                @elseif($isConfigured)
                                    <div class="status-badge badge-configured">
                                        <i class="fas fa-check-circle text-[10px]"></i> Configured
                                    </div>
                                @else
                                    <div class="status-badge badge-none">
                                        <i class="fas fa-circle-notch text-[10px]"></i> Not Configured
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-6">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Click to manage</span>
                        <i class="fas fa-chevron-down chevron text-lg" id="chevron-{{ $type->id }}"></i>
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
                    @elseif($type->type_name === 'COC Compensatory Overtime Credit')
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
                                <div class="setting-card">
                                    <div class="setting-title"><i class="fas fa-chart-line text-blue-500 mr-2"></i> Accrual Settings</div>

                                    <div class="input-group">
                                        <label class="lbl">Credits to Add</label>
                                        <div class="flex items-center gap-3">
                                            <input type="number" step="0.01" name="accrual_rate" value="{{ $accrualRate }}" class="input-std w-short" required>
                                            <span class="text-sm text-gray-400">credits</span>
                                        </div>
                                        <div class="help-text">Amount added per cycle.</div>
                                    </div>

                                    <div class="input-group">
                                        <label class="lbl">Frequency</label>
                                        <select name="accrual_period" class="input-std w-medium">
                                            <option value="None" {{ $accrualPeriod == 'None' ? 'selected' : '' }}>None (Manual Only)</option>
                                            <option value="Monthly" {{ $accrualPeriod == 'Monthly' ? 'selected' : '' }}>Monthly (Every 24th)</option>
                                            <option value="Yearly" {{ $accrualPeriod == 'Yearly' ? 'selected' : '' }}>Yearly</option>
                                        </select>
                                        <div class="help-text">How often credits are automatically added.</div>
                                    </div>
                                </div>

                                <!-- Right: Expiration -->
                                <div class="setting-card">
                                    <div class="setting-title"><i class="fas fa-hourglass-end text-orange-500 mr-2"></i> Expiration Rules</div>

                                    <div class="input-group">
                                        <label class="lbl">Expiry Type</label>
                                        <select name="expiration_rule" class="input-std w-full" onchange="toggleDate(this, '{{ $type->id }}')">
                                            <option value="None" {{ $expirationRule == 'None' ? 'selected' : '' }}>No Expiry</option>
                                            <option value="Yearly" {{ $expirationRule == 'Yearly' ? 'selected' : '' }}>Yearly (Reset Jan 1)</option>
                                            <option value="Monthly" {{ $expirationRule == 'Monthly' ? 'selected' : '' }}>End of Month</option>
                                            <option value="SpecificDate" {{ $expirationRule == 'SpecificDate' ? 'selected' : '' }}>Specific Date</option>
                                        </select>
                                        <div class="help-text">When unused credits should be forfeited.</div>
                                    </div>

                                    <div id="date_wrapper_{{ $type->id }}" class="input-group {{ $expirationRule !== 'SpecificDate' ? 'hidden' : '' }}">
                                        <label class="lbl">Expiration Date</label>
                                        <input type="date" name="expiration_date" value="{{ $policy->expiration_date ?? '' }}" class="input-std w-medium">
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom: Limits -->
                            <div class="limit-card">
                                <label class="lbl text-base mb-3">Maximum Accumulation Limit</label>
                                <div class="flex flex-col items-center">
                                    <div class="relative w-short">
                                        <input type="number" step="0.01" name="max_credits" value="{{ $maxCredits }}" 
                                               placeholder="∞" class="input-std text-center font-bold text-lg">
                                    </div>
                                    <div class="help-text mt-3 bg-blue-50 text-blue-600 px-4 py-2 rounded-lg border border-blue-100 italic">
                                        <i class="fas fa-info-circle mr-1"></i> Leave empty for unlimited accumulation (∞).
                                    </div>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="flex justify-between items-center pt-6 border-t border-slate-100 mt-6">
                                @if(auth()->check() && auth()->user()->isSuperAdmin())
                                    <button type="button" onclick="confirmDelete('{{ $type->id }}', '{{ addslashes($type->type_name) }}')" class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors text-sm font-bold flex items-center gap-2 border border-red-200">
                                        <i class="fas fa-trash-alt"></i> Delete Leave Type
                                    </button>
                                @else
                                    <div></div>
                                @endif
                                <button type="submit" class="btn-premium">
                                    <i class="fas fa-save"></i> Save Configuration
                                </button>
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
