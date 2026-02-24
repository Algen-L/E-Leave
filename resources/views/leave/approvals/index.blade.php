@extends('layouts.sdo')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/manage-users.css') }}">
    <style>
        /* Override grid for approval content needs */
        .user-card {
            grid-template-columns: 2fr 1.2fr 1.2fr 0.5fr 1fr 180px;
            align-items: center;
            border: 1px solid #f1f5f9;
            margin-bottom: 8px;
            background: white;
        }

        .user-card:hover {
            border-color: #3b82f6;
            background: #f8fafc;
            transform: translateY(-2px);
        }

        .approval-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 8px;
        }

        .btn-review {
            background: var(--primary-gradient, linear-gradient(135deg, #0f4c75 0%, #3282b8 100%));
            color: white;
            padding: 8px 18px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(15, 76, 117, 0.2);
            border: none;
        }

        .btn-review:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(15, 76, 117, 0.3);
            filter: brightness(1.1);
            color: white;
        }

        /* Scrollable Container */
        .approvals-scroll-area {
            max-height: 650px;
            overflow-y: auto;
            padding-right: 8px;
            margin-top: 10px;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f8fafc;
        }

        .approvals-scroll-area::-webkit-scrollbar {
            width: 6px;
        }

        .approvals-scroll-area::-webkit-scrollbar-track {
            background: #f8fafc;
            border-radius: 10px;
        }

        .approvals-scroll-area::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
            transition: all 0.2s;
        }

        .approvals-scroll-area::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .header-card .user-meta-label {
            font-size: 0.72rem;
            color: #94a3b8;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .leave-type-name {
            font-weight: 700;
            color: #1e293b;
            font-size: 0.95rem;
        }

        .badge-days {
            background: #e0f2fe;
            color: #0284c7;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 8px;
        }

        .status-badge-pending {
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #ffedd5;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        /* Responsive overrides */
        @media (max-width: 1024px) {
            .user-card {
                grid-template-columns: 1fr;
                gap: 16px;
                height: auto;
            }

            .header-card {
                display: none;
            }

            .user-details,
            .user-meta-value,
            .approval-actions {
                text-align: left;
                justify-content: flex-start;
            }

            .approval-actions {
                margin-top: 10px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid" style="padding: 20px;">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">{{ $title }}</h1>
            <div class="text-sm text-gray-500">
                Current User: {{ Auth::user()->full_name }} ({{ strtoupper(Auth::user()->role) }})
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="user-list">
            @if(count($applications) > 0)
                <div class="user-card header-card"
                    style="background: transparent; border: none; box-shadow: none; padding-bottom: 10px; opacity: 0.8;">
                    <div class="user-meta-label">Applicant</div>
                    <div class="user-meta-label">Leave Type</div>
                    <div class="user-meta-label">Duration</div>
                    <div class="user-meta-label">Days</div>
                    <div class="user-meta-label" style="text-align: center;">Status</div>
                    <div class="user-meta-label" style="text-align: right;">Actions</div>
                </div>

                <div class="approvals-scroll-area">
                    @foreach($applications as $app)
                        <div class="user-card">
                            <div class="user-info">
                                <div class="user-avatar">
                                    @if($app->user->profile_picture)
                                        <img src="{{ storage_url($app->user->profile_picture) }}" alt="{{ $app->user->full_name }}">
                                    @else
                                        {{ strtoupper(substr($app->user->full_name, 0, 2)) }}
                                    @endif
                                </div>
                                <div class="user-details">
                                    <div class="user-name">{{ $app->user->full_name }}</div>
                                    <div class="user-email">{{ $app->user->position }}</div>
                                </div>
                            </div>

                            <div>
                                <span class="user-meta-label">Leave Type</span>
                                <span class="leave-type-name">{{ $app->leaveType->type_name }}</span>
                                <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 4px; font-weight: 600;">
                                    Filed: {{ $app->date_filing->format('M d, Y') }}
                                </div>
                            </div>

                            <div>
                                <span class="user-meta-label">Dates</span>
                                <span class="user-meta-value">
                                    @if($app->start_date && $app->end_date)
                                        {{ $app->start_date->format('M d') }} - {{ $app->end_date->format('M d, Y') }}
                                    @else
                                        Recall dates
                                    @endif
                                </span>
                            </div>

                            <div>
                                <span class="user-meta-label">Days</span>
                                <span class="badge-days">{{ $app->days_applied }}</span>
                            </div>

                            <div style="text-align: center;">
                                <span class="user-meta-label">Status</span>
                                <span class="status-badge-pending">
                                    {{ $app->status }}
                                </span>
                            </div>

                            <div class="approval-actions">
                                <a href="{{ route('user.leave.approvals.show', $app->id) }}" class="btn-review">
                                    <i class="fas fa-eye"></i> Review Application
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3>No Pending Approvals</h3>
                    <p>You are all caught up!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="modal-backdrop"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center;">
        <div class="modal-content"
            style="background: white; border-radius: 16px; width: 400px; max-width: 90%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
            <div class="modal-header">
                <h3 style="font-size: 1.1rem; font-weight: 700;">Disapprove Application</h3>
            </div>
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <p style="margin-bottom: 12px; color: #64748b; font-size: 0.9rem;">Please provide a reason for
                        disapproval.</p>
                    <textarea name="remarks" class="form-control" rows="4" required placeholder="Reason for rejection..."
                        style="height: auto; padding-top: 12px;"></textarea>
                </div>
                <div class="modal-footer" style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeRejectModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-danger">Disapprove</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function openRejectModal(id) {
                const form = document.getElementById('rejectForm');
                form.action = "/user/leave/approvals/" + id + "/reject";
                const modal = document.getElementById('rejectModal');
                modal.style.display = 'flex';
            }

            function closeRejectModal() {
                document.getElementById('rejectModal').style.display = 'none';
            }

            // Close modal when clicking outside
            window.onclick = function (event) {
                const modal = document.getElementById('rejectModal');
                if (event.target == modal) {
                    closeRejectModal();
                }
            }
        </script>
    @endpush
@endsection