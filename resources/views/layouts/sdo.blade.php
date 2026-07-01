
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Leave Application System')</title>

    <!-- Prevent Sidebar Flash/Animation -->
    <script>
        (function () {
            // Force scroll to top on page reload
            if (history.scrollRestoration) {
                history.scrollRestoration = 'manual';
            }
            window.scrollTo(0, 0);

            const collapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (collapsed && window.innerWidth > 992) {
                document.documentElement.classList.add('sidebar-initial-collapsed');
            }
        })();
    </script>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}?v={{ time() }}">
    <style>
        #toast-container {
            position: fixed !important;
            top: 20px !important;
            right: 20px !important;
            z-index: 1000000 !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 12px !important;
            pointer-events: none !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        .toast {
            z-index: 1000001 !important;
            background: white !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
            pointer-events: auto !important;
        }
        .sidebar-section {
            display: flex;
            flex-direction: column;
        }
        .sidebar-section-header {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #64748b;
            padding: 16px 20px 8px;
            opacity: 0.8;
            font-family: 'Plus Jakarta Sans', sans-serif;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            user-select: none;
            transition: color 0.2s;
        }
        .sidebar-section-header:hover {
            color: #ffffff;
        }
        .toggle-chevron {
            font-size: 0.8rem;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: #64748b;
        }
        .sidebar-section-content {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.2s ease-out;
            max-height: 500px;
            opacity: 1;
        }
        .sidebar-section.collapsed .sidebar-section-content {
            max-height: 0 !important;
            opacity: 0 !important;
            pointer-events: none;
        }
        .sidebar-section.collapsed .toggle-chevron {
            transform: rotate(-90deg);
        }
        .nav-divider {
            height: 1px;
            background: #e2e8f0;
            margin: 8px 20px;
            opacity: 0.5;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="app-layout {{ session('sidebar_collapsed') ? 'sidebar-collapsed' : '' }}">
        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
        <!-- Sidebar -->
        <aside class="sidebar" id="mainSidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-img" onerror="this.style.display='none'">
                    <div class="logo-text">
                        <span class="logo-title">E-Leave</span>
                        <span class="logo-subtitle">Application System</span>
                    </div>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                @php
                    $currentUser = auth()->user();
                    $userRoles = $currentUser->roles->pluck('name')->toArray();
                    $hasOnlyUser = count($userRoles) === 1 && in_array('user', $userRoles);
                    
                    // Count pending approvals for officers
                    $bossIds = \App\Models\User::where('secretary_id', $currentUser->id)->pluck('id')->toArray();
                    $newPendingCount = \App\Models\LeaveApplication::where('is_viewed', false)
                        ->where(function($query) use ($currentUser, $bossIds) {
                            // HR Review
                            if ($currentUser->hasRole(['hr', 'head_hr', 'hr_review_officer', 'super_admin'])) {
                                $query->orWhere('status', 'Pending HR');
                            }
                            // Recommending
                            $query->orWhere(function($q) use ($currentUser) {
                                $q->where('recommending_officer_id', $currentUser->id)
                                  ->where('status', 'Pending Recommending');
                            });
                            // Final Approver
                            $query->orWhere(function($q) use ($currentUser, $bossIds) {
                                $q->whereIn('approving_officer_id', array_merge([$currentUser->id], $bossIds))
                                  ->where('status', 'Pending Approval');
                            });
                        })->count();
                @endphp

                <!-- 1. USER SECTION (Visible if user has the user role) -->
                @if($currentUser->hasRole('user'))
                    @if(!$hasOnlyUser)
                        <div class="sidebar-section" id="section-user">
                            <div class="sidebar-section-header" onclick="toggleSection('user')">
                                <span>USER PANEL</span>
                                <i class="fas fa-chevron-down toggle-chevron"></i>
                            </div>
                            <div class="sidebar-section-content">
                    @endif

                    @php
                        $homeRoute = $currentUser->isHigherRole() ? 'user.dashboard' : 'user.home';
                        $homeText = $currentUser->isHigherRole() ? 'Dashboard' : 'Home';
                    @endphp
                    <a href="{{ route($homeRoute) }}" class="nav-item {{ request()->routeIs($homeRoute) ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-home"></i></div>
                        <span class="nav-text">{{ $homeText }}</span>
                    </a>
                    
                    <a href="{{ route('user.leave.apply') }}" class="nav-item {{ request()->routeIs('user.leave.apply') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-file-signature"></i></div>
                        <span class="nav-text">Apply for Leave</span>
                    </a>
                    <a href="{{ route('user.leave.history') }}" class="nav-item {{ request()->routeIs('user.leave.history') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-folder-open"></i></div>
                        <span class="nav-text">My Applications</span>
                    </a>

                    @php
                        $profileRoute = $currentUser->isAdmin() || $currentUser->isSuperAdmin() ? 'admin.profile' : (($currentUser->isHR() || $currentUser->isHeadHR()) ? 'hr.profile' : 'user.profile');
                    @endphp
                    <a href="{{ route($profileRoute) }}" class="nav-item {{ request()->routeIs($profileRoute) ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-user-cog"></i></div>
                        <span class="nav-text">My Profile</span>
                    </a>
                    <a href="{{ route('user.help') }}" class="nav-item {{ request()->routeIs('user.help') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-question-circle"></i></div>
                        <span class="nav-text">Need Help?</span>
                    </a>

                    @if(!$hasOnlyUser)
                            </div>
                        </div>
                        <script>
                            (function() {
                                const collapsed = localStorage.getItem('sidebar_section_user_collapsed') === 'true';
                                if (collapsed) document.getElementById('section-user').classList.add('collapsed');
                            })();
                        </script>
                    @endif
                @endif

                <!-- 2. SUPER ADMIN / ADMIN SECTION (Also visible to HR for user management tasks) -->
                @if($currentUser->hasRole(['super_admin', 'admin', 'head_hr', 'hr', 'hr_review_officer']))
                    <div class="nav-divider"></div>
                    <div class="sidebar-section" id="section-admin">
                        <div class="sidebar-section-header" onclick="toggleSection('admin')">
                            <span>ADMINISTRATOR</span>
                            <i class="fas fa-chevron-down toggle-chevron"></i>
                        </div>
                        <div class="sidebar-section-content">
                            @if($currentUser->hasRole(['super_admin', 'admin']))
                                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                    <div class="nav-icon"><i class="fas fa-chart-line"></i></div>
                                    <span class="nav-text">Admin Dashboard</span>
                                </a>
                            @endif
                            <a href="{{ route('admin.manage-users') }}" class="nav-item {{ request()->routeIs('admin.manage-users*') ? 'active' : '' }}">
                                <div class="nav-icon"><i class="fas fa-users"></i></div>
                                <span class="nav-text">Manage Users</span>
                            </a>
                            <a href="{{ route('admin.register-user') }}" class="nav-item {{ request()->routeIs('admin.register-user') ? 'active' : '' }}">
                                <div class="nav-icon"><i class="fas fa-user-plus"></i></div>
                                <span class="nav-text">Register User</span>
                            </a>
                            <a href="{{ route('admin.activity-logs') }}" class="nav-item {{ request()->routeIs('admin.activity-logs') ? 'active' : '' }}">
                                <div class="nav-icon"><i class="fas fa-list-alt"></i></div>
                                <span class="nav-text">Activity Logs</span>
                            </a>
                            @if($currentUser->hasRole('super_admin'))
                                <a href="{{ route('admin.auth-reset-management') }}" class="nav-item {{ request()->routeIs('admin.auth-reset-management') ? 'active' : '' }}">
                                    <div class="nav-icon"><i class="fas fa-key"></i></div>
                                    <span class="nav-text">Auth Reset</span>
                                </a>
                                <a href="{{ route('admin.signatories') }}" class="nav-item {{ request()->routeIs('admin.signatories') ? 'active' : '' }}">
                                    <div class="nav-icon"><i class="fas fa-building"></i></div>
                                    <span class="nav-text">Offices & Signatories</span>
                                </a>
                            @endif
                        </div>
                    </div>
                    <script>
                        (function() {
                            const collapsed = localStorage.getItem('sidebar_section_admin_collapsed') === 'true';
                            if (collapsed) document.getElementById('section-admin').classList.add('collapsed');
                        })();
                    </script>
                @endif

                <!-- 3. HR / HEAD HR SECTION -->
                @if($currentUser->hasRole(['hr', 'head_hr']))
                    <div class="nav-divider"></div>
                    <div class="sidebar-section" id="section-hr">
                        <div class="sidebar-section-header" onclick="toggleSection('hr')">
                            <span>HR MANAGEMENT</span>
                            <i class="fas fa-chevron-down toggle-chevron"></i>
                        </div>
                        <div class="sidebar-section-content">
                            @php
                                $hrDashboardRoute = $currentUser->hasRole('head_hr') ? 'head-hr.dashboard' : 'hr.dashboard';
                            @endphp
                            <a href="{{ route($hrDashboardRoute) }}" class="nav-item {{ request()->routeIs($hrDashboardRoute) ? 'active' : '' }}">
                                <div class="nav-icon"><i class="fas fa-user-tie"></i></div>
                                <span class="nav-text">HR Dashboard</span>
                            </a>
                            <a href="{{ route('user.leave.approvals') }}" class="nav-item {{ request()->routeIs('user.leave.approvals') ? 'active' : '' }}">
                                <div class="nav-icon"><i class="fas fa-check-double"></i></div>
                                <span class="nav-text">Pending Approvals</span>
                                @if($newPendingCount > 0)
                                    <span class="badge rounded-pill ms-auto animate__animated animate__pulse animate__infinite" 
                                        style="font-size: 0.65rem; padding: 0.35em 0.65em; background-color: #ef4444 !important; color: white !important; font-weight: 800 !important; box-shadow: 0 2px 5px rgba(239, 68, 68, 0.4);">
                                        {{ $newPendingCount }}
                                    </span>
                                @endif
                            </a>
                            <a href="{{ route('hr-staff.manage-credits') }}" class="nav-item {{ request()->routeIs('hr-staff.manage-credits*') ? 'active' : '' }}">
                                <div class="nav-icon"><i class="fas fa-coins"></i></div>
                                <span class="nav-text">Manage Credits</span>
                            </a>
                            @if($currentUser->hasRole('head_hr'))
                                <a href="{{ route('head-hr.leave-policies') }}" class="nav-item {{ request()->routeIs('head-hr.leave-policies*') ? 'active' : '' }}">
                                    <div class="nav-icon"><i class="fas fa-sliders-h"></i></div>
                                    <span class="nav-text">Credit Policies</span>
                                </a>
                            @endif
                        </div>
                    </div>
                    <script>
                        (function() {
                            const collapsed = localStorage.getItem('sidebar_section_hr_collapsed') === 'true';
                            if (collapsed) document.getElementById('section-hr').classList.add('collapsed');
                        })();
                    </script>
                @endif

                <!-- 4. HR REVIEW OFFICER SECTION -->
                @if($currentUser->hasRole('hr_review_officer'))
                    <div class="nav-divider"></div>
                    <div class="sidebar-section" id="section-hr_review_officer">
                        <div class="sidebar-section-header" onclick="toggleSection('hr_review_officer')">
                            <span>HR REVIEW OFFICER</span>
                            <i class="fas fa-chevron-down toggle-chevron"></i>
                        </div>
                        <div class="sidebar-section-content">
                            <a href="{{ route('hr.dashboard') }}" class="nav-item {{ request()->routeIs('hr.dashboard') ? 'active' : '' }}">
                                <div class="nav-icon"><i class="fas fa-user-shield"></i></div>
                                <span class="nav-text">HR Dashboard</span>
                            </a>
                            <a href="{{ route('user.leave.approvals') }}" class="nav-item {{ request()->routeIs('user.leave.approvals') ? 'active' : '' }}">
                                <div class="nav-icon"><i class="fas fa-check-double"></i></div>
                                <span class="nav-text">Pending Approvals</span>
                                @if($newPendingCount > 0)
                                    <span class="badge rounded-pill ms-auto animate__animated animate__pulse animate__infinite" 
                                        style="font-size: 0.65rem; padding: 0.35em 0.65em; background-color: #ef4444 !important; color: white !important; font-weight: 800 !important; box-shadow: 0 2px 5px rgba(239, 68, 68, 0.4);">
                                        {{ $newPendingCount }}
                                    </span>
                                @endif
                            </a>
                            <a href="{{ route('hr-staff.manage-credits') }}" class="nav-item {{ request()->routeIs('hr-staff.manage-credits*') ? 'active' : '' }}">
                                <div class="nav-icon"><i class="fas fa-coins"></i></div>
                                <span class="nav-text">Manage Credits</span>
                            </a>
                        </div>
                    </div>
                    <script>
                        (function() {
                            const collapsed = localStorage.getItem('sidebar_section_hr_review_officer_collapsed') === 'true';
                            if (collapsed) document.getElementById('section-hr_review_officer').classList.add('collapsed');
                        })();
                    </script>
                @endif

                <!-- 5. RECORD PERSONNEL SECTION -->
                @if($currentUser->hasRole('record_personnel'))
                    <div class="nav-divider"></div>
                    <div class="sidebar-section" id="section-record_personnel">
                        <div class="sidebar-section-header" onclick="toggleSection('record_personnel')">
                            <span>RECORD PERSONNEL</span>
                            <i class="fas fa-chevron-down toggle-chevron"></i>
                        </div>
                        <div class="sidebar-section-content">
                            <a href="{{ route('records.dashboard') }}" class="nav-item {{ request()->routeIs('records.dashboard') ? 'active' : '' }}">
                                <div class="nav-icon"><i class="fas fa-archive"></i></div>
                                <span class="nav-text">Records Dashboard</span>
                            </a>
                            <a href="{{ route('records.index') }}" class="nav-item {{ request()->routeIs('records.index*') ? 'active' : '' }}">
                                <div class="nav-icon"><i class="fas fa-folder-open"></i></div>
                                <span class="nav-text">Application Records</span>
                            </a>
                        </div>
                    </div>
                    <script>
                        (function() {
                            const collapsed = localStorage.getItem('sidebar_section_record_personnel_collapsed') === 'true';
                            if (collapsed) document.getElementById('section-record_personnel').classList.add('collapsed');
                        })();
                    </script>
                @endif

                <!-- 6. HIGH LEVEL ROLES SECTIONS (SDS, ASDS, SGOD CHIEF, CID CHIEF, AO) -->
                @foreach(['sds', 'asds', 'sgod_chief', 'cid_chief', 'ao'] as $hlRole)
                    @if($currentUser->hasRole($hlRole))
                        <div class="nav-divider"></div>
                        <div class="sidebar-section" id="section-{{ $hlRole }}">
                            <div class="sidebar-section-header" onclick="toggleSection('{{ $hlRole }}')">
                                <span>{{ strtoupper(str_replace('_', ' ', $hlRole)) }} PANEL</span>
                                <i class="fas fa-chevron-down toggle-chevron"></i>
                            </div>
                            <div class="sidebar-section-content">
                                <a href="{{ route('user.dashboard') }}" class="nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                                    <div class="nav-icon"><i class="fas fa-chart-bar"></i></div>
                                    <span class="nav-text">Dashboard</span>
                                </a>
                                <a href="{{ route('user.leave.approvals') }}" class="nav-item {{ request()->routeIs('user.leave.approvals') ? 'active' : '' }}">
                                    <div class="nav-icon"><i class="fas fa-check-double"></i></div>
                                    <span class="nav-text">Pending Approvals</span>
                                    @if($newPendingCount > 0)
                                        <span class="badge rounded-pill ms-auto animate__animated animate__pulse animate__infinite" 
                                            style="font-size: 0.65rem; padding: 0.35em 0.65em; background-color: #ef4444 !important; color: white !important; font-weight: 800 !important; box-shadow: 0 2px 5px rgba(239, 68, 68, 0.4);">
                                            {{ $newPendingCount }}
                                        </span>
                                    @endif
                                </a>
                            </div>
                        </div>
                        <script>
                            (function() {
                                const collapsed = localStorage.getItem('sidebar_section_{{ $hlRole }}_collapsed') === 'true';
                                if (collapsed) document.getElementById('section-{{ $hlRole }}').classList.add('collapsed');
                            })();
                        </script>
                    @endif
                @endforeach
            </nav>
            
            <div class="sidebar-footer">
                @php
                    $footerProfileRoute = auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() ? route('admin.profile') : (auth()->user()->isHR() || auth()->user()->isHeadHR() ? route('hr.profile') : route('user.profile'));
                @endphp
                <a href="{{ $footerProfileRoute }}" class="user-info-link">
                    <div class="user-info">
                        @if(auth()->user()->profile_picture)
                            <img src="{{ storage_url(auth()->user()->profile_picture) }}" alt="Profile" class="user-avatar">
                        @else
                            <div class="user-avatar-placeholder">
                                {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="user-details">
                            <div class="user-name">{{ auth()->user()->full_name }}</div>
                            <div class="user-role">{{ strtoupper(auth()->user()->position ?: 'No Position Set') }}</div>
                        </div>
                    </div>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="logout-btn" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <div class="top-bar-left">
                    <button class="mobile-menu-toggle" id="toggleSidebar">
                        <i class="bi bi-grid-fill"></i>
                    </button>
                    <div class="breadcrumb">
                        <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                    </div>
                </div>
                <div class="top-bar-right" style="display: flex; align-items: center; gap: 16px;">
                    @if(auth()->check() && isset($notifications))
                        <div class="notif-wrapper" style="position: relative; margin-right: 4px;">
                            <button class="notif-bell-btn pulse-icon" id="notifBell" onclick="openNotifModal()" style="background: rgba(15, 76, 117, 0.08); border: 1px solid rgba(15, 76, 117, 0.15); width: 38px; height: 38px; border-radius: 50%; color: var(--primary); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; position: relative;">
                                <i class="fas fa-bell" style="font-size: 1.1rem;"></i>
                                @if(isset($unreadCount) && $unreadCount > 0)
                                    <span class="notif-badge" style="position: absolute; top: -4px; right: -4px; background: #ef4444; color: white; font-size: 0.62rem; font-weight: 800; min-width: 16px; height: 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; padding: 2px;">{{ $unreadCount }}</span>
                                @endif
                            </button>
                        </div>
                    @endif

                    <!-- Head Date Format exactly as requested -->
                    <div class="current-date-box">
                        <div class="time-section">
                            <span id="real-time-clock">12:00:00 AM</span>
                        </div>
                        <div class="date-section">
                            <i class="bi bi-calendar3"></i>
                            <span id="real-time-date">{{ now()->format('F j, Y') }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Wrapper -->
            <div class="content-wrapper {{ count(request()->query()) === 0 ? 'animate__animated animate__zoomIn' : '' }}">
                <!-- Premium Filter Area Yield -->
                @yield('filter-section')

                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="admin-footer">
                <div class="footer-content">
                    <div class="footer-left">
                        <span>&copy; {{ date('Y') }} E-Leave Application System. All rights reserved.</span>
                    </div>
                    <div class="developer-attr">
                        ICT UNIT {{ date('Y') }}
                    </div>
                </div>
            </footer>
        </main>
    </div>

    <!-- Toast Container (Moved to root for absolute top-level z-index) -->
    <div id="toast-container"></div>
    
    <script>
        // Toast Notification System
        function showToast(message, type = 'success', duration = 5000) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            
            toast.innerHTML = `
                <i class="fas ${icon} toast-icon"></i>
                <div class="toast-message">${message}</div>
                <button class="toast-close">&times;</button>
            `;
            
            container.appendChild(toast);
            
            // Trigger animation
            setTimeout(() => toast.classList.add('show'), 10);
            
            const closeBtn = toast.querySelector('.toast-close');
            closeBtn.onclick = () => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            };
            
            if (duration > 0) {
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.classList.remove('show');
                        setTimeout(() => toast.remove(), 300);
                    }
                }, duration);
            }
        }
    </script>

    @if(session('success'))
        <script>showToast("{!! addslashes(session('success')) !!}", 'success');</script>
    @endif
    
    @if(session('error'))
        <script>showToast("{!! addslashes(session('error')) !!}", 'error');</script>
    @endif
    
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <script>showToast("{!! addslashes($error) !!}", 'error');</script>
        @endforeach
    @endif
    
    <script src="{{ asset('js/scripts.js') }}"></script>
    
    <script>
        // Disable right-click and copy-paste
        document.addEventListener('contextmenu', event => event.preventDefault());
        document.addEventListener('copy', event => event.preventDefault());
        document.addEventListener('paste', event => event.preventDefault());
        document.addEventListener('cut', event => event.preventDefault());

        // Collapsible Sidebar Sections Function
        function toggleSection(sectionId) {
            const section = document.getElementById('section-' + sectionId);
            if (!section) return;
            const isCollapsed = section.classList.toggle('collapsed');
            localStorage.setItem('sidebar_section_' + sectionId + '_collapsed', isCollapsed ? 'true' : 'false');
        }
    </script>

    @if(auth()->check() && isset($notifications))
    <!-- Notification Hub Modal (Custom Minimalist Layout) -->
    <div class="notif-modal-overlay" id="notificationModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); z-index: 99999; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease;">
        <div class="notif-modal-window" style="background: white; border-radius: 16px; width: 90%; max-width: 500px; max-height: 85vh; display: flex; flex-direction: column; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); overflow: hidden; transform: scale(0.9); transition: transform 0.3s ease;">
            <div class="notif-modal-header" style="display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #f1f5f9;">
                <h5 class="notif-modal-title" style="margin: 0; font-weight: 800; color: #1e293b; font-size: 1.1rem;"><i class="fas fa-bell me-2" style="color: var(--primary);"></i>Notifications</h5>
                <button class="notif-modal-close" onclick="closeNotifModal()" style="background: none; border: none; font-size: 1.5rem; color: #94a3b8; cursor: pointer;">&times;</button>
            </div>
            
            <div class="notif-modal-subheader" style="display: flex; align-items: center; justify-content: space-between; padding: 10px 20px; background: #f8fafc; border-bottom: 1px solid #f1f5f9; font-size: 0.78rem;">
                <span class="notif-count-text" style="color: #64748b; font-weight: 600;">{{ $unreadCount }} unread messages</span>
                <div class="notif-global-actions" style="display: flex; gap: 12px;">
                    @if($unreadCount > 0)
                        <button onclick="markAllRead()" style="background: none; border: none; color: var(--primary); font-weight: 700; cursor: pointer; font-size: 0.78rem; padding: 0;">Mark all as read</button>
                    @endif
                    <button onclick="clearAllNotifs()" style="background: none; border: none; color: #ef4444; font-weight: 700; cursor: pointer; font-size: 0.78rem; padding: 0;">Clear all history</button>
                </div>
            </div>

            <div class="notif-modal-body" style="padding: 10px 0; overflow-y: auto; flex: 1;">
                <div class="notif-modal-list" style="display: flex; flex-direction: column;">
                    @forelse($notifications as $notif)
                        <div class="notif-modal-item {{ $notif->is_read ? '' : 'unread' }}" style="display: flex; align-items: flex-start; padding: 14px 20px; border-bottom: 1px solid #f8fafc; position: relative; transition: background 0.2s; cursor: default; {{ $notif->is_read ? '' : 'background: rgba(15, 76, 117, 0.03);' }}">
                            <div class="notif-item-indicator" style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; {{ $notif->is_read ? '' : 'background: var(--primary);' }}"></div>
                            <div class="notif-item-icon" style="margin-right: 14px; margin-top: 2px; color: #94a3b8; font-size: 1.1rem;">
                                <i class="fas fa-envelope-open-text"></i>
                            </div>
                            <div class="notif-item-content" style="flex: 1; min-width: 0;">
                                @if($notif->link_url)
                                    <a href="{{ $notif->link_url }}" style="text-decoration: none; color: inherit;" onclick="readNotif(event, {{ $notif->id }});">
                                        <p class="notif-item-text" style="margin: 0 0 4px 0; font-size: 0.85rem; color: #334155; line-height: 1.4; word-break: break-word; font-weight: {{ $notif->is_read ? '500' : '700' }};">{{ $notif->message }}</p>
                                    </a>
                                @else
                                    <p class="notif-item-text" style="margin: 0 0 4px 0; font-size: 0.85rem; color: #334155; line-height: 1.4; word-break: break-word; font-weight: {{ $notif->is_read ? '500' : '700' }};">{{ $notif->message }}</p>
                                @endif
                                <span class="notif-item-time" style="font-size: 0.7rem; color: #94a3b8;">
                                    {{ \Carbon\Carbon::parse($notif->created_at)->setTimezone('Asia/Manila')->diffForHumans() }}
                                </span>
                            </div>
                            <div class="notif-item-tools" style="display: flex; gap: 8px; margin-left: 10px; margin-top: 2px;">
                                @if(!$notif->is_read)
                                    <button onclick="readNotif(event, {{ $notif->id }})" title="Mark as Read" style="background: none; border: none; color: #10b981; cursor: pointer; padding: 4px; font-size: 0.85rem;"><i class="fas fa-check"></i></button>
                                @endif
                                <button onclick="deleteNotif(event, {{ $notif->id }})" title="Delete" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 4px; font-size: 0.85rem;"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </div>
                    @empty
                        <div class="notif-modal-empty" style="text-align: center; padding: 40px 20px; color: #94a3b8;">
                            <i class="fas fa-bell-slash" style="font-size: 2.5rem; margin-bottom: 12px; opacity: 0.5;"></i>
                            <p style="margin: 0; font-size: 0.9rem;">Your inbox is clear</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Confirmation Modal for Notification actions -->
    <div class="confirm-modal-overlay" id="confirmModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); z-index: 100000; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease;">
        <div class="confirm-modal-window" style="background: white; border-radius: 16px; padding: 24px; max-width: 400px; text-align: center; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); transform: scale(0.9); transition: transform 0.3s ease;">
            <div class="confirm-icon-box" style="width: 56px; height: 56px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 16px auto;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h4 class="confirm-title" id="confirmTitle" style="margin: 0 0 8px 0; color: #1e293b; font-weight: 800;">Confirm Action</h4>
            <p class="confirm-message" id="confirmMessage" style="margin: 0 0 20px 0; color: #64748b; font-size: 0.88rem; line-height: 1.5;">Are you sure you want to proceed?</p>
            <div class="confirm-actions" style="display: flex; gap: 12px; justify-content: center;">
                <button onclick="closeConfirmModal()" style="border: 1px solid #e2e8f0; background: #f8fafc; color: #475569; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 0.85rem;">Cancel</button>
                <button id="confirmBtn" style="border: none; background: #ef4444; color: white; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 0.85rem;">Confirm</button>
            </div>
        </div>
    </div>

    <script>
        // Open Notification Modal
        window.openNotifModal = function() {
            const modal = document.getElementById('notificationModal');
            if (modal) {
                modal.style.display = 'flex';
                setTimeout(() => {
                    modal.style.opacity = '1';
                    modal.querySelector('.notif-modal-window').style.transform = 'scale(1)';
                }, 10);
                document.body.style.overflow = 'hidden';
            }
        };

        // Close Notification Modal
        window.closeNotifModal = function() {
            const modal = document.getElementById('notificationModal');
            if (modal) {
                modal.style.opacity = '0';
                modal.querySelector('.notif-modal-window').style.transform = 'scale(0.9)';
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 300);
                document.body.style.overflow = '';
            }
        };

        // Close on background click
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('notificationModal');
            if (event.target == modal) {
                closeNotifModal();
            }
        });

        // Read Notification
        window.readNotif = function(e, notifId) {
            if (e) e.stopPropagation();
            fetch("{{ route('user.notifications.read') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ notification_id: notifId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload(); 
                }
            });
        };

        // Mark all as read
        window.markAllRead = function(e) {
            if (e) e.stopPropagation();
            fetch("{{ route('user.notifications.read-all') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).then(() => {
                window.location.reload();
            });
        };

        // Confirm modal handler
        let confirmCallback = null;
        window.showConfirmModal = function(title, message, onConfirm) {
            document.getElementById('confirmTitle').innerText = title;
            document.getElementById('confirmMessage').innerText = message;
            confirmCallback = onConfirm;
            
            const modal = document.getElementById('confirmModal');
            if (modal) {
                modal.style.display = 'flex';
                setTimeout(() => {
                    modal.style.opacity = '1';
                    modal.querySelector('.confirm-modal-window').style.transform = 'scale(1)';
                }, 10);
            }
        };

        window.closeConfirmModal = function() {
            const modal = document.getElementById('confirmModal');
            if (modal) {
                modal.style.opacity = '0';
                modal.querySelector('.confirm-modal-window').style.transform = 'scale(0.9)';
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 300);
            }
        };

        const confirmBtn = document.getElementById('confirmBtn');
        if (confirmBtn) {
            confirmBtn.onclick = function() {
                if (confirmCallback) confirmCallback();
                closeConfirmModal();
            };
        }

        // Delete single notification
        window.deleteNotif = function(e, notifId) {
            if (e) e.stopPropagation();
            
            showConfirmModal(
                'Delete Notification',
                'Are you sure you want to remove this message? This cannot be undone.',
                () => {
                    const url = "{{ route('user.notifications.delete', ['id' => ':id']) }}".replace(':id', notifId);
                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    }).then(res => res.json()).then(data => {
                        if (data.success) {
                            window.location.reload();
                        }
                    }).catch(err => console.error('Delete error:', err));
                }
            );
        };

        // Clear all notifications
        window.clearAllNotifs = function() {
            showConfirmModal(
                'Clear History',
                'This will permanently delete ALL your notifications. This action is irreversible. Proceed?',
                () => {
                    fetch("{{ route('user.notifications.delete-all') }}", {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    }).then(res => res.json()).then(data => {
                        if (data.success) {
                            window.location.reload();
                        }
                    }).catch(err => {
                        console.error('Clear notifications error:', err);
                        window.location.reload();
                    });
                }
            );
        };
    </script>
    @endif

    @stack('scripts')
</body>
</html>
