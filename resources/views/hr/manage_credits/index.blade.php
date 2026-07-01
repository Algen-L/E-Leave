@extends('layouts.sdo')

@section('title', 'Manage Leave Credits')
@section('page-title', 'Employee Leave Credits')

@push('styles')
<style>
    .page-header {
        background: var(--primary-gradient);
        padding: 24px 32px;
        border-radius: 20px;
        box-shadow: 0 10px 25px -5px rgba(15, 76, 117, 0.2), 0 8px 10px -6px rgba(15, 76, 117, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.1);
        margin-bottom: 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }
    .page-title {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
        gap: 2px;
        padding-left: 16px;
        border-left: 4px solid rgba(255, 255, 255, 0.4);
    }
    .page-title h2 { font-size: 1.5rem; font-weight: 800; color: white; margin: 0; letter-spacing: -0.02em; line-height: 1.1; }
    .page-title p { font-size: 0.9rem; color: rgba(255, 255, 255, 0.85); margin: 0; font-weight: 500; }
    
    .search-form { display: flex; gap: 8px; }
    .search-input {
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 10px 20px;
        font-size: 0.95rem;
        width: 280px;
        outline: none;
        background: white;
        color: var(--primary);
        font-weight: 600;
        transition: all 0.2s;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .search-input:focus { 
        border-color: white; 
        box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.15); 
    }
    .search-btn {
        background: white; 
        color: var(--primary); 
        border: none;
        width: 44px;
        height: 44px;
        border-radius: 12px; 
        cursor: pointer;
        transition: all 0.2s;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .search-btn:hover { 
        background: #f8fafc; 
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }

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
    .action-btn:hover { border-color: #1b4a9a; color: #1b4a9a; background: #e8f0ff; }
    
    .pagination-wrapper { padding: 16px 24px; border-top: 1px solid #f1f5f9; }

    /* Stack Pattern for Mobile */
    @media screen and (max-width: 768px) {
        .page-header {
            padding: 20px;
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }
        .page-title {
            border-left: none;
            border-bottom: 2px solid rgba(255, 255, 255, 0.4);
            padding-left: 0;
            padding-bottom: 12px;
            align-items: center;
        }
        .search-form { width: 100%; }
        .search-input { flex: 1; width: auto; }

        .stack-card-table thead { display: none; }
        .stack-card-table, .stack-card-table tbody, .stack-card-table tr, .stack-card-table td {
            display: block;
            width: 100%;
        }
        .stack-card-table tr {
            margin-bottom: 20px;
            border: 1px solid #eef2f6;
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .stack-card-table td {
            text-align: right !important;
            padding: 14px 16px 14px 40% !important;
            position: relative;
            border-bottom: 1px solid #f8fafc;
            min-height: 48px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
        .stack-card-table td:last-child {
            border-bottom: none;
            background: #f8fafc;
            justify-content: center;
            padding-left: 16px !important;
        }
        .stack-card-table td::before {
            content: attr(data-label);
            position: absolute;
            left: 16px;
            width: 35%;
            padding-right: 10px;
            white-space: nowrap;
            text-align: left;
            font-weight: 800;
            color: #94a3b8;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            top: 50%;
            transform: translateY(-50%);
        }
        
        .user-profile { 
            justify-content: flex-end; 
            width: 100%;
        }
        .user-info { text-align: right; }
        .user-info .name { font-size: 0.85rem; line-height: 1.2; }
        .user-info .email { font-size: 0.7rem; opacity: 0.8; }
        .user-avatar { width: 32px; height: 32px; font-size: 0.85rem; }
        
        [data-label="Position"] {
            font-size: 0.8rem !important;
            line-height: 1.3;
            word-break: break-word;
        }
        
        .action-btn {
            width: 100%;
            justify-content: center;
            padding: 10px;
            background: white;
        }
    }

    /* Desktop Specific Fixes for Stack Card Table */
    @media screen and (min-width: 993px) {
        .stack-card-table td[data-label="Employee"] {
            flex: 1.5 !important;
            min-width: 250px !important;
        }
        .stack-card-table td[data-label="Position"] {
            flex: 1 !important;
        }
        .user-info {
            min-width: 0;
            flex: 1;
            overflow: hidden;
        }
        .user-info .email {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
    }
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
        <table class="users-table stack-card-table">
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
                    <td data-label="Employee">
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
                    <td data-label="Position" style="color: #475569; font-size: 0.9rem;">{{ $user->position ?? 'N/A' }}</td>
                    <td data-label="Credits Status">
                        @php
                            $hasCredits = \App\Models\LeaveCredit::where('user_id', $user->id)->exists();
                        @endphp
                        @if($hasCredits)
                            <span class="badge badge-allocated">Allocated</span>
                        @else
                            <span class="badge badge-pending">Pending</span>
                        @endif
                    </td>
                    <td data-label="Action" style="text-align: right;">
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
