@extends('layouts.sdo')

@section('title', 'Superadmin Dashboard')
@section('page-title', 'System Management Portal')

@push('styles')
    <!-- Ensure Bootstrap Grid is available -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .admin-dashboard-wrapper {
            background: #f8fafc;
            color: #334155;
            padding: 1rem;
        }

        /* Compact Grid for Metrics */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 1200px) {
            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .metrics-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Modern Stat Cards - Compact */
        .modern-stat-card {
            background: #ffffff;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            padding: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .modern-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .stat-label {
            color: #64748b;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            margin: 0;
            white-space: nowrap;
        }

        .stat-value {
            color: #1e293b;
            font-weight: 800;
            font-size: 1.75rem;
            margin: 0.1rem 0;
        }

        .stat-icon-box {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .card-primary .stat-icon-box {
            background: #eff6ff;
            color: #3b82f6;
        }

        .card-success .stat-icon-box {
            background: #ecfdf5;
            color: #10b981;
        }

        .card-warning .stat-icon-box {
            background: #fffbeb;
            color: #f59e0b;
        }

        .card-danger .stat-icon-box {
            background: #fef2f2;
            color: #ef4444;
        }

        .badge-status-light {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.65rem;
            font-weight: 600;
            background: #f1f5f9;
            color: #64748b;
        }

        /* Main Dashboard Grid */
        .dashboard-main-grid {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 1.5rem;
            align-items: start;
        }

        @media (max-width: 1200px) {
            .dashboard-main-grid {
                grid-template-columns: 1fr;
            }
        }

        .glass-container {
            background: #ffffff;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .charts-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 992px) {
            .charts-row {
                grid-template-columns: 1fr;
            }
        }

        .content-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            background: #fafafa;
        }

        .content-header h6 {
            margin: 0;
            font-size: 0.85rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Tables - Compact */
        .custom-table {
            width: 100%;
            margin: 0;
        }

        .custom-table thead th {
            background: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .custom-table tbody td {
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.85rem;
            vertical-align: middle;
        }

        .activity-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            margin-right: 8px;
            display: inline-block;
        }

        .user-avatar-mini {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 8px;
        }

        .avatar-placeholder-mini {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-weight: bold;
            color: #64748b;
            margin-right: 8px;
        }

        /* Modern Action Buttons */
        .modern-action-btn {
            display: flex;
            align-items: center;
            padding: 0.6rem 0.85rem;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            text-decoration: none;
            color: #475569;
            font-weight: 500;
            transition: 0.2s;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }

        .modern-action-btn:hover {
            background: #ffffff;
            border-color: #3b82f6;
            color: #3b82f6;
            transform: translateX(4px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .action-icon {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            font-size: 0.85rem;
        }

        .bg-indigo-light {
            background: #e0e7ff;
        }

        .bg-teal-light {
            background: #ccfbf1;
        }

        .bg-rose-light {
            background: #ffe4e6;
        }
    </style>
@endpush

@section('content')
    <div class="admin-dashboard-wrapper">
        <!-- Header Section -->
        <div class="mb-4">
            <h2 class="fw-bold mb-1" style="color: #1a1c24; font-size: 1.5rem;">System Command Center</h2>
            <p class="text-muted small">Global administration and infrastructure health monitoring.</p>
        </div>

        <!-- Metric Cards Grid -->
        <div class="metrics-grid">
            <div class="modern-stat-card card-primary">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">Total Users</p>
                        <h3 class="stat-value">{{ $totalUsers }}</h3>
                        <span class="badge-status-light">Registrations</span>
                    </div>
                    <div class="stat-icon-box"><i class="fas fa-users"></i></div>
                </div>
            </div>
            <div class="modern-stat-card card-success">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">Active Sessions</p>
                        <h3 class="stat-value">{{ $activeToday }}</h3>
                        <span class="badge-status-light">Online Today</span>
                    </div>
                    <div class="stat-icon-box"><i class="fas fa-user-shield"></i></div>
                </div>
            </div>
            @if(auth()->user()->isSuperAdmin())
                <div class="modern-stat-card card-warning">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label">Security Flags</p>
                            <h3 class="stat-value">{{ $securityStats['total_security_events'] }}</h3>
                            <span class="badge-status-light">OTP/Access Events</span>
                        </div>
                        <div class="stat-icon-box"><i class="fas fa-shield-alt"></i></div>
                    </div>
                </div>
                <div class="modern-stat-card card-danger">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label">Blocked Entries</p>
                            <h3 class="stat-value">{{ $securityStats['blocked_users'] }}</h3>
                            <span class="badge-status-light">Security Locks</span>
                        </div>
                        <div class="stat-icon-box"><i class="fas fa-user-lock"></i></div>
                    </div>
                </div>
            @else
                <div class="modern-stat-card card-warning">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label">Month Growth</p>
                            <h3 class="stat-value">{{ $newRegistrations }}</h3>
                            <span class="badge-status-light">New Users</span>
                        </div>
                        <div class="stat-icon-box"><i class="fas fa-chart-line"></i></div>
                    </div>
                </div>
                <div class="modern-stat-card card-danger">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label">Unread Alerts</p>
                            <h3 class="stat-value">{{ $unreadCount }}</h3>
                            <span class="badge-status-light">System Notices</span>
                        </div>
                        <div class="stat-icon-box"><i class="fas fa-bell"></i></div>
                    </div>
                </div>
            @endif
        </div>

        <div class="dashboard-main-grid">
            <!-- Main Content Area -->
            <div class="main-col">
                @if(auth()->user()->isSuperAdmin())
                    <!-- Charts Row -->
                    <div class="charts-row">
                        <div class="glass-container">
                            <div class="content-header">
                                <h6>User Growth & Engagement</h6>
                            </div>
                            <div class="p-3">
                                <div style="height: 220px;"><canvas id="growthChart"></canvas></div>
                            </div>
                        </div>
                        <div class="glass-container">
                            <div class="content-header">
                                <h6>Platform Privilege Map</h6>
                            </div>
                            <div class="p-3 d-flex flex-column align-items-center">
                                <div style="height: 180px; width: 100%;"><canvas id="roleChart"></canvas></div>
                                <div id="chart-legend" class="mt-2 small text-muted"></div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Activity Feed -->
                <div class="glass-container">
                    <div class="content-header d-flex justify-content-between align-items-center">
                        <h6>Global Infrastructure Audit</h6>
                        <a href="{{ route('admin.activity-logs') }}"
                            class="btn btn-sm btn-link text-primary p-0 text-decoration-none small fw-bold">Audit Logs</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table custom-table">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($auditTrail as $log)
                                    <tr>
                                        <td class="text-muted small">{{ $log->created_at->format('M j, g:i A') }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($log->user && $log->user->profile_picture)
                                                    <img src="{{ storage_url($log->user->profile_picture) }}" alt=""
                                                        class="user-avatar-mini">
                                                @else
                                                    <div class="avatar-placeholder-mini">
                                                        {{ strtoupper(substr($log->user->full_name ?? 'U', 0, 1)) }}
                                                    </div>
                                                @endif
                                                <span>{{ $log->user->full_name ?? 'Unknown' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge-status-light"
                                                style="background: #f1f5f9;">{{ $log->action }}</span>
                                        </td>
                                        <td class="text-muted">{{ Str::limit($log->details, 50) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Side column -->
            <div class="side-col">
                <!-- Systems Control -->
                <div class="glass-container p-3">
                    <h6 class="fw-bold mb-3"
                        style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">
                        Control Panel</h6>
                    <div class="d-grid">
                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('admin.auth-reset-management') }}" class="modern-action-btn">
                                <div class="action-icon bg-indigo-light"><i class="fas fa-key text-indigo"></i></div>
                                <span>Auth Management</span>
                            </a>
                            <a href="{{ route('admin.signatories') }}" class="modern-action-btn">
                                <div class="action-icon bg-teal-light"><i class="fas fa-signature text-teal"></i></div>
                                <span>Signatory Control</span>
                            </a>
                        @endif
                        <a href="{{ route('admin.manage-users') }}" class="modern-action-btn">
                            <div class="action-icon bg-success-light"><i class="fas fa-users-cog text-success"></i></div>
                            <span>User Governance</span>
                        </a>
                        <a href="{{ route('admin.register-user') }}" class="modern-action-btn">
                            <div class="action-icon bg-info-light"><i class="fas fa-plus text-info"></i></div>
                            <span>Provision User</span>
                        </a>
                    </div>
                </div>

                <!-- Health Snapshot -->
                <div class="glass-container">
                    <div class="content-header">
                        <h6>System Health</h6>
                    </div>
                    <div class="p-3">
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Database</span>
                            <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Stable</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Storage</span>
                            <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Active</span>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Avg Visits</span>
                            <span
                                class="text-primary fw-bold">{{ auth()->user()->isSuperAdmin() ? $securityStats['avg_visits'] : 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div class="glass-container p-3 text-center">
                    <p class="text-muted" style="font-size: 0.7rem;">System Engine v4.1 | Build 2024</p>
                    <img src="{{ asset('images/logo.png') }}" alt=""
                        style="height: 25px; opacity: 0.15; filter: grayscale(1); margin-top: 5px;">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(auth()->user()->isSuperAdmin())
                // Growth Chart
                const growthCtx = document.getElementById('growthChart').getContext('2d');
                const gradient = growthCtx.createLinearGradient(0, 0, 0, 220);
                gradient.addColorStop(0, 'rgba(99, 102, 241, 0.15)');
                gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

                new Chart(growthCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($userGrowth->pluck('month')) !!},
                        datasets: [{
                            label: 'New Users',
                            data: {!! json_encode($userGrowth->pluck('count')) !!},
                            borderColor: '#6366f1',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            backgroundColor: gradient,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#6366f1',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 9 } } },
                            y: {
                                grid: { borderDash: [4, 4], color: '#f1f5f9' },
                                ticks: { color: '#94a3b8', font: { size: 9 }, precision: 0 }
                            }
                        }
                    }
                });

                // Role Distribution Chart
                const roleCtx = document.getElementById('roleChart').getContext('2d');
                new Chart(roleCtx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($roleDistribution->pluck('label')) !!},
                        datasets: [{
                            data: {!! json_encode($roleDistribution->pluck('value')) !!},
                            backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#3b82f6'],
                            hoverOffset: 8,
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        cutout: '72%'
                    }
                });
            @endif
        });
    </script>
@endsection