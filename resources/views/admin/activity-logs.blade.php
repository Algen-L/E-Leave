@extends('layouts.sdo')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/activity-logs.css') }}?v={{ time() }}">
    <style>
        /* High-Definition Header System */
        .premium-filter-container {
            padding: 0;
            background: transparent !important;
            border-radius: 16px 16px 0 0;
            overflow: hidden;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            @if(count(request()->query()) <= 1 && request()->type == 'system')
                animation: fadeInDown 0.6s ease-out forwards;
            @else
                animation: none !important;
                transform: none !important;
            @endif
        }

        .header-top-row {
            padding: 12px 24px;
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
            padding: 4px 12px;
            border-radius: 99px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Unified Tab System */
        .log-tabs {
            display: flex;
            gap: 4px;
            background: rgba(255, 255, 255, 0.15);
            padding: 4px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .log-tab {
            padding: 8px 16px;
            border-radius: 9px;
            font-size: 0.8rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .log-tab:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .log-tab.active {
            background: white;
            color: var(--primary);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .log-tab i {
            font-size: 0.9rem;
        }

        .header-filters-row {
            width: 100%;
            background: white;
            padding: 24px;
            border-top: 1px solid rgba(15, 76, 117, 0.05);
        }

        /* Credit Log Specific Styles */
        .balance-change {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 8px;
            background: #f8fafc;
            padding: 8px 12px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            width: fit-content;
        }

        .balance-pill {
            display: flex;
            flex-direction: column;
            line-height: 1;
        }

        .balance-label {
            font-size: 0.6rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 4px;
        }

        .balance-val {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            font-size: 0.95rem;
            color: #1e293b;
        }

        .balance-arrow {
            color: #cbd5e1;
            font-size: 0.8rem;
        }

        .change-indicator {
            margin-left: 8px;
            font-size: 0.85rem;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 6px;
        }

        .change-add { background: #f0fdf4; color: #166534; }
        .change-sub { background: #fef2f2; color: #991b1b; }

        .log-item {
            border-left: 4px solid transparent;
            transition: all 0.2s;
        }

        .log-item:hover {
            border-left-color: var(--primary);
            background: #f8fafc;
        }

        .activity-details {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 4px;
            line-height: 1.4;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
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
    </style>
@endpush

@section('content')

    <!-- Unified Logs Header & Tabs -->
    <div class="premium-filter-container">
        <div class="header-top-row" style="justify-content: flex-start;">
            <div class="log-tabs">
                <a href="{{ route('admin.activity-logs', ['type' => 'system']) }}" class="log-tab {{ $type === 'system' ? 'active' : '' }}">
                    <i class="fas fa-desktop"></i> System Logs
                </a>
                @unless(Auth::user()->role === 'hr_review_officer')
                <a href="{{ route('admin.activity-logs', ['type' => 'officer']) }}" class="log-tab {{ $type === 'officer' ? 'active' : '' }}">
                    <i class="fas fa-user-shield"></i> Officer Actions
                </a>
                @endunless
                <a href="{{ route('admin.activity-logs', ['type' => 'credit']) }}" class="log-tab {{ $type === 'credit' ? 'active' : '' }}">
                    <i class="fas fa-coins"></i> Credit History
                </a>
            </div>
        </div>

        <div class="header-filters-row">
            <form method="GET" action="{{ route('admin.activity-logs') }}">
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="filter-row">
                    <div class="filter-group" style="flex: 2;">
                        <label class="filter-label">Quick Search</label>
                        <input type="text" class="custom-select" name="search"
                            placeholder="Search keywords, users, or details..." value="{{ $filters['search'] ?? '' }}"
                            style="background-image: none; padding-left: 14px;">
                    </div>

                    @if($type === 'officer')
                    <div class="filter-group">
                        <label class="filter-label">Personnel</label>
                        <select class="custom-select" name="officer_id">
                            <option value="">All Officers</option>
                            @foreach($officers as $officer)
                                <option value="{{ $officer->id }}" {{ $filters['officer_id'] == $officer->id ? 'selected' : '' }}>
                                    {{ $officer->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="filter-group">
                        <label class="filter-label">Category</label>
                        <select class="custom-select" name="action">
                            @if($type === 'credit')
                                <option value="">All Types</option>
                                <option value="allocate" {{ $filters['action'] == 'allocate' ? 'selected' : '' }}>Allocations</option>
                                <option value="deduct" {{ $filters['action'] == 'deduct' ? 'selected' : '' }}>Deductions</option>
                                <option value="update" {{ $filters['action'] == 'update' ? 'selected' : '' }}>Modifications</option>
                                <option value="add_coc" {{ $filters['action'] == 'add_coc' ? 'selected' : '' }}>COC Credit</option>
                            @else
                                <option value="">All Activities</option>
                                <option value="login" {{ $filters['action'] == 'login' ? 'selected' : '' }}>Logins</option>
                                <option value="create" {{ $filters['action'] == 'create' ? 'selected' : '' }}>Registrations</option>
                                <option value="update" {{ $filters['action'] == 'update' ? 'selected' : '' }}>Updates</option>
                                <option value="Verify" {{ $filters['action'] == 'Verify' ? 'selected' : '' }}>Verifications</option>
                                <option value="Approve" {{ $filters['action'] == 'Approve' ? 'selected' : '' }}>Approvals</option>
                            @endif
                        </select>
                    </div>

                    <div class="filter-buttons">
                        <button type="submit" class="btn-filter-apply">
                            <i class="fas fa-search"></i> Apply
                        </button>
                        <a href="{{ route('admin.activity-logs', ['type' => $type]) }}" class="btn-filter-reset">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>

                <input type="hidden" name="date_range" id="dateRangeInput" value="{{ $filters['date_range'] ?? '' }}">
                <div class="quick-dates">
                    <span class="date-pill {{ ($filters['date_range'] ?? '') == 'today' ? 'active' : '' }}" data-range="today">Today</span>
                    <span class="date-pill {{ ($filters['date_range'] ?? '') == '7days' ? 'active' : '' }}" data-range="7days">Last 7 Days</span>
                    <span class="date-pill {{ ($filters['date_range'] ?? '') == '30days' ? 'active' : '' }}" data-range="30days">Last 30 Days</span>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Feed -->
    <div class="activity-feed">
        @forelse($logs as $log)
            <div class="activity-item log-item">
                <div class="activity-icon-simple">
                    @if($type === 'credit')
                        @if($log->action == 'allocate') <i class="fas fa-plus-circle" style="color: #10b981;"></i>
                        @elseif($log->action == 'deduct') <i class="fas fa-minus-circle" style="color: #ef4444;"></i>
                        @elseif($log->action == 'add_coc') <i class="fas fa-clock" style="color: #f59e0b;"></i>
                        @else <i class="fas fa-edit" style="color: #1b4a9a;"></i> @endif
                    @else
                        @if(Str::contains(strtolower($log->action), 'login')) <i class="fas fa-sign-in-alt" style="color: #10b981;"></i>
                        @elseif(Str::contains(strtolower($log->action), 'verify')) <i class="fas fa-check-circle" style="color: #1b4a9a;"></i>
                        @elseif(Str::contains(strtolower($log->action), 'approve')) <i class="fas fa-stamp" style="color: #8b5cf6;"></i>
                        @elseif(Str::contains(strtolower($log->action), 'register')) <i class="fas fa-user-plus" style="color: #1b4a9a;"></i>
                        @else <i class="fas fa-clipboard-list" style="color: #64748b;"></i> @endif
                    @endif
                </div>

                <div class="activity-content">
                    @php
                        $applicationId = null;
                        if (preg_match('/#(\d+)/', $log->details . ' ' . $log->action, $matches)) {
                            $applicationId = $matches[1];
                        }
                    @endphp

                    @if($applicationId)
                        <a href="{{ route('user.leave.approvals.show', $applicationId) }}" class="activity-deep-link" style="text-decoration: none; display: block; position: relative;">
                            <div class="link-indicator" style="position: absolute; top: 0; right: 0; font-size: 0.7rem; color: var(--primary); font-weight: 700;">
                                View Details <i class="fas fa-external-link-alt ml-1"></i>
                            </div>
                    @endif

                    @if($type === 'credit')
                        <div class="officer-badge">
                            <i class="fas fa-user-circle mr-1"></i> {{ $log->targetUser->full_name ?? 'Unknown User' }}
                        </div>
                        <div class="activity-user-name">
                            {{ str_replace('_', ' ', strtoupper($log->action)) }}: {{ $log->leave_type_name }}
                        </div>
                        <div class="activity-details">
                            Performed by: <strong>{{ $log->actor->full_name ?? 'System' }}</strong>
                            @if($log->reason) <br> <i class="fas fa-quote-left mr-1 opacity-50"></i>{{ $log->reason }} @endif
                        </div>

                        @if($log->previous_value !== null && $log->new_value !== null)
                            @php $diff = $log->new_value - $log->previous_value; @endphp
                            <div class="balance-change">
                                <div class="balance-pill">
                                    <span class="balance-label">From</span>
                                    <span class="balance-val">{{ number_format($log->previous_value, 2) }}</span>
                                </div>
                                <i class="fas fa-chevron-right balance-arrow"></i>
                                <div class="balance-pill">
                                    <span class="balance-label">To</span>
                                    <span class="balance-val">{{ number_format($log->new_value, 2) }}</span>
                                </div>
                                <span class="change-indicator {{ $diff >= 0 ? 'change-add' : 'change-sub' }}">
                                    {{ $diff >= 0 ? '+' : '' }}{{ number_format($diff, 2) }}
                                </span>
                            </div>
                        @endif
                    @else
                        @if($type === 'officer')
                            <div class="officer-badge">
                                <i class="fas fa-user-tie mr-1"></i> {{ $log->user->full_name ?? 'System' }}
                            </div>
                        @endif

                        <div class="activity-user-name">
                            @if($type === 'system') {{ $log->user->full_name ?? 'Unknown' }} @else {{ $log->action }} @endif
                        </div>

                        <div class="activity-action" style="color: #1e293b; font-weight: 700;">
                            @if($type === 'system') {{ $log->action }} @else {{ $log->details }} @endif
                        </div>

                        @if($type === 'system' && $log->details)
                            <div class="activity-details">{{ $log->details }}</div>
                        @endif
                    @endif

                    @if($applicationId)
                        </a>
                    @endif
                </div>

                <div class="activity-time">
                    <div style="font-weight: 700; color: #1e293b;">{{ $log->created_at->format('M d, Y') }}</div>
                    <div style="font-size: 0.75rem; color: #64748b;">{{ $log->created_at->format('g:i A') }}</div>
                    @if($type !== 'system')
                        <div style="font-size: 0.7rem; color: #cbd5e1; margin-top: 4px;">{{ $log->ip_address ?? '' }}</div>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h3>No logs found</h3>
                <p>Try adjusting your search criteria or date range.</p>
            </div>
        @endforelse
    </div>

    @if($logs->hasPages())
    <div class="pagination-container">
        <div class="pagination-info">
            Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} results
        </div>
        <div class="pagination-links">
            {{ $logs->links('vendor.pagination.saas') }}
        </div>
    </div>
    @endif

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
@endsection
