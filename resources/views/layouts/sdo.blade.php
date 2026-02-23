<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SDO System')</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
    
    @stack('styles')
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
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
                        <span class="nav-icon"><i class="fas fa-home"></i></span>
                        <span class="nav-text">Dashboard</span>
                    </a>
                    <a href="{{ route('admin.manage-users') }}" class="nav-item {{ request()->routeIs('admin.manage-users*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-users"></i></span>
                        <span class="nav-text">Manage Users</span>
                    </a>
                    <a href="{{ route('admin.register-user') }}" class="nav-item {{ request()->routeIs('admin.register-user') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-user-plus"></i></span>
                        <span class="nav-text">Register User</span>
                    </a>
                    <a href="{{ route('admin.activity-logs') }}" class="nav-item {{ request()->routeIs('admin.activity-logs') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-list-alt"></i></span>
                        <span class="nav-text">Activity Logs</span>
                    </a>
                    
                    @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('admin.auth-reset-management') }}" class="nav-item {{ request()->routeIs('admin.auth-reset-management') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-key"></i></span>
                        <span class="nav-text">Auth Reset</span>
                    </a>
                    <a href="{{ route('admin.signatories') }}" class="nav-item {{ request()->routeIs('admin.signatories') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-signature"></i></span>
                        <span class="nav-text">Signatories</span>
                    </a>
                    @endif

                    @if(auth()->user()->isHR() || auth()->user()->isHeadHR())
                    
                    <a href="{{ route('hr-staff.manage-credits') }}" class="nav-item {{ request()->routeIs('hr-staff.manage-credits*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-coins"></i></span>
                        <span class="nav-text">Manage Credits</span>
                    </a>
                    @endif

                    @if(auth()->user()->isHeadHR() || auth()->user()->isSuperAdmin())
                    <a href="{{ route('head-hr.leave-policies') }}" class="nav-item {{ request()->routeIs('head-hr.leave-policies*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-sliders-h"></i></span>
                        <span class="nav-text">Credit Policies</span>
                    </a>
                    <a href="{{ route('head-hr.audit-logs') }}" class="nav-item {{ request()->routeIs('head-hr.audit-logs*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-clipboard-check"></i></span>
                        <span class="nav-text">Audit & Approvals</span>
                    </a>
                    @endif

                    @if(auth()->user()->isHR())
                    <a href="{{ route('user.leave.approvals') }}" class="nav-item {{ request()->routeIs('user.leave.approvals') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-check-double"></i></span>
                        <span class="nav-text">Pending Approvals</span>
                    </a>
                    @endif
                    
                    <div class="nav-divider"></div>
                    
                    <a href="{{ route('admin.profile') }}" class="nav-item {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-user-cog"></i></span>
                        <span class="nav-text">My Profile</span>
                    </a>
                @else
                    <a href="{{ route('user.home') }}" class="nav-item {{ request()->routeIs('user.home') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-home"></i></span>
                        <span class="nav-text">Home</span>
                    </a>
                    
                    <a href="{{ route('user.leave.apply') }}" class="nav-item {{ request()->routeIs('user.leave.apply') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-file-signature"></i></span>
                        <span class="nav-text">Apply for Leave</span>
                    </a>
                    <a href="{{ route('user.leave.history') }}" class="nav-item {{ request()->routeIs('user.leave.history') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-folder-open"></i></span>
                        <span class="nav-text">My Applications</span>
                    </a>

                    <!-- Approval Dashboard Link for Officers -->
                    @if(in_array(auth()->user()->role, ['cid_chief', 'sgod_chief', 'ao', 'asds', 'sds', 'hr', 'head_hr', 'super_admin']))
                    <a href="{{ route('user.leave.approvals') }}" class="nav-item {{ request()->routeIs('user.leave.approvals') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-check-double"></i></span>
                        <span class="nav-text">Pending Approvals</span>
                    </a>
                    @endif

                    <div class="nav-divider"></div>

                    <a href="{{ route('user.profile') }}" class="nav-item {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-user"></i></span>
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
            <!-- Top Bar -->
            <header class="top-bar">
                <div class="top-bar-left">
                    <button class="mobile-toggle" id="mobileToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="top-bar-right">
                    <div class="top-bar-datetime">
                        <span class="live-clock" id="liveClock"></span>
                        <span class="live-date"><i class="fas fa-calendar-alt"></i> {{ now()->format('F j, Y') }}</span>
                    </div>
                </div>
            </header>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <!-- Toast Container -->
                <div id="toast-container"></div>
                
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="admin-footer">
                &copy; {{ date('Y') }} SDO System. All rights reserved.
            </footer>
        </main>
    </div>
    
    <script>
        // Sidebar Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const mobileToggle = document.getElementById('mobileToggle');
            const mainContent = document.querySelector('.main-content');
            
            // Restore sidebar state from localStorage (Desktop only)
            if (window.innerWidth > 992) {
                const sidebarState = localStorage.getItem('sidebarState');
                if (sidebarState === 'collapsed') {
                    // Disable transitions momentarily to prevent animation on page load
                    sidebar.style.transition = 'none';
                    if(mainContent) mainContent.style.transition = 'none';
                    
                    sidebar.classList.add('collapsed');
                    if(mainContent) mainContent.classList.add('sidebar-collapsed');
                    
                    // Force reflow
                    void sidebar.offsetWidth;
                    
                    // Re-enable transitions
                    setTimeout(() => {
                        sidebar.style.transition = '';
                        if(mainContent) mainContent.style.transition = '';
                    }, 100);
                }
            }
            
            mobileToggle?.addEventListener('click', function() {
                const isMobile = window.innerWidth <= 992;
                if (isMobile) {
                    sidebar.classList.toggle('mobile-open');
                    overlay.classList.toggle('active');
                } else {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('sidebar-collapsed');
                    
                    // Save state to localStorage
                    if (sidebar.classList.contains('collapsed')) {
                        localStorage.setItem('sidebarState', 'collapsed');
                    } else {
                        localStorage.setItem('sidebarState', 'expanded');
                    }
                }
            });
            
            overlay?.addEventListener('click', function() {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            });
        });
        
        // Toast Notification System
        function showToast(message, type = 'success', duration = 5000) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            const icons = {
                success: 'check-circle',
                error: 'times-circle',
                warning: 'exclamation-triangle',
                info: 'info-circle'
            };
            
            toast.innerHTML = `
                <div class="toast-icon">
                    <i class="fas fa-${icons[type] || icons.success}"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">${type.charAt(0).toUpperCase() + type.slice(1)}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
                <div class="toast-progress"></div>
            `;
            
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('hiding');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }
        
        // Show session messages as toasts
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif
        
        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
        
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                showToast("{{ $error }}", 'error');
            @endforeach
        @endif
        
        // Live Clock
        function updateClock() {
            const now = new Date();
            let h = now.getHours();
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            document.getElementById('liveClock').textContent = `${h}:${m}:${s} ${ampm}`;
        }
        updateClock();
        setInterval(updateClock, 1000);
    </script>
    
    @stack('scripts')
</body>
</html>
