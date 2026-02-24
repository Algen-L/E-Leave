@extends('layouts.sdo')

@section('title', 'Manage Users')
@section('page-title', 'Manage Users')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/manage-users.css') }}">
@endpush

@section('content')
    <!-- Tabs -->
    <div class="management-tabs">
        <a href="{{ route('admin.manage-users', ['view' => 'active']) }}"
            class="tab-item {{ $view === 'active' ? 'active' : '' }}">
            <i class="fas fa-user-check"></i>
            Active Users
        </a>
        <a href="{{ route('admin.manage-users', ['view' => 'inactive']) }}"
            class="tab-item {{ $view === 'inactive' ? 'active' : '' }}">
            <i class="fas fa-user-times"></i>
            Inactive Users
        </a>
        <a href="{{ route('admin.manage-users', ['view' => 'all']) }}"
            class="tab-item {{ $view === 'all' ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            All Users
            <span class="tab-badge">{{ count($users) }}</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="filter-bar-card">
        <form method="GET" action="{{ route('admin.manage-users') }}" class="filter-form-flex">
            <input type="hidden" name="view" value="{{ $view }}">

            <div class="search-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" name="search" placeholder="Search by name, username, email..."
                    value="{{ $filters['search'] }}">
            </div>

            <select class="filter-select" name="filter_role">
                <option value="">All Roles</option>
                <option value="user" {{ $filters['role'] === 'user' ? 'selected' : '' }}>User</option>
                <option value="head_hr" {{ $filters['role'] === 'head_hr' ? 'selected' : '' }}>HR PERSONNEL</option>
            </select>

            <select class="filter-select" name="filter_office">
                <option value="">All Offices</option>
                @foreach($offices as $office)
                    <option value="{{ $office->name }}" {{ $filters['office'] === $office->name ? 'selected' : '' }}>
                        {{ $office->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="filter-btn">
                <i class="fas fa-search"></i>
                Filter
            </button>
        </form>
    </div>

    <!-- Table Header -->
    <div class="table-header">
        <span>User</span>
        <span>Username</span>
        <span>Role</span>
        <span>Office</span>
        <span>Status</span>
        <span>Actions</span>
    </div>

    <!-- Users List -->
    <div class="user-list">
        @forelse($users as $u)
            <div class="user-card">
                <div class="user-info">
                    <div class="user-avatar">
                        @if($u->profile_picture)
                            <img src="{{ storage_url($u->profile_picture) }}" alt="{{ $u->full_name }}">
                        @else
                            {{ strtoupper(substr($u->full_name, 0, 2)) }}
                        @endif
                    </div>
                    <div class="user-details">
                        <div class="user-name">{{ $u->full_name }}</div>
                        <div class="user-email">{{ $u->gmail }}</div>
                    </div>
                </div>

                <div>
                    <span class="user-meta-label">Username</span>
                    <span class="user-meta-value">{{ $u->username }}</span>
                </div>

                <div>
                    <span class="user-meta-label">Role</span>
                    <span class="badge badge-role-{{ $u->role }}">
                        {{ ucfirst(str_replace('_', ' ', $u->role)) }}
                    </span>
                </div>

                <div>
                    <span class="user-meta-label">Office</span>
                    <span class="user-meta-value">{{ $u->office_station ?: '-' }}</span>
                </div>

                <div>
                    <span class="badge badge-status {{ $u->is_active ? 'badge-active' : 'badge-inactive' }}">
                        {{ $u->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <div class="user-actions">
                    <a href="{{ route('admin.users.edit', $u) }}" class="action-btn action-btn-edit" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>

                    @if($u->id !== auth()->id())
                        <form action="{{ route('admin.users.toggle-status', $u) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="action-btn action-btn-toggle"
                                title="{{ $u->is_active ? 'Deactivate' : 'Activate' }}">
                                <i class="fas fa-{{ $u->is_active ? 'ban' : 'check' }}"></i>
                            </button>
                        </form>

                        <form action="{{ route('admin.users.delete', $u) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Are you sure you want to delete this user?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn action-btn-delete" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-users-slash"></i>
                </div>
                <h3>No users found</h3>
                <p>Try adjusting your search or filter criteria</p>
            </div>
        @endforelse
    </div>
@endsection