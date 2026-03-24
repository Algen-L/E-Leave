@extends('layouts.sdo')

@section('title', 'Superadmin Dashboard')
@section('page-title', 'System Management Portal')

@push('styles')
    <!-- Ensure Bootstrap Grid is available -->
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
    <style>
        /* Hide main scrollbar */
        html, body {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }

        html::-webkit-scrollbar, body::-webkit-scrollbar {
            display: none; /* Webkit browsers */
        }

        .admin-dashboard-wrapper {
            background: #f8fafc;
            color: #334155;
            padding: 0.75rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            animation: fadeIn 0.6s ease-out;
        }

        /* Sequential Animations for Metric Cards */
        .metrics-grid .modern-stat-card {
            opacity: 0;
            animation: backInDown 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .metrics-grid .modern-stat-card:nth-child(1) { animation-delay: 0.1s; }
        .metrics-grid .modern-stat-card:nth-child(2) { animation-delay: 0.2s; }
        .metrics-grid .modern-stat-card:nth-child(3) { animation-delay: 0.3s; }
        .metrics-grid .modern-stat-card:nth-child(4) { animation-delay: 0.4s; }

        /* Sequential Animations for Middle Row */
        .middle-row-grid .glass-container {
            opacity: 0;
            animation: backInDown 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .middle-row-grid .glass-container:nth-child(3) { animation-delay: 0.7s; }

        /* Page Header Entrance */
        .page-title, .current-date-box {
            opacity: 0;
            animation: fadeInDown 0.6s ease-out forwards;
        }

        /* Sequential Animations for Audit Trail */
        .audit-table tbody tr {
            opacity: 0;
            animation: backInDown 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @foreach(range(1, 10) as $i)
            .audit-table tbody tr:nth-child({{ $i }}) {
                animation-delay: {{ 0.9 + ($i * 0.05) }}s;
            }
        @endforeach

        /* Audit Container Entrance */
        .audit-container {
            opacity: 0;
            animation: backInDown 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            animation-delay: 0.8s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Complex Privilege Map Animations */
        @keyframes privilegeFlipCenter {
            0% { transform: perspective(400px) rotate3d(0, 1, 0, -360deg); opacity: 0; }
            100% { transform: perspective(400px) rotate3d(0, 1, 0, 0deg); opacity: 1; }
        }

        @keyframes privilegeLegendReveal {
            0% { max-width: 0; opacity: 0; margin-left: 0; }
            100% { max-width: 500px; opacity: 1; margin-left: 2rem; }
        }

        .privilege-container.complex-sequence {
            justify-content: center; /* Center during flip */
            transition: justify-content 0.6s ease;
            min-height: 220px;
        }

        .privilege-chart-wrapper.complex-sequence {
            animation: privilegeFlipCenter 1s forwards;
            animation-delay: 0.6s;
            opacity: 0; /* Start hidden */
        }

        .privilege-legend.complex-sequence {
            max-width: 0;
            opacity: 0;
            overflow: hidden;
            white-space: nowrap;
            animation: privilegeLegendReveal 1s forwards;
            animation-delay: 1.6s; /* Starts after flip */
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

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
            margin-bottom: 0.75rem;
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
            border-radius: 0.85rem;
            border: 1px solid rgba(226, 232, 240, 0.8);
            padding: 0.7rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
            overflow: hidden;
        }

        .modern-stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: rgba(59, 130, 246, 0.2);
        }

        .stat-label {
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            margin: 0;
            white-space: nowrap;
            letter-spacing: 0.05em;
        }
        .stat-value {
            color: #1e293b;
            font-weight: 800;
            font-size: 1.6rem;
            margin: 0;
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        .stat-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            transition: transform 0.3s ease;
        }

        .modern-stat-card:hover .stat-icon-box {
            transform: scale(1.1) rotate(-5deg);
        }

        .badge-status-light {
            font-size: 0.65rem;
            font-weight: 600;
            color: #94a3b8;
            white-space: nowrap;
        }

        /* Main Dashboard Grid Restructuring */
        .middle-row-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            align-items: stretch;
        }

        .middle-row-grid .glass-container {
            height: 100%;
            margin-bottom: 0;
            display: flex;
            flex-direction: column;
        }

        .middle-row-grid .glass-container .p-3, 
        .middle-row-grid .glass-container .privilege-container,
        .middle-row-grid .glass-container .px-4 {
            flex: 1;
        }

        @media (max-width: 1200px) {
            .middle-row-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .middle-row-grid {
                grid-template-columns: 1fr;
            }
        }

        .glass-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 1rem;
            overflow: hidden;
            transition: all 0.4s ease;
        }

        .glass-container:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
            border-color: rgba(203, 213, 225, 0.8);
        }

        .charts-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        @media (max-width: 992px) {
            .charts-row {
                grid-template-columns: 1fr;
            }
        }

        .content-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(241, 245, 249, 1);
            background: transparent;
            display: flex;
            align-items: center;
        }

        .content-header h6 {
            margin: 0;
            font-size: 0.9rem;
            font-weight: 800;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.075em;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .card-primary .stat-icon-box { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .card-success .stat-icon-box { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .card-warning .stat-icon-box { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .card-danger .stat-icon-box { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

        /* Tables - Compact */
        .custom-table {
            width: 100%;
            margin: 0;
        }

        .custom-table thead th {
            background: #f8fafc;
            color: #64748b;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            letter-spacing: 0.025em;
        }

        .custom-table tbody td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.85rem;
            vertical-align: middle;
            color: #475569;
        }

        .activity-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 10px;
            display: inline-block;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .user-avatar-mini {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .avatar-placeholder-mini {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            color: #475569;
            margin-right: 10px;
        }

        /* Modern Action Buttons */
        .modern-action-btn {
            display: flex;
            align-items: center;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            text-decoration: none !important;
            color: #1e293b !important;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .modern-action-btn:hover {
            background: #f8fafc;
            border-color: #3b82f6;
            color: #3b82f6 !important;
            transform: translateX(6px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .action-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .modern-action-btn:hover .action-icon {
            transform: scale(1.1);
        }

        .bg-indigo-light { background: rgba(99, 102, 241, 0.12); color: #6366f1; }
        .bg-teal-light { background: rgba(20, 184, 166, 0.12); color: #14b8a6; }
        .bg-rose-light { background: rgba(244, 63, 94, 0.12); color: #f43f5e; }
        .bg-success-light { background: rgba(34, 197, 94, 0.12); color: #22c55e; }
        .bg-info-light { background: rgba(6, 182, 212, 0.12); color: #06b6d4; }

        /* Chart Header Metrics */
        .chart-header-metrics {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid rgba(241, 245, 249, 1);
        }

        .chart-metric-item {
            display: flex;
            flex-direction: column;
        }

        .chart-metric-label {
            font-size: 0.65rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .chart-metric-value-wrapper {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .chart-metric-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: #1e293b;
            line-height: 1;
        }

        .chart-metric-icon-purple {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 3px solid #8b5cf6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8b5cf6;
            font-size: 0.6rem;
        }

        .chart-metric-trend-up {
            color: #10b981;
            font-size: 1.1rem;
            font-weight: 800;
        }

        .chart-metric-divider {
            width: 1px;
            height: 40px;
            background: #f1f5f9;
        }

        .chart-legend-bottom {
            display: flex;
            justify-content: flex-end;
            padding: 0.5rem 1.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #475569;
        }

        .legend-dot-purple {
            width: 8px;
            height: 8px;
            background: #8b5cf6;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }

        /* Privilege Map Specifics */
        .privilege-container {
            display: flex;
            align-items: center;
            justify-content: center; /* Center by default for the sequence */
            padding: 1.25rem;
            gap: 0; /* Managed by legend reveal margin */
        }

        .privilege-chart-wrapper {
            position: relative;
            width: 180px;
            height: 180px;
            flex-shrink: 0;
        }

        .privilege-center-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            pointer-events: none;
            width: 100%;
        }

        .center-label {
            display: block;
            font-size: 0.65rem;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 2px;
        }

        .center-value {
            display: block;
            font-size: 1.75rem;
            font-weight: 800;
            color: #3b82f6;
            line-height: 1;
        }

        .privilege-legend {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .legend-header {
            font-size: 0.75rem;
            font-weight: 700;
            color: #94a3b8;
            margin-bottom: 0.5rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.75rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .legend-item-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .legend-color-box {
            width: 14px;
            height: 14px;
            border-radius: 3px;
        }

        .legend-count {
            font-weight: 700;
            color: #1e293b;
        }

        @media (max-width: 576px) {
            .privilege-container {
                flex-direction: column;
                gap: 1.5rem;
            }
        }

        /* Redesigned Control Panel */
        .system-control-header {
            font-size: 0.9rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.075em;
            margin-bottom: 1.5rem;
            padding-left: 0.25rem;
        }

        .control-panel-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .control-action-card {
            background: #ffffff;
            border-radius: 1rem;
            padding: 1.15rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(226, 232, 240, 0.7);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .control-action-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.1);
            border-color: #3b82f6;
        }

        .card-icon-wrapper {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }

        .control-action-card:hover .card-icon-wrapper {
            transform: scale(1.1);
        }

        .card-content-area {
            flex: 1;
        }

        .card-title-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.25rem;
        }

        .card-main-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: #1e293b;
            margin: 0;
        }

        .card-description {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 500;
            margin: 0;
        }

        .compliance-badge {
            background: #f1f5f9;
            color: #64748b;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            text-transform: lowercase;
        }

        .bg-purple-soft { background: #f5f3ff; color: #8b5cf6; }
        .bg-teal-soft { background: #f0fdfa; color: #14b8a6; }
        .bg-green-soft { background: #f0fdf4; color: #10b981; }
        .bg-cyan-soft { background: #ecfeff; color: #06b6d4; }

        .control-panel-footer {
            display: flex;
            justify-content: center;
            margin-top: 1rem;
            margin-bottom: 2rem;
        }

        .view-settings-btn {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1.75rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 800;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none !important;
            transition: all 0.3s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .view-settings-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-2px);
        }

        /* Global Infrastructure Audit Specifics */
        .audit-scroll-container {
            max-height: 400px;
            overflow-y: auto;
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }

        .audit-scroll-container::-webkit-scrollbar {
            display: none; /* Webkit browsers (Chrome, Safari, Brave) */
        }

        .audit-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .audit-table th {
            position: sticky;
            top: 0;
            background: #f8fafc;
            padding: 0.65rem 1.25rem;
            font-size: 0.7rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e2e8f0;
            z-index: 10;
        }

        .audit-table td {
            padding: 0.85rem 1.25rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.85rem;
        }

        .audit-table tr:last-child td {
            border-bottom: none;
        }

        .audit-table tr:hover {
            background-color: #f8fafc;
        }

        .audit-action-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: capitalize;
            letter-spacing: 0.01em;
        }

        /* Action Badge Colors */
        .badge-login { background: #eef2ff; color: #6366f1; }
        .badge-create { background: #ecfdf5; color: #10b981; }
        .badge-update { background: #fffbeb; color: #f59e0b; }
        .badge-delete { background: #fef2f2; color: #ef4444; }
        .badge-default { background: #f1f5f9; color: #64748b; }

        .audit-user-box {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .audit-avatar {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            object-fit: cover;
        }

        .audit-avatar-placeholder {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #f1f5f9;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.8rem;
        }

        .audit-timestamp {
            color: #94a3b8;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .audit-details {
            color: #475569;
            font-weight: 500;
        }
    </style>
@endpush

@section('content')
    <div class="admin-dashboard-wrapper">

        <!-- Metric Cards Grid -->
        <div class="metrics-grid">
            <div class="modern-stat-card card-primary">
                <div class="d-flex flex-column align-items-center" style="min-width: 60px;">
                    <div class="stat-icon-box"><i class="fas fa-users"></i></div>
                </div>
                <div class="ms-3 d-flex flex-column">
                    <div class="d-flex align-items-center gap-2">
                        <p class="stat-label mb-0">Total Users:</p>
                        <h3 class="stat-value mb-0">{{ $totalUsers }}</h3>
                    </div>
                    <span class="badge-status-light mt-1">Registrations</span>
                </div>
            </div>
            <div class="modern-stat-card card-success">
                <div class="d-flex flex-column align-items-center" style="min-width: 60px;">
                    <div class="stat-icon-box"><i class="fas fa-user-shield"></i></div>
                </div>
                <div class="ms-3 d-flex flex-column">
                    <div class="d-flex align-items-center gap-2">
                        <p class="stat-label mb-0">Active Sessions:</p>
                        <h3 class="stat-value mb-0">{{ $activeToday }}</h3>
                    </div>
                    <span class="badge-status-light mt-1">Online Today</span>
                </div>
            </div>
            @if(auth()->user()->isSuperAdmin())
                <div class="modern-stat-card card-warning">
                    <div class="d-flex flex-column align-items-center" style="min-width: 60px;">
                        <div class="stat-icon-box"><i class="fas fa-shield-alt"></i></div>
                    </div>
                    <div class="ms-3 d-flex flex-column">
                        <div class="d-flex align-items-center gap-2">
                            <p class="stat-label mb-0">Security Flags:</p>
                            <h3 class="stat-value mb-0">{{ $securityStats['total_security_events'] }}</h3>
                        </div>
                        <span class="badge-status-light mt-1">OTP/Access Events</span>
                    </div>
                </div>
                <div class="modern-stat-card card-danger">
                    <div class="d-flex flex-column align-items-center" style="min-width: 60px;">
                        <div class="stat-icon-box"><i class="fas fa-user-lock"></i></div>
                    </div>
                    <div class="ms-3 d-flex flex-column">
                        <div class="d-flex align-items-center gap-2">
                            <p class="stat-label mb-0">Blocked Entries:</p>
                            <h3 class="stat-value mb-0">{{ $securityStats['blocked_users'] }}</h3>
                        </div>
                        <span class="badge-status-light mt-1">Security Locks</span>
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

        <!-- Middle Row: Charts & Control -->
        <div class="middle-row-grid">
            @if(auth()->user()->isSuperAdmin())
                <!-- User Growth & Engagement -->
                <div class="glass-container">
                    <div class="content-header">
                        <h6>User Growth & Engagement</h6>
                    </div>
                    <div class="chart-header-metrics">
                        <div class="chart-metric-item">
                            <span class="chart-metric-label">Total Users</span>
                            <div class="chart-metric-value-wrapper">
                                <span class="chart-metric-value">{{ $totalUsers }}</span>
                                <div class="chart-metric-icon-purple">
                                    <i class="fas fa-circle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="chart-metric-divider"></div>
                        <div class="chart-metric-item">
                            <span class="chart-metric-label">Monthly Engagement</span>
                            <div class="chart-metric-value-wrapper">
                                <span class="chart-metric-value">{{ $monthlyEngagement }}%</span>
                                @if($registrationTrendUp)
                                    <span class="chart-metric-trend-up">↑</span>
                                @else
                                    <span class="chart-metric-trend-up" style="color: #ef4444;">↓</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="p-3">
                        <div class="animate__animated animate__lightSpeedInLeft" style="height: 220px; animation-delay: 0.6s;"><canvas id="growthChart"></canvas></div>
                    </div>
                    <div class="chart-legend-bottom">
                        <span><span class="legend-dot-purple"></span> New Users <span style="color: #8b5cf6">(Purple)</span></span>
                    </div>
                </div>

                <!-- Platform Privilege Map -->
                <div class="glass-container">
                    <div class="content-header">
                        <h6>Platform Privilege Map</h6>
                    </div>
                    <div class="privilege-container complex-sequence">
                        <div class="privilege-chart-wrapper complex-sequence">
                            <canvas id="roleChart"></canvas>
                            <div class="privilege-center-text">
                                <span class="center-label">Total Privileges</span>
                                <span class="center-value">{{ $roleDistribution->sum('value') }}</span>
                            </div>
                        </div>
                        <div class="privilege-legend complex-sequence">
                            <div class="legend-header">Legend</div>
                            @php
                                $colors = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#3b82f6'];
                            @endphp
                                @foreach($roleDistribution as $index => $role)
                                    <div class="legend-item">
                                        <div class="legend-item-left">
                                            <div class="legend-color-box" style="background: {{ $colors[$index % count($colors)] }}"></div>
                                            <span>{{ strtoupper(str_replace('_', ' ', $role->label)) }}</span>
                                        </div>
                                        <span class="legend-count">{{ $role->value }}</span>
                                    </div>
                                @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- System Control Panel -->
            <div class="glass-container">
                <div class="content-header">
                    <h6>Control Panel</h6>
                </div>
                <div class="px-4 py-4">
                    <div class="control-panel-grid">
                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('admin.auth-reset-management') }}" class="control-action-card" style="padding: 0.75rem; gap: 0.75rem;">
                                <div class="card-icon-wrapper bg-purple-soft" style="width: 36px; height: 36px; font-size: 1rem;">
                                    <i class="fas fa-key"></i>
                                </div>
                                <div class="card-content-area">
                                    <h4 class="card-main-title" style="font-size: 0.85rem;">Auth Management</h4>
                                    <p class="card-description" style="font-size: 0.7rem;">Security & Methods</p>
                                </div>
                            </a>

                            <a href="{{ route('admin.signatories') }}" class="control-action-card" style="padding: 0.75rem; gap: 0.75rem;">
                                <div class="card-icon-wrapper bg-teal-soft" style="width: 36px; height: 36px; font-size: 1rem;">
                                    <i class="fas fa-signature"></i>
                                </div>
                                <div class="card-content-area">
                                    <h4 class="card-main-title" style="font-size: 0.85rem;">Signatory Control</h4>
                                    <p class="card-description" style="font-size: 0.7rem;">Signature Workflows</p>
                                </div>
                            </a>
                        @endif

                        <a href="{{ route('admin.manage-users') }}" class="control-action-card" style="padding: 0.75rem; gap: 0.75rem;">
                            <div class="card-icon-wrapper bg-green-soft" style="width: 36px; height: 36px; font-size: 1rem;">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <div class="card-content-area">
                                <h4 class="card-main-title" style="font-size: 0.85rem;">User Governance</h4>
                                <p class="card-description" style="font-size: 0.7rem;">Roles & Access</p>
                            </div>
                        </a>

                        <a href="{{ route('admin.register-user') }}" class="control-action-card" style="padding: 0.75rem; gap: 0.75rem;">
                            <div class="card-icon-wrapper bg-cyan-soft" style="width: 36px; height: 36px; font-size: 1rem;">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="card-content-area">
                                <h4 class="card-main-title" style="font-size: 0.85rem;">Provision User</h4>
                                <p class="card-description" style="font-size: 0.7rem;">Onboard New Users</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Row: Global Infrastructure Audit (Full Width) -->
        <div class="glass-container audit-container">
            <div class="content-header d-flex justify-content-between align-items-center">
                <h6>Global Infrastructure Audit</h6>
                <a href="{{ route('admin.activity-logs') }}"
                    class="btn btn-sm btn-link text-primary p-0 text-decoration-none small fw-bold">Audit Logs</a>
            </div>
            <div class="audit-scroll-container">
                <table class="audit-table">
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
                            @php
                                $action = strtolower($log->action);
                                $badgeClass = 'badge-default';
                                
                                if (str_contains($action, 'login')) $badgeClass = 'badge-login';
                                elseif (str_contains($action, 'create') || str_contains($action, 'add')) $badgeClass = 'badge-create';
                                elseif (str_contains($action, 'update') || str_contains($action, 'edit') || str_contains($action, 'mod')) $badgeClass = 'badge-update';
                                elseif (str_contains($action, 'delet') || str_contains($action, 'remov')) $badgeClass = 'badge-delete';
                            @endphp
                            <tr>
                                <td>
                                    <span class="audit-timestamp">{{ $log->created_at->format('M j, g:i A') }}</span>
                                </td>
                                <td>
                                    <div class="audit-user-box">
                                        @if($log->user && $log->user->profile_picture)
                                            <img src="{{ storage_url($log->user->profile_picture) }}" alt="" class="audit-avatar">
                                        @else
                                            <div class="audit-avatar-placeholder">
                                                {{ strtoupper(substr($log->user->full_name ?? 'U', 0, 1)) }}
                                            </div>
                                        @endif
                                        <span class="fw-bold text-dark">{{ $log->user->full_name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="audit-action-badge {{ $badgeClass }}">{{ $log->action }}</span>
                                </td>
                                <td class="audit-details">{{ Str::limit($log->details, 60) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
                        labels: {!! json_encode(collect($userGrowth)->pluck('month')) !!},
                        datasets: [{
                            label: 'New Users',
                            data: {!! json_encode(collect($userGrowth)->pluck('count')) !!},
                            borderColor: '#8b5cf6',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            backgroundColor: gradient,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#8b5cf6',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { 
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                titleColor: '#1e293b',
                                bodyColor: '#475569',
                                titleFont: { family: "'Plus Jakarta Sans'", size: 12, weight: '700' },
                                bodyFont: { family: "'Plus Jakarta Sans'", size: 11, weight: '600' },
                                padding: 12,
                                cornerRadius: 10,
                                borderColor: 'rgba(226, 232, 240, 0.8)',
                                borderWidth: 1,
                                displayColors: true,
                                boxWidth: 8,
                                boxHeight: 8,
                                usePointStyle: true,
                                callbacks: {
                                    title: function(context) {
                                        return context[0].label.toUpperCase();
                                    },
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== undefined) {
                                            label += context.parsed.y;
                                        }
                                        return label.toUpperCase();
                                    }
                                }
                            }
                        },
                        scales: {
                            x: { 
                                grid: { display: false }, 
                                ticks: { color: '#94a3b8', font: { size: 10, weight: '600' } } 
                            },
                            y: {
                                grid: { color: '#f1f5f9', drawBorder: false },
                                ticks: { 
                                    color: '#94a3b8', 
                                    font: { size: 10, weight: '600' }, 
                                    precision: 0,
                                    padding: 10
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Role Distribution Chart
                const roleCtx = document.getElementById('roleChart').getContext('2d');
                const roleChart = new Chart(roleCtx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($roleDistribution->pluck('label')) !!},
                        datasets: [{
                            data: {!! json_encode($roleDistribution->pluck('value')) !!},
                            backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#3b82f6'],
                            hoverOffset: 12,
                            borderWidth: 4,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 0 // Disable initial animation for smooth rotation
                        },
                        plugins: { 
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                bodyColor: '#475569',
                                bodyFont: { family: "'Plus Jakarta Sans'", size: 11, weight: '700' },
                                padding: 12,
                                cornerRadius: 10,
                                borderColor: 'rgba(226, 232, 240, 0.8)',
                                borderWidth: 1,
                                displayColors: true,
                                boxWidth: 8,
                                boxHeight: 8,
                                usePointStyle: true,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label = label.replace(/_/g, ' ');
                                            label += ': ';
                                        }
                                        if (context.parsed !== undefined) {
                                            label += context.parsed;
                                        }
                                        return label.toUpperCase();
                                    }
                                }
                            }
                        },
                        cutout: '80%'
                    }
                });

                // Spinning Animation Logic
                let rotation = 0;
                let isPaused = false;

                function animate() {
                    if (!isPaused) {
                        rotation += 0.2; // Adjust speed here
                        if (rotation >= 360) rotation = 0;
                        roleChart.options.rotation = rotation;
                        roleChart.update('none'); // Update without animation for smoothness
                    }
                    requestAnimationFrame(animate);
                }

                // Hover triggers
                roleChart.canvas.addEventListener('mouseenter', () => { isPaused = true; });
                roleChart.canvas.addEventListener('mouseleave', () => { isPaused = false; });

                animate();
            @endif
        });
    </script>
@endsection