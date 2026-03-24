@extends('layouts.sdo')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/activity-logs.css') }}?v={{ time() }}">
    <style>
        .premium-filter-container {
            animation: fadeInDown 0.6s ease-out;
        }

        .activity-feed .activity-item {
            opacity: 0;
            animation: backInDown 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @foreach(range(1, 20) as $i)
            .activity-feed .activity-item:nth-child({{ $i }}) {
                animation-delay: {{ $i * 0.05 }}s;
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
    </style>
@endpush

@section('content')

    <!-- Premium Filter Container -->
    <div class="premium-filter-container">
        <div class="filter-header">
            <div class="filter-title">
                <i class="fas fa-filter"></i>
                Filter Activities
            </div>
        </div>

        <form method="GET" action="{{ route('admin.activity-logs') }}">
            <div class="filter-row">
                <div class="filter-group" style="flex: 2;">
                    <label class="filter-label">Search</label>
                    <input type="text" class="custom-select" name="search"
                        placeholder="Search by user, action, or details..." value="{{ $filters['search'] }}"
                        style="background-image: none; padding-left: 14px;">
                </div>

                <div class="filter-group">
                    <label class="filter-label">Action Type</label>
                    <select class="custom-select" name="action">
                        <option value="">All Actions</option>
                        <option value="login" {{ ($filters['action'] ?? '') == 'login' ? 'selected' : '' }}>Login</option>
                        <option value="logout" {{ ($filters['action'] ?? '') == 'logout' ? 'selected' : '' }}>Logout</option>
                        <option value="create" {{ ($filters['action'] ?? '') == 'create' ? 'selected' : '' }}>Create</option>
                        <option value="update" {{ ($filters['action'] ?? '') == 'update' ? 'selected' : '' }}>Update</option>
                        <option value="delete" {{ ($filters['action'] ?? '') == 'delete' ? 'selected' : '' }}>Delete</option>
                    </select>
                </div>

                <div class="filter-buttons">
                    <button type="submit" class="btn-filter-apply">
                        <i class="fas fa-search"></i>
                        Apply Filters
                    </button>
                    <a href="{{ route('admin.activity-logs') }}" class="btn-filter-reset">
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

    <!-- Activity Feed -->
    <div class="activity-feed">
        @forelse($logs as $log)
            @php
                $actionType = 'view';
                if (Str::contains(strtolower($log->action), 'login'))
                    $actionType = 'login';
                elseif (Str::contains(strtolower($log->action), 'logout'))
                    $actionType = 'logout';
                elseif (Str::contains(strtolower($log->action), ['create', 'register', 'add']))
                    $actionType = 'create';
                elseif (Str::contains(strtolower($log->action), ['update', 'edit', 'change']))
                    $actionType = 'update';
                elseif (Str::contains(strtolower($log->action), ['delete', 'remove']))
                    $actionType = 'delete';

                $actionLabels = [
                    'login' => 'Logged In',
                    'logout' => 'Logged Out',
                    'create' => 'Created Record',
                    'update' => 'Updated Record',
                    'delete' => 'Deleted Record',
                    'view' => $log->action,
                ];
            @endphp

            <div class="activity-item">
                <div class="activity-icon-simple">
                    <i class="fas fa-clipboard-list"></i>
                </div>

                <div class="activity-content">
                    <div class="activity-user-name">{{ $log->user->full_name ?? 'Unknown' }}</div>
                    <div class="activity-action {{ $actionType }}">{{ $actionLabels[$actionType] }}</div>
                </div>

                <div class="activity-time">
                    {{ $log->created_at->format('M d, Y') }} &bull; {{ $log->created_at->format('g:i A') }}
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h3>No activity logs found</h3>
                <p>Activity will appear here as users interact with the system</p>
            </div>
        @endforelse
    </div>
@endsection