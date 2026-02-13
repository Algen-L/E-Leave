@extends('layouts.sdo')

@section('title', 'My Applications')
@section('page-title', 'My Applications')

@push('styles')
<style>
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }
    
    .summary-card {
        background: white;
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 16px;
        border-top: 4px solid var(--primary);
    }
    
    .summary-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .summary-info {
        display: flex;
        flex-direction: column;
    }
    
    .summary-label {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 600;
    }
    
    .summary-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
    }
    
    .history-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .history-header {
        padding: 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }
    
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table th {
        text-align: left;
        padding: 16px 24px;
        background: #f8fafc;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.05em;
    }
    
    .table td {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
    }
    
    .status-pending { background: #fff7ed; color: #c2410c; }
    .status-approved { background: #f0fdf4; color: #15803d; }
    .status-disapproved { background: #fef2f2; color: #b91c1c; }
    
    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        background: #f1f5f9;
        transition: all 0.2s;
    }
    
    .action-btn:hover {
        background: #e2e8f0;
        color: var(--primary);
    }
    
    .action-btn.download-form {
        color: #2563eb;
        background: #eff6ff;
    }
    
    .action-btn.download-form:hover {
        background: #dbeafe;
    }
</style>
@endpush

@section('content')
<div class="summary-grid">
    <div class="summary-card">
        <div class="summary-icon" style="background: rgba(15, 76, 117, 0.1); color: var(--primary);">
            <i class="fas fa-file-invoice"></i>
        </div>
        <div class="summary-info">
            <span class="summary-label">Total Applications</span>
            <span class="summary-value">{{ $stats['total'] }}</span>
        </div>
    </div>
    <div class="summary-card" style="border-top-color: #16a34a;">
        <div class="summary-icon" style="background: rgba(22, 163, 74, 0.1); color: #16a34a;">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="summary-info">
            <span class="summary-label">Approved</span>
            <span class="summary-value">{{ $stats['approved'] }}</span>
        </div>
    </div>
    <div class="summary-card" style="border-top-color: #d97706;">
        <div class="summary-icon" style="background: rgba(217, 119, 6, 0.1); color: #d97706;">
            <i class="fas fa-clock"></i>
        </div>
        <div class="summary-info">
            <span class="summary-label">Pending</span>
            <span class="summary-value">{{ $stats['pending'] }}</span>
        </div>
    </div>
</div>

<div class="history-card">
    <div class="history-header">
        <h2 class="font-bold text-lg"><i class="fas fa-history mr-2"></i> Application History</h2>
        <a href="{{ route('user.leave.apply') }}" class="btn-save px-4 py-2">
            <i class="fas fa-plus mr-2"></i> Apply New
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Date Filed</th>
                    <th>Leave Type</th>
                    <th>Inclusive Dates</th>
                    <th>Days</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                <tr>
                    <td>
                        <div class="font-bold">{{ $app->date_filing->format('M d, Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $app->created_at->format('h:i A') }}</div>
                    </td>
                    <td>{{ $app->leaveType->type_name }}</td>
                    <td>
                        {{ $app->start_date->format('M d') }} - {{ $app->end_date->format('M d, Y') }}
                    </td>
                    <td>{{ $app->days_applied }}</td>
                    <td>
                        <span class="status-badge status-{{ strtolower($app->status) }}">
                            {{ $app->status }}
                        </span>
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('user.leave.form6', $app->id) }}" class="action-btn download-form text-blue-600" title="Download Form 6 (Word)">
                                <i class="fas fa-file-word fa-lg"></i>
                            </a>
                            <a href="{{ route('user.leave.form6', ['id' => $app->id, 'format' => 'pdf']) }}" class="action-btn download-pdf text-red-600" title="Save as PDF">
                                <i class="fas fa-file-pdf fa-lg"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-500">
                        No applications found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
