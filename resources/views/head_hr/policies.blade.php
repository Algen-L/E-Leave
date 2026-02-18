@extends('layouts.sdo')

@section('title', 'Leave Policies')
@section('page-title', 'Policies Configuration')

@push('styles')
<style>
    /* Global Layout */
    .policy-wrapper { max-width: 1000px; margin: 0 auto; }
    
    /* Policy Card Container */
    .policy-card {
        background: white;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        margin-bottom: 16px;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        transition: box-shadow 0.2s;
    }
    .policy-card:hover { box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }

    /* Header */
    .policy-header {
        padding: 16px 24px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
        border-left: 4px solid transparent;
        transition: all 0.2s;
    }
    .policy-header:hover { background: #fdfdfd; }
    .policy-header.active-header { border-left-color: #3b82f6; background: #f8fafc; border-bottom: 1px solid #f1f5f9; }

    /* Collapsible Body */
    .policy-body { 
        padding: 32px 40px; 
        background: #fdfdfd;
        display: none; 
    }
    .policy-body.active { display: block; animation: fadeIn 0.3s ease-in-out; }
    
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }

    /* Internal Cards */
    .settings-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 32px;
    }
    .setting-card {
        background: white;
        border: 1px solid #eef2f6;
        border-radius: 8px;
        padding: 24px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }
    .setting-title {
        font-size: 0.8rem;
        font-weight: 700;
        color: #1e293b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f1f5f9;
    }

    /* Form Elements */
    .input-group { margin-bottom: 16px; }
    .input-group:last-child { margin-bottom: 0; }
    
    .lbl { 
        display: block; 
        font-size: 0.85rem; 
        font-weight: 600; 
        color: #475569; 
        margin-bottom: 6px; 
    }
    .input-std {
        width: 100%;
        padding: 8px 12px;
        font-size: 0.9rem;
        color: #334155;
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        transition: border 0.2s, box-shadow 0.2s;
    }
    .input-std:focus {
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
    }
    
    /* Specific Input Widths */
    .w-short { max-width: 120px; }
    .w-medium { max-width: 240px; }

    .help-text {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 5px;
        font-style: italic;
    }

    /* Bottom Section */
    .limit-section {
        max-width: 500px;
        margin: 0 auto 32px auto;
        padding-top: 24px;
        border-top: 1px dashed #e2e8f0;
        text-align: center;
    }
    .limit-input-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* Action Footer */
    .form-footer {
        display: flex;
        justify-content: flex-end;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }
    .btn-save {
        background: #0f172a;
        color: white;
        padding: 10px 24px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: background 0.2s;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-save:hover { background: #1e293b; }

    /* Typography & Icons */
    .title-text { font-size: 1rem; font-weight: 700; color: #334155; }
    .badge-status {
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
        padding: 4px 10px; border-radius: 20px; letter-spacing: 0.03em;
    }
    .bg-green { background: #ecfdf5; color: #047857; }
    .bg-gray { background: #f1f5f9; color: #64748b; }

    .chevron { color: #cbd5e1; transition: transform 0.2s; }
    .chevron.active { transform: rotate(180deg); color: #64748b; }
</style>
@endpush

@section('content')
<div class="policy-wrapper">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Leave Credit Policies</h2>
            <p class="text-sm text-gray-500 mt-1">Configure accumulation and expiration rules for each leave type.</p>
        </div>
        <button onclick="toggleCreateModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow-sm text-sm font-medium transition-colors flex items-center gap-2">
            <i class="fas fa-plus"></i> New Leave Type
        </button>
    </div>

    <!-- Create Leave Type Modal -->
    <div id="createLeaveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Create New Leave Type</h3>
                <form action="{{ route('head-hr.leave-types.store') }}" method="POST" class="mt-4 text-left">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="type_name">
                            Leave Type Name
                        </label>
                        <input type="text" name="type_name" id="type_name" required 
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                            Description (Optional)
                        </label>
                        <textarea name="description" id="description" rows="3" 
                                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                    </div>
                    <div class="flex items-center justify-end mt-4 gap-2">
                        <button type="button" onclick="toggleCreateModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition-colors">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors">Create</button>
                    </div>
                </form>
            </div>
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

            // Define special leave types with no expiration
            $noExpiryLeaves = [
                'Maternity Leave' => [
                    'details' => '105 days per pregnancy',
                    'usage' => 'Used per childbirth'
                ],
                'Paternity Leave' => [
                    'details' => '7 days per childbirth',
                    'usage' => 'Used per delivery of spouse'
                ],
                'VAWC Leave' => [
                    'details' => '10 days per valid case',
                    'usage' => 'Used when needed'
                ],
                'Rehabilitation Leave' => [
                    'details' => 'Up to 6 months',
                    'usage' => 'Based on medical recommendation'
                ],
                'Special Leave Benefits for Women' => [
                    'details' => 'Up to 2 months',
                    'usage' => 'Used per gynecological surgery'
                ],
                'Terminal Leave' => [
                    'details' => 'Based on total unused leave credits',
                    'usage' => 'Used upon resignation or retirement'
                ],
                'Adoption Leave' => [
                    'details' => 'Used per approved adoption',
                    'usage' => ''
                ],
            ];
            
            $specialType = $noExpiryLeaves[$type->type_name] ?? null;
        @endphp
        
        <div class="policy-card {{ $specialType ? 'border-l-4 border-indigo-500' : '' }}">
            <div class="policy-header" onclick="togglePolicy('{{ $type->id }}')" id="header-{{ $type->id }}">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                             <h3 class="title-text">{{ $type->type_name }}</h3>
                             @if($specialType)
                                <span class="bg-indigo-100 text-indigo-700 text-xs px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">
                                    No Expiry
                                </span>
                             @endif
                        </div>
                        <div class="mt-1">
                            @if($isMandatory)
                                <span class="badge-status bg-blue-100 text-blue-800">System Managed</span>
                            @elseif($isConfigured)
                                <span class="badge-status bg-green">Configured</span>
                            @else
                                <span class="badge-status bg-gray">Not Configured</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-xs text-gray-400 font-medium">Click to manage</span>
                    <i class="fas fa-chevron-down chevron" id="chevron-{{ $type->id }}"></i>
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
                @elseif($type->type_name === 'Compensatory Time Off')
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
                @else
                    @if($specialType)
                        <div class="bg-indigo-50 border border-indigo-100 rounded-md p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-indigo-500 mt-0.5"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-indigo-800">LEAVES WITH NO YEARLY EXPIRATION</h3>
                                    <div class="mt-2 text-sm text-indigo-700">
                                        <p class="font-bold">{{ $specialType['details'] }}</p>
                                        @if($specialType['usage'])
                                            <p>{{ $specialType['usage'] }}</p>
                                        @endif
                                        <p class="mt-1 text-xs text-indigo-500 italic">These depend on event or case. They do not expire at the end of the year.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

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
                        <div class="limit-section">
                            <div class="limit-input-container">
                                <label class="lbl text-base mb-2">Maximum Credit Limit</label>
                                <div class="relative">
                                    <input type="number" step="0.01" name="max_credits" value="{{ $maxCredits }}" placeholder="∞" class="input-std w-short text-center font-bold">
                                </div>
                                <div class="help-text mt-2"> Leave empty for unlimited accumulation.</div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="form-footer">
                            <button type="submit" class="btn-save">
                                <i class="fas fa-save"></i> Save Configuration
                            </button>
                        </div>
                    </form>
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
        modal.classList.toggle('hidden');
    }
</script>
@endsection
