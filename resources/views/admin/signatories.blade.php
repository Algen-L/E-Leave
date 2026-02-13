@extends('layouts.sdo')

@section('title', 'Manage Signatories')

@section('page-title', 'Manage Leave Form Signatories')

@push('styles')
<style>
    .signatory-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        max-width: 800px;
        margin: 0 auto;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .field-label {
        display: block;
        font-weight: 600;
        font-size: 0.85rem;
        color: #475569;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .field-input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 0.95rem;
        color: #1e293b;
        transition: all 0.2s;
    }
    .field-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(15, 76, 117, 0.1);
    }
    .position-badge {
        display: inline-block;
        padding: 4px 8px;
        background: #f1f5f9;
        border-radius: 6px;
        color: #64748b;
        font-size: 0.75rem;
        font-weight: 700;
        margin-bottom: 4px;
    }
    .btn-save {
        padding: 12px 24px;
        background: var(--primary);
        color: white;
        font-weight: 600;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-save:hover {
        background: #0e3f5f;
    }
</style>
@endpush

@section('content')
<div class="signatory-card">
    <div class="mb-6 pb-4 border-b border-gray-100">
        <p class="text-sm text-gray-500">
            Define the names of the officials holding the following positions. 
            These names will automatically appear on generated Form 6 documents based on user selection.
        </p>
    </div>

    <form action="{{ route('admin.signatories.update') }}" method="POST">
        @csrf
        
        @foreach($signatories as $index => $sig)
            <div class="form-group border-b border-gray-100 pb-6 mb-6">
                <input type="hidden" name="signatories[{{ $index }}][id]" value="{{ $sig->id }}">
                
                <div class="mb-3">
                    <span class="position-badge">{{ $sig->position }}</span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Full Name</label>
                        <input type="text" class="field-input" 
                               name="signatories[{{ $index }}][name]" 
                               value="{{ old("signatories.$index.name", $sig->name) }}" 
                               placeholder="Enter full name">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Position Title</label>
                        <input type="text" class="field-input" 
                               name="signatories[{{ $index }}][title]" 
                               value="{{ old("signatories.$index.title", $sig->title) }}" 
                               placeholder="Enter position title">
                    </div>
                </div>
            </div>
        @endforeach

        <div class="flex justify-end mt-8">
            <button type="submit" class="btn-save">
                <i class="fas fa-save mr-2"></i> Save Signatories
            </button>
        </div>
    </form>
</div>
@endsection
