@extends('layouts.sdo')

@section('title', 'Manage Signatories')

@section('page-title', 'Manage Leave Form Signatories')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/signatories.css') }}?v={{ time() }}">
<style>
    .page-intro {
        animation: fadeInDown 0.6s ease-out;
    }

    .signatories-grid .sig-card {
        opacity: 0;
        animation: backInDown 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    @foreach(range(1, 10) as $i)
        .signatories-grid .sig-card:nth-child({{ $i }}) {
            animation-delay: {{ 0.1 + ($i * 0.1) }}s;
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
</style>
@endpush

@section('content')
<div class="signatories-container">
    <div class="page-intro">
        <p>
            <i class="fas fa-info-circle mr-2 text-blue-500"></i>
            Define the names of the officials holding the following positions. 
            These names will automatically appear on generated Form 6 documents based on user selection.
        </p>
    </div>

    <form action="{{ route('admin.signatories.update') }}" method="POST" id="signatoriesForm">
        @csrf
        
        <div class="signatories-grid">
            @php
                $roleIcons = [
                    'CID CHIEF' => 'fas fa-book',
                    'CID CHIEF' => 'fas fa-book', // Support both
                    'SGOD CHIEF' => 'fas fa-project-diagram',
                    'AO' => 'fas fa-user-tie',
                    'ASDS' => 'fas fa-user-shield',
                    'SDS' => 'fas fa-universal-access',
                    'VERIFIER OF LEAVE CREDITS' => 'fas fa-user-check'
                ];
                $roleBadges = [
                    'CID CHIEF' => 'role-badge-cid',
                    'SGOD CHIEF' => 'role-badge-sgod',
                    'AO' => 'role-badge-ao',
                    'ASDS' => 'role-badge-asds',
                    'SDS' => 'role-badge-sds',
                    'VERIFIER OF LEAVE CREDITS' => 'role-badge-verifier'
                ];
                $headerClasses = [
                    'CID CHIEF' => 'header-cid',
                    'SGOD CHIEF' => 'header-sgod',
                    'AO' => 'header-ao',
                    'ASDS' => 'header-asds',
                    'SDS' => 'header-sds',
                    'VERIFIER OF LEAVE CREDITS' => 'header-verifier'
                ];
            @endphp

            @foreach($signatories as $index => $sig)
                @php
                    $posKey = strtoupper($sig->position);
                @endphp
                <div class="sig-card">
                    <div class="sig-card-header {{ $headerClasses[$posKey] ?? 'header-default' }}">
                        <div class="sig-role-info">
                            <div class="sig-role-icon">
                                <i class="{{ $roleIcons[$posKey] ?? 'fas fa-user' }}"></i>
                            </div>
                            <span class="sig-role-badge {{ $roleBadges[$posKey] ?? 'role-badge-default' }}">
                                {{ $sig->position }}
                            </span>
                        </div>
                        <div class="sig-active-status">
                            <i class="fas fa-check-circle"></i> Active Signatory
                        </div>
                    </div>
                    
                    <div class="sig-card-body">
                        <input type="hidden" name="signatories[{{ $index }}][id]" value="{{ $sig->id }}">
                        
                        <div class="sig-form-grid">
                            <div class="sig-input-group">
                                <label class="field-label">Full Name</label>
                                <input type="text" class="field-input" 
                                       name="signatories[{{ $index }}][name]" 
                                       value="{{ old("signatories.$index.name", $sig->name) }}" 
                                       placeholder="Enter official's full name">
                            </div>
                            <div class="sig-input-group">
                                <label class="field-label">Position Title</label>
                                <input type="text" class="field-input" 
                                       name="signatories[{{ $index }}][title]" 
                                       value="{{ old("signatories.$index.title", $sig->title) }}" 
                                       placeholder="Enter official's position title">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Fixed Action Bar -->
        <div class="action-bar">
            <div class="action-bar-content">
                <div class="status-indicator">
                    <div class="status-dot"></div>
                    <span>All changes are ready to be saved</span>
                </div>
                <button type="submit" class="btn-save-fixed">
                    <i class="fas fa-save"></i>
                    Save All Changes
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
