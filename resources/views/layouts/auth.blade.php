<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') - SDO System</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tom Select for dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <!-- Custom Auth CSS -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
    
    @stack('styles')
</head>
<body class="auth-page">
    <!-- Animated Grid Background -->
    <div class="grid-background" id="gridBackground"></div>
    
    <!-- Toast Container -->
    <div id="toast-container"></div>
    
    @yield('content')
    
    <!-- Tom Select -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    
    <script>
        // Grid Background Animation
        document.addEventListener('DOMContentLoaded', function() {
            const grid = document.getElementById('gridBackground');
            const cols = Math.ceil(window.innerWidth / 90) + 1;
            const rows = Math.ceil(window.innerHeight / 90) + 1;
            
            for (let i = 0; i < cols * rows; i++) {
                const tile = document.createElement('div');
                tile.className = 'grid-tile';
                grid.appendChild(tile);
            }
            
            const tiles = document.querySelectorAll('.grid-tile');
            
            function activateRandomTile() {
                tiles.forEach(t => t.classList.remove('active', 'glow'));
                const randomIndex = Math.floor(Math.random() * tiles.length);
                tiles[randomIndex].classList.add('active');
                
                // Add glow to nearby tiles
                const nearbyIndices = [
                    randomIndex - 1, randomIndex + 1,
                    randomIndex - cols, randomIndex + cols
                ];
                nearbyIndices.forEach(idx => {
                    if (idx >= 0 && idx < tiles.length) {
                        tiles[idx].classList.add('glow');
                    }
                });
            }
            
            setInterval(activateRandomTile, 2000);
            activateRandomTile();
        });
        
        // Toast Notification System
        function showToast(message, type = 'error', duration = 5000) {
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
                    <i class="fas fa-${icons[type] || icons.error}"></i>
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
    </script>
    
    @stack('scripts')
</body>
</html>
