@extends('layouts.sdo')

@section('title', 'Review Officer Log')
@section('page-title', 'Review Officer Log')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/activity-logs.css') }}?v={{ time() }}">
    <style>
        .premium-filter-container {
            padding: 0;
            background: transparent !important;
            border-radius: 16px 16px 0 0;
            overflow: hidden;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 10px;
            @if(count(request()->query()) === 0)
                animation: fadeInDown 0.6s ease-out forwards;
            @else
                animation: none !important;
                transform: none !important;
            @endif
        }

        .header-top-row {
            padding: 18px 24px;
            background: var(--primary-gradient) !important;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-title-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: white;
            padding: 6px 16px;
            border-radius: 99px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Unique identifier for this page */
        .hr-log-identifier {
            background: #e8f0ff;
            color: #1e3a8a;
            padding: 2px 10px;
            border-radius: 99px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            margin-left: 8px;
            border: 1px solid #dce7ff;
        }

        .header-filters-row {
            width: 100%;
            background: white;
            padding: 24px;
            border-top: 1px solid rgba(15, 76, 117, 0.05);
        }

        /* Activity Item Enhancements */
        .activity-feed .activity-item {
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }

        .activity-feed .activity-item:hover {
            border-left-color: var(--primary);
            background: #f8fafc;
        }

        .activity-details {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 4px;
            line-height: 1.4;
        }

        .officer-badge {
            font-size: 0.7rem;
            background: #f1f5f9;
            color: #475569;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 4px;
        }

        .empty-state {
            padding: 60px 20px;
            text-align: center;
            background: white;
            border-radius: 16px;
            border: 1px dashed #cbd5e1;
        }

        .empty-state-icon {
            font-size: 3rem;
            color: #94a3b8;
            margin-bottom: 16px;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endpush

@section('content')
    <!-- Premium Filter Container -->
    <div class="premium-filter-container">
        <div class="header-top-row">
            <div class="header-title-box">
                <div class="header-icon-container" style="color: var(--primary);">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="header-text-main">
                    Review Officer Log
                    <span class="hr-log-identifier">HR Audit</span>
                </div>
            </div>
            
            <div class="premium-badge-glass">
                <span class="badge-dot"></span>
                <span class="badge-text" style="color: #475569;">Verified Operations</span>
            </div>
        </div>

        <div class="header-filters-row">
            <form method="GET" action="{{ route('hr.review-officer-log') }}">
                <div class="filter-row">
                    <div class="filter-group" style="flex: 2;">
                        <label class="filter-label">Quick Search</label>
                        <input type="text" class="custom-select" name="search"
                            placeholder="Identify actions, details or target users..." value="{{ $filters['search'] }}"
                            style="background-image: none; padding-left: 14px;">
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">HR Officer</label>
                        <select class="custom-select" name="officer_id">
                            <option value="">All Officers</option>
                            @foreach($officers as $officer)
                                <option value="{{ $officer->id }}" {{ $filters['officer_id'] == $officer->id ? 'selected' : '' }}>
                                    {{ $officer->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Operation Type</label>
                        <select class="custom-select" name="action">
                            <option value="">All Activities</option>
                            <option value="Register" {{ $filters['action'] == 'Register' ? 'selected' : '' }}>Account Registration</option>
                            <option value="Credit" {{ $filters['action'] == 'Credit' ? 'selected' : '' }}>Credit Allocation</option>
                            <option value="Profile" {{ $filters['action'] == 'Profile' ? 'selected' : '' }}>Profile Management</option>
                            <option value="Verify" {{ $filters['action'] == 'Verify' ? 'selected' : '' }}>Verification</option>
                            <option value="Approve" {{ $filters['action'] == 'Approve' ? 'selected' : '' }}>Final Approval</option>
                        </select>
                    </div>

                    <div class="filter-buttons">
                        <button type="submit" class="btn-filter-apply">
                            <i class="fas fa-bolt"></i>
                            Refresh
                        </button>
                    </div>
                </div>

                <input type="hidden" name="date_range" id="dateRangeInput" value="{{ $filters['date_range'] }}">
                <div class="quick-dates">
                    <span class="date-pill {{ $filters['date_range'] == 'today' ? 'active' : '' }}" data-range="today">Today</span>
                    <span class="date-pill {{ $filters['date_range'] == '7days' ? 'active' : '' }}" data-range="7days">Last 7 Days</span>
                    <span class="date-pill {{ $filters['date_range'] == '30days' ? 'active' : '' }}" data-range="30days">Last 30 Days</span>
                    <span class="date-pill {{ $filters['date_range'] == 'all' ? 'active' : '' }}" data-range="all">Complete History</span>
                </div>
            </form>
        </div>

        <script>
            document.querySelectorAll('.date-pill').forEach(pill => {
                pill.addEventListener('click', function () {
                    const form = this.closest('form');
                    const rangeInput = form.querySelector('#dateRangeInput');
                    rangeInput.value = this.dataset.range;
                    form.submit();
                });
            });
        </script>
    </div>

    <!-- Activity Content -->
    <div class="activity-feed">
        @forelse($logs as $log)
            <div class="activity-item">
                <div class="activity-icon-simple">
                    @if(Str::contains(strtolower($log->action), 'register'))
                        <i class="fas fa-user-plus" style="color: #1b4a9a;"></i>
                    @elseif(Str::contains(strtolower($log->action), 'credit'))
                        <i class="fas fa-coins" style="color: #f59e0b;"></i>
                    @elseif(Str::contains(strtolower($log->action), 'verify'))
                        <i class="fas fa-check-circle" style="color: #10b981;"></i>
                    @elseif(Str::contains(strtolower($log->action), 'approve'))
                        <i class="fas fa-stamp" style="color: #8b5cf6;"></i>
                    @else
                        <i class="fas fa-fingerprint" style="color: #64748b;"></i>
                    @endif
                </div>

                <div class="activity-content">
                    <div class="officer-badge">
                        <i class="fas fa-user-tie mr-1"></i> {{ $log->user->full_name ?? 'System' }}
                    </div>
                    <div class="activity-user-name" style="font-size: 0.95rem;">
                        {{ $log->action }}
                    </div>
                    <div class="activity-details">
                        {{ $log->details }}
                    </div>
                </div>

                <div class="activity-time">
                    <div style="font-weight: 700; color: #1e293b;">{{ $log->created_at->format('M d, Y') }}</div>
                    <div style="font-size: 0.75rem; color: #64748b;">{{ $log->created_at->format('g:i A') }}</div>
                    <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 4px;">IP: {{ $log->ip_address }}</div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>No recorded activities found</h3>
                <p>Try adjusting your search criteria or date range.</p>
            </div>
        @endforelse
    </div>

    <div class="pagination-container">
        <div class="pagination-info">
            Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} entries
        </div>
        {{ $logs->links() }}
    </div>

@endsection
