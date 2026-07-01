@extends('layouts.sdo')

@section('title', 'HR Insights')
@section('page-title', 'Strategic HR Dashboard')

@push('styles')
    <!-- Ensure Bootstrap Grid is available -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Animation Keyframes */
        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes backInDown { 0% { transform: translateY(-70px) scale(0.85); opacity: 0; } 80% { transform: translateY(5px) scale(1.02); opacity: 0.8; } 100% { transform: translateY(0) scale(1); opacity: 1; } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInRight { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }

        /* Sequential Component Animations */
        @if(!request()->hasAny(['trend_range', 'dist_range']))
            .metrics-grid .modern-stat-card {
                opacity:0;
                animation: backInDown 0.65s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            }
            .metrics-grid .modern-stat-card:nth-child(1) { animation-delay: 0.1s; }
            .metrics-grid .modern-stat-card:nth-child(2) { animation-delay: 0.2s; }
            .metrics-grid .modern-stat-card:nth-child(3) { animation-delay: 0.3s; }
            .metrics-grid .modern-stat-card:nth-child(4) { animation-delay: 0.4s; }

            .glass-container {
                opacity: 0;
                animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
            }
            .charts-row .glass-container:nth-child(1) { animation-delay: 0.5s; }
            .charts-row .glass-container:nth-child(2) { animation-delay: 0.6s; }
            .main-col > .glass-container, .main-col .row .glass-container { animation-delay: 0.7s; }
            
            .side-col .glass-container { 
                opacity: 0;
                animation: fadeInRight 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; 
                animation-delay: 0.9s; 
            }

            .custom-table tbody tr {
                opacity: 0;
                animation: fadeInUp 0.5s ease-out forwards;
            }
            @foreach(range(1, 8) as $i)
                .custom-table tbody tr:nth-child({{ $i }}) {
                    animation-delay: {{ 0.8 + ($i * 0.08) }}s;
                }
            @endforeach
        @endif

        .hr-dashboard-wrapper {
            background: #f8fafc;
            color: #334155;
            padding: 1rem;
            animation: fadeInDown 0.6s ease-out;
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
        .card-pending .stat-icon-box { background: #e8f0ff; color: #1b4a9a; }
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
            background: #ffffff; border-color: #1b4a9a; color: #1b4a9a; 
            transform: translateX(4px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .modern-action-btn[disabled] { opacity: 0.5; cursor: not-allowed; }
        .action-icon { width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-right: 0.75rem; font-size: 0.85rem; }

        .bg-info-light { background: #e0f2fe; }
        .bg-success-light { background: #dcfce7; }
        .bg-warning-light { background: #fef3c7; }

        /* Sidebar Themed Card */
        .card-sidebar {
            background: var(--primary-gradient) !important;
            border: none !important;
        }
        .card-sidebar .stat-label { color: rgba(255, 255, 255, 0.8) !important; }
        .card-sidebar .stat-value { color: #ffffff !important; }
        .card-sidebar .stat-icon-box { background: rgba(255, 255, 255, 0.2) !important; color: #ffffff !important; }
        .card-sidebar .badge-status-light { background: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; }

        /* Chart Filters */
        .chart-filter-select {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 2px 8px;
            font-size: 0.7rem;
            font-weight: 700;
            color: #475569;
            outline: none;
            transition: all 0.2s;
            cursor: pointer;
            text-transform: uppercase;
        }
        .chart-filter-select:hover { border-color: #1b4a9a; background: #fff; }
        .chart-filter-select:focus { border-color: #1b4a9a; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1); }
    </style>
@endpush

@section('content')
    <div class="hr-dashboard-wrapper">

        <!-- Metric Cards Grid -->
        <div class="metrics-grid">
            <div class="modern-stat-card card-sidebar">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">Active on Leave</p>
                        <h3 class="stat-value">{{ $stats['active_today'] }}</h3>
                        <span class="badge-status-light">Currently Out</span>
                    </div>
                    <div class="stat-icon-box"><i class="fas fa-users"></i></div>
                </div>
            </div>
            <div class="modern-stat-card card-sidebar">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">Pending Verifications</p>
                        <h3 class="stat-value">{{ $stats['pending_applications'] }}</h3>
                        <span class="badge-status-light">Action Required</span>
                    </div>
                    <div class="stat-icon-box"><i class="fas fa-clock"></i></div>
                </div>
            </div>
            <div class="modern-stat-card card-sidebar">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">Expiring COC</p>
                        <h3 class="stat-value">{{ $stats['expiring_coc'] }}</h3>
                        <span class="badge-status-light">Next 30 Days</span>
                    </div>
                    <div class="stat-icon-box"><i class="fas fa-hourglass-half"></i></div>
                </div>
            </div>
            <div class="modern-stat-card card-sidebar">
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
                        <div class="content-header d-flex justify-content-between align-items-center">
                            <h6>Leave Application Trends</h6>
                            <select class="chart-filter-select" id="trendRangeSelect">
                                <option value="week" {{ $trendRange === 'week' ? 'selected' : '' }}>Week</option>
                                <option value="month" {{ $trendRange === 'month' ? 'selected' : '' }}>Month</option>
                                <option value="year" {{ $trendRange === 'year' ? 'selected' : '' }}>Year</option>
                            </select>
                        </div>
                        <div class="p-3"><div style="height: 250px;"><canvas id="trendsChart"></canvas></div></div>
                    </div>
                    <div class="glass-container">
                        <div class="content-header d-flex justify-content-between align-items-center">
                            <h6>Leave Distribution</h6>
                            <select class="chart-filter-select" id="distRangeSelect">
                                <option value="week" {{ $distRange === 'week' ? 'selected' : '' }}>Week</option>
                                <option value="month" {{ $distRange === 'month' ? 'selected' : '' }}>Month</option>
                                <option value="year" {{ $distRange === 'year' ? 'selected' : '' }}>Year</option>
                            </select>
                        </div>
                        <div class="p-3 d-flex flex-column align-items-center">
                            <div style="height: 180px; width: 100%;"><canvas id="distributionChart"></canvas></div>
                            <div id="chart-legend" class="mt-2 small text-muted"></div>
                        </div>
                    </div>
                </div>

                <!-- On Leave Today Row -->
                <div class="row g-3">
                    <div class="col-12">
                        <div class="glass-container">
                            <div class="content-header d-flex justify-content-between align-items-center">
                                <h6>On Leave Today</h6>
                                <span class="badge bg-primary rounded-pill" style="font-size: 0.65rem;">{{ $stats['active_today'] }} Total</span>
                            </div>
                            <div class="p-0">
                                @if(isset($onLeaveToday) && $onLeaveToday->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table custom-table mb-0">
                                            <tbody>
                                                @foreach($onLeaveToday as $leave)
                                                    <tr>
                                                        <td class="border-0">
                                                            <div class="fw-bold text-dark">{{ $leave->user->full_name }}</div>
                                                            <div class="text-muted extra-small" style="font-size: 0.7rem;">{{ $leave->leaveType->type_name }}</div>
                                                        </td>
                                                        <td class="text-end border-0 align-middle">
                                                            <span class="badge-status-light">Out</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="p-4 text-center text-muted small">
                                        <i class="fas fa-calendar-check d-block mb-2 opacity-25" style="font-size: 1.5rem;"></i>
                                        No active leaves recorded for today.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side column -->
            <div class="side-col">
                <div class="glass-container">
                    <div class="content-header d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold" style="font-size: 0.8rem; color: #1e293b; text-transform: uppercase;">Recent Activity</h6>
                        <a href="{{ route('admin.activity-logs', ['type' => 'system']) }}" class="btn btn-sm btn-link text-primary p-0 text-decoration-none extra-small" style="font-size: 0.65rem;">View All</a>
                    </div>
                    <div class="p-0">
                        <div class="table-responsive">
                            <table class="table custom-table mb-0">
                                <tbody>
                                    @foreach($recentActivities as $activity)
                                        <tr>
                                            <td class="border-0 py-2">
                                                <div class="d-flex align-items-center">
                                                    <span class="activity-dot {{ $activity->action === 'Login' ? 'bg-success' : 'bg-primary' }}" style="width: 6px; height: 6px;"></span>
                                                    <div class="fw-bold text-dark" style="font-size: 0.8rem;">{{ $activity->action }}</div>
                                                </div>
                                                <div class="text-muted extra-small" style="font-size: 0.65rem;">{{ $activity->user->full_name ?? 'System' }}</div>
                                            </td>
                                            <td class="text-end border-0 align-middle py-2">
                                                <div class="text-muted opacity-75" style="font-size: 0.65rem;">{{ $activity->created_at->diffForHumans(null, true) }}</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart Filter Handlers
            const trendSelect = document.getElementById('trendRangeSelect');
            const distSelect = document.getElementById('distRangeSelect');

            const updateDashboard = () => {
                const url = new URL(window.location.href);
                url.searchParams.set('trend_range', trendSelect.value);
                url.searchParams.set('dist_range', distSelect.value);
                window.location.href = url.toString();
            };

            trendSelect.addEventListener('change', updateDashboard);
            distSelect.addEventListener('change', updateDashboard);

            const trendsCtx = document.getElementById('trendsChart').getContext('2d');
            const gradient = trendsCtx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.1)');
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

            new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyTrends->pluck('label')) !!},
                    datasets: [{
                        label: 'Applications',
                        data: {!! json_encode($monthlyTrends->pluck('count')) !!},
                        borderColor: '#1b4a9a',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        backgroundColor: gradient,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#1b4a9a',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
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
                        backgroundColor: ['#1b4a9a', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#34d399', '#fbbf24'],
                        hoverOffset: 8,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '72%'
                }
            });
        });
    </script>
@endsection
