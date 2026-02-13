@extends('layouts.sdo')

@section('title', 'Authentication Reset Management')
@section('page-title', 'Authentication Reset Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth-reset-management.css') }}">
@endpush

@section('content')
<!-- Stats Row -->
<div class="stats-row stats-row-4">
    <div class="stat-card stat-primary">
        <div class="stat-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <span class="stat-value">{{ $usersWithRequests }}</span>
            <span class="stat-label">USERS WITH REQUESTS</span>
        </div>
    </div>
    
    <div class="stat-card stat-danger">
        <div class="stat-icon">
            <i class="fas fa-ban"></i>
        </div>
        <div class="stat-content">
            <span class="stat-value">{{ $blockedUsers }}</span>
            <span class="stat-label">BLOCKED USERS</span>
        </div>
    </div>
    
    <div class="stat-card stat-success">
        <div class="stat-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <span class="stat-value">{{ $activeUsers }}</span>
            <span class="stat-label">ACTIVE (REGULAR)</span>
        </div>
    </div>
    
    <div class="stat-card stat-info">
        <div class="stat-icon">
            <i class="fas fa-envelope"></i>
        </div>
        <div class="stat-content">
            <span class="stat-value">{{ $totalOtpRequests }}</span>
            <span class="stat-label">TOTAL OTP REQUESTS</span>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-bar-card">
    <form method="GET" action="{{ route('admin.auth-reset-management') }}" class="filter-form-flex">
        <div class="search-wrapper">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" name="search" placeholder="Search by name or email..." value="{{ $filters['search'] ?? '' }}">
        </div>
        
        <select class="filter-select" name="status">
            <option value="">All Status</option>
            <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="blocked" {{ ($filters['status'] ?? '') === 'blocked' ? 'selected' : '' }}>Blocked</option>
        </select>
        
        <button type="submit" class="filter-btn">
            <i class="fas fa-filter"></i>
            Filter
        </button>
    </form>
</div>

<!-- Rate Limits Section -->
<div class="dashboard-card">
    <div class="card-header">
        <h2><i class="fas fa-key"></i> Authentication Reset Rate Limits</h2>
        <span class="record-count">Showing {{ count($trackingRecords) }} records</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table rate-limits-table">
                <thead>
                    <tr>
                        <th>USER</th>
                        <th>ROLE</th>
                        <th>PAGE VISITS</th>
                        <th>OTP REQUESTS</th>
                        <th>OTP INPUT</th>
                        <th>RESENDS</th>
                        <th>STATUS</th>
                        <th>LAST ACTIVITY</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trackingRecords as $record)
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar-sm {{ $record->user ? '' : 'guest' }}">
                                        @if($record->user && $record->user->profile_picture)
                                            <img src="{{ asset('storage/' . $record->user->profile_picture) }}" alt="">
                                        @else
                                            {{ strtoupper(substr($record->user->full_name ?? $record->email[0], 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="user-info-sm">
                                        <span class="user-name-sm">{{ $record->user->full_name ?? 'Guest User' }}</span>
                                        <span class="user-email-sm">{{ $record->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="role-badge role-{{ $record->user->role ?? 'guest' }}">
                                    {{ strtoupper($record->user->role ?? 'GUEST') }}
                                </span>
                            </td>
                            <td>
                                <span class="rate-value">{{ $record->page_visits }}</span>
                            </td>
                            <td>
                                <span class="rate-value">{{ $record->otp_requests }}</span>
                                <span class="rate-limit">/3</span>
                            </td>
                            <td>
                                <span class="rate-value">{{ $record->otp_inputs }}</span>
                                <span class="rate-limit">/5</span>
                            </td>
                            <td>
                                <span class="rate-value">{{ $record->resends }}</span>
                                <span class="rate-limit">/3</span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $record->is_blocked ? 'blocked' : 'active' }}">
                                    <i class="fas fa-{{ $record->is_blocked ? 'lock' : 'unlock' }}"></i>
                                    {{ $record->is_blocked ? 'Blocked' : 'Active' }}
                                </span>
                            </td>
                            <td>
                                <span class="last-activity">
                                    {{ $record->last_activity ? $record->last_activity->format('M j, Y') : '-' }}<br>
                                    <small>{{ $record->last_activity ? $record->last_activity->format('g:i A') : '' }}</small>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <form action="{{ route('admin.auth-reset.reset-counters', $record->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="action-icon-btn action-reset" title="Reset Counters">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                    </form>
                                    
                                    @if($record->is_blocked)
                                        <form action="{{ route('admin.auth-reset.unblock', $record->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="action-icon-btn action-unblock" title="Unblock User">
                                                <i class="fas fa-unlock"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.auth-reset.block', $record->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="action-icon-btn action-block" title="Block User">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($record->user)
                                        <button type="button" class="action-icon-btn action-email" onclick="sendPasswordReset('{{ $record->email }}')" title="Send Password Reset">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                    @endif
                                    
                                    <button type="button" class="action-icon-btn action-view" onclick="viewDetails({{ $record->id }})" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="empty-table">
                                <div class="empty-state-inline">
                                    <i class="fas fa-shield-alt"></i>
                                    <span>No authentication tracking records found</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div id="detailsModal" class="modal-overlay" style="display: none;">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-info-circle"></i> Security Details</h3>
            <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body" id="modalContent">
            <!-- Content loaded via JS -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function viewDetails(id) {
        // Show modal with loading state
        const modal = document.getElementById('detailsModal');
        const content = document.getElementById('modalContent');
        content.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
        modal.style.display = 'flex';
        
        // Fetch details via AJAX
        fetch(`{{ url('/admin/auth-reset') }}/${id}/details`)
            .then(response => response.json())
            .then(data => {
                content.innerHTML = `
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Email</label>
                            <span>${data.email}</span>
                        </div>
                        <div class="detail-item">
                            <label>Page Visits</label>
                            <span>${data.page_visits}</span>
                        </div>
                        <div class="detail-item">
                            <label>OTP Requests</label>
                            <span>${data.otp_requests} / 5</span>
                        </div>
                        <div class="detail-item">
                            <label>OTP Inputs</label>
                            <span>${data.otp_inputs} / 5</span>
                        </div>
                        <div class="detail-item">
                            <label>Resends</label>
                            <span>${data.resends} / 3</span>
                        </div>
                        <div class="detail-item">
                            <label>Status</label>
                            <span class="status-badge status-${data.is_blocked ? 'blocked' : 'active'}">
                                ${data.is_blocked ? 'Blocked' : 'Active'}
                            </span>
                        </div>
                        <div class="detail-item">
                            <label>Last Activity</label>
                            <span>${data.last_activity || 'N/A'}</span>
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                content.innerHTML = '<div class="error">Failed to load details</div>';
            });
    }
    
    function closeModal() {
        document.getElementById('detailsModal').style.display = 'none';
    }
    
    function sendPasswordReset(email) {
        if (confirm(`Send password reset email to ${email}?`)) {
            fetch('{{ route("admin.auth-reset.send-reset") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Password reset email sent successfully', 'success');
                } else {
                    showToast(data.message || 'Failed to send email', 'error');
                }
            })
            .catch(() => {
                showToast('An error occurred', 'error');
            });
        }
    }
    
    // Close modal on outside click
    document.getElementById('detailsModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
@endpush
