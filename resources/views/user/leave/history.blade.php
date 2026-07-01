@extends('layouts.sdo')

@section('title', 'My Applications')
@section('page-title', 'My Applications')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/history.css') }}?v={{ time() }}">
@endpush

@section('content')
    <div class="summary-grid">
        <div class="summary-card animate__animated animate__backInDown animate__fast" style="animation-delay: 0.1s;">
            <div class="summary-icon-box animate__animated animate__backInDown animate__fast" style="background: rgba(30, 95, 145, 0.1); color: var(--primary-blue); animation-delay: 0.2s;">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div class="summary-info">
                <span class="summary-label">Total Applications</span>
                <span class="summary-value">{{ $stats['total'] }}</span>
            </div>
        </div>
        <div class="summary-card animate__animated animate__backInDown animate__fast" style="animation-delay: 0.2s;">
            <div class="summary-icon-box animate__animated animate__backInDown animate__fast" style="background: rgba(34, 197, 94, 0.1); color: var(--success-green); animation-delay: 0.3s;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="summary-info">
                <span class="summary-label">Approved</span>
                <span class="summary-value">{{ $stats['approved'] }}</span>
            </div>
        </div>
        <div class="summary-card animate__animated animate__backInDown animate__fast" style="animation-delay: 0.3s;">
            <div class="summary-icon-box animate__animated animate__backInDown animate__fast" style="background: rgba(245, 158, 11, 0.1); color: var(--pending-amber); animation-delay: 0.4s;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="summary-info">
                <span class="summary-label">Pending Review</span>
                <span class="summary-value">{{ $stats['pending'] }}</span>
            </div>
        </div>
    </div>

    <div class="history-card animate__animated animate__backInDown animate__fast" style="animation-delay: 0.4s;">
        <div class="history-header">
            <h2><i class="fas fa-history"></i> Application History</h2>
            <a href="{{ route('user.leave.apply') }}" class="btn-apply-new animate__animated animate__backInDown animate__fast" style="animation-delay: 0.5s;">
                <i class="fas fa-plus"></i> Apply New Leave
            </a>
        </div>

        <div class="history-table-wrapper">
            <table class="history-table stack-card-table">
                <thead>
                    <tr>
                        <th>Tracking No.</th>
                        <th>Date Filed</th>
                        <th>Leave Type</th>
                        <th>Inclusive Dates</th>
                        <th class="text-center">Progress</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        @php
                            // Helper to determine active/completed steps
                            $s1 = $app->hr_verified_at ? 'completed' : 'active';
                            $s2 = $app->recommended_at ? 'completed' : ($s1 == 'completed' ? 'active' : '');
                            $s3 = $app->approved_at ? 'completed' : ($s2 == 'completed' ? 'active' : '');

                            if (str_contains(strtolower($app->status), 'reject') || str_contains(strtolower($app->status), 'disapprove')) {
                                if (!$app->hr_verified_at) $s1 = 'rejected';
                                else if (!$app->recommended_at) $s2 = 'rejected';
                                else $s3 = 'rejected';
                            }
                        @endphp
                        <tr onclick="window.location='{{ route('user.leave.show', $app->id) }}'">
                            <td data-label="Tracking No." class="tracking-cell" style="font-family: 'Monaco', 'Consolas', monospace; font-weight: 600; color: var(--primary-blue);">
                                {{ $app->tracking_number ?? '---' }}
                            </td>
                            <td data-label="Date Filed">
                                <div class="date-primary">{{ \Carbon\Carbon::parse($app->date_filing)->format('M d, Y') }}</div>
                                <div class="date-secondary">{{ \Carbon\Carbon::parse($app->created_at)->format('h:i A') }}</div>
                            </td>
                            <td data-label="Leave Type">
                                <div class="type-name">{{ $app->leaveType->type_name }}</div>
                            </td>
                            <td data-label="Inclusive Dates">
                                <div class="duration-dates">{{ \Carbon\Carbon::parse($app->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($app->end_date)->format('M d, Y') }}</div>
                                <div class="duration-count">{{ $app->days_applied }} {{ Str::plural('day', $app->days_applied) }}</div>
                            </td>
                            <td data-label="Progress">
                                <div class="progress-stepper">
                                    <div class="stepper-dot {{ $s1 }}" title="HR Verification"></div>
                                    <div class="stepper-line {{ $s1 == 'completed' ? 'completed' : '' }}"></div>
                                    <div class="stepper-dot {{ $s2 }}" title="Recommendation"></div>
                                    <div class="stepper-line {{ $s2 == 'completed' ? 'completed' : '' }}"></div>
                                    <div class="stepper-dot {{ $s3 }}" title="Final Approval"></div>
                                </div>
                            </td>
                            <td data-label="Status" class="text-center">
                                @php
                                    $badgeClass = 'status-pending';
                                    if (stripos($app->status, 'approve') !== false && stripos($app->status, 'pending') === false)
                                        $badgeClass = 'status-approved';
                                    if (stripos($app->status, 'reject') !== false || stripos($app->status, 'disapprove') !== false)
                                        $badgeClass = 'status-rejected';
                                @endphp
                                <span class="status-badge {{ $badgeClass }}">
                                    {{ $app->status }}
                                </span>
                                @if(\Carbon\Carbon::parse($app->end_date)->isPast() && !in_array($app->status, ['Approved', 'Disapproved', 'Rejected']))
                                    <div class="mt-1" style="font-size: 0.65rem; color: #ef4444; font-weight: 800; text-transform: uppercase; display: flex; align-items: center; justify-content: center; gap: 4px;">
                                        <i class="fas fa-calendar-times"></i> Past Dated
                                    </div>
                                @endif
                            </td>
                            <td data-label="Actions">
                                <div class="action-group" onclick="event.stopPropagation()">
                                    <a href="{{ route('user.leave.show', $app->id) }}" class="btn-view animate__animated animate__bounceIn animate__fast" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-icon"><i class="fas fa-folder-open"></i></div>
                                    <div class="empty-text">No leave applications found.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
