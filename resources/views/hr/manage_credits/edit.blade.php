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
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); 
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
    .credit-row:last-child { border-bottom: none; }
    .credit-row:hover { background-color: #f8fafc; }
    
    /* Locked State */
    .locked-bg { background-color: #fcfcfc; }
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
    .input-wrapper { position: relative; width: 160px; }
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
        box-shadow: 0 0 0 3px rgba(59,130,246,0.15); 
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
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        display: none; /* Hidden by default */
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }
    .modal-overlay.open {
        display: flex; /* Show flex when open */
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
        padding: 20px 28px;
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
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('hr-staff.manage-credits') }}" class="w-10 h-10 rounded-full bg-white border flex items-center justify-center text-gray-500 hover:text-gray-800 transition-colors shadow-sm text-lg">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $user->full_name }}</h2>
            <p class="text-gray-500 text-sm mt-1">{{ $user->email }} • {{ $user->position ?? 'No Position' }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r shadow-sm flex items-start gap-3">
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

    <form action="{{ route('hr-staff.manage-credits.update', $user->id) }}" method="POST">
        @csrf
        
        <div class="credits-card-container mb-8">
            <div class="card-header">
                <div class="header-title">
                    <i class="fas fa-layer-group text-blue-400 text-sm"></i> 
                    <span>Leave Type Category</span>
                </div>
                <div class="header-title pr-14">
                    <i class="fas fa-coins text-blue-400 text-sm"></i>
                    <span>Allocation</span>
                </div>
            </div>
            
            @foreach($leaveTypes as $type)
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
                            <span class="field-sublabel">Input initial credits to allocate</span>
                        @endif
                    </div>
                    
                    <div class="flex items-center gap-6">
                        <div class="input-wrapper">
                            <input type="number" step="0.01" min="0" 
                                name="credits[{{ $type->id }}]" 
                                value="{{ $currentVal }}" 
                                class="field-input" 
                                placeholder="0.00" 
                                {{ $isLocked ? 'readonly' : '' }}>
                        </div>
                        
                        <div class="action-area">
                            @if($isLocked)
                                <button type="button" onclick="requestUnlock({{ $type->id }}, '{{ $type->type_name }}')" class="btn-request">
                                    <i class="fas fa-pen mr-1"></i> Request Edit
                                </button>
                            @else
                            <span class="text-gray-300 text-sm italic">Editable</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex justify-end p-6 bg-white rounded-xl border border-gray-100 shadow-sm">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Save All Credits
            </button>
        </div>
    </form>
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
