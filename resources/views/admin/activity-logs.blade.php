@extends('layouts.sdo')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/activity-logs.css') }}">
@endpush

@section('content')
<!-- Stats Summary -->
<div class="activity-stats">
    <div class="stat-card">
        <div class="stat-icon" style="background: #ecfdf5; color: #059669;">
            <i class="fas fa-sign-in-alt"></i>
        </div>
        <div class="stat-value">{{ $logs->where('action', 'login')->count() }}</div>
        <div class="stat-label">Logins Today</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">
            <i class="fas fa-plus-circle"></i>
        </div>
        <div class="stat-value">{{ $logs->where('action', 'like', '%Create%')->count() }}</div>
        <div class="stat-label">Created Records</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #fdf4ff; color: #a855f7;">
            <i class="fas fa-edit"></i>
        </div>
        <div class="stat-value">{{ $logs->where('action', 'like', '%Update%')->count() }}</div>
        <div class="stat-label">Updated Records</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #fee2e2; color: #dc2626;">
            <i class="fas fa-trash"></i>
        </div>
        <div class="stat-value">{{ $logs->where('action', 'like', '%Delete%')->count() }}</div>
        <div class="stat-label">Deleted Records</div>
    </div>
</div>

<!-- Premium Filter Container -->
<div class="premium-filter-container">
    <div class="filter-header">
        <div class="filter-title">
            <i class="fas fa-filter"></i>
            Filter Activities
        </div>
    </div>
    
    <form method="GET" action="{{ route('admin.activity-logs') }}">
        <div class="filter-row">
            <div class="filter-group" style="flex: 2;">
                <label class="filter-label">Search</label>
                <input type="text" class="custom-select" name="search" placeholder="Search by user, action, or details..." value="{{ $filters['search'] }}" style="background-image: none; padding-left: 14px;">
            </div>
            
            <div class="filter-group">
                <label class="filter-label">Action Type</label>
                <select class="custom-select" name="action">
                    <option value="">All Actions</option>
                    <option value="login" {{ ($filters['action'] ?? '') == 'login' ? 'selected' : '' }}>Login</option>
                    <option value="logout" {{ ($filters['action'] ?? '') == 'logout' ? 'selected' : '' }}>Logout</option>
                    <option value="create" {{ ($filters['action'] ?? '') == 'create' ? 'selected' : '' }}>Create</option>
                    <option value="update" {{ ($filters['action'] ?? '') == 'update' ? 'selected' : '' }}>Update</option>
                    <option value="delete" {{ ($filters['action'] ?? '') == 'delete' ? 'selected' : '' }}>Delete</option>
                </select>
            </div>
            
            <div class="filter-buttons">
                <button type="submit" class="btn-filter-apply">
                    <i class="fas fa-search"></i>
                    Apply Filters
                </button>
                <a href="{{ route('admin.activity-logs') }}" class="btn-filter-reset">
                    <i class="fas fa-redo"></i>
                    Reset
                </a>
            </div>
        </div>
        
        <input type="hidden" name="date_range" id="dateRangeInput" value="{{ $filters['date_range'] ?? '' }}">
        <div class="quick-dates">
            <span class="date-pill {{ ($filters['date_range'] ?? '') == '' ? 'active' : '' }}" data-range="">All Time</span>
            <span class="date-pill {{ ($filters['date_range'] ?? '') == 'today' ? 'active' : '' }}" data-range="today">Today</span>
            <span class="date-pill {{ ($filters['date_range'] ?? '') == '7days' ? 'active' : '' }}" data-range="7days">Last 7 Days</span>
            <span class="date-pill {{ ($filters['date_range'] ?? '') == '30days' ? 'active' : '' }}" data-range="30days">Last 30 Days</span>
        </div>
    </form>

<script>
document.querySelectorAll('.date-pill').forEach(pill => {
    pill.addEventListener('click', function() {
        document.querySelectorAll('.date-pill').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('dateRangeInput').value = this.dataset.range;
        this.closest('form').submit();
    });
});
</script>
</div>

<!-- Activity Feed -->
<div class="activity-feed">
    @forelse($logs as $log)
        @php
            $actionType = 'view';
            if (Str::contains(strtolower($log->action), 'login')) $actionType = 'login';
            elseif (Str::contains(strtolower($log->action), 'logout')) $actionType = 'logout';
            elseif (Str::contains(strtolower($log->action), ['create', 'register', 'add'])) $actionType = 'create';
            elseif (Str::contains(strtolower($log->action), ['update', 'edit', 'change'])) $actionType = 'update';
            elseif (Str::contains(strtolower($log->action), ['delete', 'remove'])) $actionType = 'delete';

            $actionLabels = [
                'login' => 'Logged In',
                'logout' => 'Logged Out',
                'create' => 'Created Record',
                'update' => 'Updated Record',
                'delete' => 'Deleted Record',
                'view' => $log->action,
            ];
        @endphp
        
        <div class="activity-item">
            <div class="activity-icon-simple">
                <i class="fas fa-clipboard-list"></i>
            </div>
            
            <div class="activity-content">
                <div class="activity-user-name">{{ $log->user->full_name ?? 'Unknown' }}</div>
                <div class="activity-action {{ $actionType }}">{{ $actionLabels[$actionType] }}</div>
            </div>
            
            <div class="activity-time">
                {{ $log->created_at->format('M d, Y') }} &bull; {{ $log->created_at->format('g:i A') }}
            </div>
        </div>
    @empty
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-history"></i>
            </div>
            <h3>No activity logs found</h3>
            <p>Activity will appear here as users interact with the system</p>
        </div>
    @endforelse
</div>
@endsection