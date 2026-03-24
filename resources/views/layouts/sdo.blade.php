
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SDO System')</title>

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
                        <span class="logo-title">SDO System</span>
                        <span class="logo-subtitle">Management Portal</span>
                    </div>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->isHR() || auth()->user()->isHeadHR())
                    <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-home"></i></div>
                        <span class="nav-text">Dashboard</span>
                    </a>
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
                    
                    @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('admin.auth-reset-management') }}" class="nav-item {{ request()->routeIs('admin.auth-reset-management') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-key"></i></div>
                        <span class="nav-text">Auth Reset</span>
                    </a>
                    <a href="{{ route('admin.signatories') }}" class="nav-item {{ request()->routeIs('admin.signatories') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-signature"></i></div>
                        <span class="nav-text">Signatories</span>
                    </a>
                    @endif

                    @if(auth()->user()->isHR() || auth()->user()->isHeadHR())
                    
                    <a href="{{ route('hr-staff.manage-credits') }}" class="nav-item {{ request()->routeIs('hr-staff.manage-credits*') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-coins"></i></div>
                        <span class="nav-text">Manage Credits</span>
                    </a>
                    @endif

                    @if(auth()->user()->isHeadHR() || auth()->user()->isSuperAdmin())
                    <a href="{{ route('head-hr.leave-policies') }}" class="nav-item {{ request()->routeIs('head-hr.leave-policies*') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-sliders-h"></i></div>
                        <span class="nav-text">Credit Policies</span>
                    </a>
                    <a href="{{ route('head-hr.audit-logs') }}" class="nav-item {{ request()->routeIs('head-hr.audit-logs*') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-clipboard-check"></i></div>
                        <span class="nav-text">Audit & Approvals</span>
                    </a>
                    @endif

                    @if(auth()->user()->isHR())
                    <a href="{{ route('user.leave.approvals') }}" class="nav-item {{ request()->routeIs('user.leave.approvals') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-check-double"></i></div>
                        <span class="nav-text">Pending Approvals</span>
                    </a>
                    @endif
                    
                    <div class="nav-divider"></div>
                    
                    <a href="{{ route('admin.profile') }}" class="nav-item {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-user-cog"></i></div>
                        <span class="nav-text">My Profile</span>
                    </a>
                @elseif(auth()->user()->isRecordPersonnel())
                    <a href="{{ route('records.dashboard') }}" class="nav-item {{ request()->routeIs('records.dashboard') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-home"></i></div>
                        <span class="nav-text">Dashboard</span>
                    </a>
                    
                    <a href="{{ route('records.index') }}" class="nav-item {{ request()->routeIs('records.index*') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-archive"></i></div>
                        <span class="nav-text">Application Records</span>
                    </a>
                    
                    <div class="nav-divider"></div>
                    
                    <a href="{{ route('user.profile') }}" class="nav-item {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-user-cog"></i></div>
                        <span class="nav-text">My Profile</span>
                    </a>
                @else
                    <a href="{{ route('user.home') }}" class="nav-item {{ request()->routeIs('user.home') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-home"></i></div>
                        <span class="nav-text">Home</span>
                    </a>
                    
                    <a href="{{ route('user.leave.apply') }}" class="nav-item {{ request()->routeIs('user.leave.apply') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-file-signature"></i></div>
                        <span class="nav-text">Apply for Leave</span>
                    </a>
                    <a href="{{ route('user.leave.history') }}" class="nav-item {{ request()->routeIs('user.leave.history') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-folder-open"></i></div>
                        <span class="nav-text">My Applications</span>
                    </a>

                    <!-- Approval Dashboard Link for Officers -->
                    @if(in_array(auth()->user()->role, ['cid_chief', 'sgod_chief', 'ao', 'asds', 'sds', 'hr', 'head_hr', 'super_admin']))
                    <a href="{{ route('user.leave.approvals') }}" class="nav-item {{ request()->routeIs('user.leave.approvals') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-check-double"></i></div>
                        <span class="nav-text">Pending Approvals</span>
                    </a>
                    @endif

                    <div class="nav-divider"></div>

                    <a href="{{ route('user.profile') }}" class="nav-item {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-user"></i></div>
                        <span class="nav-text">My Profile</span>
                    </a>
                @endif
            </nav>
            
            <div class="sidebar-footer">
                <a href="{{ auth()->user()->isAdmin() || auth()->user()->isHR() ? route('admin.profile') : route('user.profile') }}" class="user-info-link">
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
                            <div class="user-role">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</div>
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
                    <!-- Note: Burger menu injected via JS here -->
                    <div class="breadcrumb">
                        <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                    </div>
                </div>
                <div class="top-bar-right">
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
            <div class="content-wrapper animate__animated animate__zoomIn">
                <!-- Premium Filter Area Yield -->
                @yield('filter-section')

                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="admin-footer">
                <div class="footer-content">
                    <div class="footer-left">
                        <span>&copy; {{ date('Y') }} SDO System. All rights reserved.</span>
                    </div>
                    <div class="developer-attr">
                        Developed by: Algen D. Loveres & Cedrick V. Bacaresas
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
    
    @stack('scripts')
</body>
</html>
