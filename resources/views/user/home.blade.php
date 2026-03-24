@extends('layouts.sdo')

@section('title', 'Home')
@section('page-title', 'Welcome, ' . $user->full_name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user-home.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/leave-dashboard.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/activity-card.css') }}?v={{ time() }}">
    <style>
        /* Force styles for the redesigned section */
        .timeoff-balances-container {
            background: #fff !important;
            border-radius: 12px !important;
            border: 1px solid #e2e8f0 !important;
            overflow: hidden !important;
            margin-bottom: 12px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
            display: block !important;
        }
        .timeoff-header {
            padding: 8px 16px !important;
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            color: #334155 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
        }
        .timeoff-header i {
            color: #10b981 !important; /* Emerald green */
            font-size: 1.1rem !important;
        }
        .timeoff-body {
            display: flex !important;
            align-items: stretch !important;
        }
        .timeoff-col {
            flex: 1 !important;
            padding: 16px 16px !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 12px !important;
            position: relative !important;
        }
        .timeoff-col:not(:last-child)::after {
            content: '' !important;
            position: absolute !important;
            right: 0 !important;
            top: 0 !important;
            bottom: 0 !important;
            width: 1px !important;
            background: #f1f5f9 !important;
        }
        .timeoff-item-header {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
        }
        .timeoff-icon-box {
            width: 32px !important;
            height: 32px !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 0.9rem !important;
        }
        .vl-box { background: #dcfce7 !important; color: #166534 !important; }
        .sl-box { background: #ffedd5 !important; color: #9a3412 !important; }
        .cto-box { background: #ede9fe !important; color: #5b21b6 !important; }
        .timeoff-label {
            font-size: 1.1rem !important;
            font-weight: 700 !important;
            color: #1e293b !important;
        }
        .timeoff-value {
            font-size: 2.5rem !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            line-height: 1 !important;
        }

        /* Leave Request Summary Redesign */
        .leave-request-container {
            background: #eef2f6 !important; /* Slightly bluish background */
            border-radius: 16px !important;
            overflow: hidden !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        }
        .leave-request-header {
            background: #1e5f91 !important; /* Blue header */
            padding: 10px 16px !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            color: white !important;
        }
        .leave-request-header i {
            width: 38px !important;
            height: 38px !important;
            background: rgba(255, 255, 255, 0.15) !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1.1rem !important;
        }
        .leave-request-header h2 {
            margin: 0 !important;
            font-size: 0.8rem !important;
            font-weight: 500 !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            color: #f8fafc !important;
            letter-spacing: 0.8px !important;
            text-transform: uppercase !important;
        }
        .leave-request-body {
            padding: 12px 20px !important;
        }
        .status-cards-grid {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 12px !important;
        }
        .status-card {
            background: white !important;
            border-radius: 12px !important;
            padding: 10px 8px !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important;
        }
        .status-icon-box {
            width: 32px !important;
            height: 32px !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1rem !important;
        }
        .pending-box { background: #fef3c7 !important; color: #d97706 !important; }
        .approved-box { background: #dcfce7 !important; color: #16a34a !important; }
        .disapproved-box { background: #fee2e2 !important; color: #dc2626 !important; }
        .total-box { background: #f1f5f9 !important; color: #64748b !important; }
        
        .status-value {
            font-size: 1.6rem !important;
            font-weight: 800 !important;
            color: #1e293b !important;
            line-height: 1 !important;
        }
        .status-label {
            font-size: 0.7rem !important;
            font-weight: 700 !important;
            color: #64748b !important;
            text-transform: uppercase !important;
        }

        /* Layout Alignments */
        .home-layout {
            display: grid !important;
            grid-template-columns: 280px 1fr !important;
            gap: 12px !important;
            align-items: stretch !important; /* Forces equal height columns */
        }
        .home-left, .home-right {
            display: flex !important;
            flex-direction: column !important;
        }
        .activity-log-card {
            flex: 1 !important; /* Fills remaining height */
            display: flex !important;
            flex-direction: column !important;
            background: white !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            overflow: hidden !important;
            margin-top: 12px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
        }
        .activity-body {
            flex: 1 !important;
            max-height: none !important; /* Removes height limit */
            overflow-y: auto !important;
        }

        /* Activity Header Icon Color */
        .activity-header h2 i {
            color: #f59e0b !important; /* Amber/Orange */
            margin-right: 8px !important;
        }
        @media (max-width: 992px) {
            .timeoff-body { flex-direction: column !important; }
            .timeoff-col:not(:last-child)::after { display: none !important; }
            .timeoff-col:not(:last-child) { border-bottom: 1px solid #f1f5f9 !important; }
        }
    </style>
@endpush

@section('content')
    <div class="home-layout">
        <!-- Left Column: Profile Card & Leave Stats -->
        <div class="home-left">
            <div class="profile-summary-card animate__animated animate__backInDown animate__fast" style="animation-delay: 0.3s;">
                <div class="profile-summary-banner">
                    <!-- Notification Bell -->
                    <div class="notification-bell-wrapper">
                        <button id="notif-bell-trigger" class="notif-bell-btn" title="Notifications">
                            <i class="fas fa-bell"></i>
                            @if($unreadCount > 0)
                                <span class="bell-badge">{{ $unreadCount }}</span>
                            @endif
                        </button>
                        <div id="notif-dropdown" class="notif-dropdown-card">
                            <div class="notif-dropdown-header">
                                <h3>Notifications</h3>
                                @if($unreadCount > 0)
                                    <span class="unread-pill">{{ $unreadCount }} New</span>
                                @endif
                            </div>
                            <div class="notif-dropdown-body">
                                @forelse($notifications as $notification)
                                    <div class="notif-dropdown-item {{ $notification->read_at ? 'read' : 'unread' }}" data-id="{{ $notification->id }}">
                                        <div class="notif-item-avatar">
                                            @if($notification->sender && $notification->sender->profile_picture)
                                                <img src="{{ storage_url($notification->sender->profile_picture) }}" alt="">
                                            @else
                                                <div class="notif-avatar-init">
                                                    {{ strtoupper(substr($notification->sender->full_name ?? 'S', 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="notif-item-content">
                                            <div class="notif-item-header">
                                                <span class="notif-item-sender">{{ $notification->sender->full_name ?? 'System' }}</span>
                                                <span class="notif-item-time">{{ $notification->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="notif-item-text">{{ $notification->message }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="notif-dropdown-empty">
                                        <i class="fas fa-bell-slash"></i>
                                        <p>All caught up!</p>
                                        <span>No new notifications at the moment.</span>
                                    </div>
                                @endforelse
                            </div>
                            <div class="notif-dropdown-footer">
                                <a href="#" class="view-all-link">View All Notifications</a>
                                <span class="footer-divider"></span>
                                <a href="#" class="clear-all-btn">Mark all as read</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="profile-summary-body">
                    <div class="profile-summary-avatar">
                        @if($user->profile_picture)
                            <img src="{{ storage_url($user->profile_picture) }}" alt="{{ $user->full_name }}">
                        @else
                            <div class="avatar-placeholder">
                                {{ strtoupper(substr($user->full_name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h3 class="profile-summary-name">{{ $user->full_name }}</h3>
                    <span class="profile-summary-role">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                </div>

                <div class="profile-summary-details">
                    <div class="detail-item">
                        <div class="detail-icon"><i class="fas fa-building"></i></div>
                        <div class="detail-text">
                            <span class="detail-label">OFFICE / STATION</span>
                            <span class="detail-value">{{ $user->office_station ?: 'Not set' }}</span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon"><i class="fas fa-envelope"></i></div>
                        <div class="detail-text">
                            <span class="detail-label">GMAIL</span>
                            <span class="detail-value">{{ $user->gmail }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="leave-request-container animate__animated animate__backInDown animate__fast" style="margin-top: 12px; animation-delay: 0.1s;">
                <div class="leave-request-header">
                    <i class="fas fa-clipboard-list"></i>
                    <h2>LEAVE REQUEST SUMMARY</h2>
                </div>
                <div class="leave-request-body">
                    <div class="status-cards-grid">
                        <div class="status-card">
                            <div class="status-icon-box pending-box">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                            <div class="status-value">{{ $leaveSummary['pending'] }}</div>
                            <div class="status-label">PENDING</div>
                        </div>
                        <div class="status-card">
                            <div class="status-icon-box approved-box">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="status-value">{{ $leaveSummary['approved'] }}</div>
                            <div class="status-label">APPROVED</div>
                        </div>
                        <div class="status-card">
                            <div class="status-icon-box disapproved-box">
                                <i class="fas fa-times"></i>
                            </div>
                            <div class="status-value">{{ $leaveSummary['disapproved'] }}</div>
                            <div class="status-label">DISAPPROVED</div>
                        </div>
                        <div class="status-card">
                            <div class="status-icon-box total-box" style="font-weight: 800; font-size: 1.6rem;">
                                &Sigma;
                            </div>
                            <div class="status-value">{{ $leaveSummary['total'] }}</div>
                            <div class="status-label">TOTAL</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Notifications + Welcome -->
        <div class="home-right">
            @if($profileIncomplete)
                <div class="setup-profile-notice">
                    <div class="notice-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <div class="notice-content">
                        <h3>Complete Your Profile</h3>
                        <p>To file for leave, you must first set up your essential details in your profile. Please provide the
                            following:</p>
                        <ul class="notice-list">
                            @if(empty($user->position))
                            <li><i class="fas fa-check-circle"></i> Position</li> @endif
                            @if(empty($user->salary))
                            <li><i class="fas fa-check-circle"></i> Salary</li> @endif
                            @if(empty($user->recommending_officer_id))
                            <li><i class="fas fa-check-circle"></i> Recommending Approver</li> @endif
                            @if(empty($user->approving_officer_id))
                            <li><i class="fas fa-check-circle"></i> Final Approver</li> @endif
                            @if(empty($user->esignature))
                            <li><i class="fas fa-check-circle"></i> E-Signature</li> @endif
                        </ul>
                    </div>
                    <div class="notice-actions">
                        <a href="{{ route('user.profile') }}" class="btn-setup">
                            <i class="fas fa-cog"></i> Setup Now
                        </a>
                    </div>
                </div>
            @endif

            <!-- Leave Dashboard Section (Redesigned) -->
            <div class="timeoff-balances-container animate__animated animate__backInDown animate__fast" style="animation-delay: 0.2s;">
                <div class="timeoff-header">
                    <i class="fas fa-wallet"></i>
                    Current Time-Off Balances
                </div>
                <div class="timeoff-body">
                    <!-- Vacation Leave -->
                    <div class="timeoff-col">
                        <div class="timeoff-item-header">
                            <div class="timeoff-icon-box vl-box">
                                <i class="fas fa-plane"></i>
                            </div>
                            <span class="timeoff-label">Vacation Leave</span>
                        </div>
                        <div class="timeoff-value">{{ number_format($credits['vl'], 2) }}</div>
                    </div>
                    <!-- Sick Leave -->
                    <div class="timeoff-col">
                        <div class="timeoff-item-header">
                            <div class="timeoff-icon-box sl-box">
                                <i class="fas fa-briefcase-medical"></i>
                            </div>
                            <span class="timeoff-label">Sick Leave</span>
                        </div>
                        <div class="timeoff-value">{{ number_format($credits['sl'], 2) }}</div>
                    </div>
                    <!-- CTO Credit -->
                    <div class="timeoff-col">
                        <div class="timeoff-item-header">
                            <div class="timeoff-icon-box cto-box">
                                <i class="fas fa-clock"></i>
                            </div>
                            <span class="timeoff-label">CTO Credit</span>
                        </div>
                        <div class="timeoff-value">{{ number_format($credits['cto'], 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Activity Log Card (Priority View) -->
            <div class="activity-log-card animate__animated animate__backInDown animate__fast" style="animation-delay: 0s;">
                <div class="activity-header">
                    <h2><i class="fas fa-history"></i> Recent Activity</h2>
                    <span class="badge bg-primary-soft text-primary" style="font-size: 10px; padding: 2px 6px;">NEW</span>
                </div>
                <div class="activity-body">
                    <div class="activity-list">
                        @forelse($activityLogs as $log)
                            <div class="activity-item-card">
                                <div class="activity-main">
                                    <span class="activity-subject">{{ $user->full_name }}</span>
                                    <div class="activity-meta">
                                        <span class="leave-tag">{{ $log->leave_type_name }}</span>
                                        <span class="banner-divider" style="font-size: 10px;">&bull;</span>
                                        <span class="activity-time">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="activity-values">
                                    @php
                                        $isDeduction = strtolower($log->action) === 'deduction';
                                        $diff = $log->new_value - $log->previous_value;
                                        $diffDisplay = ($diff > 0 ? '+' : '') . number_format($diff, 2);
                                    @endphp
                                    <span class="action-badge {{ $isDeduction ? 'action-deduction' : 'action-addition' }}">
                                        {{ $isDeduction ? 'Deduction' : 'Addition' }}
                                    </span>
                                    <div class="balance-pill">
                                        <span class="val-old">{{ number_format($log->previous_value, 2) }}</span>
                                        <i class="fas fa-chevron-right val-arrow"></i>
                                        <span class="val-new">{{ number_format($log->new_value, 2) }}</span>
                                        <span class="val-diff {{ $diff < 0 ? 'diff-negative' : 'diff-positive' }}">
                                            ({{ $diffDisplay }})
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="activity-empty">
                                <i class="fas fa-layer-group" style="font-size: 2rem; margin-bottom: 12px; opacity: 0.3;"></i>
                                <p>No recent leave activities logged.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bellBtn = document.getElementById('notif-bell-trigger');
            const dropdown = document.getElementById('notif-dropdown');

            if (bellBtn && dropdown) {
                bellBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.classList.toggle('show');
                });

                document.addEventListener('click', function(e) {
                    if (!dropdown.contains(e.target) && e.target !== bellBtn) {
                        dropdown.classList.remove('show');
                    }
                });
            }
        });
    </script>
    @endpush
@endsection