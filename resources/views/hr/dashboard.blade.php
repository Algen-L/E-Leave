@extends('layouts.sdo')

@section('title', 'HR Insights')
@section('page-title', 'Strategic HR Dashboard')

@push('styles')
    <!-- Ensure Bootstrap Grid is available -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hr-dashboard-wrapper {
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

        @media (max-width: 1200px) { .metrics-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px) { .metrics-grid { grid-template-columns: 1fr; } }

        /* Modern Stat Cards - Compact */
        .modern-stat-card {
            background: #ffffff;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            padding: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .modern-stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 15px -3px rgba(0,0,0,0.1); }

        .stat-label { color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; margin: 0; white-space: nowrap; }
        .stat-value { color: #1e293b; font-weight: 800; font-size: 1.75rem; margin: 0.1rem 0; }
        .stat-icon-box { 
            width: 38px; height: 38px; border-radius: 8px; display: flex; 
            align-items: center; justify-content: center; font-size: 1rem;
        }

        .card-active .stat-icon-box { background: #ecfdf5; color: #10b981; }
        .card-pending .stat-icon-box { background: #eff6ff; color: #3b82f6; }
        .card-warning .stat-icon-box { background: #fffbeb; color: #f59e0b; }
        .card-critical .stat-icon-box { background: #fef2f2; color: #ef4444; }

        .badge-status-light { padding: 2px 6px; border-radius: 4px; font-size: 0.65rem; font-weight: 600; background: #f1f5f9; color: #64748b; }

        /* Main Dashboard Grid */
        .dashboard-main-grid {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 1.5rem;
            align-items: start;
        }

        @media (max-width: 1200px) { .dashboard-main-grid { grid-template-columns: 1fr; } }

        .glass-container {
            background: #ffffff;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .charts-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 992px) { .charts-row { grid-template-columns: 1fr; } }

        .content-header { padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9; }
        .content-header h6 { margin: 0; font-size: 0.9rem; color: #1e293b; }

        /* Tables - Compact */
        .custom-table { width: 100%; margin: 0; }
        .custom-table thead th { 
            background: #f8fafc; color: #64748b; font-weight: 600; font-size: 0.7rem; 
            text-transform: uppercase; padding: 0.75rem 1.25rem; border-bottom: 1px solid #f1f5f9;
        }
        .custom-table tbody td { padding: 0.75rem 1.25rem; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; }
        .activity-dot { width: 7px; height: 7px; border-radius: 50%; margin-right: 8px; display: inline-block; }

        /* Modern Action Buttons */
        .modern-action-btn {
            display: flex; align-items: center; padding: 0.6rem 0.85rem; border-radius: 8px;
            background: #f8fafc; border: 1px solid #e2e8f0; text-decoration: none; color: #475569;
            font-weight: 500; transition: 0.2s; font-size: 0.85rem; margin-bottom: 0.5rem;
        }
        .modern-action-btn:hover:not([disabled]) { 
            background: #ffffff; border-color: #3b82f6; color: #3b82f6; 
            transform: translateX(4px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .modern-action-btn[disabled] { opacity: 0.5; cursor: not-allowed; }
        .action-icon { width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-right: 0.75rem; font-size: 0.85rem; }

        .bg-info-light { background: #e0f2fe; }
        .bg-success-light { background: #dcfce7; }
        .bg-warning-light { background: #fef3c7; }
    </style>
@endpush

@section('content')
    <div class="hr-dashboard-wrapper">
        <!-- Header Section -->
        <div class="mb-4">
            <h2 class="fw-bold mb-1" style="color: #1a1c24; font-size: 1.5rem;">HR Insights Dashboard</h2>
            <p class="text-muted small">Real-time organizational manpower and leave credit analytics.</p>
        </div>

        <!-- Metric Cards Grid -->
        <div class="metrics-grid">
            <div class="modern-stat-card card-active">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">Active on Leave</p>
                        <h3 class="stat-value">{{ $stats['active_today'] }}</h3>
                        <span class="badge-status-light">Currently Out</span>
                    </div>
                    <div class="stat-icon-box"><i class="fas fa-users"></i></div>
                </div>
            </div>
            <div class="modern-stat-card card-pending">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">Pending Verifications</p>
                        <h3 class="stat-value">{{ $stats['pending_applications'] }}</h3>
                        <span class="badge-status-light">Action Required</span>
                    </div>
                    <div class="stat-icon-box"><i class="fas fa-clock"></i></div>
                </div>
            </div>
            <div class="modern-stat-card card-warning">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">Expiring COC</p>
                        <h3 class="stat-value">{{ $stats['expiring_coc'] }}</h3>
                        <span class="badge-status-light">Next 30 Days</span>
                    </div>
                    <div class="stat-icon-box"><i class="fas fa-hourglass-half"></i></div>
                </div>
            </div>
            <div class="modern-stat-card card-critical">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">Credit Hoarders</p>
                        <h3 class="stat-value">{{ $stats['hoarding_count'] }}</h3>
                        <span class="badge-status-light">Policy Limit</span>
                    </div>
                    <div class="stat-icon-box"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
            </div>
        </div>

        <div class="dashboard-main-grid">
            <!-- Main Content Area -->
            <div class="main-col">
                <!-- Charts Row -->
                <div class="charts-row">
                    <div class="glass-container">
                        <div class="content-header"><h6>Leave Application Trends</h6></div>
                        <div class="p-3"><div style="height: 250px;"><canvas id="trendsChart"></canvas></div></div>
                    </div>
                    <div class="glass-container">
                        <div class="content-header"><h6>Leave Distribution</h6></div>
                        <div class="p-3 d-flex flex-column align-items-center">
                            <div style="height: 180px; width: 100%;"><canvas id="distributionChart"></canvas></div>
                            <div id="chart-legend" class="mt-2 small text-muted"></div>
                        </div>
                    </div>
                </div>

                <!-- Activity Feed -->
                <div class="glass-container">
                    <div class="content-header d-flex justify-content-between align-items-center">
                        <h6>Recent System Activity</h6>
                        <a href="{{ route('admin.activity-logs') }}" class="btn btn-sm btn-link text-primary p-0 text-decoration-none small">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table custom-table">
                            <thead>
                                <tr>
                                    <th>Activity</th>
                                    <th>User</th>
                                    <th>Details</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentActivities as $activity)
                                    <tr>
                                        <td>
                                            <span class="activity-dot {{ $activity->action === 'Login' ? 'bg-success' : 'bg-primary' }}"></span>
                                            <span class="fw-medium">{{ $activity->action }}</span>
                                        </td>
                                        <td>{{ $activity->user->full_name ?? 'System' }}</td>
                                        <td class="text-muted">{{ Str::limit($activity->description, 40) }}</td>
                                        <td class="text-muted opacity-75 small">{{ $activity->created_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Side column -->
            <div class="side-col">
                <div class="glass-container p-3">
                    <h6 class="fw-bold mb-3" style="font-size: 0.85rem; color: #64748b; text-transform: uppercase;">Quick Actions</h6>
                    <div class="d-grid">
                        <a href="{{ route('hr-staff.manage-credits') }}" class="modern-action-btn">
                            <div class="action-icon bg-info-light"><i class="fas fa-coins text-info"></i></div>
                            <span>Manage Credits</span>
                        </a>
                        <button class="modern-action-btn" disabled>
                            <div class="action-icon bg-success-light"><i class="fas fa-file-invoice text-success"></i></div>
                            <span>Monetization Tool</span>
                        </button>
                        <button class="modern-action-btn" disabled>
                            <div class="action-icon bg-warning-light"><i class="fas fa-calendar-alt text-warning"></i></div>
                            <span>Manpower Map</span>
                        </button>
                    </div>
                </div>

                <div class="glass-container p-4 text-center">
                    <p class="text-muted small mb-0">System Version 2.5</p>
                    <hr class="my-2 opacity-10">
                    <img src="{{ asset('images/logo.png') }}" alt="" style="height: 30px; opacity: 0.2; filter: grayscale(1);">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const trendsCtx = document.getElementById('trendsChart').getContext('2d');
            const gradient = trendsCtx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.1)');
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

            new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyTrends->pluck('month')) !!},
                    datasets: [{
                        label: 'Applications',
                        data: {!! json_encode($monthlyTrends->pluck('count')) !!},
                        borderColor: '#3b82f6',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        backgroundColor: gradient,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#3b82f6',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 10 } } },
                        y: { 
                            grid: { borderDash: [4, 4], color: '#f1f5f9' },
                            ticks: { color: '#94a3b8', font: { size: 10 }, precision: 0 }
                        }
                    }
                }
            });

            const distCtx = document.getElementById('distributionChart').getContext('2d');
            new Chart(distCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($distribution->pluck('label')) !!},
                    datasets: [{
                        data: {!! json_encode($distribution->pluck('value')) !!},
                        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
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
        });
    </script>
@endsection
