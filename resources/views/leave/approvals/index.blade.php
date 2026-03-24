@extends('layouts.sdo')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/manage-users.css') }}">
    <style>
        .approvals-premium {
            animation: fadeIn 0.4s ease-out;
            font-family: 'Plus Jakarta Sans', sans-serif;
            position: relative;
        }

        /* Header Hero Section Update */
        .approvals-header-hero {
            position: relative;
            padding: 35px 40px 24px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
        }

        .approvals-header-hero::before {
            display: none;
        }

        .header-title-main {
            font-size: 2.8rem;
            font-weight: 900;
            color: #1e293b;
            letter-spacing: -0.03em;
            line-height: 1;
            position: relative;
            z-index: 1;
        }

        .header-title-accent {
            color: #f97316;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
        }

        .header-title-accent::after {
            content: '';
            position: absolute;
            bottom: 8px;
            left: 0;
            width: 100%;
            height: 12px;
            background: rgba(249, 115, 22, 0.1);
            z-index: -1;
            border-radius: 4px;
        }

        .officer-badge-premium {
            background: white;
            padding: 12px 24px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 15px 35px -10px rgba(15, 76, 117, 0.15);
            border: 1px solid rgba(15, 76, 117, 0.1);
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
        }

        .officer-badge-premium:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 40px -5px rgba(15, 76, 117, 0.2);
        }

        .officer-icon-wrapper {
            width: 50px;
            height: 50px;
            background: var(--primary-gradient);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.4rem;
            box-shadow: 0 8px 15px rgba(15, 76, 117, 0.2);
        }

        .officer-name-highlight {
            color: #0f4c75;
            font-size: 1.1rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        /* Glassmorphism User Card (Compact Flex Layout) */
        .user-card {
            display: grid;
            grid-template-columns: 2.2fr 1.2fr 1.2fr 1fr 1fr;
            gap: 24px;
            align-items: center;
            border: 1px solid rgba(226, 232, 240, 0.7);
            margin-bottom: 12px;
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 16px !important;
            padding: 16px 24px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
            width: 100%;
        }

        @media (max-width: 1200px) {
            .user-card {
                display: flex;
                flex-wrap: wrap;
                grid-template-columns: none;
            }
        }

        .user-card:hover {
            border-color: var(--primary) !important;
            background: white !important;
            transform: translateY(-3px);
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.1) !important;
        }

        /* Sequential Animation */
        .approvals-scroll-area .user-card {
            opacity: 0;
            animation: backInDown 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @foreach(range(1, 20) as $i)
            .approvals-scroll-area .user-card:nth-child({{ $i }}) {
                animation-delay: {{ $i * 0.1 }}s;
            }
        @endforeach

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

        .approval-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
        }

        .btn-review {
            background: var(--primary-gradient, linear-gradient(135deg, #0f4c75 0%, #3282b8 100%));
            color: white;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(15, 76, 117, 0.2);
            border: none;
        }

        .btn-review:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(15, 76, 117, 0.3);
            filter: brightness(1.1);
            color: white;
        }

        /* Scrollable Container */
        .approvals-scroll-area {
            max-height: calc(100vh - 280px);
            overflow-y: auto;
            padding: 10px 10px 40px 10px;
            margin: 0 -10px;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .approvals-scroll-area::-webkit-scrollbar {
            width: 5px;
        }

        .approvals-scroll-area::-webkit-scrollbar-track {
            background: transparent;
        }

        .approvals-scroll-area::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .header-card .user-meta-label {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0 10px;
        }

        .user-card .user-name {
            font-size: 1.05rem !important;
            font-weight: 800 !important;
            color: #1e293b !important;
            letter-spacing: -0.01em;
        }

        .leave-type-name {
            font-weight: 700;
            color: var(--primary);
            font-size: 0.95rem;
            display: block;
        }

        .badge-days {
            background: rgba(14, 165, 233, 0.1);
            color: #0369a1;
            font-weight: 800;
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 0.85rem;
            display: inline-block;
        }

        .status-badge-pending {
            background: rgba(249, 115, 22, 0.1);
            color: #ea580c;
            border: 1px solid rgba(249, 115, 22, 0.2);
            font-weight: 800;
            padding: 6px 14px;
            border-radius: 99px;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-badge-pending::before {
            content: '';
            width: 8px;
            height: 8px;
            background: #f97316;
            border-radius: 50%;
            display: inline-block;
            animation: pulse-orange 2s infinite;
        }

        @keyframes pulse-orange {
            0% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.4); }
            70% { box-shadow: 0 0 0 8px rgba(249, 115, 22, 0); }
            100% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0); }
        }

        /* Modal Enhancements */
        .modal-content {
            border: none !important;
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.95) !important;
            border-radius: 24px !important;
            overflow: hidden;
        }

        .modal-header {
            padding: 24px 30px !important;
            border-bottom: 1px solid #f1f5f9 !important;
            background: #f8fafc;
        }

        .modal-body {
            padding: 30px !important;
        }

        .modal-footer {
            padding: 20px 30px !important;
            border-top: 1px solid #f1f5f9 !important;
            background: #f8fafc;
        }

        .form-control {
            border-radius: 14px !important;
            border: 2px solid #f1f5f9 !important;
            padding: 12px 16px !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-size: 0.9rem !important;
            transition: all 0.2s !important;
        }

        .form-control:focus {
            border-color: #f97316 !important;
            box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1) !important;
            background: white !important;
        }
        .tab-toggle-container {
            display: inline-flex;
            background: #f1f5f9;
            padding: 6px;
            border-radius: 16px;
            gap: 5px;
            margin-top: 20px;
        }

        .tab-btn {
            padding: 10px 24px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            text-decoration: none !important;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .tab-btn.active {
            background: white;
            color: #0f4c75;
            box-shadow: 0 4px 15px rgba(15, 76, 117, 0.1);
        }

        .tab-btn:not(.active):hover {
            color: #1e293b;
            background: rgba(255,255,255,0.8);
            transform: translateY(-1px);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid approvals-premium" style="padding: 24px;">
        <div class="unified-approvals-container animate__animated animate__fadeInUp" style="background: white; border-radius: 24px; box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05); border: 1px solid rgba(226, 232, 240, 0.8); overflow: hidden;">
            <!-- Refined Header Hero -->
            <div class="approvals-header-hero">
            <div class="header-text-group">
                <h1 class="header-title-main">
                    @php
                        $titleParts = explode(' ', $title);
                        $lastWord = array_pop($titleParts);
                        $firstWords = implode(' ', $titleParts);
                    @endphp
                    {{ $firstWords }} <span class="header-title-accent">{{ $lastWord }}</span>
                </h1>
                <p class="text-slate-500 mt-3 font-semibold text-lg max-w-md leading-relaxed">
                    Efficiency starts here. Review and process leave applications with precision.
                </p>

                <div class="tab-toggle-container animate__animated animate__fadeInLeft" style="animation-delay: 0.2s;">
                    <a href="{{ route('user.leave.approvals', ['tab' => 'pending']) }}" class="tab-btn {{ ($tab ?? 'pending') !== 'processed' ? 'active' : '' }}">
                        <i class="fas fa-clock"></i> Pending ({{ ($tab ?? 'pending') !== 'processed' ? count($applications) : '...' }})
                    </a>
                    <a href="{{ route('user.leave.approvals', ['tab' => 'processed']) }}" class="tab-btn {{ ($tab ?? 'pending') === 'processed' ? 'active' : '' }}">
                        <i class="fas fa-check-circle"></i> Processed
                    </a>
                </div>
            </div>
            
            <div class="officer-badge-premium">
                <div class="officer-icon-wrapper">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div>
                    <div class="text-[0.68rem] uppercase font-black text-slate-400 tracking-widest leading-none mb-1">Authenticated Officer</div>
                    <div class="officer-name-highlight">{{ Auth::user()->full_name }}</div>
                </div>
            </div>
            </div>
        </div>

        <div style="padding: 24px 40px 40px 40px; background: rgba(248, 250, 252, 0.4);">
            <!-- Search Bar Section -->
            <div class="search-container" style="margin-bottom: 24px; position: relative; max-width: 600px;">
            <i class="fas fa-search" style="position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.1rem; z-index: 5;"></i>
            <input type="text" id="approvalSearchInput" class="form-control" placeholder="Search by applicant name, role, or leave type..." style="width: 100%; padding-left: 48px !important; border-radius: 16px; height: 54px; font-size: 0.95rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); border: 1px solid rgba(226, 232, 240, 0.8);">
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
                <div class="approvals-scroll-area">
                    @foreach($applications as $app)
                        <div class="user-card">
                            <div class="user-info" style="display: flex; align-items: center; gap: 15px;">
                                <div class="user-avatar" style="width: 45px; height: 45px; border-radius: 12px; background: #e0f2fe; color: #0369a1; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.1rem; flex-shrink: 0;">
                                    @if($app->user->profile_picture)
                                        <img src="{{ storage_url($app->user->profile_picture) }}" alt="{{ $app->user->full_name }}" style="width: 100%; height: 100%; border-radius: 12px; object-fit: cover;">
                                    @else
                                        {{ strtoupper(substr($app->user->full_name, 0, 2)) }}
                                    @endif
                                </div>
                                <div class="user-details">
                                    <div class="user-name" style="line-height: 1.2; margin-bottom: 2px;">{{ $app->user->full_name }}</div>
                                    <div class="user-email" style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase;">{{ str_replace('_', ' ', $app->user->role) }}</div>
                                </div>
                            </div>

                            <div style="flex: 1; min-width: 200px;">
                                <span class="user-meta-label">Type of Leave</span>
                                <span class="leave-type-name"><i class="fas fa-file-alt mr-1 text-primary/60"></i> {{ $app->leaveType->type_name }}</span>
                                <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 4px; font-weight: 700; display: flex; align-items: center; gap: 5px;">
                                    <i class="far fa-clock"></i> Filed: {{ $app->date_filing->format('M d, Y') }}
                                </div>
                            </div>

                            <div style="flex: 1; min-width: 150px;">
                                <span class="user-meta-label">Total Duration</span>
                                <div class="mt-2">
                                    <span class="badge-days" style="padding: 8px 16px; font-size: 1.1rem; box-shadow: 0 4px 6px -1px rgba(14, 165, 233, 0.1);">
                                        {{ (float) $app->days_applied }} Day(s)
                                    </span>
                                </div>
                            </div>

                            <div style="text-align: right;">
                                <span class="user-meta-label" style="display: block; margin-bottom: 4px; text-align: right;">Status</span>
                                <span class="status-badge-pending" style="white-space: nowrap;">
                                    {{ $app->status }}
                                </span>
                            </div>

                            <div class="approval-actions" style="display: flex; justify-content: flex-end;">
                                <a href="{{ route('user.leave.approvals.show', $app->id) }}" class="btn-review" style="white-space: nowrap;">
                                    <i class="fas {{ ($tab ?? 'pending') === 'processed' ? 'fa-eye' : 'fa-pen-nib' }}"></i>
                                    {{ ($tab ?? 'pending') === 'processed' ? 'View Details' : 'Review Application' }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                </div>
            @else
            </div>
            <div class="empty-state animate__animated animate__fadeInUp" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(10px); border: 2px dashed rgba(15, 76, 117, 0.2); border-radius: 24px; padding: 100px 40px; text-align: center; margin-top: 20px;">
                <div class="empty-state-icon" style="background: white; border-radius: 50%; width: 80px; height: 80px; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 10px 20px rgba(0,0,0,0.05); margin-bottom: 20px;">
                    <i class="fas fa-check-double text-blue-500 text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-800">No {{ ($tab ?? 'pending') === 'processed' ? 'Processed' : 'Pending' }} Approvals</h3>
                <p class="text-slate-500 font-medium mt-2">
                    @if(($tab ?? 'pending') === 'processed')
                        You haven't processed any applications yet.
                    @else
                        Everything is handled! We'll notify you when new applications arrive.
                    @endif
                </p>
                @if(($tab ?? 'pending') === 'processed')
                    <a href="{{ route('user.leave.approvals', ['tab' => 'pending']) }}" class="btn-review mt-6" style="display: inline-flex; width: auto;">
                        <i class="fas fa-arrow-left"></i> Back to Pending
                    </a>
                @endif
            </div>
            @endif
        </div> <!-- End inner padding div -->
        </div> <!-- End unified-approvals-container -->
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

            // Search Functionality
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('approvalSearchInput');
                if (searchInput) {
                    searchInput.addEventListener('input', function(e) {
                        const searchTerm = e.target.value.toLowerCase();
                        const cards = document.querySelectorAll('.approvals-scroll-area .user-card');
                        
                        cards.forEach(card => {
                            const textContent = card.innerText.toLowerCase();
                            if (textContent.includes(searchTerm)) {
                                card.style.display = 'flex';
                            } else {
                                card.style.display = 'none';
                            }
                        });
                    });
                }
            });

        </script>
    @endpush
@endsection