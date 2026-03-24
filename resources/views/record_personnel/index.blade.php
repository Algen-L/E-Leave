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
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
        }

        .header-title-main {
            font-size: 2.8rem;
            font-weight: 900;
            color: #1e293b;
            letter-spacing: -0.03em;
            line-height: 1;
            position: relative;
            z-index: 1;
        }

        .header-title-accent {
            color: #0ea5e9;
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
        }

        .header-title-accent::after {
            content: '';
            position: absolute;
            bottom: 8px;
            left: 0;
            width: 100%;
            height: 12px;
            background: rgba(14, 165, 233, 0.1);
            z-index: -1;
            border-radius: 4px;
        }

        /* Glassmorphism User Card (Compact Flex Layout) */
        .user-card {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
            justify-content: space-between;
            align-items: center;
            border: 1px solid rgba(226, 232, 240, 0.7);
            margin-bottom: 12px;
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 16px !important;
            padding: 16px 24px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
        }

        .user-card:hover {
            border-color: var(--primary) !important;
            background: white !important;
            transform: translateY(-3px);
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.1) !important;
        }

        /* Sequential Animation */
        .records-scroll-area .user-card {
            opacity: 0;
            animation: backInDown 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @foreach(range(1, 20) as $i)
            .records-scroll-area .user-card:nth-child({{ $i }}) {
                animation-delay: {{ $i * 0.1 }}s;
            }
        @endforeach

        @keyframes backInDown {
            0% {
                transform: translateY(-50px) scale(0.95);
                opacity: 0;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .btn-view {
            background: white;
            color: #475569;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            border: 1px solid #cbd5e1;
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.1);
            background: #f8fafc;
            color: #0f4c75;
        }

        /* Scrollable Container */
        .records-scroll-area {
            max-height: calc(100vh - 350px);
            overflow-y: auto;
            padding: 10px 10px 40px 10px;
            margin: 0 -10px;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .records-scroll-area::-webkit-scrollbar {
            width: 5px;
        }

        .records-scroll-area::-webkit-scrollbar-track {
            background: transparent;
        }

        .records-scroll-area::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .user-meta-label {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            display: block;
            margin-bottom: 4px;
        }

        .user-card .user-name {
            font-size: 1.05rem !important;
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
            color: #0369a1;
            font-weight: 800;
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 0.85rem;
            display: inline-block;
        }

        .status-badge {
            font-weight: 800;
            padding: 6px 14px;
            border-radius: 99px;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 120px;
        }

        .status-approved {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .status-pending {
            background: #ffedd5;
            color: #9a3412;
            border: 1px solid #fed7aa;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
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
                    <p class="text-slate-500 mt-3 font-semibold text-lg max-w-md leading-relaxed">
                        Search and filter through all leave applications filed in the system.
                    </p>
                </div>
                
                <div class="officer-badge-premium" style="background: white; padding: 12px 24px; border-radius: 20px; display: flex; align-items: center; gap: 15px; box-shadow: 0 15px 35px -10px rgba(15, 76, 117, 0.15); border: 1px solid rgba(15, 76, 117, 0.1);">
                    <div class="officer-icon-wrapper" style="width: 50px; height: 50px; background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.4rem;">
                        <i class="fas fa-archive"></i>
                    </div>
                    <div>
                        <div class="text-[0.68rem] uppercase font-black text-slate-400 tracking-widest leading-none mb-1">Record Personnel</div>
                        <div class="officer-name-highlight" style="color: #0c4a6e; font-size: 1.1rem; font-weight: 800; text-transform: uppercase;">{{ Auth::user()->full_name }}</div>
                    </div>
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
                    <button type="submit" class="btn btn-primary" style="border-radius: 16px; padding: 0 24px; background: #0ea5e9; border: none; font-weight: 700;">Filter</button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('records.index') }}" class="btn btn-light" style="border-radius: 16px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-times"></i></a>
                    @endif
                </form>

            <div class="user-list">
                @if(count($applications) > 0)
                    <div class="records-scroll-area">
                        @foreach($applications as $app)
                            <div class="user-card">
                                <div class="user-info" style="flex: 1; min-width: 250px; display: flex; align-items: center; gap: 15px;">
                                    <div class="user-avatar" style="width: 45px; height: 45px; border-radius: 12px; background: #e0f2fe; color: #0369a1; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.1rem; flex-shrink: 0;">
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

                                <div style="flex: 1; min-width: 200px;">
                                    <span class="user-meta-label">Type of Leave</span>
                                    <span class="leave-type-name"><i class="fas fa-file-alt mr-1 text-sky-500/80"></i> {{ $app->leaveType->type_name }}</span>
                                    <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 4px; font-weight: 700; display: flex; align-items: center; gap: 5px;">
                                        <i class="far fa-clock"></i> Filed: {{ $app->date_filing->format('M d, Y') }}
                                    </div>
                                </div>

                                <div style="flex: 1; min-width: 200px;">
                                    <span class="user-meta-label">Duration</span>
                                    <span class="user-meta-value" style="display: flex; align-items: center; gap: 6px; font-weight: 600; color: #334155; margin-bottom: 4px;">
                                        <span class="badge-days" style="padding: 4px 10px; font-size: 0.75rem;">{{ (float) $app->days_applied }}</span> Days
                                    </span>
                                </div>

                                <div style="text-align: right; min-width: 150px;">
                                    <span class="user-meta-label" style="text-align: center;">Status</span>
                                    @php
                                        $statusClass = 'status-pending';
                                        if($app->status == 'Approved') $statusClass = 'status-approved';
                                        if($app->status == 'Disapproved') $statusClass = 'status-rejected';
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">
                                        {{ $app->status }}
                                    </span>
                                </div>

                                <div class="approval-actions" style="min-width: 120px; justify-content: flex-end;">
                                    <a href="{{ route('records.leave.show', $app->id) }}" class="btn-view">
                                        <i class="fas fa-eye text-slate-400"></i> Details
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div style="margin-top: 20px;">
                        {{ $applications->links() }}
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
