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
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800">Leave Credit Policies</h2>
        <p class="text-sm text-gray-500 mt-1">Configure accumulation and expiration rules for each leave type.</p>
    </div>

    @foreach($types as $type)
        @php 
            $policy = $policies->get($type->id); 
            $accrualRate = $policy->accrual_rate ?? 0;
            $accrualPeriod = $policy->accrual_period ?? 'Monthly';
            $expirationRule = $policy->expiration_rule ?? 'None';
            $maxCredits = $policy->max_credits ?? '';
            $isConfigured = $policy ? true : false;
        @endphp
        
        <div class="policy-card">
            <div class="policy-header" onclick="togglePolicy('{{ $type->id }}')" id="header-{{ $type->id }}">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div>
                        <h3 class="title-text">{{ $type->type_name }}</h3>
                        <div class="mt-1">
                            @if($isConfigured)
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
                                <div class="help-text">How often credits are automatically added. (Monthly run date: 24th)</div>
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
</script>
@endsection
