/**
 * UI Core Functionality for Extracted Components
 * Handles the Real-time clock, Sidebar toggling, and Custom Filter Dropdown.
 */

document.addEventListener('DOMContentLoaded', () => {
    /* -------------------------------------------------------------------------- */
    /* Real-Time Clock Logic                                                      */
    /* -------------------------------------------------------------------------- */
    function updateClock() {
        const clockElement = document.getElementById('real-time-clock');
        if (!clockElement) return;

        const now = new Date();
        const options = {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        };
        clockElement.textContent = now.toLocaleTimeString('en-US', options);
    }

    updateClock();
    setInterval(updateClock, 1000);

    /* -------------------------------------------------------------------------- */
    /* Sidebar Toggling Logic                                                     */
    /* -------------------------------------------------------------------------- */
    const sidebar = document.getElementById('mainSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const layout = document.querySelector('.app-layout');
    let mobileToggle = document.getElementById('toggleSidebar');

    function toggleDesktopCollapse() {
        if (sidebar) sidebar.classList.toggle('collapsed');
        if (layout) layout.classList.toggle('sidebar-collapsed');
        if (sidebar) localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    }

    function toggleMobileMenu() {
        if (sidebar) sidebar.classList.toggle('mobile-open');
        if (overlay) overlay.classList.toggle('show');
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

    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('show');
        });
    }

    // Persistence & Initialization
    if (sidebar && window.innerWidth > 992 && localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed');
        if (layout) {
            layout.classList.add('sidebar-collapsed');
        }
    }

    // Cleanup flash-prevention class if it exists
    document.documentElement.classList.remove('sidebar-initial-collapsed');

    /* -------------------------------------------------------------------------- */
    /* Custom Dropdown Filter Logic                                               */
    /* -------------------------------------------------------------------------- */
    const dropdown = document.getElementById('filterDropdown');
    const trigger = document.getElementById('dropdownTrigger');
    const menu = document.getElementById('dropdownMenu');
    const input = document.getElementById('filterInput');
    const text = document.getElementById('selectedFilterText');
    const items = document.querySelectorAll('.dropdown-item-custom');
    const customDateInputs = document.getElementById('customDateInputs');

    if (dropdown && trigger && menu && items.length > 0) {
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.classList.toggle('show');
            trigger.classList.toggle('active');
        });

        items.forEach(item => {
            item.addEventListener('click', () => {
                const value = item.getAttribute('data-value');

                // Update value and text
                if (input) input.value = value;
                if (text) text.textContent = item.textContent.trim();

                // Update active state
                items.forEach(i => i.classList.remove('active'));
                item.classList.add('active');

                // Handle Custom Range
                if (value === 'custom') {
                    if (customDateInputs) customDateInputs.style.display = 'flex';
                    menu.classList.remove('show');
                    trigger.classList.remove('active');
                } else {
                    if (customDateInputs) customDateInputs.style.display = 'none';
                    // Trigger custom event or form submit in real integration
                    console.log('Filter changed to:', value);
                }
            });
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target)) {
                menu.classList.remove('show');
                trigger.classList.remove('active');
            }
        });

        // Mobile UX: Hide Custom Inputs on load if mobile
        if (window.innerWidth <= 992 && input && input.value === 'custom') {
            if (customDateInputs) customDateInputs.style.display = 'none';
        }
    }
});
