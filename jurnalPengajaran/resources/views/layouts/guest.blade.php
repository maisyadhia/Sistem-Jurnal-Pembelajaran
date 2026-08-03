<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-Jurnal')</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
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
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9ff;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.5);
        }
        @yield('styles')
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-surface text-on-surface">
    <main class="relative z-10 w-full max-w-4xl bg-surface-container-lowest rounded-xl overflow-hidden shadow-2xl border border-outline-variant">
        @yield('content')
    </main>
    
    @stack('scripts')
</body>
</html>