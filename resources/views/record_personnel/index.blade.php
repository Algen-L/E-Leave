@extends('layouts.sdo')

@section('title', 'Application Records')
@section('page-title', 'Application Records')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/manage-users.css') }}">
    <style>
        .records-premium {
            animation: fadeIn 0.4s ease-out;
            font-family: 'Plus Jakarta Sans', sans-serif;
            position: relative;
        }

        /* Header Hero Section Update */
        .records-header-hero {
            position: relative;
            padding: 35px 40px 24px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .header-title-main {
            font-size: 2.8rem;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: -0.03em;
            line-height: 1;
            position: relative;
            z-index: 1;
        }

        .header-title-accent {
            color: #ffffff;
            opacity: 0.9;
            position: relative;
        }

        .header-title-accent::after {
            content: '';
            position: absolute;
            bottom: 8px;
            left: 0;
            width: 100%;
            height: 12px;
            background: rgba(255, 255, 255, 0.1);
            z-index: -1;
            border-radius: 4px;
        }

        /* Adjust description text */
        .header-text-group p {
            color: rgba(255, 255, 255, 0.7) !important;
        }

        /* Responsive Grid (Adapts to Sidebar and Viewport) */
        .user-card {
            display: grid;
            grid-template-columns: minmax(200px, 1.2fr) minmax(100px, 0.8fr) minmax(160px, 1fr) minmax(100px, 0.7fr) minmax(120px, 0.8fr) minmax(150px, 1fr);
            align-items: center;
            gap: 12px;
            border: 1px solid rgba(226, 232, 240, 0.6);
            margin-bottom: 12px;
            background: #ffffff !important;
            border-radius: 12px !important;
            padding: 12px 20px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02) !important;
            min-height: 85px;
            width: 100%;
            overflow: hidden;
        }

        /* Sidebar Responsive Adjustments */
        .app-layout.sidebar-collapsed .user-card {
            grid-template-columns: minmax(240px, 1.3fr) minmax(110px, 0.9fr) minmax(180px, 1.1fr) minmax(120px, 0.8fr) minmax(140px, 0.9fr) minmax(180px, 1.1fr);
            gap: 15px;
        }

        .user-card:hover {
            border-color: #1b4a9a !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px -5px rgba(0, 0, 0, 0.1) !important;
        }

        /* Column Headers */
        .column-label {
            font-size: 0.62rem;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 5px;
            display: block;
            white-space: nowrap;
        }

        .user-profile-group {
            display: flex;
            align-items: center;
            gap: 15px;
            min-width: 0;
        }

        .avatar-frame {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            overflow: hidden;
            flex-shrink: 0;
            background: #f1f5f9;
        }

        .avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .name-role-group {
            line-height: 1.2;
            overflow: hidden;
        }

        .user-full-name {
            font-size: 1rem;
            font-weight: 800;
            color: #1e293b;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role-label {
            font-size: 0.68rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
        }

        .tracking-group {
            vertical-align: middle;
        }

        .tracking-val {
            font-size: 0.85rem;
            font-weight: 700;
            color: #475569;
        }

        .leave-type-group {
            line-height: 1.4;
            overflow: hidden;
        }

        .leave-type-val {
            font-size: 0.9rem;
            font-weight: 800;
            color: #0c4a6e;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .filed-date-val {
            font-size: 0.72rem;
            color: #94a3b8;
            font-weight: 600;
        }

        .duration-pill {
            background: #e0f2fe;
            color: #1b4a9a;
            padding: 6px 14px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 0.8rem;
            display: inline-block;
            text-align: center;
            min-width: 80px;
        }

        .status-pill-dot {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            white-space: nowrap;
            justify-content: center;
            min-width: 130px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .status-pending-hr {
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #ffedd5;
        }
        .status-pending-hr .status-dot { background: #f97316; }

        .status-approved-hr {
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #dcfce7;
        }
        .status-approved-hr .status-dot { background: #22c55e; }

        .status-disapproved-hr {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fee2e2;
        }
        .status-disapproved-hr .status-dot { background: #ef4444; }

        .btn-review-app {
            background: #075985;
            color: white !important;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 0.82rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            text-decoration: none !important;
            width: 100%;
            justify-content: center;
            white-space: nowrap;
        }

        .btn-review-app:hover {
            background: #0c4a6e;
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(7, 89, 133, 0.2);
        }

        /* Scroll Area Configuration (Shows ~6 items, scrollable for 20) */
        .records-scroll-area {
            max-height: 620px;
            overflow-y: auto;
            padding-right: 8px;
            margin-right: -8px;
        }

        /* Modern Scrollbar Styling */
        .records-scroll-area::-webkit-scrollbar {
            width: 6px;
        }

        .records-scroll-area::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .records-scroll-area::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
            transition: background 0.2s;
        }

        .records-scroll-area::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Sequential Animation */
        .records-scroll-area .user-card {
            opacity: 0;
            animation: fadeIn 0.4s ease-out forwards;
        }

        @foreach(range(1, 20) as $i)
            .records-scroll-area .user-card:nth-child({{ $i }}) {
                animation-delay: {{ $i * 0.04 }}s;
            }
        @endforeach
    </style>
@endpush

@section('content')
    <div class="container-fluid records-premium" style="padding: 24px;">
        
        <div class="unified-approvals-container animate__animated animate__fadeInUp" style="background: white; border-radius: 24px; box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05); border: 1px solid rgba(226, 232, 240, 0.8); overflow: hidden;">
            <div class="records-header-hero">
                <div class="header-text-group">
                    <h1 class="header-title-main">
                        Leave <span class="header-title-accent">Records</span>
                    </h1>
                    <p class="mt-2 font-semibold text-mg max-w-md leading-relaxed">
                        Search and filter through all leave applications filed in the system.
                    </p>
                </div>
            </div>

            <div style="padding: 24px 40px 40px 40px; background: rgba(248, 250, 252, 0.4);">
                <!-- Search Bar Section -->
                <form action="{{ route('records.index') }}" method="GET" class="search-container" style="margin-bottom: 24px; position: relative; max-width: 600px; display: flex; gap: 10px;">
                    <div style="flex: 1; position: relative;">
                        <i class="fas fa-search" style="position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.1rem; z-index: 5;"></i>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search applicant name..." style="width: 100%; padding-left: 48px !important; border-radius: 16px; height: 50px; font-size: 0.95rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); border: 1px solid rgba(226, 232, 240, 0.8);">
                    </div>
                    <select name="status" class="form-control" style="width: 200px; border-radius: 16px; height: 50px; border: 1px solid rgba(226, 232, 240, 0.8);">
                        <option value="">All Statuses</option>
                        <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Disapproved" {{ request('status') == 'Disapproved' ? 'selected' : '' }}>Disapproved</option>
                        <option value="Pending HR" {{ request('status') == 'Pending HR' ? 'selected' : '' }}>Pending HR</option>
                        <option value="Pending Recommending" {{ request('status') == 'Pending Recommending' ? 'selected' : '' }}>Pending Recommending</option>
                        <option value="Pending Approval" {{ request('status') == 'Pending Approval' ? 'selected' : '' }}>Pending Approval</option>
                    </select>
                    <button type="submit" class="btn btn-primary" style="border-radius: 16px; padding: 0 24px; background: #1b4a9a; border: none; font-weight: 700;">Filter</button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('records.index') }}" class="btn btn-light" style="border-radius: 16px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-times"></i></a>
                    @endif
                </form>

            <div class="user-list">
                @if(count($applications) > 0)
                    <div class="records-scroll-area">
                        @foreach($applications as $app)
                            <div class="user-card anim-{{ $loop->iteration }}">
                                <!-- Profile Column -->
                                <div class="user-profile-group">
                                    <div class="avatar-frame">
                                        @if($app->user->profile_picture)
                                            <img src="{{ storage_url($app->user->profile_picture) }}" class="avatar-img" alt="Profile">
                                        @else
                                            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#e0f2fe; color:#1b4a9a; font-weight:800;">
                                                {{ strtoupper(substr($app->user->first_name, 0, 1)) }}{{ strtoupper(substr($app->user->last_name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="name-role-group">
                                        <span class="user-full-name">{{ $app->user->full_name }}</span>
                                        <span class="user-role-label">USER</span>
                                    </div>
                                </div>

                                <!-- Tracking Column -->
                                <div class="tracking-group">
                                    <span class="column-label">Tracking No.</span>
                                    <span class="tracking-val">---</span>
                                </div>

                                <!-- Leave Type Column -->
                                <div class="leave-type-group">
                                    <span class="column-label">Type of Leave</span>
                                    <span class="leave-type-val">
                                        <i class="fas fa-file-invoice text-sky-600"></i> {{ $app->leaveType->type_name }}
                                    </span>
                                    <span class="filed-date-val"><i class="far fa-clock"></i> Filed: {{ $app->date_filing->format('M d, Y') }}</span>
                                </div>

                                <!-- Duration Column -->
                                <div class="duration-group">
                                    <span class="column-label">Total Duration</span>
                                    <span class="duration-pill">
                                        {{ (float) $app->days_applied }} Day(s)
                                    </span>
                                </div>

                                <!-- Status Column -->
                                <div class="status-group">
                                    <span class="column-label" style="text-align: center;">Status</span>
                                    @php
                                        $statusClass = 'status-pending-hr';
                                        if($app->status == 'Approved') $statusClass = 'status-approved-hr';
                                        elseif($app->status == 'Disapproved') $statusClass = 'status-disapproved-hr';
                                        
                                        // Specific text mapping for "dot" style
                                        $displayText = strtoupper($app->status);
                                        if(str_contains(strtolower($app->status), 'pending')) $displayText = 'PENDING HR';
                                    @endphp
                                    <div class="status-pill-dot {{ $statusClass }}">
                                        <div class="status-dot"></div>
                                        {{ $displayText }}
                                    </div>
                                </div>

                                <!-- Action Column -->
                                <div class="action-column">
                                    <a href="{{ route('records.leave.show', $app->id) }}" class="btn-review-app">
                                        <i class="fas fa-pen-nib"></i> Review Application
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                </div>
                <div class="empty-state animate__animated animate__fadeInUp" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(10px); border: 2px dashed rgba(226, 232, 240, 1); border-radius: 24px; padding: 100px 40px; text-align: center; margin-top: 20px;">
                    <div class="empty-state-icon" style="background: white; border-radius: 50%; width: 80px; height: 80px; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 10px 20px rgba(0,0,0,0.05); margin-bottom: 20px;">
                        <i class="fas fa-folder-open text-slate-300 text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">No Records Found</h3>
                    <p class="text-slate-500 font-medium mt-2">Try adjusting your search or filter criteria.</p>
                </div>
                @endif
            </div> <!-- End inner padding div -->
        </div> <!-- End unified-approvals-container -->
    </div>
@endsection
