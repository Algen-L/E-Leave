<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Leave Application System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-[#1b4a9a] shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('items.index') }}" class="flex items-center">
                        <i class="fas fa-boxes text-white text-2xl mr-3"></i>
                        <span class="text-white text-xl font-bold">E-Leave Application System</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('items.index') }}" class="text-white hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-list mr-1"></i> Items
                    </a>
                    <a href="{{ route('items.create') }}" class="bg-white text-[#1b4a9a] hover:bg-blue-50 px-4 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-plus mr-1"></i> Add Item
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-auto py-4 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col justify-center items-center gap-1 text-gray-500 text-sm">
            <div class="flex items-center gap-2">
                <span>&copy; {{ date('Y') }} E-Leave Application System. All rights reserved.</span>
            </div>
            <div class="text-xs text-gray-400 font-semibold uppercase tracking-wider">
                ICT UNIT {{ date('Y') }}
            </div>
        </div>
    </footer>
    <script>
        // Disable right-click and copy-paste
        document.addEventListener('contextmenu', event => event.preventDefault());
        document.addEventListener('copy', event => event.preventDefault());
        document.addEventListener('paste', event => event.preventDefault());
        document.addEventListener('cut', event => event.preventDefault());
    </script>
</body>
</html>
