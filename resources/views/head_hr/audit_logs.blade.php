@extends('layouts.sdo')

@section('title', 'Audit Logs')
@section('page-title', 'Leave Credit Audit Logs')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/activity-logs.css') }}">
    <style>
        /* Modern Design System & Glassmorphism */
        /* Modern Design System & SaaS Aesthetics */
        :root {
            --bg-saas: #ffffff;
            --card-white: #ffffff;
            --accent-indigo: #4F46E5;
            --accent-blue: #3B82F6;
            --accent-green: #10B981;
            --accent-orange: #F59E0B;
            --accent-purple: #8B5CF6;
            --navy-deep: #1E293B;
            --saas-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --saas-shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        body {
            background-color: #ffffff !important;
            font-family: 'Inter', 'Poppins', -apple-system, sans-serif !important;
        }

        .logs-container {
            max-width: 100%; /* Changed from 1400px to fully maximize space */
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 12px; /* Reduced gap further */
            padding-bottom: 16px;
            animation: fadeIn 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Sequential Animations */
        .stat-premium {
            opacity: 0;
            animation: backInDown 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .stat-premium:nth-child(1) { animation-delay: 0.1s; }
        .stat-premium:nth-child(2) { animation-delay: 0.2s; }
        .stat-premium:nth-child(3) { animation-delay: 0.3s; }
        .stat-premium:nth-child(4) { animation-delay: 0.4s; }

        .log-section-header {
            opacity: 0;
            animation: fadeInDown 0.6s ease-out 0.5s forwards;
        }

        .log-item {
            opacity: 0;
            animation: backInDown 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @foreach(range(1, 20) as $i)
            .log-item:nth-child({{ $i }}) {
                animation-delay: {{ 0.6 + ($i * 0.05) }}s;
            }
        @endforeach

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

        /* Glassmorphism Cards */
        .glass-card {
            background: #ffffff !important;
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 16px 16px 0 0; /* Reduced radius slightly to save vertical space */
            box-shadow: var(--saas-shadow);
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Stats Overview */
        /* Minimalist Metric Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px; /* Reduced gap */
        }

        .stat-premium {
            background: var(--card-white);
            border-radius: 16px;
            padding: 10px 14px; /* Squeezed padding further */
            box-shadow: var(--saas-shadow);
            border: 1px solid rgba(226, 232, 240, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            gap: 8px; /* Reduced gap */
        }

        .stat-premium:hover {
            transform: translateY(-2px);
            box-shadow: var(--saas-shadow-hover);
        }

        .stat-top {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stat-icon-box {
            width: 48px; /* Reduced icon size slightly */
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        /* Specific Icon Colors */
        .box-blue { background: rgba(59, 130, 246, 0.1); color: var(--accent-blue); }
        .box-green { background: rgba(16, 185, 129, 0.1); color: var(--accent-green); }
        .box-orange { background: rgba(245, 158, 11, 0.1); color: var(--accent-orange); }
        .box-purple { background: rgba(139, 92, 246, 0.1); color: var(--accent-purple); }

        .stat-value { font-size: 2.2rem; font-weight: 800; color: var(--navy-deep); letter-spacing: -0.02em; line-height: 1; }
        .stat-label { font-size: 0.75rem; font-weight: 700; color: #94A3B8; text-transform: capitalize; letter-spacing: 0.02em; }

        /* Filter Row Redesign */
        .premium-filter-container {
            padding: 12px 16px;
        }

        .log-section-header {
            padding: 12px 20px; /* Reduced padding further */
            display: flex;
            flex-direction: column;
            gap: 12px; /* Reduced gap from 20px */
            background: #0f4c75 !important;
            border-radius: 16px 16px 0 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .header-top-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .header-filters-row {
            padding-top: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background: transparent !important;
        }

        .header-title-box {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-icon-container {
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .header-text-main {
            font-size: 1.15rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.01em;
            text-transform: uppercase;
        }

        .filter-row {
            display: flex;
            gap: 12px; /* Reduced from 16px */
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 250px;
        }

        .filter-label {
            display: block;
            font-size: 0.7rem;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 6px; /* Reduced from 10px */
        }

        .input-premium, .custom-select, .custom-select-trigger, .btn-filter-apply, .btn-filter-reset {
            height: 40px !important; /* Compressing height across all inputs from 48px to 40px */
        }

        .input-premium, .custom-select {
            width: 100%;
            height: 40px; /* Reduced to match global squeeze */
            padding: 0 12px; /* Reduced horizontal padding */
            background: #ffffff !important;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px; /* Condensed border radius */
            font-size: 0.9rem; /* Slightly smaller */
            font-weight: 600;
            color: #1e293b;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .custom-select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 12px center !important;
            background-size: 14px !important;
            padding-right: 32px;
        }

        .input-premium:hover, .custom-select:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        .input-premium:focus, .custom-select:focus {
            border-color: var(--accent-indigo);
            background: white !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15); /* Slightly smaller shadow ring */
            outline: none;
        }

        .btn-filter-apply {
            height: 40px;
            padding: 0 20px;
            background: #1e293b;
            color: white;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s;
        }

        .btn-filter-reset {
            height: 40px;
            width: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            color: #64748b;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            transition: all 0.2s;
        }

        /* Custom Dropdown Specific Styles */
        .filter-custom-dropdown {
            position: relative;
            width: 100%;
        }

        .option-icon-box {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        /* SaaS Segmented Control */
        .saas-segmented-control {
            display: flex;
            background: rgba(15, 23, 42, 0.4) !important;
            padding: 4px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            gap: 4px;
            height: 40px; /* match inputs */
        }

        .segmented-item {
            flex: 1;
            text-align: center;
            padding: 6px 10px; /* Squeezed inner padding */
            font-size: 0.75rem;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            border-radius: 8px; /* Slightly tighter */
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .segmented-item.active {
            background: #ffffff !important;
            color: #0f4c75 !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .segmented-item:hover:not(.active) {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.15);
        }

        /* SaaS Primary Button (Vibrant Orange) */
        .saas-primary-btn {
            background: #f59e0b !important;
            border: 1px solid #d97706;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 12px;
            color: white;
            font-weight: 700;
            height: 44px;
            padding: 0 24px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .saas-primary-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.3);
            filter: brightness(1.1);
        }

        /* Export Button */
        .btn-export {
            height: 40px;
            padding: 0 16px;
            background: white;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            color: #475569;
            font-weight: 700;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-export:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #1e293b;
        }

        /* Logs List Refinement */
        .logs-list {
            max-height: 700px;
            overflow-y: auto;
            padding: 8px;
        }

        .log-item {
            padding: 16px; /* Squeezed from 24px */
            margin: 10px 0; /* Squeezed from 16px */
            border-radius: 16px; /* Slightly tighter border radius */
            border: 1px solid #f1f5f9;
            display: flex;
            gap: 16px; /* Reduced from 20px */
            background: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .log-item:hover {
            border-color: #e2e8f0;
            box-shadow: 0 8px 16px -6px rgba(0, 0, 0, 0.08); /* Condensed shadow */
            transform: translateY(-2px);
        }

        .log-icon-wrapper {
            width: 44px; /* Reduced from 56px */
            height: 44px; /* Reduced from 56px */
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem; /* Scaled down icon */
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }

        .log-item:hover .log-icon-wrapper {
            transform: scale(1.05) rotate(-5deg);
        }

        .icon-allocate { background: #f0fdf4; color: #16a34a; }
        .icon-deduct { background: #fef2f2; color: #dc2626; }
        .icon-update { background: #eff6ff; color: #2563eb; }
        .icon-add_coc { background: #faf5ff; color: #9333ea; }

        .log-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 6px; /* Squeezed from 12px */
        }

        .log-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 8px; /* Squeezed from 12px */
        }

        .actor-info {
            display: flex;
            flex-direction: column;
            gap: 0; /* Removed gap */
        }

        .actor-name { 
            font-family: 'Inter', sans-serif;
            font-weight: 700; 
            color: #0f172a; 
            font-size: 1.1rem;
            letter-spacing: -0.01em;
        }

        .target-box {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: #64748b;
        }

        .target-name { 
            font-weight: 600; 
            color: #334155;
        }

        .action-tag-pill {
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 999px;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .tag-allocate { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .tag-deduct { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .tag-update { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .tag-add_coc { background: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff; }

        .log-metadata {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            font-weight: 500;
            color: #94a3b8;
        }

        .leave-tag {
            background: #f8fafc;
            color: #64748b;
            padding: 4px 10px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-weight: 600;
        }

        .reason-text {
            font-size: 0.85rem;
            color: #64748b;
            font-style: italic;
            border-left: 2px solid #e2e8f0;
            padding-left: 12px;
            margin: 4px 0;
        }

        /* Balance Change Pill */
        .balance-change-container {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-top: 4px;
        }

        .balance-pill {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            gap: 12px;
        }

        .val-box {
            display: flex;
            flex-direction: column;
            line-height: 1;
        }

        .val-label {
            font-size: 0.6rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 3px;
            letter-spacing: 0.05em;
        }

        .val-num { 
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            font-weight: 700; 
            font-size: 1.05rem; 
        }

        .val-num.old { color: #64748b; opacity: 0.7; }
        .val-num.new { color: #0f172a; }

        .change-indicator {
            padding: 6px 14px;
            border-radius: 12px;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 800;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
        }

        .change-add { 
            background: #f0fdf4; 
            color: #15803d; 
            border: 1px solid #bbf7d0;
        }

        .change-subtract { 
            background: #fff7ed; 
            color: #c2410c; 
            border: 1px solid #fed7aa;
        }

        .change-deduct { 
            background: #fef2f2; 
            color: #b91c1c; 
            border: 1px solid #fecaca; 
        }

        /* Restoration: Secure Log Entry Badge */
        .premium-badge-glass {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 99px;
            display: flex;
            align-items: center;
            gap: 8px;
            backdrop-filter: blur(4px);
        }

        .badge-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .badge-text {
            font-size: 0.85rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.95);
            letter-spacing: 0.01em;
            font-family: 'Inter', sans-serif;
        }

        /* SaaS Pagination Design */
        .saas-pagination-wrapper {
            margin-top: 16px; /* Reduced from 24px */
            padding: 12px 16px; /* Squeezed from 16px 32px */
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            border-radius: 0 0 16px 16px;
        }

        .saas-pagination-info {
            font-size: 0.8rem; /* Smaller font */
            color: #64748b;
            font-weight: 500;
        }

        .pagination-premium .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            gap: 2px;
        }

        .pagination-premium .page-item {
            margin: 0;
        }

        .pagination-premium .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            background: transparent;
            border: none;
            border-radius: 8px;
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .pagination-premium .page-item.active .page-link {
            background: #f0f9ff !important;
            border: none !important;
            color: #0f4c75 !important;
            box-shadow: none;
        }

        .pagination-premium .page-item.disabled .page-link {
            background: transparent;
            color: #cbd5e1;
            border: none;
            cursor: not-allowed;
        }

        .pagination-premium .page-link:hover:not(.disabled):not(.active) {
            border: none;
            color: #0f4c75;
            background: #f8fafc;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .saas-pagination-wrapper {
                flex-direction: column;
                text-align: center;
                gap: 16px;
                padding: 16px;
            }
        }

    </style>
@endpush

@section('content')
    <div class="logs-container">
        <!-- Stats Overview -->
        <div class="stats-grid">
            <div class="stat-premium">
                <div class="stat-top">
                    <div class="stat-icon-box box-blue">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="stat-value">{{ $stats['total'] }}</div>
                </div>
                <div class="stat-label">Total Activities</div>
            </div>

            <div class="stat-premium">
                <div class="stat-top">
                    <div class="stat-icon-box box-green">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="stat-value">{{ $stats['allocate'] }}+</div>
                </div>
                <div class="stat-label">Allocations</div>
            </div>

            <div class="stat-premium">
                <div class="stat-top">
                    <div class="stat-icon-box box-orange">
                        <i class="fas fa-minus-circle"></i>
                    </div>
                    <div class="stat-value">{{ $stats['deduct'] }}+</div>
                </div>
                <div class="stat-label">Deductions</div>
            </div>

            <div class="stat-premium">
                <div class="stat-top">
                    <div class="stat-icon-box box-purple">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="stat-value">{{ $stats['update'] }}+</div>
                </div>
                <div class="stat-label">Updates</div>
            </div>
        </div>

        <!-- Logs Section -->
        <div class="glass-card">
            <div class="log-section-header">
                <div class="header-top-row">
                    <div class="header-title-box">
                        <div class="header-icon-container">
                            <i class="fas fa-history"></i>
                        </div>
                        <div class="header-text-main">
                            Audit Activity Stream
                        </div>
                    </div>
                    
                    <div class="premium-badge-glass">
                        <span class="badge-dot"></span>
                        <span class="badge-text">Secure Log Entry</span>
                    </div>
                </div>

                <div class="header-filters-row">
                    <form action="{{ route('head-hr.audit-logs') }}" method="GET">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label class="filter-label">Quick Search</label>
                                <input type="text" class="input-premium" name="search"
                                    placeholder="User, action, or details..." value="{{ $filters['search'] ?? '' }}">
                            </div>

                            <div class="filter-group">
                                <label class="filter-label">Action Category</label>
                                <div class="filter-custom-dropdown" id="actionCategoryDropdown">
                                    <input type="hidden" name="action" id="actionInput" value="{{ $filters['action'] ?? '' }}">
                                    <div class="custom-select-trigger saas-trigger {{ isset($filters['action']) && $filters['action'] != '' ? 'has-value' : '' }}">
                                        <span class="custom-select-text">
                                            @php
                                                $actionLabels = [
                                                    '' => 'All Activities',
                                                    'allocate' => 'Allocations',
                                                    'update' => 'Modifications',
                                                    'deduct' => 'Deductions',
                                                    'add_coc' => 'COC Credit'
                                                ];
                                                $currentAction = $filters['action'] ?? '';
                                                echo $actionLabels[$currentAction] ?? 'All Activities';
                                            @endphp
                                        </span>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                    <div class="custom-select-options saas-options">
                                        <div class="custom-option {{ ($filters['action'] ?? '') == '' ? 'selected' : '' }}" data-value="">
                                            All Activities
                                        </div>
                                        <div class="custom-option {{ ($filters['action'] ?? '') == 'allocate' ? 'selected' : '' }}" data-value="allocate">
                                            Allocations
                                        </div>
                                        <div class="custom-option {{ ($filters['action'] ?? '') == 'update' ? 'selected' : '' }}" data-value="update">
                                            Modifications
                                        </div>
                                        <div class="custom-option {{ ($filters['action'] ?? '') == 'deduct' ? 'selected' : '' }}" data-value="deduct">
                                            Deductions
                                        </div>
                                        <div class="custom-option {{ ($filters['action'] ?? '') == 'add_coc' ? 'selected' : '' }}" data-value="add_coc">
                                            COC Credit
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="filter-group" style="flex: 1.5; min-width: 320px;">
                                <label class="filter-label">Time Range</label>
                                <input type="hidden" name="date_range" id="dateRangeInput" value="{{ $filters['date_range'] ?? '' }}">
                                <div class="saas-segmented-control">
                                    <div class="segmented-item {{ ($filters['date_range'] ?? '') == '' ? 'active' : '' }}" data-range="">All-time</div>
                                    <div class="segmented-item {{ ($filters['date_range'] ?? '') == 'today' ? 'active' : '' }}" data-range="today">Today</div>
                                    <div class="segmented-item {{ ($filters['date_range'] ?? '') == '7days' ? 'active' : '' }}" data-range="7days">Week</div>
                                    <div class="segmented-item {{ ($filters['date_range'] ?? '') == '30days' ? 'active' : '' }}" data-range="30days">Month</div>
                                </div>
                            </div>

                            <div class="filter-group" style="flex: 0 0 auto; min-width: auto; display: flex; gap: 10px; align-items: flex-end; padding-bottom: 1px;">
                                <button type="submit" class="btn-filter-apply saas-primary-btn">
                                    <i class="fas fa-filter"></i>
                                    Apply Filter
                                </button>
                                <a href="{{ route('head-hr.audit-logs') }}" class="btn-filter-reset" title="Clear Filters">
                                    <i class="fas fa-redo-alt"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="logs-list">
                @forelse($logs as $log)
                    @php
                        $isAddition = in_array($log->action, ['allocate', 'add_coc']);
                        $isDeduction = $log->action == 'deduct';
                        $diff = ($log->new_value ?? 0) - ($log->previous_value ?? 0);
                        $diffFormatted = ($diff > 0 ? '+' : '') . number_format($diff, 2);
                        
                        $changeClass = 'change-update';
                        if ($diff > 0) $changeClass = 'change-add';
                        elseif ($isDeduction) $changeClass = 'change-deduct';
                        elseif ($diff < 0) $changeClass = 'change-subtract';
                    @endphp
                    <div class="log-item">
                        <div class="log-icon-wrapper icon-{{ $log->action }}">
                            @if($log->action == 'allocate') <i class="fas fa-plus-circle"></i>
                            @elseif($log->action == 'deduct') <i class="fas fa-minus-circle"></i>
                            @elseif($log->action == 'update') <i class="fas fa-edit"></i>
                            @elseif($log->action == 'add_coc') <i class="fas fa-clock"></i>
                            @else <i class="fas fa-info-circle"></i> @endif
                        </div>
                        
                        <div class="log-content">
                            <div class="log-header">
                                <div class="actor-info">
                                    <span class="actor-name">{{ $log->targetUser->full_name ?? 'Unknown User' }}</span>
                                    <div class="target-box">
                                        <span class="action-tag-pill tag-{{ $log->action }}">
                                            @if($isAddition) <i class="fas fa-arrow-up"></i>
                                            @elseif($isDeduction) <i class="fas fa-arrow-down"></i>
                                            @endif
                                            {{ str_replace('_', ' ', $log->action) }}
                                        </span>
                                        <span class="text-slate-300">•</span>
                                        <span>Action by <span class="target-name">{{ $log->actor->full_name ?? 'System' }}</span></span>
                                    </div>
                                </div>
                                <div class="meta-item">
                                    <i class="far fa-clock"></i>
                                    {{ $log->created_at->diffForHumans() }}
                                </div>
                            </div>

                            <div class="log-metadata">
                                <span class="leave-tag">
                                    <i class="fas fa-tag mr-2 opacity-50"></i>{{ $log->leave_type_name }}
                                </span>
                                @if($log->reason)
                                    <div class="reason-text">"{{ $log->reason }}"</div>
                                @endif
                            </div>

                            @if($log->previous_value !== null || $log->new_value !== null)
                                <div class="balance-change-container">
                                    <div class="balance-pill">
                                        <div class="val-box">
                                            <span class="val-label">From</span>
                                            <span class="val-num old">{{ number_format($log->previous_value ?? 0, 2) }}</span>
                                        </div>
                                        <i class="fas fa-chevron-right text-slate-300"></i>
                                        <div class="val-box">
                                            <span class="val-label">To</span>
                                            <span class="val-num new">{{ number_format($log->new_value ?? 0, 2) }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="change-indicator {{ $changeClass }}">
                                        @if($diff > 0) <i class="fas fa-caret-up"></i>
                                        @elseif($diff < 0) <i class="fas fa-caret-down"></i>
                                        @endif
                                        {{ $diffFormatted }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-illustration">
                            <i class="fas fa-history"></i>
                        </div>
                        <h3 class="text-2xl font-black text-slate-300">Clean Records</h3>
                        <p class="text-slate-400 mt-2 font-bold">No matching audit logs were found for your current criteria.</p>
                    </div>
                @endforelse
            </div>

            <div class="p-8 border-t border-slate-100 bg-slate-50/20">
                <div class="saas-pagination-wrapper">
                    <div class="pagination-info">
                        Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} results
                    </div>
                    <div class="pagination-premium">
                        {{ $logs->links('vendor.pagination.saas') }}
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Custom Dropdown JS Logic
            document.addEventListener('DOMContentLoaded', function() {
                const dropdown = document.getElementById('actionCategoryDropdown');
                if (dropdown) {
                    const trigger = dropdown.querySelector('.custom-select-trigger');
                    const optionsContainer = dropdown.querySelector('.custom-select-options');
                    const options = dropdown.querySelectorAll('.custom-option');
                    const textSpan = trigger.querySelector('.custom-select-text');
                    const hiddenInput = dropdown.querySelector('#actionInput');
                    
                    // Toggle dropdown
                    trigger.addEventListener('click', function(e) {
                        e.stopPropagation();
                        // Close any other open dropdowns first
                        document.querySelectorAll('.custom-select-options.show').forEach(el => {
                            if(el !== optionsContainer) {
                                el.classList.remove('show');
                                el.previousElementSibling.classList.remove('active');
                            }
                        });
                        
                        optionsContainer.classList.toggle('show');
                        trigger.classList.toggle('active');
                    });
                    
                    // Option selection
                    options.forEach(option => {
                        option.addEventListener('click', function() {
                            const value = this.getAttribute('data-value');
                            // Extract just the text node, ignore the icon HTML
                            const text = Array.from(this.childNodes).filter(node => node.nodeType === Node.TEXT_NODE).map(n => n.textContent.trim()).join('');
                            
                            // Update UI
                            textSpan.textContent = text;
                            options.forEach(opt => opt.classList.remove('selected'));
                            this.classList.add('selected');
                            trigger.classList.add('has-value');
                            
                            // Update hidden input
                            hiddenInput.value = value;
                            
                            // Close dropdown
                            optionsContainer.classList.remove('show');
                            trigger.classList.remove('active');
                            
                            // Submit the form to apply filter instantly
                            dropdown.closest('form').submit();
                        });
                    });
                    
                    // Close dropdown when clicking outside
                    document.addEventListener('click', function(e) {
                        if (!dropdown.contains(e.target)) {
                            optionsContainer.classList.remove('show');
                            trigger.classList.remove('active');
                        }
                    });
                }
            });

            // SaaS Segmented Control Interaction
            document.querySelectorAll('.segmented-item').forEach(item => {
                item.addEventListener('click', function () {
                    document.querySelectorAll('.segmented-item').forEach(p => p.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById('dateRangeInput').value = this.dataset.range;
                    this.closest('form').submit();
                });
            });

            // Export Interaction Placeholder
            document.querySelector('.btn-export').addEventListener('click', function() {
                alert('Export Engine Initiated: Preparing records for CSV/PDF generation. Please wait...');
            });
        </script>
    </div>
@endsection