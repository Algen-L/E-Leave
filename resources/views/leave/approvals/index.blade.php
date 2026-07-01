@extends('layouts.sdo')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/manage-users.css') }}">
    <style>
        .approvals-premium {
            animation: fadeIn 0.4s ease-out;
            font-family: 'Plus Jakarta Sans', sans-serif;
            position: relative;
        }

        .approvals-header-hero {
            position: relative;
            background: #1b4a9a !important; /* Sidebar blue shade */
            padding: 24px 44px !important;
            border-radius: 24px !important;
            margin: 0 !important;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            box-shadow: 0 12px 30px -5px rgba(15, 76, 117, 0.2) !important;
            overflow: hidden;
            z-index: 10;
        }

        /* Abstract background glow */
        .approvals-header-hero::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(27, 108, 168, 0.25) 0%, rgba(15, 76, 117, 0) 70%);
            z-index: 0;
            pointer-events: none;
        }

        .header-title-main {
            font-size: 2.8rem !important;
            font-weight: 900 !important;
            color: white !important;
            letter-spacing: -0.04em !important;
            line-height: 1 !important;
            position: relative;
            z-index: 1;
        }

        .header-title-accent {
            color: rgba(255, 255, 255, 0.7) !important;
            background: none !important;
            -webkit-text-fill-color: initial !important;
        }

        .header-text-group p {
            color: rgba(255, 255, 255, 0.8) !important;
            font-size: 0.95rem !important;
            font-weight: 500 !important;
            max-width: 550px !important;
            margin-top: 8px !important;
            line-height: 1.4 !important;
        }

        /* Glassmorphic Tabs (integrated in hero) */
        .tab-toggle-container {
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 4px !important;
            border-radius: 99px !important;
            display: inline-flex;
            gap: 2px;
            margin-top: 18px !important;
            position: relative;
            z-index: 1;
        }

        .tab-btn {
            color: rgba(255, 255, 255, 0.75) !important;
            padding: 8px 22px !important;
            border-radius: 99px !important;
            font-weight: 800 !important;
            font-size: 0.72rem !important;
            letter-spacing: 0.04em;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            border: none !important;
            background: transparent !important;
            text-decoration: none !important; /* Added to remove underline */
        }

        .tab-btn.active {
            background: white !important;
            color: #1b4a9a !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15) !important;
            transform: translateY(0) !important;
        }

        .tab-btn:not(.active):hover {
            background: rgba(255, 255, 255, 0.18) !important;
            color: white !important;
            transform: translateY(-1px) !important;
        }

        /* Officer Badge (Glass Card) */
        .officer-badge-premium {
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            padding: 24px 32px !important;
            border-radius: 28px !important;
            box-shadow: 0 15px 35px rgba(15, 76, 117, 0.2) !important;
            min-width: 300px;
            display: flex;
            align-items: center;
            gap: 20px;
            z-index: 1;
        }

        .officer-icon-wrapper {
            background: white !important;
            color: #1b4a9a !important;
            width: 48px !important;
            height: 48px !important;
            border-radius: 16px !important;
            font-size: 1.5rem !important;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1) !important;
        }

        .officer-name-highlight {
            color: white !important;
            font-weight: 900 !important;
            font-size: 1.35rem !important;
            letter-spacing: 0.02em !important;
            text-transform: uppercase !important;
        }

        /* Glassmorphism User Card (Compact Flex Layout) */
        .user-card {
            display: grid;
            grid-template-columns: 1.4fr 0.9fr 1.1fr 0.8fr 0.9fr 1fr; /* Optimized distribution */
            gap: 12px;
            padding: 10px 16px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
            width: 100%;
            align-items: center;
            position: relative; /* Added for absolute children */
        }

        .badge-new-dot {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #ef4444;
            color: white;
            font-size: 0.55rem;
            font-weight: 900;
            padding: 2px 5px;
            border-radius: 4px;
            text-transform: uppercase;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
            z-index: 5;
            letter-spacing: 0.05em;
        }

        @media (max-width: 1200px) {
            .user-card {
                display: flex;
                flex-wrap: wrap;
                grid-template-columns: none;
            }
        }

        @media (max-width: 768px) {
            .user-card {
                flex-direction: column;
                align-items: stretch;
                padding: 16px !important;
                gap: 15px;
            }

            .user-card > div {
                width: 100%;
                min-width: 0 !important;
                text-align: left !important;
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid var(--border-light);
                padding-bottom: 10px;
            }

            .user-card > div.user-info {
                border-bottom: 2px solid var(--primary-light);
                padding-bottom: 12px;
                margin-bottom: 5px;
            }

            .user-card > div.approval-actions {
                border-bottom: none;
                padding-bottom: 0;
                margin-top: 10px;
                justify-content: stretch;
            }

            .btn-review {
                width: 100%;
                justify-content: center;
                padding: 12px !important;
            }

            .user-card .user-meta-label {
                display: block !important;
            }

            .user-card .status-badge-pending, .user-card .badge-days {
                margin-left: auto;
            }
        }

        .user-card:hover {
            border-color: var(--primary) !important;
            background: white !important;
            transform: translateY(-3px);
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.1) !important;
        }

        /* Sequential Animation */
        .approvals-scroll-area .user-card {
            opacity: 0;
            animation: backInDown 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @foreach(range(1, 20) as $i)
            .approvals-scroll-area .user-card:nth-child({{ $i }}) {
                animation-delay: {{ $i * 0.1 }}s;
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

        .approval-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
        }

        .btn-review {
            background: var(--primary-gradient, linear-gradient(135deg, #1b4a9a 0%, #3282b8 100%));
            color: white;
            padding: 8px 16px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(15, 76, 117, 0.2);
            border: none;
        }

        .btn-review:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(15, 76, 117, 0.3);
            filter: brightness(1.1);
            color: white;
        }

        /* Scrollable Container */
        .approvals-scroll-area {
            max-height: calc(100vh - 280px);
            overflow-y: auto;
            padding: 10px 10px 40px 10px;
            margin: 0 -10px;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .approvals-scroll-area::-webkit-scrollbar {
            width: 5px;
        }

        .approvals-scroll-area::-webkit-scrollbar-track {
            background: transparent;
        }

        .approvals-scroll-area::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .header-card .user-meta-label {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0 10px;
        }

        .user-card .user-name {
            font-size: 0.95rem !important;
            font-weight: 800 !important;
            color: #1e293b !important;
            letter-spacing: -0.01em;
        }

        .leave-type-name {
            font-weight: 700;
            color: var(--primary);
            font-size: 0.95rem;
            display: block;
        }

        .badge-days {
            background: rgba(14, 165, 233, 0.1);
            color: #1b4a9a;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            display: inline-block;
        }

        .status-badge-pending {
            background: rgba(249, 115, 22, 0.1);
            color: #ea580c;
            border: 1px solid rgba(249, 115, 22, 0.2);
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 99px;
            font-size: 0.62rem;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .status-badge-pending::before {
            content: '';
            width: 6px;
            height: 6px;
            background: #f97316;
            border-radius: 50%;
            display: inline-block;
            animation: pulse-orange 2s infinite;
        }

        @keyframes pulse-orange {
            0% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.4); }
            70% { box-shadow: 0 0 0 8px rgba(249, 115, 22, 0); }
            100% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0); }
        }

        .status-badge-past {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.2);
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 99px;
            font-size: 0.62rem;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .status-badge-past::before {
            content: '';
            width: 6px;
            height: 6px;
            background: #ef4444;
            border-radius: 50%;
            display: inline-block;
        }

        /* Modal Enhancements */
        .modal-content {
            border: none !important;
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.95) !important;
            border-radius: 24px !important;
            overflow: hidden;
        }

        .modal-header {
            padding: 24px 30px !important;
            border-bottom: 1px solid #f1f5f9 !important;
            background: #f8fafc;
        }

        .modal-body {
            padding: 30px !important;
        }

        .modal-footer {
            padding: 20px 30px !important;
            border-top: 1px solid #f1f5f9 !important;
            background: #f8fafc;
        }

        .form-control {
            border-radius: 14px !important;
            border: 2px solid #f1f5f9 !important;
            padding: 12px 16px !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-size: 0.9rem !important;
            transition: all 0.2s !important;
        }

        .form-control:focus {
            border-color: #f97316 !important;
            box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1) !important;
            background: white !important;
        }
        .tab-btn.active {
            background: white;
            color: #1b4a9a;
            box-shadow: 0 4px 15px rgba(15, 76, 117, 0.1);
        }

        /* Advanced Filter Bar */
        .filter-section-premium {
            background: white;
            border-radius: 16px;
            padding: 12px 20px;
            margin-bottom: 20px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-wrap: nowrap; /* Force single row */
            gap: 16px;
            align-items: flex-end;
            overflow-x: auto; /* Handle overflow if screen is small */
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
            min-width: 120px; 
        }

        .filter-group.narrow {
            flex: 0.5;
            min-width: 120px;
        }

        .filter-label {
            font-size: 0.65rem;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }

        .filter-input-wrapper {
            position: relative;
        }

        .filter-input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .filter-input {
            width: 100%;
            height: 38px;
            padding: 8px 12px 8px 36px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            font-size: 0.85rem;
            font-weight: 600;
            color: #1e293b;
            transition: all 0.2s;
        }

        .filter-input:focus {
            border-color: #1b4a9a;
            background: white;
            box-shadow: 0 0 0 4px rgba(15, 76, 117, 0.05);
            outline: none;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            flex-shrink: 0; /* Don't shrink buttons */
            margin-bottom: 0px; /* Alignment fix */
        }

        .btn-filter {
            height: 38px;
            padding: 0 16px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            white-space: nowrap;
        }

        .btn-apply {
            background: #1b4a9a;
            color: white;
        }

        .btn-apply:hover {
            background: #3b66bc;
            transform: translateY(-1px);
        }

        .btn-reset {
            background: #f1f5f9;
            color: #64748b;
            text-decoration: none;
        }

        .btn-reset:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        .btn-pdf {
            background: #dc2626;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.1);
            width: 38px; /* Square button for icon-only */
        }

        .btn-pdf:hover {
            background: #b91c1c;
            transform: translateY(-1px);
            box-shadow: 0 6px 12px -2px rgba(220, 38, 38, 0.2);
        }

        .btn-icon-only {
            width: 38px;
            padding: 0 !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid approvals-premium" style="padding: 16px 20px;">
        <div class="unified-approvals-container animate__animated animate__fadeInUp" style="background: white; border-radius: 20px; box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05); border: 1px solid rgba(226, 232, 240, 0.8); overflow: hidden;">
            <!-- Compact Redesigned Hero Header -->
            <div class="approvals-header-hero">
                <div class="header-text-group">
                    <h1 class="header-title-main">
                        @php
                            $titleParts = explode(' ', $title);
                            $lastWord = array_pop($titleParts);
                            $firstWords = implode(' ', $titleParts);
                        @endphp
                        {{ $firstWords }} <span class="header-title-accent">{{ $lastWord }}</span>
                    </h1>
                    <p>Efficiency starts here. Review and process leave applications with precision.</p>

                    <div class="tab-toggle-container animate__animated animate__fadeInLeft" style="animation-delay: 0.1s;">
                        <a href="{{ route('user.leave.approvals', ['tab' => 'pending']) }}" class="tab-btn {{ ($tab ?? 'pending') !== 'processed' ? 'active' : '' }}">
                            <i class="fas fa-clock"></i> Pending ({{ ($tab ?? 'pending') !== 'processed' ? count($applications) : '...' }})
                        </a>
                        <a href="{{ route('user.leave.approvals', ['tab' => 'processed']) }}" class="tab-btn {{ ($tab ?? 'pending') === 'processed' ? 'active' : '' }}">
                            <i class="fas fa-check-circle"></i> Processed
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div style="padding: 16px 40px 24px 40px; background: rgba(248, 250, 252, 0.4);">
            <!-- Advanced Filter Bar -->
            <form action="{{ route('user.leave.approvals') }}" method="GET" class="filter-section-premium animate__animated animate__fadeInDown">
                <input type="hidden" name="tab" value="{{ $tab ?? 'pending' }}">
                
                <div class="filter-group">
                    <label class="filter-label">Search Applicant</label>
                    <div class="filter-input-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" class="filter-input" placeholder="Name or position..." value="{{ $search ?? '' }}">
                    </div>
                </div>

                <div class="filter-group narrow">
                    <label class="filter-label">From Date (Filing)</label>
                    <div class="filter-input-wrapper">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="date" name="start_date" class="filter-input" value="{{ $startDate ?? '' }}">
                    </div>
                </div>

                <div class="filter-group narrow">
                    <label class="filter-label">To Date (Filing)</label>
                    <div class="filter-input-wrapper">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="date" name="end_date" class="filter-input" value="{{ $endDate ?? '' }}">
                    </div>
                </div>

                @if(($tab ?? 'pending') === 'processed')
                <div class="filter-group">
                    <label class="filter-label">Application Progress</label>
                    <div class="filter-input-wrapper">
                        <i class="fas fa-tasks"></i>
                        <select name="status" class="filter-input" style="padding-left: 36px; -webkit-appearance: none; -moz-appearance: none; appearance: none; cursor: pointer;">
                            <option value="">All Progress</option>
                            <option value="Approved" {{ ($status ?? '') === 'Approved' ? 'selected' : '' }}>Already Approved</option>
                            <option value="Pending Recommending" {{ ($status ?? '') === 'Pending Recommending' ? 'selected' : '' }}>Pending Recommendation</option>
                            <option value="Pending Approval" {{ ($status ?? '') === 'Pending Approval' ? 'selected' : '' }}>For Approval</option>
                            <option value="Rejected" {{ ($status ?? '') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                </div>
                @endif

                <div class="filter-actions">
                    <button type="submit" class="btn-filter btn-apply" title="Apply Filters">
                        <i class="fas fa-filter"></i> Apply
                    </button>
                    <a href="{{ route('user.leave.approvals', ['tab' => $tab ?? 'pending']) }}" class="btn-filter btn-reset btn-icon-only" title="Reset Filters">
                        <i class="fas fa-undo"></i>
                    </a>
                    
                </div>
            </form>



        <div class="user-list">
            @if(count($applications) > 0)
                <div class="approvals-scroll-area">
                    @foreach($applications as $app)
                        <div class="user-card">
                            @if(!$app->is_viewed && ($tab ?? 'pending') !== 'processed')
                                <div class="badge-new-dot animate__animated animate__pulse animate__infinite">NEW</div>
                            @endif
                            <div class="user-info" style="display: flex; align-items: center; gap: 15px;">
                                <div class="user-avatar" style="width: 45px; height: 45px; border-radius: 12px; background: #e0f2fe; color: #1b4a9a; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.1rem; flex-shrink: 0;">
                                    @if($app->user->profile_picture)
                                        <img src="{{ storage_url($app->user->profile_picture) }}" alt="{{ $app->user->full_name }}" style="width: 100%; height: 100%; border-radius: 12px; object-fit: cover;">
                                    @else
                                        {{ strtoupper(substr($app->user->full_name, 0, 2)) }}
                                    @endif
                                </div>
                                <div class="user-details">
                                    <div class="user-name" style="line-height: 1.2; margin-bottom: 2px;">{{ $app->user->full_name }}</div>
                                    <div class="user-email" style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase;">{{ str_replace('_', ' ', $app->user->role) }}</div>
                                </div>
                            </div>

                            <div style="flex: 1; min-width: 100px;">
                                <span class="user-meta-label">Tracking No.</span>
                                <span class="tracking-number-highlight-list" style="font-family: 'Monaco', 'Consolas', monospace; font-weight: 800; color: #1b4a9a; font-size: 0.8rem; display: block; margin-top: 2px;">
                                    {{ $app->tracking_number ?? '---' }}
                                </span>
                            </div>

                            <div style="flex: 1; min-width: 140px;">
                                <span class="user-meta-label">Type of Leave</span>
                                <span class="leave-type-name" style="font-size: 0.85rem;"><i class="fas fa-file-alt mr-1 text-primary/60"></i> {{ $app->leaveType->type_name }}</span>
                                <div style="font-size: 0.65rem; color: #94a3b8; margin-top: 2px; font-weight: 700; display: flex; align-items: center; gap: 4px;">
                                    <i class="far fa-clock"></i> Filed: {{ $app->date_filing->format('M d, Y') }}
                                </div>
                            </div>

                            <div style="flex: 1; min-width: 100px;">
                                <span class="user-meta-label">Total Duration</span>
                                <div class="mt-1">
                                    <span class="badge-days" style="box-shadow: 0 4px 6px -1px rgba(14, 165, 233, 0.1);">
                                        {{ (float) $app->days_applied }} Day(s)
                                    </span>
                                </div>
                            </div>

                            <div style="text-align: center;"> <!-- Centered for better separation -->
                                <span class="user-meta-label" style="display: block; margin-bottom: 4px; text-align: center;">Status</span>
                                 <span class="status-badge-pending" style="white-space: nowrap;">
                                    {{ $app->status }}
                                </span>
                                @php
                                    $isPast = $app->end_date && $app->end_date->isPast();
                                @endphp
                                @if($isPast && ($tab ?? 'pending') !== 'processed')
                                    <div class="mt-1">
                                        <span class="status-badge-past" title="This leave period has already passed.">
                                            Past Date
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="approval-actions" style="display: flex; justify-content: flex-end;">
                                <a href="{{ route('user.leave.approvals.show', ['id' => $app->id, 'tab' => $tab ?? 'pending']) }}" class="btn-review" style="white-space: nowrap;">
                                    <i class="fas {{ ($tab ?? 'pending') === 'processed' ? 'fa-eye' : 'fa-pen-nib' }}"></i>
                                    {{ ($tab ?? 'pending') === 'processed' ? 'View Details' : 'Review Application' }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                </div>
            @else
            </div>
            <div class="empty-state animate__animated animate__fadeInUp" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(10px); border: 2px dashed rgba(15, 76, 117, 0.2); border-radius: 24px; padding: 100px 40px; text-align: center; margin-top: 20px;">
                <div class="empty-state-icon" style="background: white; border-radius: 50%; width: 80px; height: 80px; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 10px 20px rgba(0,0,0,0.05); margin-bottom: 20px;">
                    <i class="fas fa-check-double text-blue-500 text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-800">No {{ ($tab ?? 'pending') === 'processed' ? 'Processed' : 'Pending' }} Approvals</h3>
                <p class="text-slate-500 font-medium mt-2">
                    @if(($tab ?? 'pending') === 'processed')
                        You haven't processed any applications yet.
                    @else
                        Everything is handled! We'll notify you when new applications arrive.
                    @endif
                </p>
                @if(($tab ?? 'pending') === 'processed')
                    <a href="{{ route('user.leave.approvals', ['tab' => 'pending']) }}" class="btn-review mt-6" style="display: inline-flex; width: auto;">
                        <i class="fas fa-arrow-left"></i> Back to Pending
                    </a>
                @endif
            </div>
            @endif
        </div> <!-- End inner padding div -->
        </div> <!-- End unified-approvals-container -->
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="modal-backdrop"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center;">
        <div class="modal-content"
            style="background: white; border-radius: 16px; width: 400px; max-width: 90%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
            <div class="modal-header">
                <h3 style="font-size: 1.1rem; font-weight: 700;">Disapprove Application</h3>
            </div>
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <p style="margin-bottom: 12px; color: #64748b; font-size: 0.9rem;">Please provide a reason for
                        disapproval.</p>
                    <textarea name="remarks" class="form-control" rows="4" required placeholder="Reason for rejection..."
                        style="height: auto; padding-top: 12px;"></textarea>
                </div>
                <div class="modal-footer" style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeRejectModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-danger">Disapprove</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function openRejectModal(id) {
                const form = document.getElementById('rejectForm');
                form.action = "/user/leave/approvals/" + id + "/reject";
                const modal = document.getElementById('rejectModal');
                modal.style.display = 'flex';
            }

            function closeRejectModal() {
                document.getElementById('rejectModal').style.display = 'none';
            }

            // Close modal when clicking outside
            window.onclick = function (event) {
                const modal = document.getElementById('rejectModal');
                if (event.target == modal) {
                    closeRejectModal();
                }
            }

            // Search Functionality
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('approvalSearchInput');
                if (searchInput) {
                    searchInput.addEventListener('input', function(e) {
                        const searchTerm = e.target.value.toLowerCase();
                        const cards = document.querySelectorAll('.approvals-scroll-area .user-card');
                        
                        cards.forEach(card => {
                            const textContent = card.innerText.toLowerCase();
                            if (textContent.includes(searchTerm)) {
                                card.style.display = 'flex';
                            } else {
                                card.style.display = 'none';
                            }
                        });
                    });
                }
            });

        </script>
    @endpush
@endsection
