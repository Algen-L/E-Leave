@extends('layouts.sdo')

@section('title', 'Home')
@section('page-title', 'Overview')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-redesign.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="dash-container animate__animated animate__fadeIn">
    
    <!-- Top Metrics Row: Profile + Credits -->
    <div class="top-metrics-grid">
        <!-- 1. Profile Summary Card (replaces Total Revenue) -->
        <div class="premium-card-black animate__animated animate__fadeInDown">
            <!-- Decorative Background (contains overflow) -->
            <div class="card-deco-inner"></div>
            
            <div class="profile-card-header">
                <span class="profile-title-mini">User Profile Summary</span>
            </div>
            <div style="display: flex; align-items: center; gap: 20px;">
                @if($user->profile_picture)
                    <img src="{{ storage_url($user->profile_picture) }}" alt="{{ $user->full_name }}" class="profile-avatar-circle">
                @else
                    <div class="profile-avatar-circle" style="background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 800;">
                        {{ strtoupper(substr($user->full_name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h3 class="profile-name-large">{{ $user->full_name }}</h3>
                </div>
            </div>
            <div style="margin-top: 12px; font-size: 0.8rem; color: rgba(255,255,255,0.6); font-weight: 600;">
                {{ $user->position ?: 'No Position Set' }} • {{ $user->office_station ?: 'SDO Office' }}
            </div>
        </div>

        <!-- 2. VL Credit Card -->
        <div class="metric-card animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="metric-header">
                <span class="metric-name">VACATION LEAVE</span>
                <div class="metric-icon-box vl-accent"><i class="fas fa-plane"></i></div>
            </div>
            <div class="metric-value">{{ format_credit_3_decimal($credits['vl']) }}</div>
            <div class="metric-trend trend-up">
                <i class="fas fa-chart-line"></i> Current Balance
            </div>
        </div>

        <!-- 3. SL Credit Card -->
        <div class="metric-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="metric-header">
                <span class="metric-name">SICK LEAVE</span>
                <div class="metric-icon-box sl-accent"><i class="fas fa-briefcase-medical"></i></div>
            </div>
            <div class="metric-value">{{ format_credit_3_decimal($credits['sl']) }}</div>
            <div class="metric-trend trend-up">
                <i class="fas fa-chart-line"></i> Current Balance
            </div>
        </div>

        <!-- 4. CTO Credit Card -->
        <div class="metric-card animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
            <div class="metric-header">
                <span class="metric-name">CTO CREDIT</span>
                <div class="metric-icon-box cto-accent"><i class="fas fa-clock"></i></div>
            </div>
            <div class="metric-value">{{ format_credit_3_decimal($credits['cto']) }}</div>
            <div class="metric-trend trend-up">
                <i class="fas fa-chart-line"></i> Current Balance
            </div>
        </div>
    </div>

    <!-- Middle Row: Recent Activity + Calendar -->
    <div class="main-content-grid">
        <!-- 5. Application History -->
        <div class="content-card animate__animated animate__fadeInLeft" style="animation-delay: 0.4s;">
            <div class="card-title">
                <span><i class="fas fa-history text-primary me-2"></i> Application Progress History</span>
                <a href="{{ route('user.leave.history') }}" class="btn btn-sm btn-link text-muted" style="font-size: 0.8rem; text-decoration: none;">View All</a>
            </div>
            <div class="timeline-list">
                @forelse($applicationHistory as $app)
                    @php
                        $statusColor = '#f59e0b'; // default warning/pending
                        if($app->status == 'Approved') $statusColor = '#10b981';
                        elseif($app->status == 'Disapproved') $statusColor = '#ef4444';
                        
                        $displayStatus = $app->status;
                        if($app->status == 'Pending HR') $displayStatus = 'Verification (HR)';
                        if($app->status == 'Pending Recommending') $displayStatus = 'Recommended Review';
                        if($app->status == 'Pending Approval') $displayStatus = 'Final Approval Review';
                    @endphp
                    <div class="timeline-item">
                        <div class="timeline-marker" style="background: {{ $statusColor }}; box-shadow: 0 0 0 3px {{ $statusColor }}20;"></div>
                        <div class="timeline-content">
                            <div class="timeline-desc">
                                <span class="fw-bold">{{ $app->leaveType->type_name }}</span> - 
                                <span style="color: {{ $statusColor }}; font-weight: 700;">{{ $displayStatus }}</span>
                            </div>
                            <div class="timeline-meta">
                                {{ $app->updated_at->diffForHumans() }} • 
                                Period: {{ $app->start_date->format('M d') }} - {{ $app->end_date->format('M d, Y') }}
                                @if($app->end_date->isPast() && !in_array($app->status, ['Approved', 'Disapproved', 'Rejected']))
                                    <span class="ms-2" style="color: #ef4444; font-weight: 800; font-size: 0.65rem; text-transform: uppercase;">
                                        <i class="fas fa-calendar-times"></i> Past Dated
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-clipboard-list fa-2x mb-3 opacity-25"></i>
                        <p>No recent applications found.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- NEW: Leave Deduction Log (Center Column) -->
        <div class="content-card animate__animated animate__fadeInUp" style="animation-delay: 0.45s;">
            <div class="card-title">
                <span><i class="fas fa-minus-circle text-danger me-2"></i> Leave Deduction Log</span>
            </div>
            <div class="deduction-list" style="max-height: 480px; overflow-y: auto;">
                @forelse($deductionLogs as $log)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; margin-bottom: 10px; background: #f8fafc; border-radius: 12px; border: 1px solid #f1f5f9; transition: all 0.2s;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: #fee2e2; color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                                <i class="fas fa-minus-circle"></i>
                            </div>
                            <div>
                                <div style="font-weight: 700; color: #1e293b; font-size: 0.85rem; line-height: 1.2;">{{ $log->leave_type_name }}</div>
                                <div style="color: #64748b; font-size: 0.65rem; line-height: 1.4;">
                                    {{ $log->reason ?: 'Approved Leave' }} • {{ $log->created_at->format('M d') }}
                                </div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 800; color: #ef4444; font-size: 1.1rem; line-height: 1;">-{{ number_format($log->previous_value - $log->new_value, 1) }}</div>
                            <div style="color: #94a3b8; font-size: 0.6rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">Days</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-history fa-2x mb-3 opacity-25"></i>
                        <p>No recent deductions found.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 6. Attendance Hub -->
        <div class="content-card animate__animated animate__fadeInRight" style="animation-delay: 0.5s;">
            <div class="card-title">
                <span><i class="fas fa-calendar-alt text-primary me-2"></i> Attendance Hub</span>
            </div>
            <div class="calendar-widget">
                <div class="cal-header">
                    <span class="cal-month">{{ now()->format('F Y') }}</span>
                    <div style="display: flex; gap: 8px;">
                        <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 0.65rem;">APPROVED</span>
                        <span class="badge" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; font-size: 0.65rem;">PENDING</span>
                    </div>
                </div>
                <div class="cal-grid" id="dashboardCalendar">
                    <div class="cal-day-label">Su</div>
                    <div class="cal-day-label">Mo</div>
                    <div class="cal-day-label">Tu</div>
                    <div class="cal-day-label">We</div>
                    <div class="cal-day-label">Th</div>
                    <div class="cal-day-label">Fr</div>
                    <div class="cal-day-label">Sa</div>
                    <!-- JS will fill dates -->
                </div>
            </div>
            
            <style>
                .cal-date.clickable {
                    cursor: pointer;
                    transition: all 0.2s;
                }
                .cal-date.clickable:hover {
                    background: rgba(15, 76, 117, 0.1);
                    transform: scale(1.1);
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                }
                .cal-date.clickable:active {
                    transform: scale(0.95);
                }
            </style>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calGrid = document.getElementById('dashboardCalendar');
        const now = new Date();
        const year = now.getFullYear();
        const month = now.getMonth();
        const today = now.getDate();

        // Get first day and total days
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        // Application Data from Backend
        const appDates = {!! json_encode($calendarApps->map(function($app) {
            return [
                'id' => $app->id,
                'start' => $app->start_date->format('Y-m-d'),
                'end' => $app->end_date->format('Y-m-d'),
                'status' => $app->status
            ];
        })) !!};

        // Fill empty spaces before first day
        for (let i = 0; i < firstDay; i++) {
            calGrid.innerHTML += '<div class="cal-date empty"></div>';
        }

        // Fill actual dates
        for (let d = 1; d <= daysInMonth; d++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
            const isToday = d === today;
            
            // Check if this date fall within any application range
            let markerHtml = '';
            let clickableInfo = null;
            appDates.forEach(app => {
                if (dateStr >= app.start && dateStr <= app.end) {
                    const markerClass = (app.status === 'Approved') ? 'marker-approved' : 
                                      (app.status === 'Disapproved') ? 'marker-disapproved' : 'marker-pending';
                    markerHtml = `<span class="cal-marker ${markerClass}"></span>`;
                    clickableInfo = { id: app.id, status: app.status };
                }
            });

            const clickableClass = clickableInfo ? 'clickable' : '';
            const tooltip = clickableInfo ? `View ${clickableInfo.status} Application` : '';
            
            // Build the URL using the route name and then replace the ID placeholder
            const baseUrl = "{{ route('user.leave.show', ['id' => ':id']) }}";
            const onClickAttr = clickableInfo ? `onclick="window.location.href='${baseUrl.replace(':id', clickableInfo.id)}'"` : '';

            calGrid.innerHTML += `
                <div class="cal-date ${isToday ? 'today' : ''} ${clickableClass}" 
                     title="${tooltip}" 
                     ${onClickAttr}>
                    ${d}
                    ${markerHtml}
                </div>
            `;
    });
</script>
@endpush
@endsection
