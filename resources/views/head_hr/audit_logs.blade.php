@extends('layouts.sdo')

@section('title', 'Audit Logs')
@section('page-title', 'Leave Credit Audit Logs')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/activity-logs.css') }}">
    <style>
        /* Card Container */
        .logs-container {
            max-width: 1024px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .section-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        /* Section Headers */
        .section-header {
            padding: 16px 24px;
            border-bottom: 1px solid #eef2f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-orange {
            background: #fff7ed;
            border-bottom-color: #ffedd5;
        }

        .header-gray {
            background: #f8fafc;
        }

        .section-title {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .text-orange {
            color: #c2410c;
        }

        .text-gray {
            color: #475569;
        }

        .badge-count {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 12px;
        }

        .badge-orange {
            background: #ffedd5;
            color: #9a3412;
        }

        /* Request Item */
        .request-item {
            padding: 24px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            transition: background 0.2s;
        }

        .request-item:last-child {
            border-bottom: none;
        }

        .request-item:hover {
            background: #fffcf8;
        }

        .req-info h4 {
            margin: 0;
            font-size: 1rem;
            color: #1e293b;
            font-weight: 700;
        }

        .req-sub {
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 4px;
        }

        .req-reason {
            margin-top: 8px;
            font-size: 0.85rem;
            color: #334155;
            background: #f8fafc;
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            display: inline-block;
            font-style: italic;
        }

        .req-time {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 8px;
        }

        /* Buttons */
        .btn-group {
            display: flex;
            gap: 8px;
        }

        .btn-approve {
            background: #16a34a;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }

        .btn-approve:hover {
            background: #15803d;
        }

        .btn-reject {
            background: white;
            color: #dc2626;
            border: 1px solid #fee2e2;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }

        .btn-reject:hover {
            background: #fef2f2;
            border-color: #fecaca;
        }

        /* Log Item */
        .log-item {
            padding: 16px 24px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .log-item:last-child {
            border-bottom: none;
        }

        .log-item:hover {
            background: #f8fafc;
        }

        .log-icon-circle {
            width: 36px;
            height: 36px;
            background: #e0f2fe;
            color: #0284c7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.95rem;
        }

        .log-content {
            flex: 1;
        }

        .log-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .log-desc {
            font-size: 0.95rem;
            color: #334155;
            line-height: 1.4;
        }

        .actor-name {
            font-weight: 700;
            color: #0f172a;
        }

        .action-badge {
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            background: #f1f5f9;
            color: #475569;
            padding: 2px 6px;
            border-radius: 4px;
            margin: 0 4px;
            vertical-align: middle;
        }

        .log-time {
            font-size: 0.75rem;
            color: #94a3b8;
            font-family: monospace;
            white-space: nowrap;
        }

        .log-extra {
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 4px;
        }

        .diff-box {
            margin-top: 8px;
            font-family: 'Consolas', monospace;
            font-size: 0.8rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 6px 10px;
            border-radius: 6px;
            display: inline-block;
        }

        .val-old {
            color: #ef4444;
            text-decoration: line-through;
            opacity: 0.7;
        }

        .val-arrow {
            color: #94a3b8;
            margin: 0 6px;
            font-size: 0.7rem;
        }

        .val-new {
            color: #16a34a;
            font-weight: 700;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #94a3b8;
        }

        .empty-icon {
            font-size: 2rem;
            margin-bottom: 12px;
            color: #cbd5e1;
        }

        /* Scrollable List */
        .logs-list {
            max-height: 600px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f8fafc;
        }

        .logs-list::-webkit-scrollbar {
            width: 6px;
        }

        .logs-list::-webkit-scrollbar-track {
            background: #f8fafc;
        }

        .logs-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .logs-list::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endpush

@section('content')
    <div class="logs-container">


        <!-- Premium Filter Container -->
        <div class="premium-filter-container">
            <div class="filter-header">
                <div class="filter-title">
                    <i class="fas fa-filter"></i>
                    Filter Activities
                </div>
            </div>

            <form method="GET" action="{{ route('head-hr.audit-logs') }}">
                <div class="filter-row">
                    <div class="filter-group" style="flex: 2;">
                        <label class="filter-label">Search</label>
                        <input type="text" class="custom-select" name="search"
                            placeholder="Search by user, action, or details..." value="{{ $filters['search'] ?? '' }}"
                            style="background-image: none; padding-left: 14px;">
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Action Type</label>
                        <select class="custom-select" name="action">
                            <option value="">All Actions</option>
                            <option value="allocate" {{ ($filters['action'] ?? '') == 'allocate' ? 'selected' : '' }}>Allocate
                            </option>
                            <option value="update" {{ ($filters['action'] ?? '') == 'update' ? 'selected' : '' }}>Update
                            </option>
                            <option value="deduct" {{ ($filters['action'] ?? '') == 'deduct' ? 'selected' : '' }}>Deduct
                            </option>
                            <option value="add_coc" {{ ($filters['action'] ?? '') == 'add_coc' ? 'selected' : '' }}>Add COC
                            </option>
                        </select>
                    </div>

                    <div class="filter-buttons">
                        <button type="submit" class="btn-filter-apply">
                            <i class="fas fa-search"></i>
                            Apply Filters
                        </button>
                        <a href="{{ route('head-hr.audit-logs') }}" class="btn-filter-reset">
                            <i class="fas fa-redo"></i>
                            Reset
                        </a>
                    </div>
                </div>

                <input type="hidden" name="date_range" id="dateRangeInput" value="{{ $filters['date_range'] ?? '' }}">
                <div class="quick-dates">
                    <span class="date-pill {{ ($filters['date_range'] ?? '') == '' ? 'active' : '' }}" data-range="">All
                        Time</span>
                    <span class="date-pill {{ ($filters['date_range'] ?? '') == 'today' ? 'active' : '' }}"
                        data-range="today">Today</span>
                    <span class="date-pill {{ ($filters['date_range'] ?? '') == '7days' ? 'active' : '' }}"
                        data-range="7days">Last 7 Days</span>
                    <span class="date-pill {{ ($filters['date_range'] ?? '') == '30days' ? 'active' : '' }}"
                        data-range="30days">Last 30 Days</span>
                </div>
            </form>

            <script>
                document.querySelectorAll('.date-pill').forEach(pill => {
                    pill.addEventListener('click', function () {
                        document.querySelectorAll('.date-pill').forEach(p => p.classList.remove('active'));
                        this.classList.add('active');
                        document.getElementById('dateRangeInput').value = this.dataset.range;
                        this.closest('form').submit();
                    });
                });
            </script>
        </div>

        <!-- Logs Section -->
        <div class="section-card">
            <div class="section-header header-gray">
                <div class="section-title text-gray">
                    <i class="fas fa-history"></i> Recent HR Actions
                </div>
                <span class="text-sm text-gray show-all-txt">Showing latest actions</span>
            </div>

            <div class="logs-list">
                @forelse($logs as $log)
                    <div class="log-item">
                        <div class="log-icon-circle">
                            @if($log->action == 'allocate') <i class="fas fa-plus"></i>
                            @elseif($log->action == 'deduct') <i class="fas fa-minus"></i>
                            @elseif($log->action == 'update') <i class="fas fa-pen"></i>
                            @else <i class="fas fa-info"></i> @endif
                        </div>
                        <div class="log-content">
                            <div class="log-header">
                                <div class="log-desc">
                                    <span class="actor-name">{{ $log->actor->full_name ?? 'System' }}</span>
                                    <span class="action-badge">{{ $log->action }}</span>
                                    <span class="actor-name">{{ $log->targetUser->full_name ?? 'Unknown' }}</span>
                                </div>
                                <div class="log-time">{{ $log->created_at->diffForHumans() }}</div>
                            </div>

                            <div class="log-extra">
                                Targeting <strong>{{ $log->leave_type_name }}</strong>
                                @if($log->reason)
                                    • <span class="italic">"{{ $log->reason }}"</span>
                                @endif
                            </div>

                            @if($log->previous_value !== null || $log->new_value !== null)
                                <div class="diff-box">
                                    <span class="val-old">{{ $log->previous_value ?? '0.00' }}</span>
                                    <i class="fas fa-arrow-right val-arrow"></i>
                                    <span class="val-new">{{ $log->new_value ?? '0.00' }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-history empty-icon"></i>
                        <p>No audit logs available yet.</p>
                    </div>
                @endforelse
            </div>

            <div class="p-4 border-t border-gray-100">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
@endsection