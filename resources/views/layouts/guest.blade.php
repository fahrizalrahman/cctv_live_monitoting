<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @php
            $appName = \App\Models\Setting::where('key', 'app_name')->first();
            $appNameDisplay = $appName && $appName->value ? $appName->value : 'CCTV MONITOR';
            $appLogo = \App\Models\Setting::where('key', 'app_logo')->first();
        @endphp
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $appNameDisplay }} - Login</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Tailwind CSS & JS -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Lucide Icons CDN -->
        <script src="https://unpkg.com/lucide@latest"></script>

        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
            h1, h2, h3, h4, h5, h6 {
                font-family: 'Outfit', sans-serif;
            }
        </style>
    </head>
    <body class="antialiased bg-[#090d16] text-slate-200 min-h-screen flex items-center justify-center relative overflow-hidden">
        <!-- Abstract Glowing Shapes Background -->
        <div class="absolute top-1/4 left-1/4 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-indigo-500/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-1/4 right-1/4 translate-x-1/2 translate-y-1/2 w-[500px] h-[500px] bg-purple-500/10 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="w-full max-w-md px-6 relative z-10">
            <!-- App Logo/Header -->
            <div class="text-center mb-8">
                @if($appLogo && $appLogo->value)
                    <div class="inline-flex items-center justify-center mb-4">
                        <img src="{{ asset(Storage::url($appLogo->value)) }}" alt="Logo" class="h-16 object-contain" />
                    </div>
                @else
                    <div class="inline-flex items-center justify-center p-3.5 bg-indigo-600 rounded-2xl text-white shadow-xl shadow-indigo-600/30 mb-4 ring-4 ring-indigo-500/10">
                        <i data-lucide="video" class="w-8 h-8"></i>
                    </div>
                @endif
                <h1 class="text-3xl font-extrabold tracking-tight bg-gradient-to-r from-indigo-400 via-purple-300 to-pink-400 bg-clip-text text-transparent">{{ $appNameDisplay }}</h1>
                <p class="text-xs text-slate-500 mt-1 uppercase tracking-widest font-semibold">Security Control Center</p>
            </div>

            <!-- Login Card -->
            <div class="bg-[#0e1424]/60 backdrop-blur-xl border border-slate-800 p-8 rounded-3xl shadow-2xl shadow-indigo-950/20">
                {{ $slot }}
            </div>
            
            <div class="text-center mt-6">
                <a href="{{ route('map') }}" class="text-xs text-slate-500 hover:text-indigo-400 font-medium inline-flex items-center gap-1.5 transition-colors">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Back to CCTV Maps Dashboard</span>
                </a>
            </div>
        </div>

        <script>
            lucide.createIcons();
        </script>
    </body>
</html>
