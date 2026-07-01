@extends('layouts.sdo')

@section('title', 'Record Personnel Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        .dashboard-hero-premium {
            background: linear-gradient(135deg, #123166 0%, #1b4a9a 100%);
            border-radius: 20px;
            padding: 40px;
            color: white;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(10, 47, 74, 0.2);
        }

        .hero-accent-circle {
            position: absolute;
            top: -50px;
            right: -50px;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .stats-grid-premium {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card-premium {
            background: white;
            border-radius: 16px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        .stat-card-premium:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px -5px rgba(0,0,0,0.1);
            border-color: #1b4a9a;
        }

        .stat-icon-box {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .icon-blue { background: #e8f0ff; color: #1b4a9a; }
        .icon-green { background: #f0fdf4; color: #15803d; }
        .icon-orange { background: #fff7ed; color: #c2410c; }
        .icon-sky { background: #f0f9ff; color: #1b4a9a; }

        .stat-value-premium {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
            line-height: 1.2;
        }

        .stat-label-premium {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .recent-activity-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        .card-header-premium {
            padding: 20px 24px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
        }

        .card-title-premium {
            font-size: 1.1rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .log-item {
            padding: 16px 24px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: background 0.2s;
        }

        .log-item:hover {
            background: #f8fafc;
        }

        .log-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-weight: 700;
            flex-shrink: 0;
        }

        .log-content {
            flex: 1;
        }

        .log-action {
            font-size: 0.9rem;
            color: #334155;
            margin-bottom: 2px;
        }

        .log-time {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .btn-view-all {
            background: #1b4a9a;
            color: white;
            padding: 8px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-view-all:hover {
            background: #123166;
            color: white;
            transform: translateY(-1px);
        }

        .progress-mini {
            height: 6px;
            background: #f1f5f9;
            border-radius: 10px;
            margin-top: 8px;
            overflow: hidden;
        }

        .progress-bar-mini {
            height: 100%;
            background: #1b4a9a;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid animate__animated animate__fadeIn" style="padding: 24px;">
    
    <!-- Hero Section -->
    <div class="dashboard-hero-premium">
        <div class="hero-accent-circle"></div>
        <div style="position: relative; z-index: 1;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                <div style="background: rgba(255,255,255,0.15); padding: 8px 16px; border-radius: 30px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                    <i class="fas fa-archive me-1"></i> Auditor & Record Management
                </div>
            </div>
            <h1 style="font-size: 3rem; font-weight: 900; margin-bottom: 15px; letter-spacing: -1px; line-height: 1; color: #ffffff;">System Records Repository</h1>
            <p style="font-size: 1.15rem; color: rgba(255,255,255,0.85); max-width: 700px; line-height: 1.6; margin-bottom: 0;">
                Access and monitor all personnel leave data with comprehensive auditing tools. Efficiently track the status of applications across the entire division from a centralized platform.
            </p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid-premium">
        <div class="stat-card-premium">
            <div class="stat-icon-box icon-blue"><i class="fas fa-database"></i></div>
            <div>
                <div class="stat-label-premium">System-Wide Records</div>
                <div class="stat-value-premium">{{ number_format($stats['total']) }}</div>
            </div>
        </div>
        <div class="stat-card-premium">
            <div class="stat-icon-box icon-green"><i class="fas fa-check-double"></i></div>
            <div>
                <div class="stat-label-premium">Verified Approved</div>
                <div class="stat-value-premium">{{ number_format($stats['approved']) }}</div>
            </div>
        </div>
        <div class="stat-card-premium">
            <div class="stat-icon-box icon-orange"><i class="fas fa-hourglass-half"></i></div>
            <div>
                <div class="stat-label-premium">Active Pending</div>
                <div class="stat-value-premium">{{ number_format($stats['pending']) }}</div>
            </div>
        </div>
        <div class="stat-card-premium">
            <div class="stat-icon-box icon-sky"><i class="fas fa-calendar-check"></i></div>
            <div style="flex: 1;">
                <div class="stat-label-premium">Applications ({{ now()->format('M') }})</div>
                <div class="stat-value-premium">{{ number_format($stats['this_month']) }}</div>
                <div class="progress-mini">
                    @php $percent = $stats['this_month'] > 0 ? min(100, ($stats['this_month'] / max(1, $stats['last_month'])) * 100) : 0; @endphp
                    <div class="progress-bar-mini" style="width: {{ $percent }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Latest Applications -->
            <div class="recent-activity-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                <div class="card-header-premium">
                    <h3 class="card-title-premium"><i class="fas fa-file-invoice text-primary"></i> Latest System Applications</h3>
                    <a href="{{ route('records.index') }}" class="btn-view-all">Manage Repository</a>
                </div>
                <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                    @forelse($recentApplications as $application)
                        <div class="log-item">
                            <div class="log-avatar" style="background: #e8f0ff; color: #123166;">
                                {{ strtoupper(substr($application->user->first_name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="log-content">
                                <div class="log-action">
                                    <strong style="color: #1e293b;">{{ $application->user->full_name }}</strong> 
                                    <span class="mx-1" style="color: #94a3b8;">filed</span>
                                    <span style="font-weight: 700; color: #1b4a9a;">{{ $application->leaveType->name }}</span>
                                </div>
                                <div class="log-time">
                                    <i class="far fa-clock me-1"></i> {{ $application->created_at->diffForHumans() }}
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-calendar-day me-1"></i> {{ $application->days_applied }} days
                                </div>
                            </div>
                            <div class="application-summary-progress" style="text-align: right; min-width: 140px;">
                                @php
                                    $statusClass = 'bg-secondary';
                                    if(str_contains(strtolower($application->status), 'pending')) $statusClass = 'bg-warning';
                                    elseif(str_contains(strtolower($application->status), 'approved')) $statusClass = 'bg-success';
                                    elseif(str_contains(strtolower($application->status), 'disapproved')) $statusClass = 'bg-danger';
                                    elseif(str_contains(strtolower($application->status), 'recommended')) $statusClass = 'bg-info';
                                @endphp
                                <span class="badge {{ $statusClass }}" style="border-radius: 6px; padding: 6px 10px; font-size: 0.7rem; text-transform: uppercase;">
                                    {{ $application->status }}
                                </span>
                                <div class="progress-mini" style="width: 100%; margin-top: 8px;">
                                    @php
                                        $progress = 10;
                                        if(str_contains(strtolower($application->status), 'recommended')) $progress = 50;
                                        elseif(str_contains(strtolower($application->status), 'approved')) $progress = 100;
                                        elseif(str_contains(strtolower($application->status), 'disapproved')) $progress = 100;
                                    @endphp
                                    <div class="progress-bar-mini" style="width: {{ $progress }}%; background: {{ $statusClass == 'bg-success' ? '#16a34a' : ($statusClass == 'bg-danger' ? '#dc2626' : '#1b4a9a') }};"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-5 text-center text-muted">
                            <i class="fas fa-folder-open fa-3x mb-3 opacity-20"></i>
                            <p>No recent applications found.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Link / Info -->
            <div class="recent-activity-card animate__animated animate__fadeInUp" style="animation-delay: 0.4s; height: 100%;">
                <div class="card-header-premium">
                    <h3 class="card-title-premium"><i class="fas fa-info-circle text-info"></i> Account Status</h3>
                </div>
                <div class="card-body">
                    <div style="background: #f8fafc; border-radius: 12px; padding: 20px; border: 1px solid #e2e8f0; margin-bottom: 20px;">
                        <h4 style="font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 10px;">ROLE PERMISSIONS</h4>
                        <ul style="padding-left: 18px; color: #64748b; font-size: 0.85rem; line-height: 1.8;">
                            <li>System-wide Application Access</li>
                            <li>Personal Profile (No Recommender/Approver)</li>
                            <li>Digital Signature Requirement Skipped</li>
                            <li>Read-Only Archive Access</li>
                        </ul>
                    </div>
                    
                    <p class="text-muted small" style="line-height: 1.6;">
                        Your account is configured for <strong>Records & Auditing</strong>. You do not need leave credits or signature setup for your own profile as your role is primarily for management and verification.
                    </p>
                    
                    <div class="mt-4">
                        <a href="{{ route('user.profile') }}" class="btn btn-outline-secondary w-100" style="border-radius: 10px; font-weight: 600;">
                            <i class="fas fa-user-cog me-2"></i> Account Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
