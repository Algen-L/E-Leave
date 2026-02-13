@extends('layouts.sdo')

@section('title', 'Manage Leave Credits')
@section('page-title', 'Employee Leave Credits')

@push('styles')
<style>
    .page-header {
        background: white;
        padding: 20px 24px;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #eef2f6;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }
    .page-title h2 { font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0; }
    .page-title p { font-size: 0.875rem; color: #64748b; margin-top: 4px; }
    
    .search-form { display: flex; gap: 8px; }
    .search-input {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 8px 16px;
        font-size: 0.9rem;
        width: 260px;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .search-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
    .search-btn {
        background: #3b82f6; color: white; border: none;
        padding: 8px 16px; border-radius: 8px; cursor: pointer;
        transition: background 0.2s;
        display: flex; align-items: center; justify-content: center;
    }
    .search-btn:hover { background: #2563eb; }

    .users-table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #eef2f6;
        overflow: hidden;
    }
    .users-table { width: 100%; border-collapse: collapse; }
    .users-table th {
        background: #f8fafc;
        text-align: left;
        padding: 14px 24px;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        font-weight: 700;
        border-bottom: 1px solid #e2e8f0;
    }
    .users-table td {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .users-table tr:last-child td { border-bottom: none; }
    .users-table tr:hover { background-color: #f8fafc; }

    .user-profile { display: flex; align-items: center; gap: 12px; }
    .user-avatar {
        width: 40px; height: 40px;
        background: #e0f2fe; color: #0284c7;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 1rem;
        flex-shrink: 0;
    }
    .user-info .name { font-weight: 600; color: #1e293b; font-size: 0.95rem; }
    .user-info .email { color: #64748b; font-size: 0.8rem; margin-top: 2px; }

    .badge {
        display: inline-block; padding: 4px 10px;
        border-radius: 20px; font-size: 0.75rem; font-weight: 700;
    }
    .badge-allocated { background: #dcfce7; color: #166534; }
    .badge-pending { background: #f1f5f9; color: #64748b; }

    .action-btn {
        display: inline-flex; align-items: center; gap: 6px;
        background: white; border: 1px solid #e2e8f0;
        color: #475569; font-weight: 600; font-size: 0.85rem;
        padding: 6px 14px; border-radius: 6px;
        text-decoration: none; transition: all 0.2s;
    }
    .action-btn:hover { border-color: #3b82f6; color: #2563eb; background: #eff6ff; }
    
    .pagination-wrapper { padding: 16px 24px; border-top: 1px solid #f1f5f9; }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="page-header">
        <div class="page-title">
            <h2>Employee List</h2>
            <p>Select an employee to allocate or view leave credits.</p>
        </div>
        <form action="{{ route('hr-staff.manage-credits') }}" method="GET" class="search-form">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email..." class="search-input">
            <button type="submit" class="search-btn">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <div class="users-table-container">
        <table class="users-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Position</th>
                    <th>Credits Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="user-profile">
                            <div class="user-avatar">
                                {{ substr($user->first_name, 0, 1) }}
                            </div>
                            <div class="user-info">
                                <div class="name">{{ $user->full_name }}</div>
                                <div class="email">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="color: #475569; font-size: 0.9rem;">{{ $user->position ?? 'N/A' }}</td>
                    <td>
                        @php
                            $hasCredits = \App\Models\LeaveCredit::where('user_id', $user->id)->exists();
                        @endphp
                        @if($hasCredits)
                            <span class="badge badge-allocated">Allocated</span>
                        @else
                            <span class="badge badge-pending">Pending</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('hr-staff.manage-credits.edit', $user->id) }}" class="action-btn">
                            <i class="fas fa-sliders-h"></i> Manage
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 40px; color: #94a3b8;">
                        <i class="fas fa-users-slash" style="font-size: 2rem; margin-bottom: 12px; display: block;"></i>
                        No employees found matching your search.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($users->hasPages())
        <div class="pagination-wrapper">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
