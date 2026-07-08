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
                @yield('content')
            </div>
        </main>
    </div>
    
    @stack('scripts')
</body>
</html>