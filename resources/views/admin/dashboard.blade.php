@extends('layouts.sdo')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Row -->
<div class="stats-row stats-row-4">
    <!-- Total Users Card -->
    <div class="stat-card stat-primary">
        <div class="stat-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <span class="stat-value">{{ $totalUsers }}</span>
            <span class="stat-label">Total Users</span>
        </div>
    </div>
    
    <!-- Active Today Card -->
    <div class="stat-card stat-success">
        <div class="stat-icon">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-content">
            <span class="stat-value">{{ $activeToday }}</span>
            <span class="stat-label">Active Today</span>
        </div>
    </div>
    
    <!-- New Registrations Card -->
    <div class="stat-card stat-warning">
        <div class="stat-icon">
            <i class="fas fa-user-plus"></i>
        </div>
        <div class="stat-content">
            <span class="stat-value">{{ $newRegistrations }}</span>
            <span class="stat-label">New This Month</span>
        </div>
    </div>
    
    <!-- Inactive Users Card -->
    <div class="stat-card stat-danger">
        <div class="stat-icon">
            <i class="fas fa-user-times"></i>
        </div>
        <div class="stat-content">
            <span class="stat-value">{{ $totalUsers - $activeToday }}</span>
            <span class="stat-label">Inactive</span>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="dashboard-card">
    <div class="card-header">
        <h2><i class="fas fa-history"></i> Recent Activity</h2>
        <a href="{{ route('admin.activity-logs') }}" class="btn btn-primary btn-sm">View All</a>
    </div>
    <div class="card-body">
        <div class="activity-feed">
            @forelse($auditTrail as $log)
                <div class="feed-item {{ strtolower($log->user->office_station ?? '') }}">
                    @if($log->user->profile_picture)
                        <img src="{{ storage_url($log->user->profile_picture) }}" alt="" class="feed-avatar">
                    @else
                        <div class="feed-avatar-placeholder">
                            {{ strtoupper(substr($log->user->full_name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                    <div class="feed-info">
                        <span class="feed-user">{{ $log->user->full_name ?? 'Unknown User' }}</span>
                        <span class="feed-activity">{{ $log->action }}: {{ Str::limit($log->details, 40) }}</span>
                    </div>
                    <span class="feed-time">{{ $log->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <div class="feed-item">
                    <div class="feed-info">
                        <span class="feed-activity" style="color: var(--text-muted);">No recent activity to display</span>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection