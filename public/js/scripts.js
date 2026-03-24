/**
 * Extracted exported_design/js/scripts.js
 * Contains exactly the UI logic from the original LDP system.
 */

document.addEventListener('DOMContentLoaded', function () {
    // ----------------------------------------------------
    // 1. Sidebar Toggle Logic
    // ----------------------------------------------------
    const sidebar = document.getElementById('mainSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle'); // Internal Chevron
    const overlay = document.getElementById('sidebarOverlay');
    const layout = document.querySelector('.app-layout');

    function toggleDesktopCollapse() {
        if (!sidebar) return;
        
        const isCollapsing = !sidebar.classList.contains('collapsed');
        const animatedElements = sidebar.querySelectorAll('.nav-text, .logo-title, .logo-subtitle, .user-details');
        
        animatedElements.forEach(el => {
            el.classList.remove('animate__animated', 'animate__backOutLeft', 'animate__backInLeft');
            // Force reflow
            void el.offsetWidth;
            
            if (isCollapsing) {
                el.classList.add('animate__animated', 'animate__backOutLeft');
            } else {
                el.classList.add('animate__animated', 'animate__backInLeft');
            }
        });

        sidebar.classList.toggle('collapsed');
        if (layout) layout.classList.toggle('sidebar-collapsed');
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    }

    function toggleMobileMenu() {
        if (sidebar) sidebar.classList.toggle('mobile-open');
        if (overlay) overlay.classList.toggle('show');
    }

    // Logic to inject/bind the Burger Button (Top-bar Burger)
    function initBurgerToggle() {
        let mobileToggle = document.getElementById('toggleSidebar');

        // If button doesn't exist, try to inject it into .top-bar-left
        if (!mobileToggle) {
            const topBarLeft = document.querySelector('.top-bar-left');
            if (topBarLeft) {
                mobileToggle = document.createElement('button');
                mobileToggle.className = 'mobile-menu-toggle';
                mobileToggle.id = 'toggleSidebar';
                mobileToggle.innerHTML = '<i class="bi bi-grid-fill"></i>';
                topBarLeft.prepend(mobileToggle);
            }
        }

        if (mobileToggle) {
            mobileToggle.addEventListener('click', function () {
                if (window.innerWidth > 992) {
                    toggleDesktopCollapse();
                } else {
                    toggleMobileMenu();
                }
            });
        }
    }

    initBurgerToggle();

    // Sidebar Internal Toggle (Chevron)
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            toggleDesktopCollapse();
        });
    }

    // Overlay Close
    if (overlay) {
        overlay.addEventListener('click', function () {
            if (sidebar) sidebar.classList.remove('mobile-open');
            overlay.classList.remove('show');
        });
    }

    // Persistence & Initialization
    if (window.innerWidth > 992 && localStorage.getItem('sidebarCollapsed') === 'true') {
        if (sidebar) sidebar.classList.add('collapsed');
        if (layout) {
            layout.classList.add('sidebar-collapsed');
        }
    }

    // Cleanup flash-prevention class
    document.documentElement.classList.remove('sidebar-initial-collapsed');


    // ----------------------------------------------------
    // 2. Head Date Format (Live Clock Logic)
    // ----------------------------------------------------
    function updateClock() {
        const clockElement = document.getElementById('real-time-clock');
        if (!clockElement) return;

        const now = new Date();
        let hours = now.getHours();
        let minutes = now.getMinutes();
        let seconds = now.getSeconds();
        const ampm = hours >= 12 ? 'PM' : 'AM';

        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'

        // Pad with standard zeros
        const strHours = hours < 10 ? '0' + hours : hours;
        const strMinutes = minutes < 10 ? '0' + minutes : minutes;
        const strSeconds = seconds < 10 ? '0' + seconds : seconds;

        clockElement.textContent = `${strHours}:${strMinutes}:${strSeconds} ${ampm}`;
    }

    // Set clock initially then trigger interval
    updateClock();
    setInterval(updateClock, 1000);


    // ----------------------------------------------------
    // 3. Filter Option Custom Dropdown Logic
    // ----------------------------------------------------
    const setupCustomSelect = (containerId) => {
        const container = document.getElementById(containerId);
        if (!container) return;

        const trigger = container.querySelector('.custom-select-trigger');
        const options = container.querySelector('.custom-select-options');
        const text = container.querySelector('.custom-select-text');
        const hiddenInput = container.querySelector('input[type="hidden"]');
        const optionItems = container.querySelectorAll('.custom-option');

        if (!trigger || !options) return;

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            // Close other dropdowns first
            document.querySelectorAll('.custom-select-options').forEach(opt => {
                if (opt !== options) opt.classList.remove('show');
            });
            document.querySelectorAll('.custom-select-trigger').forEach(trig => {
                if (trig !== trigger) trig.classList.remove('active');
            });

            options.classList.toggle('show');
            trigger.classList.toggle('active');
        });

        optionItems.forEach(item => {
            item.addEventListener('click', () => {
                const val = item.getAttribute('data-value');
                if (hiddenInput) hiddenInput.value = val;
                if (text) text.textContent = item.textContent.trim();

                // Update UI
                optionItems.forEach(i => i.classList.remove('selected'));
                item.classList.add('selected');

                options.classList.remove('show');
                trigger.classList.remove('active');
            });
        });
    };

    setupCustomSelect('statusSelect');

    // Global Click to close dropdowns
    document.addEventListener('click', () => {
        document.querySelectorAll('.custom-select-options').forEach(opt => opt.classList.remove('show'));
        document.querySelectorAll('.custom-select-trigger').forEach(trig => trig.classList.remove('active'));
    });
});
