@extends('layouts.sdo')

@section('title', 'Home')
@section('page-title', 'Welcome, ' . $user->full_name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user-home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/leave-dashboard.css') }}">
@endpush

@section('content')
    <div class="home-layout">
        <!-- Left Column: Profile Card & Leave Stats -->
        <div class="home-left">
            <div class="profile-summary-card">
                <div class="profile-summary-banner"></div>
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
                        </ul>
                    </div>
                    <div class="notice-actions">
                        <a href="{{ route('user.profile') }}" class="btn-setup">
                            <i class="fas fa-cog"></i> Setup Now
                        </a>
                    </div>
                </div>
            @endif

            <!-- Leave Dashboard Section -->
            <div class="leave-stats-container">
                <div class="credit-stats-grid">
                    <div class="credit-card">
                        <div class="credit-icon credit-vl"><i class="fas fa-plane"></i></div>
                        <span class="credit-val">{{ number_format($credits['vl'], 2) }}</span>
                        <span class="credit-label">Vacation Leave</span>
                    </div>
                    <div class="credit-card">
                        <div class="credit-icon credit-sl"><i class="fas fa-briefcase-medical"></i></div>
                        <span class="credit-val">{{ number_format($credits['sl'], 2) }}</span>
                        <span class="credit-label">Sick Leave</span>
                    </div>
                    <div class="credit-card">
                        <div class="credit-icon credit-cto"><i class="fas fa-clock"></i></div>
                        <span class="credit-val">{{ number_format($credits['cto'], 2) }}</span>
                        <span class="credit-label">CTO Credit</span>
                    </div>
                </div>

                <div class="leave-summary-card">
                    <div class="summary-header">
                        <i class="fas fa-clipboard-list"></i>
                        <h2>Leave Summary</h2>
                    </div>
                    <div class="summary-grid">
                        <div class="summary-item">
                            <span class="summary-count">{{ $leaveSummary['pending'] }}</span>
                            <span class="summary-label">Pending</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-count">{{ $leaveSummary['approved'] }}</span>
                            <span class="summary-label">Approved</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-count">{{ $leaveSummary['disapproved'] }}</span>
                            <span class="summary-label">Disapproved</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-count">{{ $leaveSummary['total'] }}</span>
                            <span class="summary-label">Total</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications Card -->
            <div class="notifications-card">
                <div class="notif-header">
                    <h2><i class="fas fa-bullhorn"></i> Notifications</h2>
                    @if($unreadCount > 0)
                        <span class="notif-badge">{{ $unreadCount }}</span>
                    @endif
                </div>
                <div class="notif-body">
                    @forelse($notifications as $notification)
                        <div class="notif-item" data-id="{{ $notification->id }}">
                            <div class="notif-avatar">
                                @if($notification->sender && $notification->sender->profile_picture)
                                    <img src="{{ storage_url($notification->sender->profile_picture) }}" alt="">
                                @else
                                    <div class="notif-avatar-placeholder">
                                        {{ strtoupper(substr($notification->sender->full_name ?? 'S', 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="notif-content">
                                <span class="notif-sender">{{ $notification->sender->full_name ?? 'System' }}</span>
                                <p class="notif-message">{{ $notification->message }}</p>
                                <span class="notif-time">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="notif-empty">
                            <i class="fas fa-bell-slash"></i>
                            <span>No new notifications.</span>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
@endsection