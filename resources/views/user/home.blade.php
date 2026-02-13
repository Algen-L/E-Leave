@extends('layouts.sdo')

@section('title', 'Home')
@section('page-title', 'Welcome, ' . $user->full_name)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-home.css') }}">
@endpush

@section('content')
<div class="home-layout">
    <!-- Left Column: Profile Card -->
    <div class="home-left">
        <div class="profile-summary-card">
            <div class="profile-summary-banner"></div>
            <div class="profile-summary-body">
                <div class="profile-summary-avatar">
                    @if($user->profile_picture)
                        <img src="{{ asset($user->profile_picture) }}" alt="{{ $user->full_name }}">
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
                                <img src="{{ asset($notification->sender->profile_picture) }}" alt="">
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
        
        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <h2>Welcome to the Template System</h2>
            <p>This is a plain system template with authentication and hierarchy logic.</p>
        </div>
    </div>
</div>
@endsection
