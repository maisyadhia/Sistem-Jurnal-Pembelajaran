<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-Jurnal')</title>
    
    <!-- CSS & Fonts -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: @json(config('theme.colors')),
                    borderRadius: @json(config('theme.borderRadius')),
                    spacing: @json(config('theme.spacing')),
                    fontFamily: @json(config('theme.fontFamily')),
                    fontSize: @json(config('theme.fontSize'))
                }
            }
        }
    </script>
    
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9ff;
        }
        @yield('styles')
    </style>
</head>
<body class="bg-surface text-on-surface">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('components.sidebar')
        
        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            <!-- Top Navigation -->
            @include('components.topnav')
            
            <!-- Page Content -->
            <div class="p-margin-mobile md:p-margin-desktop max-w-container-max mx-auto w-full flex-1">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 px-4 py-3 rounded-lg mb-4 flex items-center gap-3 shadow-sm">
                        <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                        <span class="flex-1 font-medium">{{ session('success') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600 transition-colors">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-lg mb-4 flex items-center gap-3 shadow-sm">
                        <span class="material-symbols-outlined text-red-500">error</span>
                        <span class="flex-1 font-medium">{{ session('error') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 transition-colors">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                @endif
                
                @if(session('warning'))
                    <div class="bg-amber-50 border-l-4 border-amber-500 text-amber-800 px-4 py-3 rounded-lg mb-4 flex items-center gap-3 shadow-sm">
                        <span class="material-symbols-outlined text-amber-500">warning</span>
                        <span class="flex-1 font-medium">{{ session('warning') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-amber-400 hover:text-amber-600 transition-colors">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                @endif
                
                @if(session('info'))
                    <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-800 px-4 py-3 rounded-lg mb-4 flex items-center gap-3 shadow-sm">
                        <span class="material-symbols-outlined text-blue-500">info</span>
                        <span class="flex-1 font-medium">{{ session('info') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-blue-400 hover:text-blue-600 transition-colors">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-lg mb-4 flex items-center gap-3 shadow-sm">
                        <span class="material-symbols-outlined text-red-500">error</span>
                        <span class="flex-1 font-medium">{{ $errors->first() }}</span>
                        <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 transition-colors">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </main>
    </div>
    
    @stack('scripts')
</body>
</html>