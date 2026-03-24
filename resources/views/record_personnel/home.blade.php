@extends('layouts.sdo')

@section('title', 'Record Personnel Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        .dashboard-hero-premium {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 24px;
            padding: 40px;
            color: white;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.3);
        }

        .hero-accent-circle {
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.2) 0%, transparent 70%);
            border-radius: 50%;
        }

        .stats-grid-premium {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card-premium {
            background: white;
            border-radius: 20px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card-premium:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px -10px rgba(0,0,0,0.1);
            border-color: #0ea5e9;
        }

        .stat-icon-box {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .icon-total { background: #f0f9ff; color: #0ea5e9; }
        .icon-approved { background: #f0fdf4; color: #22c55e; }
        .icon-pending { background: #fffaf5; color: #f97316; }
        .icon-rejected { background: #fef2f2; color: #ef4444; }

        .stat-value-premium {
            font-size: 1.75rem;
            font-weight: 900;
            color: #1e293b;
            line-height: 1.1;
        }

        .stat-label-premium {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 4px;
        }

        .action-card-premium {
            background: white;
            border-radius: 24px;
            padding: 32px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        .btn-enter-records {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: white;
            padding: 16px 32px;
            border-radius: 16px;
            font-weight: 800;
            font-size: 1.1rem;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s;
            text-decoration: none;
            box-shadow: 0 10px 20px -5px rgba(14, 165, 233, 0.4);
        }

        .btn-enter-records:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 15px 30px -10px rgba(14, 165, 233, 0.6);
            color: white;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid animate__animated animate__fadeIn" style="padding: 24px;">
    
    <!-- Hero Section -->
    <div class="dashboard-hero-premium">
        <div class="hero-accent-circle"></div>
        <div style="position: relative; z-index: 1;">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                <div style="background: rgba(14, 165, 233, 0.2); width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-archive text-sky-400"></i>
                </div>
                <span style="text-transform: uppercase; font-weight: 800; font-size: 0.8rem; letter-spacing: 0.2em; color: #38bdf8;">Record Personnel Portal</span>
            </div>
            <h1 style="font-size: 2.5rem; font-weight: 900; margin-bottom: 15px;">Welcome back, {{ Auth::user()->first_name }}!</h1>
            <p style="font-size: 1.1rem; color: #94a3b8; max-width: 600px; line-height: 1.6;">
                Manage and monitor all leave applications within the system. Access full historical records and verify application details with ease.
            </p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid-premium">
        <div class="stat-card-premium animate__animated animate__backInDown" style="animation-delay: 0.1s;">
            <div class="stat-icon-box icon-total"><i class="fas fa-layer-group"></i></div>
            <div>
                <div class="stat-value-premium">{{ number_format($stats['total']) }}</div>
                <div class="stat-label-premium">Total Applications</div>
            </div>
        </div>
        <div class="stat-card-premium animate__animated animate__backInDown" style="animation-delay: 0.2s;">
            <div class="stat-icon-box icon-approved"><i class="fas fa-check-circle"></i></div>
            <div>
                <div class="stat-value-premium">{{ number_format($stats['approved']) }}</div>
                <div class="stat-label-premium">Approved</div>
            </div>
        </div>
        <div class="stat-card-premium animate__animated animate__backInDown" style="animation-delay: 0.3s;">
            <div class="stat-icon-box icon-pending"><i class="fas fa-clock"></i></div>
            <div>
                <div class="stat-value-premium">{{ number_format($stats['pending']) }}</div>
                <div class="stat-label-premium">Pending Actions</div>
            </div>
        </div>
        <div class="stat-card-premium animate__animated animate__backInDown" style="animation-delay: 0.4s;">
            <div class="stat-icon-box icon-rejected"><i class="fas fa-times-circle"></i></div>
            <div>
                <div class="stat-value-premium">{{ number_format($stats['disapproved']) }}</div>
                <div class="stat-label-premium">Disapproved</div>
            </div>
        </div>
    </div>

    <!-- Action Section -->
    <div class="action-card-premium animate__animated animate__fadeInUp" style="animation-delay: 0.5s;">
        <div style="display: flex; align-items: center; gap: 25px;">
            <div style="width: 80px; height: 80px; background: #f8fafc; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #cbd5e1; border: 1px solid #e2e8f0;">
                <i class="fas fa-search"></i>
            </div>
            <div>
                <h2 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-bottom: 5px;">Application Repository</h2>
                <p style="color: #64748b; font-weight: 500;">Browse, search, and filter through all leave records across the entire system.</p>
            </div>
        </div>
        <a href="{{ route('records.index') }}" class="btn-enter-records">
            Browse All Records <i class="fas fa-arrow-right"></i>
        </a>
    </div>

</div>
@endsection
