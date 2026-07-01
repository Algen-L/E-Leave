<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') - E-Leave Application System</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tom Select for dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <!-- Custom Auth CSS -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    @stack('styles')
</head>
<body class="auth-page morph-init">

    
    <!-- Toast Container -->
    <div id="toast-container"></div>
    
    @yield('content')
    
    <!-- Tom Select -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    
    <script>
        // Advanced 2-Stage Physical Morphing
        document.addEventListener('DOMContentLoaded', function() {
            const authCard = document.querySelector('.login-container');
            
            // BULLETPROOF FALLBACK
            function revealAuthCard() {
                document.body.classList.remove('morph-init');
                if (authCard) authCard.classList.remove('morph-hidden');
            }

            if (!authCard) {
                revealAuthCard();
                return;
            }

            // --- STAGE 2: Entrance Morph (Height adjustment & Content Fade In) ---
            try {
                const morphDataString = sessionStorage.getItem('authMorphData');
                if (morphDataString) {
                    const morphData = JSON.parse(morphDataString);
                    
                    authCard.classList.add('morph-hidden');
                    authCard.style.transition = 'none';
                    
                    authCard.style.setProperty('width', `${morphData.width}px`, 'important');
                    authCard.style.setProperty('height', `${morphData.height}px`, 'important');
                    authCard.style.setProperty('padding', morphData.padding, 'important');
                    
                    authCard.offsetHeight; // Reflow
                    
                    document.body.classList.remove('morph-init');
                    
                    authCard.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                    
                    authCard.style.removeProperty('width');
                    authCard.style.removeProperty('height');
                    authCard.style.removeProperty('padding');
                    
                    setTimeout(() => {
                        authCard.classList.remove('morph-hidden');
                    }, 150); // Start fading in contents before the box fully stops morphing

                    sessionStorage.removeItem('authMorphData');
                } else {
                    revealAuthCard();
                }
            } catch (e) {
                console.warn('Morph transition error:', e);
                revealAuthCard();
            }

            // --- STAGE 1: Exit Morph (Width adjustment on current page) ---
            window.triggerExitMorph = function(href) {
                // 1. Instantly hide current contents
                authCard.classList.add('morph-hidden');
                
                // 2. Lock current height so CSS can animate vertical morphing
                const startRect = authCard.getBoundingClientRect();
                authCard.style.height = `${startRect.height}px`;
                authCard.offsetHeight; // Trigger reflow so the browser logs the starting height

                // 3. Trigger physical CSS width/padding/height transformation
                if (href.includes('register')) {
                    authCard.classList.add('morph-to-register');
                    authCard.classList.remove('morph-to-login');
                } else if (href.includes('login') || href.includes('password')) {
                    authCard.classList.add('morph-to-login');
                    authCard.classList.remove('morph-to-register');
                    authCard.classList.remove('register-mode');
                }
                
                // 4. Wait for visual morph to conclude, THEN save shape and navigate
                setTimeout(() => {
                    const rect = authCard.getBoundingClientRect();
                    const computed = window.getComputedStyle(authCard);
                    
                    sessionStorage.setItem('authMorphData', JSON.stringify({
                        width: rect.width,
                        height: rect.height,
                        padding: computed.padding
                    }));
                    
                    window.location.href = href;
                }, 250); // Cut the morph slightly early to make it feel extremely snappy
            }

            // Bind to standard Links
            document.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (!href || href.startsWith('#') || this.target === '_blank' || e.ctrlKey || e.shiftKey) return;

                    if (href.includes('login') || href.includes('register') || href.includes('password')) {
                        e.preventDefault();
                        window.triggerExitMorph(href);
                    }
                });
            });

            // Bind to Form Submissions (Just fade contents and save state, no target width change known)
            const authForm = document.querySelector('form');
            if (authForm) {
                authForm.addEventListener('submit', function(e) {
                    if (this.classList.contains('ajax-auth-form')) return;
                    if (this.checkValidity()) {
                        authCard.classList.add('morph-hidden');
                        const rect = authCard.getBoundingClientRect();
                        const computed = window.getComputedStyle(authCard);
                        sessionStorage.setItem('authMorphData', JSON.stringify({
                            width: rect.width,
                            height: rect.height,
                            padding: computed.padding
                        }));
                    }
                });
            }
        });


        
        // Toast Notification System (LDPVER2 Style)
        function showToast(message, type = 'error', duration = 5000) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            const title = type.charAt(0).toUpperCase() + type.slice(1);
            
            // Icon selection (Supporting both FA and BI if needed, but keeping FA for existing infrastructure)
            let iconClass = 'fa-info-circle';
            if (type === 'success') iconClass = 'fa-check-circle';
            if (type === 'error') iconClass = 'fa-times-circle';
            if (type === 'warning') iconClass = 'fa-exclamation-triangle';

            toast.innerHTML = `
                <div class="toast-icon">
                    <i class="fas ${iconClass}"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('removing');
                toast.style.animation = 'slideOutRight 0.3s forwards';
                toast.addEventListener('animationend', () => {
                    if(toast.parentElement) toast.remove();
                });
            }, duration);
        }
    </script>
    
    @stack('scripts')
    <script>
        // Disable right-click and copy-paste
        document.addEventListener('contextmenu', event => event.preventDefault());
        document.addEventListener('copy', event => event.preventDefault());
        document.addEventListener('paste', event => event.preventDefault());
        document.addEventListener('cut', event => event.preventDefault());

        // Reset persistent form data on logout/login page
        localStorage.removeItem('leave_form_data');
    </script>
</body>
</html>
