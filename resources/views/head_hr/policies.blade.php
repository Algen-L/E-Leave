@extends('layouts.sdo')

@section('title', 'Leave Policies')
@section('page-title', 'Policies Configuration')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/policies.css') }}">
<style>
    .policy-actions-group {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
    }

    .btn-delete-type {
        width: 36px !important;
        height: 36px !important;
        border-radius: 10px !important;
        background: #fff !important;
        color: #ef4444 !important;
        border: 1px solid #fee2e2 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: 0 1px 2px rgba(239, 68, 68, 0.05) !important;
    }

    .btn-delete-type:hover {
        background: #ef4444 !important;
        color: #fff !important;
        border-color: #ef4444 !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2) !important;
    }
    
    .action-divider {
        width: 1px !important;
        height: 24px !important;
        background: #e2e8f0 !important;
        margin: 0 4px !important;
    }

    .toggle-trigger {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        padding: 6px 12px !important;
        border-radius: 8px !important;
        transition: all 0.2s !important;
        color: #64748b !important;
        font-size: 0.8125rem !important;
        font-weight: 600 !important;
    }

    .policy-header:hover .toggle-trigger {
        background: #f1f5f9 !important;
        color: #334155 !important;
    }
</style>
@endpush

@section('content')
<div class="policy-wrapper">
    <div class="page-header-card">
        <div class="header-info-group">
            <div class="header-icon-box">
                <i class="fas fa-sliders-h"></i>
            </div>
            <div class="header-title-text">
                <h2>Leave Credit Policies</h2>
                <p>Configure accumulation and expiration rules for each leave type.</p>
            </div>
        </div>
        <button onclick="toggleCreateModal()" class="btn-create-leave">
            <i class="fas fa-plus-circle"></i>
            <span>New Leave Type</span>
        </button>
    </div>

    <!-- Create Leave Type Modal -->
    <div id="createLeaveModal" class="modal-overlay" onclick="handleOutsideClick(event)">
        <div class="modal-content-new">
            <div class="modal-header-new">
                <h3>Create New Leave Type</h3>
                <p>Define a new category of leave for the system.</p>
                <button type="button" onclick="toggleCreateModal()" class="modal-close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('head-hr.leave-types.store') }}" method="POST">
                @csrf
                <div class="modal-body-new">
                    <div class="form-group-new">
                        <label class="form-label-new" for="type_name">Leave Type Name</label>
                        <div class="input-wrapper-new">
                            <i class="fas fa-tag input-icon-new"></i>
                            <input type="text" name="type_name" id="type_name" required 
                                   placeholder="e.g. Special Privilege Leave"
                                   class="input-new">
                        </div>
                    </div>
                    <div class="form-group-new">
                        <label class="form-label-new" for="description">Description (Optional)</label>
                        <textarea name="description" id="description" rows="3" 
                                  placeholder="Provide context or rules for this leave type..."
                                  class="input-new textarea-new"></textarea>
                    </div>
                </div>
                <div class="modal-footer-new">
                    <button type="button" onclick="toggleCreateModal()" class="btn-new btn-cancel-new">Cancel</button>
                    <button type="submit" class="btn-new btn-submit-new">Create Leave Type</button>
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
                'Adoption Leave' => [
                    'details' => '60 days per approved adoption',
                    'usage' => 'Used upon legal placement of child'
                ],
                'VAWC Leave' => [
                    'details' => 'Up to 10 days per year (extendable)',
                    'usage' => 'Used for victims of violence'
                ],
                'VAWC Leave (RA 9262)' => [
                    'details' => 'Up to 10 days per year (extendable)',
                    'usage' => 'Used for victims of violence'
                ],
                '10-Day VAWC Leave' => [
                    'details' => '10 days per incident',
                    'usage' => 'Used for victims of violence'
                ],
                'Rehabilitation Leave' => [
                    'details' => 'Up to 6 months',
                    'usage' => 'Based on injury sustained in performance of duty'
                ],
                'Rehabilitation Privilege' => [
                    'details' => 'Up to 6 months',
                    'usage' => 'Based on injury sustained in performance of duty'
                ],
                'Special Leave Benefits for Women' => [
                    'details' => 'Up to 2 months',
                    'usage' => 'Used per gynecological surgery'
                ],
                'Special Emergency (Calamity) Leave' => [
                    'details' => '5 days per year',
                    'usage' => 'Used during natural calamities/disasters'
                ],
                'Study Leave' => [
                    'details' => 'Up to 6 months or 1 year',
                    'usage' => 'Used for bar/board exam review or completion of degree'
                ],
                'Terminal Leave' => [
                    'details' => 'Lump sum of accumulated credits',
                    'usage' => 'Used upon separation from service'
                ],
                'Monetization of Leave Credits' => [
                    'details' => 'Conversion of credits to cash',
                    'usage' => 'Used for emergency or financial needs (subject to budget)'
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
                            @elseif($specialType)
                                <span class="badge-status bg-indigo-100 text-indigo-800">Per-Instance</span>
                            @elseif($isConfigured)
                                <span class="badge-status bg-green">Configured</span>
                            @else
                                <span class="badge-status bg-gray">Not Configured</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="policy-actions-group">
                    @if(auth()->user()->isSuperAdmin())
                        <form action="{{ route('head-hr.leave-types.delete', $type->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this leave type? This will also remove any associated credit policies.')" style="margin: 0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete-type" title="Delete Leave Type" onclick="event.stopPropagation()">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                        <div class="action-divider"></div>
                    @endif
                    <div class="toggle-trigger">
                        <span>Click to manage</span>
                        <i class="fas fa-chevron-down chevron" id="chevron-{{ $type->id }}"></i>
                    </div>
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
                @elseif($specialType)
                    <div class="bg-indigo-50 border border-indigo-100 rounded-md p-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-calendar-check text-indigo-500 mt-1"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-bold text-indigo-800">Event-Based / Per-Instance Policy</h3>
                                <div class="mt-2 text-sm text-indigo-700 space-y-2">
                                    <p>This leave type is granted based on specific life events, medical conditions, or legal entitlements rather than monthly accrual.</p>
                                    <ul class="list-disc pl-5 mt-3 space-y-1">
                                        <li><strong>Entitlement:</strong> {{ $specialType['details'] }}</li>
                                        <li><strong>Trigger:</strong> {{ $specialType['usage'] }}</li>
                                        <li>This leave does <strong>not expire</strong> annually; it is available whenever the qualifying event occurs.</li>
                                        <li>Credits are not "earned" over time but are available in full upon validation of the required documents.</li>
                                    </ul>
                                    <div class="mt-4 p-3 bg-white bg-opacity-60 rounded border border-indigo-100">
                                        <p class="font-medium text-indigo-900"><i class="fas fa-check-circle mr-1"></i> No Credit Configuration Required</p>
                                        <p class="text-xs text-indigo-600 mt-1">The system bypasses manual credit allocation for this category.</p>
                                    </div>
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
        modal.classList.toggle('active');
        
        if (modal.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                document.getElementById('type_name').focus();
            }, 300);
        } else {
            document.body.style.overflow = '';
        }
    }

    function handleOutsideClick(event) {
        if (event.target.id === 'createLeaveModal') {
            toggleCreateModal();
        }
    }
</script>
@endsection
