@extends('layouts.sdo')

@section('title', 'Audit Logs')
@section('page-title', 'Leave Credit Audit Logs')

@push('styles')
<style>
    /* Card Container */
    .logs-container { max-width: 1024px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px; }
    
    .section-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    /* Section Headers */
    .section-header {
        padding: 16px 24px;
        border-bottom: 1px solid #eef2f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .header-orange { background: #fff7ed; border-bottom-color: #ffedd5; }
    .header-gray { background: #f8fafc; }

    .section-title {
        font-size: 1rem; font-weight: 700; color: #1e293b;
        display: flex; align-items: center; gap: 8px;
    }
    .text-orange { color: #c2410c; }
    .text-gray { color: #475569; }
    
    .badge-count {
        font-size: 0.75rem; font-weight: 700;
        padding: 2px 8px; border-radius: 12px;
    }
    .badge-orange { background: #ffedd5; color: #9a3412; }
    
    /* Request Item */
    .request-item {
        padding: 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        transition: background 0.2s;
    }
    .request-item:last-child { border-bottom: none; }
    .request-item:hover { background: #fffcf8; }

    .req-info h4 { margin: 0; font-size: 1rem; color: #1e293b; font-weight: 700; }
    .req-sub { font-size: 0.85rem; color: #64748b; margin-top: 4px; }
    .req-reason {
        margin-top: 8px; font-size: 0.85rem; color: #334155;
        background: #f8fafc; padding: 6px 10px; border-radius: 6px;
        border: 1px solid #e2e8f0; display: inline-block; font-style: italic;
    }
    .req-time { font-size: 0.75rem; color: #94a3b8; margin-top: 8px; }

    /* Buttons */
    .btn-group { display: flex; gap: 8px; }
    .btn-approve {
        background: #16a34a; color: white; border: none;
        padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 0.85rem;
        cursor: pointer; display: flex; align-items: center; gap: 6px;
        transition: background 0.2s;
    }
    .btn-approve:hover { background: #15803d; }
    
    .btn-reject {
        background: white; color: #dc2626; border: 1px solid #fee2e2;
        padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 0.85rem;
        cursor: pointer; display: flex; align-items: center; gap: 6px;
        transition: background 0.2s;
    }
    .btn-reject:hover { background: #fef2f2; border-color: #fecaca; }

    /* Log Item */
    .log-item {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: flex-start; gap: 16px;
    }
    .log-item:last-child { border-bottom: none; }
    .log-item:hover { background: #f8fafc; }

    .log-icon-circle {
        width: 36px; height: 36px; background: #e0f2fe; color: #0284c7;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 0.95rem;
    }

    .log-content { flex: 1; }
    .log-header { display: flex; justify-content: space-between; align-items: flex-start; }
    .log-desc { font-size: 0.95rem; color: #334155; line-height: 1.4; }
    .actor-name { font-weight: 700; color: #0f172a; }
    .action-badge { 
        font-size: 0.65rem; font-weight: 800; text-transform: uppercase;
        background: #f1f5f9; color: #475569; padding: 2px 6px; border-radius: 4px;
        margin: 0 4px; vertical-align: middle;
    }
    .log-time { font-size: 0.75rem; color: #94a3b8; font-family: monospace; white-space: nowrap; }
    
    .log-extra { font-size: 0.85rem; color: #64748b; margin-top: 4px; }
    .diff-box {
        margin-top: 8px; font-family: 'Consolas', monospace; font-size: 0.8rem;
        background: #f8fafc; border: 1px solid #e2e8f0; padding: 6px 10px; border-radius: 6px;
        display: inline-block;
    }
    .val-old { color: #ef4444; text-decoration: line-through; opacity: 0.7; }
    .val-arrow { color: #94a3b8; margin: 0 6px; font-size: 0.7rem; }
    .val-new { color: #16a34a; font-weight: 700; }

    .empty-state { text-align: center; padding: 40px; color: #94a3b8; }
    .empty-icon { font-size: 2rem; margin-bottom: 12px; color: #cbd5e1; }
</style>
@endpush

@section('content')
<div class="logs-container">

    <!-- Pending Requests Section -->
    <div class="section-card">
        <div class="section-header header-orange">
            <div class="section-title text-orange">
                <i class="fas fa-lock-open"></i> Pending Unlock Requests
            </div>
            <span class="badge-count badge-orange">{{ $requests->count() }} Pending</span>
        </div>
        
        @if($requests->count() > 0)
            <div class="requests-list">
                @foreach($requests as $req)
                <div class="request-item">
                    <div class="req-info">
                        <h4>{{ $req->user->full_name }}</h4>
                        <div class="req-sub">Requested by: <strong>{{ $req->requester->full_name }}</strong></div>
                        <div class="req-reason">"{{ $req->reason }}"</div>
                        <div class="req-time"><i class="far fa-clock mr-1"></i> {{ $req->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="btn-group">
                        <form action="{{ route('head-hr.requests.handle', $req->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="btn-approve">
                                <i class="fas fa-check"></i> Approve
                            </button>
                        </form>

                        <form action="{{ route('head-hr.requests.handle', $req->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn-reject">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-check-circle empty-icon"></i>
                <p>No pending requests at the moment.</p>
            </div>
        @endif
    </div>

    <!-- Logs Section -->
    <div class="section-card">
        <div class="section-header header-gray">
            <div class="section-title text-gray">
                <i class="fas fa-history"></i> Recent HR Actions
            </div>
            <span class="text-sm text-gray show-all-txt">Showing latest actions</span>
        </div>

        <div class="logs-list">
        @forelse($logs as $log)
            <div class="log-item">
                <div class="log-icon-circle">
                    @if($log->action == 'allocate') <i class="fas fa-plus"></i>
                    @elseif($log->action == 'deduct') <i class="fas fa-minus"></i>
                    @elseif($log->action == 'update') <i class="fas fa-pen"></i>
                    @else <i class="fas fa-info"></i> @endif
                </div>
                <div class="log-content">
                    <div class="log-header">
                        <div class="log-desc">
                            <span class="actor-name">{{ $log->actor->full_name ?? 'System' }}</span>
                            <span class="action-badge">{{ $log->action }}</span>
                            <span class="actor-name">{{ $log->targetUser->full_name ?? 'Unknown' }}</span>
                        </div>
                        <div class="log-time">{{ $log->created_at->diffForHumans() }}</div>
                    </div>

                    <div class="log-extra">
                        Targeting <strong>{{ $log->leave_type_name }}</strong>
                        @if($log->reason)
                             • <span class="italic">"{{ $log->reason }}"</span>
                        @endif
                    </div>

                    @if($log->previous_value !== null || $log->new_value !== null)
                    <div class="diff-box">
                        <span class="val-old">{{ $log->previous_value ?? '0.00' }}</span>
                        <i class="fas fa-arrow-right val-arrow"></i>
                        <span class="val-new">{{ $log->new_value ?? '0.00' }}</span>
                    </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-history empty-icon"></i>
                <p>No audit logs available yet.</p>
            </div>
        @endforelse
        </div>
        
        <div class="p-4 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection