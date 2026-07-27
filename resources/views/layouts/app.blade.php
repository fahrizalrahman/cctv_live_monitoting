<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CCTV Live Monitor') }}</title>

        <!-- Google Fonts (Outfit & Inter) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Tailwind CSS & JS -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Lucide Icons CDN -->
        <script src="https://unpkg.com/lucide@latest"></script>

        <!-- Leaflet.js CSS (For maps usage in CRUD & dashboards) -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

        <!-- Hls.js CDN (For HLS video streams playback) -->
        <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
            h1, h2, h3, h4, h5, h6 {
                font-family: 'Outfit', sans-serif;
            }
            /* Custom Scrollbar */
            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            ::-webkit-scrollbar-track {
                background: #0f172a;
            }
            ::-webkit-scrollbar-thumb {
                background: #334155;
                border-radius: 3px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: #475569;
            }
            
            /* Desktop/Mobile handling without Tailwind JIT dependency */
            /* Mobile ( < 1024px) */
            @media (max-width: 1023px) {
                .mobile-header-only {
                    display: flex !important;
                }
                .mobile-btn-only {
                    display: block !important;
                }
                .sidebar-responsive {
                    position: fixed !important;
                    transform: translateX(-100%);
                }
                .sidebar-responsive.sidebar-open {
                    transform: translateX(0);
                }
            }
            
            /* Desktop ( >= 1024px ) */
            @media (min-width: 1024px) {
                .mobile-header-only, .mobile-btn-only, #sidebar-backdrop {
                    display: none !important;
                }
                .sidebar-responsive {
                    position: static !important;
                    transform: none !important;
                }
            }
        </style>
        @stack('styles')
    </head>
    <body class="antialiased bg-[#090d16] text-slate-200 min-h-screen overflow-x-hidden">
        @php
            $appLogo = \App\Models\Setting::where('key', 'app_logo')->first();
            $appName = \App\Models\Setting::where('key', 'app_name')->first();
            $appNameDisplay = $appName && $appName->value ? $appName->value : 'CCTV MONITOR';
        @endphp
        <div class="flex min-h-screen relative">
            <!-- Sidebar Backdrop (Mobile) -->
            <div id="sidebar-backdrop" class="fixed inset-0 z-40 backdrop-blur-sm" style="display: none; background-color: rgba(0,0,0,0.6); opacity: 0; transition: opacity 0.3s;"></div>

            <!-- Sidebar -->
            <aside id="sidebar" class="sidebar-responsive inset-y-0 left-0 z-50 w-64 bg-[#0d1321] border-r border-slate-800 flex flex-col justify-between shrink-0 transition-transform duration-300">
                <div>
                    <!-- Logo / Brand -->
                    <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800">
                        <div class="flex items-center gap-3">
                            @if($appLogo && $appLogo->value)
                                <img src="{{ asset(Storage::url($appLogo->value)) }}" alt="Logo" class="h-8 object-contain" />
                            @else
                                <div class="bg-indigo-600 p-2 rounded-lg text-white shadow-lg shadow-indigo-500/30">
                                    <i data-lucide="video" class="w-6 h-6"></i>
                                </div>
                            @endif
                            <div>
                                <span class="font-bold text-lg tracking-wide bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">{{ $appNameDisplay }}</span>
                                <span class="block text-[10px] text-slate-500 font-medium tracking-widest uppercase">Live Streaming</span>
                            </div>
                        </div>
                        <button id="close-sidebar-btn" class="mobile-btn-only p-1.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <!-- Navigation Links -->
                    <nav class="p-4 pt-6 pb-6 space-y-2">
                        <!-- Direct Link to Public Map -->
                        <a href="{{ route('map') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all text-emerald-400 hover:bg-emerald-500/10 hover:text-emerald-300">
                            <i data-lucide="map" class="w-4 h-4"></i>
                            <span>Public Map View</span>
                        </a>

                        <div class="h-px bg-slate-800 my-4"></div>

                        @if(isset($sidebarMenus))
                            @foreach($sidebarMenus as $menu)
                                <div>
                                    <!-- Main Menu Item -->
                                    <a href="{{ url($menu->url) }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ Request::is(ltrim($menu->url, '/').'*') ? 'bg-indigo-600/20 text-indigo-400 border-l-2 border-indigo-500' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                                        <div class="flex items-center gap-3">
                                            @if($menu->icon)
                                                <i data-lucide="{{ $menu->icon }}" class="w-4 h-4"></i>
                                            @else
                                                <i data-lucide="circle" class="w-3 h-3"></i>
                                            @endif
                                            <span>{{ $menu->name }}</span>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        @endif
                    </nav>
                </div>

                <!-- User Profile & Logout at Bottom -->
                <div class="p-4 border-t border-slate-800 bg-[#0a0e1a]/50">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center border border-slate-700 text-indigo-400 font-bold uppercase">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </div>
                        <div class="overflow-hidden">
                            <span class="block text-sm font-semibold truncate text-slate-200">{{ Auth::user()->name }}</span>
                            <span class="block text-[10px] text-slate-500 font-medium truncate capitalize">
                                {{ Auth::user()->roles->first()?->name ?? 'User' }}
                            </span>
                        </div>
                    </div>

                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold bg-rose-600/10 text-rose-400 hover:bg-rose-600 hover:text-white border border-rose-500/20 transition-all">
                            <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                            <span>Sign Out</span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col overflow-y-auto max-h-screen w-full relative">
                
                <!-- Mobile Header (Hidden on Desktop) -->
                <div class="mobile-header-only items-center justify-between p-4 border-b border-slate-800 bg-[#0d1321] shrink-0 sticky top-0 z-30">
                    <div class="flex items-center gap-3 w-full p-2 mb-2">
                        @if($appLogo && $appLogo->value)
                            <img src="{{ asset(Storage::url($appLogo->value)) }}" alt="Logo" class="h-6 object-contain" />
                        @else
                            <div class="bg-indigo-600 p-1.5 rounded-md text-white shadow-lg shadow-indigo-500/30">
                                <i data-lucide="video" class="w-5 h-5"></i>
                            </div>
                        @endif
                        <span class="font-bold text-base tracking-wide bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">{{ $appNameDisplay }}</span>
                    </div>
                    <button id="mobile-menu-btn" class="p-2 bg-slate-800/50 text-slate-300 hover:text-white rounded-lg border border-slate-700 transition-all">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                </div>


                <!-- Page Content -->
                <main class="flex-1 p-4 md:p-6 lg:p-8 flex flex-col min-h-0">
                    <!-- Session Status Alerts -->
                    @if(session('success'))
                        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3 shadow-lg shadow-emerald-500/5 shrink-0">
                            <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm flex items-center gap-3 shadow-lg shadow-rose-500/5 shrink-0">
                            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    @yield('content')
                </main>

                <!-- Footer -->
                <footer class="h-12 border-t border-slate-800 flex items-center justify-between px-8 text-xs text-slate-600">
                    <div>
                        &copy; {{ date('Y') }} CCTV Live Monitor System.
                    </div>
                    <div>
                        Powered by Fahrizal Rahman
                    </div>
                </footer>
            </div>
        </div>

        <script>
            // Initialize Lucide Icons
            lucide.createIcons();

            // Realtime clock in header
            function updateClock() {
                const now = new Date();
                const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                const dateString = now.toLocaleDateString([], { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                const clockEl = document.getElementById('live-time');
                if (clockEl) {
                    clockEl.textContent = `${dateString} | ${timeString}`;
                }
            }
            setInterval(updateClock, 1000);
            updateClock();

            // Mobile Sidebar Toggle Logic
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const mobileBtn = document.getElementById('mobile-menu-btn');
            const closeBtn = document.getElementById('close-sidebar-btn');

            function openSidebar() {
                sidebar.classList.add('sidebar-open');
                backdrop.style.display = 'block';
                // slight delay to allow display:block to apply before animating opacity
                setTimeout(() => {
                    backdrop.style.opacity = '1';
                }, 10);
                document.body.style.overflow = 'hidden'; // Prevent scrolling on main body
            }

            function closeSidebar() {
                sidebar.classList.remove('sidebar-open');
                backdrop.style.opacity = '0';
                setTimeout(() => {
                    backdrop.style.display = 'none';
                }, 300); // match transition duration
                document.body.style.overflow = '';
            }

            if(mobileBtn) {
                mobileBtn.addEventListener('click', openSidebar);
            }
            if(backdrop) {
                backdrop.addEventListener('click', closeSidebar);
            }
            if(closeBtn) {
                closeBtn.addEventListener('click', closeSidebar);
            }
            
            // Re-initialize icons just in case newly added elements need it
            lucide.createIcons();
        </script>
        @stack('scripts')
    </body>
</html>
