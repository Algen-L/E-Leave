@extends('layouts.sdo')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/leave-dashboard.css') }}">
    <style>
        .admin-dashboard-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 1200px) {
            .admin-dashboard-layout {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Stats Row (Always visible for admins) -->
    <div class="stats-row stats-row-4 mb-4">
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

    <div class="admin-dashboard-layout">
        <div class="admin-main-col">
            <!-- Personal Leave Section (Visible for non-superadmin/non-HR admins) -->
            @if(isset($credits) && $credits)
                <div class="leave-stats-container mt-0 mb-4">
                    <div class="credit-stats-grid">
                        <div class="credit-card">
                            <div class="credit-icon credit-vl"><i class="fas fa-plane"></i></div>
                            <span class="credit-val">{{ number_format($credits['vl'], 2) }}</span>
                            <span class="credit-label">Vacation Leave</span>
                        </div>
                        <div class="credit-card">
                            <div class="credit-icon credit-sl"><i class="fas fa-briefcase-medical"></i></div>
                            <span class="credit-val">{{ number_format($credits['sl'], 2) }}</span>
                            <span class="credit-label">Sick Leave</span>
                        </div>
                        <div class="credit-card">
                            <div class="credit-icon credit-cto"><i class="fas fa-clock"></i></div>
                            <span class="credit-val">{{ number_format($credits['cto'], 2) }}</span>
                            <span class="credit-label">CTO Credit</span>
                        </div>
                    </div>

                    <div class="leave-summary-card">
                        <div class="summary-header">
                            <i class="fas fa-clipboard-list"></i>
                            <h2>My Leave Summary</h2>
                        </div>
                        <div class="summary-grid">
                            <div class="summary-item">
                                <span class="summary-count">{{ $leaveSummary['pending'] }}</span>
                                <span class="summary-label">Pending</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-count">{{ $leaveSummary['approved'] }}</span>
                                <span class="summary-label">Approved</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-count">{{ $leaveSummary['disapproved'] }}</span>
                                <span class="summary-label">Disapproved</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-count">{{ $leaveSummary['total'] }}</span>
                                <span class="summary-label">Total Filed</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

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
                                    <span class="feed-activity" style="color: var(--text-muted);">No recent activity to
                                        display</span>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-side-col">
            <!-- System Stats or Other info could go here -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2><i class="fas fa-info-circle"></i> System Info</h2>
                </div>
                <div class="card-body">
                    <p style="font-size: 0.9rem; color: #64748b;">Welcome to the E-Leave Management System. Use the sidebar
                        to navigate through administrative tools.</p>
                </div>
            </div>
        </div>
    </div>
@endsection