@extends('layouts.sdo')

@section('title', 'Manage Users')
@section('page-title', 'Manage Users')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/manage-users.css') }}?v={{ time() }}">
    <style>
        .manage-users-premium {
            @if(count(request()->query()) === 0)
                animation: fadeIn 0.4s ease-out;
            @endif
        }

        /* Sequential Entrance for Top Elements */
        .management-tabs, .filter-bar-card, .table-header {
            @if(count(request()->query()) === 0)
                opacity: 0;
                animation: fadeInDown 0.6s ease-out forwards;
            @else
                opacity: 1;
                transform: none;
            @endif
        }

        @if(count(request()->query()) === 0)
            .management-tabs { animation-delay: 0.1s; }
            .filter-bar-card { animation-delay: 0.2s; }
            .table-header { animation-delay: 0.3s; }
        @endif

        /* Sequential card animation */
        @if(count(request()->query()) === 0)
            .user-list .user-card {
                opacity: 0;
                animation: backInDown 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            }

            @foreach(range(1, 25) as $i)
                .user-list .user-card:nth-child({{ $i }}) {
                    animation-delay: {{ 0.35 + ($i * 0.05) }}s;
                }
            @endforeach
        @else
            .user-list .user-card {
                opacity: 1;
                transform: none;
                animation: none;
            }
        @endif

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

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
@endpush

@section('content')
    <div class="manage-users-premium">
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
                <input type="text" class="search-input" name="search" placeholder="Search by name, email, employee id..."
                    value="{{ $filters['search'] }}">
            </div>

            <select class="filter-select" name="filter_role">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    @if(!in_array($role, ['admin', 'hr', 'immediate_head']))
                        <option value="{{ $role }}" {{ $filters['role'] === $role ? 'selected' : '' }}>
                            @if($role === 'head_hr')
                                HR PERSONNEL
                            @else
                                {{ strtoupper(str_replace('_', ' ', $role)) }}
                            @endif
                        </option>
                    @endif
                @endforeach
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
                    <span class="user-meta-label">Role</span>
                    <span class="badge badge-role-{{ $u->role }}">
                        @if($u->role === 'head_hr')
                            HR PERSONNEL
                        @else
                            {{ strtoupper(str_replace('_', ' ', $u->role)) }}
                        @endif
                    </span>
                </div>

                <div>
                    <span class="user-meta-label">Office</span>
                    <span class="user-meta-value office-{{ str($u->office_station)->slug() }}">
                        {{ $u->office_station ?: '-' }}
                    </span>
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
</div>
@endsection
