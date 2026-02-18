@extends('layouts.sdo')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/manage-users.css') }}">
<style>
    /* Override grid for approval content needs */
    .user-card {
        grid-template-columns: 2fr 1.2fr 1.2fr 0.5fr 1fr 150px; 
        align-items: center;
    }
    
    .approval-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
    }

    .btn-icon-only {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        background: transparent;
    }
    
    .btn-pdf { color: #dc2626; background: #fef2f2; }
    .btn-pdf:hover { background: #fee2e2; }
    
    .btn-approve { 
        color: #16a34a; 
        background: #f0fdf4; 
        padding: 0 12px; 
        font-weight: 600; 
        font-size: 0.85rem; 
        height: 32px; 
        gap: 6px; 
        display: inline-flex; 
        align-items: center;
        border-radius: 6px;
        border: 1px solid transparent;
        transition: all 0.2s;
    }
    .btn-approve:hover { background: #dcfce7; border-color: #86efac; }
    
    .btn-reject { color: #dc2626; background: #fff1f2; }
    .btn-reject:hover { background: #ffe4e6; }

    .header-card .user-meta-label {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
    }
    
    /* Responsive overrides */
    @media (max-width: 1024px) {
        .user-card {
            grid-template-columns: 1fr;
            gap: 16px;
            height: auto;
        }
        .header-card { display: none; }
        .user-details, .user-meta-value, .approval-actions {
            text-align: left;
            justify-content: flex-start;
        }
        .approval-actions { margin-top: 10px; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid" style="padding: 20px;">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ $title }}</h1>
        <div class="text-sm text-gray-500">
            Current User: {{ Auth::user()->full_name }} ({{ strtoupper(Auth::user()->role) }})
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <!-- Applications List -->
    <div class="user-list">
        @if(!$applications->isEmpty())
            <!-- Header Row -->
            <div class="user-card header-card" style="background: transparent; border: none; box-shadow: none; padding-bottom: 5px; opacity: 0.7;">
                <div class="user-meta-label">Applicant</div>
                <div class="user-meta-label">Leave Type</div>
                <div class="user-meta-label">Duration</div>
                <div class="user-meta-label">Days</div>
                <div class="user-meta-label" style="text-align: center;">Status</div>
                <div class="user-meta-label" style="text-align: right;">Actions</div>
            </div>
        @endif

        @forelse($applications as $app)
            <div class="user-card">
                <!-- 1. Applicant Column -->
                <div class="user-info">
                    <div class="user-avatar">
                        @if($app->user->profile_picture)
                            <img src="{{ storage_url($app->user->profile_picture) }}" alt="{{ $app->user->full_name }}">
                        @else
                            {{ strtoupper(substr($app->user->full_name, 0, 2)) }}
                        @endif
                    </div>
                    <div class="user-details">
                        <div class="user-name">{{ $app->user->full_name }}</div>
                        <div class="user-email">{{ $app->user->position }}</div>
                    </div>
                </div>
                
                <!-- 2. Type Column -->
                <div>
                    <span class="user-meta-label">Leave Type</span>
                    <span class="user-meta-value">{{ $app->leaveType->type_name }}</span>
                    <div class="text-xs text-gray-400" style="font-size: 0.75rem; color: #94a3b8; margin-top: 4px;">
                        Filed: {{ $app->date_filing->format('M d, Y') }}
                    </div>
                </div>
                
                <!-- 3. Dates Column -->
                <div>
                    <span class="user-meta-label">Dates</span>
                    <span class="user-meta-value">
                        @if($app->start_date && $app->end_date)
                            {{ $app->start_date->format('M d') }} - {{ $app->end_date->format('M d, Y') }}
                        @else
                            Recall dates
                        @endif
                    </span>
                </div>
                
                <!-- 4. Days Column -->
                <div>
                    <span class="user-meta-label">Days</span>
                    <span class="badge" style="background: #e0f2fe; color: #0284c7;">{{ $app->days_applied }}</span>
                </div>
                
                <!-- 5. Status Column -->
                <div style="text-align: center;">
                    <span class="user-meta-label">Status</span>
                    <span class="badge badge-warning" style="font-size: 0.8rem;">
                        {{ $app->status }}
                    </span>
                </div>
                
                <!-- 6. Actions Column -->
                <div class="approval-actions">
                    <a href="{{ route('user.leave.approvals.show', $app->id) }}" class="btn-approve" style="background: #eff6ff; color: #2563eb; width: auto; padding: 6px 16px;">
                        <i class="fas fa-eye mr-2"></i> Review Application
                    </a>
                </div>
            </div>
        @empty
            <div class="empty-state" style="text-align: center; padding: 60px; background: white; border-radius: 16px; border: 2px dashed #e2e8f0;">
                <div class="empty-state-icon" style="width: 80px; height: 80px; background: #f8fafc; color: #cbd5e1; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 20px;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 style="color: #64748b; font-weight: 600;">No Pending Approvals</h3>
                <p style="color: #94a3b8;">You are all caught up!</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="modal-backdrop" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center;">
    <div class="modal-content" style="background: white; border-radius: 16px; width: 400px; max-width: 90%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
        <div class="modal-header">
            <h3 style="font-size: 1.1rem; font-weight: 700;">Disapprove Application</h3>
        </div>
        <form id="rejectForm" method="POST" action="">
            @csrf
            <div class="modal-body">
                <p style="margin-bottom: 12px; color: #64748b; font-size: 0.9rem;">Please provide a reason for disapproval.</p>
                <textarea name="remarks" class="form-control" rows="4" required placeholder="Reason for rejection..." style="height: auto; padding-top: 12px;"></textarea>
            </div>
            <div class="modal-footer" style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeRejectModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-danger">Disapprove</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openRejectModal(id) {
        const form = document.getElementById('rejectForm');
        form.action = "/user/leave/approvals/" + id + "/reject";
        const modal = document.getElementById('rejectModal');
        modal.style.display = 'flex';
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('rejectModal');
        if (event.target == modal) {
            closeRejectModal();
        }
    }
</script>
@endpush
@endsection
