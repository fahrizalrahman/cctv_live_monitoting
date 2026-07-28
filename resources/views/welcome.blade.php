<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @php
            $appName = \App\Models\Setting::where('key', 'app_name')->first();
            $appNameDisplay = $appName && $appName->value ? $appName->value : 'CCTV MONITOR';
            $mapMarkerIcon = \App\Models\Setting::where('key', 'map_marker_icon')->first();
            $mapMarkerUrl = $mapMarkerIcon && $mapMarkerIcon->value ? asset(Storage::url($mapMarkerIcon->value)) : null;
            
            $mapCenterLat = \App\Models\Setting::where('key', 'map_center_latitude')->first();
            $mapCenterLng = \App\Models\Setting::where('key', 'map_center_longitude')->first();
            $defaultLat = $mapCenterLat && $mapCenterLat->value ? $mapCenterLat->value : '-6.4025';
            $defaultLng = $mapCenterLng && $mapCenterLng->value ? $mapCenterLng->value : '106.8186';
            
            $mapZoomLevel = \App\Models\Setting::where('key', 'map_zoom_level')->first();
            $defaultZoom = $mapZoomLevel && $mapZoomLevel->value ? $mapZoomLevel->value : '12';
            
            $watermarkLogo = \App\Models\Setting::where('key', 'watermark_logo')->first();
            $runningText = \App\Models\Setting::where('key', 'running_text')->first();
        @endphp
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $appNameDisplay }} - Interactive Maps</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Tailwind CSS & JS -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Lucide Icons CDN -->
        <script src="https://unpkg.com/lucide@latest"></script>

        <!-- Leaflet.js CSS & JS CDN -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

        <!-- Hls.js CDN for live video streams playback -->
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
                background: #090d16;
            }
            ::-webkit-scrollbar-thumb {
                background: #1e293b;
                border-radius: 3px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: #334155;
            }
            /* Custom Responsive Override */
            @media (max-width: 767px) {
                .mobile-hidden { display: none !important; }
            }
            @media (min-width: 768px) {
                .desktop-hidden { display: none !important; }
            }
        </style>
    </head>
    <body class="antialiased bg-[#090d16] text-slate-200 overflow-hidden flex flex-col relative w-full" style="height: 100vh; height: 100dvh;">

        <!-- Fullscreen Map View -->
        <div id="map" class="absolute inset-0 z-10 w-full h-full"></div>

        <!-- Floating Navbar Logo on Top Left -->
        <div class="absolute top-4 left-4 z-20 flex items-center gap-3 bg-[#0d1321]/90 backdrop-blur-xl border border-slate-800/80 px-4 py-2.5 rounded-2xl shadow-xl">
            @php
                $appLogo = \App\Models\Setting::where('key', 'app_logo')->first();
            @endphp
            @if($appLogo && $appLogo->value)
                <img src="{{ asset(Storage::url($appLogo->value)) }}" alt="Logo" class="h-6 object-contain" />
            @else
                <div class="bg-indigo-600 p-2 rounded-xl text-white shadow-lg shadow-indigo-500/25">
                    <i data-lucide="video" class="w-5 h-5"></i>
                </div>
            @endif
            <div>
                <h1 class="font-extrabold text-sm tracking-wide bg-gradient-to-r from-indigo-400 via-purple-300 to-pink-400 bg-clip-text text-transparent">{{ $appNameDisplay }}</h1>
                <span class="block text-[8px] text-slate-500 font-semibold tracking-widest uppercase mt-0.5">Live Streaming Maps</span>
            </div>
        </div>

        <!-- Floating CCTV Directory Sidebar Panel on Right -->
        <aside id="sidebar" class="mobile-hidden absolute top-4 right-4 bottom-4 w-[calc(100%-2rem)] sm:w-80 bg-[#0d1321]/95 backdrop-blur-xl border border-slate-800/80 p-5 flex flex-col z-30 shadow-2xl rounded-3xl">
            <!-- Sidebar Header & Action -->
            <div class="flex items-center justify-between mb-5 pb-4 border-b border-slate-800/50">
                <span class="text-sm font-bold text-slate-200">Daftar CCTV</span>
                
                <div class="flex items-center gap-2">
                    <button onclick="toggleSidebar()" class="desktop-hidden p-2 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 rounded-xl transition-all" title="Tutup">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                    @auth
                        <a href="{{ route('dashboard') }}" class="p-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-md transition-all" title="Dashboard">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="p-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl transition-all border border-slate-700/50" title="Login Dashboard">
                            <i data-lucide="log-in" class="w-4 h-4"></i>
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Search Bar -->
            <div class="mb-3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <input id="search-input" 
                           oninput="filterCctvs()" 
                           type="text" 
                           placeholder="Cari CCTV..." 
                           class="block w-full pl-9 pr-4 py-2 bg-[#090d16]/90 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-xs" />
                </div>
            </div>

            <div class="mb-5 flex gap-2">
                <select id="group-filter" onchange="filterCctvs()" class="block w-full px-3 py-2 bg-[#090d16]/90 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-xs">
                    <option value="">Semua Group</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>

                <select id="status-filter" onchange="filterCctvs()" class="block w-full px-3 py-2 bg-[#090d16]/90 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-xs">
                    <option value="">Semua Status</option>
                    <option value="active">Online</option>
                    <option value="inactive">Offline</option>
                </select>
            </div>

            <!-- List Count -->
            <div class="flex justify-between items-center text-[10px] font-bold text-slate-500 uppercase mb-3 px-1">
                <span>CCTV Terdaftar</span>
                <span id="active-count" class="text-slate-400">Checking...</span>
            </div>

            <!-- CCTV List Scrollable -->
            <div id="cctv-list" class="flex-1 overflow-y-auto space-y-2 pr-1">
                @forelse($cctvs as $cctv)
                    <button onclick="focusOnCctv({{ $cctv->id }})" 
                            data-id="{{ $cctv->id }}"
                            data-name="{{ $cctv->name }}"
                            data-ip="{{ $cctv->ip }}"
                            data-group="{{ $cctv->cctv_group_id }}"
                            class="cctv-list-item w-full text-left bg-[#090d16]/40 hover:bg-slate-800/40 border border-slate-850 p-3 rounded-xl flex items-center justify-between gap-3 group transition-all">
                        <div class="min-w-0">
                            <span class="block text-xs font-bold text-slate-300 group-hover:text-slate-100 truncate transition-colors">{{ $cctv->name }}</span>
                        </div>
                        <div class="shrink-0 flex items-center gap-2" id="cctv-status-{{ $cctv->id }}">
                            <span class="text-[10px] text-slate-400 font-medium">Checking...</span>
                            <i data-lucide="loader-2" class="w-3 h-3 text-slate-500 animate-spin"></i>
                        </div>
                    </button>
                @empty
                    <div class="text-center py-8 text-slate-500 text-xs">
                        <i data-lucide="camera-off" class="w-8 h-8 mx-auto mb-2 text-slate-600"></i>
                        <span>Belum ada kamera terdaftar.</span>
                    </div>
                @endforelse
            </div>
        </aside>

        <!-- Mobile FAB to open sidebar -->
        <button id="mobile-sidebar-toggle" onclick="toggleSidebar()" class="desktop-hidden absolute bg-indigo-600 hover:bg-indigo-500 text-white p-4 rounded-full shadow-lg shadow-indigo-600/30 flex items-center justify-center" style="bottom: 64px; right: 24px; z-index: 50;">
            <i data-lucide="list" class="w-6 h-6"></i>
        </button>

        <!-- Floating Centered Video Player Modal Card (Depok style overlay) -->
        <div id="stream-modal" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl bg-[#0d1321]/95 backdrop-blur-xl border border-slate-800/80 rounded-3xl overflow-hidden shadow-2xl z-40 transform scale-95 opacity-0 pointer-events-none transition-all duration-300 flex flex-col">
            <!-- Modal Header -->
            <div class="bg-[#131b2e] px-6 py-4 flex items-center justify-between border-b border-slate-800/80">
                <h3 id="modal-name" class="font-bold text-slate-200 text-sm">PASAR PUCUNG 1</h3>
                <button onclick="closeStreamModal()" class="p-1.5 bg-slate-800/40 text-slate-400 hover:text-slate-200 rounded-lg border border-slate-800 transition-all">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Video Player Area -->
            <div class="aspect-video bg-black overflow-hidden flex items-center justify-center relative">
                <!-- Loading Indicator -->
                <div id="modal-player-loading" class="absolute inset-0 flex flex-col items-center justify-center bg-[#070b12]/90 z-10">
                    <i data-lucide="loader-2" class="w-8 h-8 text-indigo-500 animate-spin mb-3"></i>
                    <span class="block text-xs font-semibold text-slate-300">Menghubungkan ke CCTV...</span>
                </div>
                <!-- Watermark Logo -->
                @if($watermarkLogo && $watermarkLogo->value)
                    <style>
                        .custom-watermark {
                            position: absolute;
                            top: 8px;
                            right: 8px;
                            height: 20px;
                            z-index: 40;
                            pointer-events: none;
                        }
                        @media (min-width: 768px) {
                            .custom-watermark {
                                top: 16px;
                                right: 16px;
                                height: 44px;
                            }
                        }
                    </style>
                    <img src="{{ Storage::url($watermarkLogo->value) }}" alt="Watermark" class="custom-watermark opacity-70 drop-shadow-md object-contain" />
                @endif
                
                <video id="modal-video" class="w-full h-full object-fill bg-black relative z-0" autoplay muted playsinline controls></video>
                <iframe id="modal-video-frame" class="w-full h-full border-0 hidden relative z-0" allow="fullscreen; autoplay"></iframe>
                <div id="modal-player-error" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-[#070b12]/90 p-4 text-center z-20">
                    <i data-lucide="video-off" class="w-8 h-8 text-rose-500 mb-2"></i>
                    <span class="block text-xs font-semibold text-slate-300">Gagal Memutar Live Stream</span>
                    <span class="block text-[10px] text-slate-500 mt-1">Harap periksa kecocokan link HLS/M3U8 atau konfigurasikan transcoder RTSP Anda.</span>
                </div>
            </div>
            
            <!-- Running Text Marquee (Below Video) -->
            @if($runningText && $runningText->value)
                <div class="w-full bg-[#0a0e1a] border-t border-slate-800">
                    <marquee class="py-1.5 px-4 text-xs md:text-sm font-medium text-indigo-300 tracking-wide" scrollamount="5">{{ $runningText->value }}</marquee>
                </div>
            @endif

            <!-- Modal Footer -->
            <div class="bg-[#131b2e] px-6 py-3 flex items-center justify-between text-xs text-slate-400 font-medium border-t border-slate-800/80">
                <span class="flex items-center gap-1.5 text-emerald-400">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Live</span>
                </span>
                <span id="footer-time" class="font-mono text-slate-500">17:36:03</span>
            </div>
            <!-- End Modal Card -->
        </div>

        <script>
            // Initialize Lucide Icons
            lucide.createIcons();

            // Store markers and cctvs data
            const cctvs = @json($cctvs);
            const markers = {};
            let map;
            let currentHls = null;

            // Initialize Map
            function initMap() {
                const defaultLat = {{ $defaultLat }};
                const defaultLng = {{ $defaultLng }};
                const defaultZoom = {{ $defaultZoom }};

                // Centered on configured area by default
                map = L.map('map', {
                    zoomControl: false
                }).setView([defaultLat, defaultLng], defaultZoom);

                // Add Zoom Controls to bottom left
                L.control.zoom({
                    position: 'bottomleft'
                }).addTo(map);

                // Google Maps Satellite Hybrid (Earth style)
                L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                    attribution: '&copy; Google Maps',
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    maxZoom: 20
                }).addTo(map);

                // Add markers
                cctvs.forEach(cctv => {
                    const lat = parseFloat(cctv.latitude);
                    const lng = parseFloat(cctv.longitude);
                    
                    if (!isNaN(lat) && !isNaN(lng)) {
                        const status = cctv.realTimeStatus || 'checking';
                        let statusColor = '#94a3b8'; // Slate 400
                        let ringColor = 'rgba(148, 163, 184, 0.2)';
                        
                        if (status === 'active') {
                            statusColor = '#10b981'; // Emerald 500
                            ringColor = 'rgba(16, 185, 129, 0.4)';
                        } else if (status === 'inactive') {
                            statusColor = '#ef4444'; // Rose 500
                            ringColor = 'rgba(239, 68, 68, 0.2)';
                        }
                        
                        let markerHtml = '';
                        const customMarkerUrl = {!! json_encode($mapMarkerUrl) !!};
                        
                        if (customMarkerUrl) {
                            markerHtml = `
                                <div style="display: flex; align-items: center; justify-content: center; width: 48px; height: 48px;">
                                    <div style="position: absolute; width: 48px; height: 48px; border-radius: 50%; background: ${ringColor}; border: 2px solid ${statusColor}; animation: ${status === 'active' ? 'ping 1.8s infinite' : 'none'}; opacity: 0.8;"></div>
                                    <img src="${customMarkerUrl}" style="position: relative; width: 36px; height: 36px; border-radius: 50%; border: 2px solid #090d16; box-shadow: 0 0 8px ${statusColor}; object-fit: cover; background: white;" />
                                </div>
                            `;
                        } else {
                            markerHtml = `
                                <div style="display: flex; align-items: center; justify-content: center; width: 24px; height: 24px;">
                                    <div style="position: absolute; width: 20px; height: 20px; border-radius: 50%; background: ${ringColor}; border: 1.5px solid ${statusColor}; animation: ${status === 'active' ? 'ping 1.8s infinite' : 'none'}; opacity: 0.8;"></div>
                                    <div style="position: relative; width: 10px; height: 10px; border-radius: 50%; background: ${statusColor}; border: 1.5px solid #090d16; box-shadow: 0 0 6px ${statusColor};"></div>
                                </div>
                            `;
                        }

                        const markerIcon = L.divIcon({
                            className: 'custom-marker',
                            html: markerHtml,
                            iconSize: customMarkerUrl ? [48, 48] : [24, 24],
                            iconAnchor: customMarkerUrl ? [24, 24] : [12, 12]
                        });
                        
                        if (markers[cctv.id]) {
                            markers[cctv.id].setIcon(markerIcon);
                        } else {
                            const marker = L.marker([lat, lng], { icon: markerIcon }).addTo(map);
                            marker.on('click', () => {
                                showCctvInModal(cctv);
                            });
                            markers[cctv.id] = marker;
                        }
                    }
                });

                // Set View to bounds if there are active markers
                if (cctvs.length > 0) {
                    const group = new L.featureGroup(Object.values(markers));
                    map.fitBounds(group.getBounds().pad(0.2));
                }
            }

            // Helper to get playback URL based on stream protocol
            function getPlaybackUrl(cctv) {
                if (!cctv.stream_url) return '';
                if (cctv.stream_url.toLowerCase().startsWith('rtsp://')) {
                    return `{{ url('/') }}/go2rtc/api/stream.m3u8?src=cctv_${cctv.id}`;
                }
                return cctv.stream_url;
            }

            // Show CCTV Stream in modal overlay (Depok style)
            function showCctvInModal(cctv) {
                // Focus sidebar list item styling
                document.querySelectorAll('.cctv-list-item').forEach(item => {
                    item.classList.remove('border-indigo-500', 'bg-indigo-600/5');
                    if (item.getAttribute('data-id') == cctv.id) {
                        item.classList.add('border-indigo-500', 'bg-indigo-600/5');
                    }
                });

                // Update Modal Title
                document.getElementById('modal-name').textContent = cctv.name;

                // Elements
                const video = document.getElementById('modal-video');
                const iframe = document.getElementById('modal-video-frame');
                const errorAlert = document.getElementById('modal-player-error');
                const loadingAlert = document.getElementById('modal-player-loading');
                
                // Clear active streams first
                if (currentHls) {
                    currentHls.destroy();
                    currentHls = null;
                }
                video.pause();
                video.removeAttribute('src');
                video.load();
                iframe.removeAttribute('src');
                errorAlert.classList.add('hidden');
                loadingAlert.classList.remove('hidden');

                // Add event listeners to hide loading when playing
                video.onplaying = function() { loadingAlert.classList.add('hidden'); };
                video.onloadeddata = function() { loadingAlert.classList.add('hidden'); };

                if (cctv.stream_url && cctv.stream_url.toLowerCase().startsWith('rtsp://')) {
                    // Use go2rtc ultra-low latency WebRTC player
                    video.classList.add('hidden');
                    iframe.classList.remove('hidden');
                    
                    // We let go2rtc handle the stream using WebRTC automatically
                    iframe.src = `{{ url('/') }}/go2rtc/stream.html?src=cctv_${cctv.id}`;
                    
                    // Hide loading when iframe loads (go2rtc player handles its own UI)
                    iframe.onload = function() { 
                        loadingAlert.classList.add('hidden'); 
                        try {
                            const style = document.createElement('style');
                            style.innerHTML = '.info, #info { display: none !important; } video { object-fit: fill !important; width: 100% !important; height: 100% !important; } body { margin: 0; padding: 0; overflow: hidden; }';
                            iframe.contentWindow.document.head.appendChild(style);
                        } catch(e) {}
                    };
                    
                } else {
                    // Native Video playback for raw HTTP/HLS links
                    iframe.classList.add('hidden');
                    video.classList.remove('hidden');
                    const playbackUrl = cctv.stream_url;

                    if (Hls.isSupported()) {
                        const hls = new Hls();
                        hls.loadSource(playbackUrl);
                        hls.attachMedia(video);
                        hls.on(Hls.Events.MANIFEST_PARSED, function() {
                            video.play().catch(e => console.log('Autoplay blocked:', e));
                        });
                        hls.on(Hls.Events.ERROR, function (event, data) {
                            if (data.fatal) errorAlert.classList.remove('hidden');
                        });
                        currentHls = hls;
                    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                        video.src = playbackUrl;
                        video.addEventListener('loadedmetadata', function() {
                            video.play().catch(e => console.log('Autoplay blocked:', e));
                        });
                        video.addEventListener('error', function() {
                            loadingAlert.classList.add('hidden');
                            errorAlert.classList.remove('hidden');
                        });
                    }
                }

                // Show Modal Card
                const modal = document.getElementById('stream-modal');
                modal.classList.remove('scale-95', 'opacity-0', 'pointer-events-none');
                modal.classList.add('scale-100', 'opacity-100', 'pointer-events-auto');
            }

            function closeStreamModal() {
                // Hide Modal Card
                const modal = document.getElementById('stream-modal');
                modal.classList.remove('scale-100', 'opacity-100', 'pointer-events-auto');
                modal.classList.add('scale-95', 'opacity-0', 'pointer-events-none');
                
                // Remove sidebar list active state
                document.querySelectorAll('.cctv-list-item').forEach(item => {
                    item.classList.remove('border-indigo-500', 'bg-indigo-600/5');
                });

                // Clear player memory
                if (currentHls) {
                    currentHls.destroy();
                    currentHls = null;
                }
                const video = document.getElementById('modal-video');
                video.pause();
                video.removeAttribute('src');
                video.load();
            }

            // Focus map on CCTV marker
            function focusOnCctv(cctvId) {
                const cctv = cctvs.find(c => c.id === cctvId);
                const marker = markers[cctvId];
                
                if (cctv && marker) {
                    map.setView(marker.getLatLng(), 15);
                    showCctvInModal(cctv);
                }
            }

            // Real-time Search Filter inside Sidebar
            function filterCctvs() {
                const query = document.getElementById('search-input').value.toLowerCase();
                const selectedGroup = document.getElementById('group-filter').value;
                const selectedStatus = document.getElementById('status-filter').value;
                let activeCount = 0;
                let matchCount = 0;

                cctvs.forEach(cctv => {
                    const matchSearch = cctv.name.toLowerCase().includes(query) || cctv.ip.toLowerCase().includes(query);
                    const matchGroup = selectedGroup === '' || String(cctv.cctv_group_id) === String(selectedGroup);
                    
                    // Filter by status (note: during 'checking' state, it might not match strict active/inactive if selected)
                    const matchStatus = selectedStatus === '' || cctv.realTimeStatus === selectedStatus;
                    
                    const match = matchSearch && matchGroup && matchStatus;
                    
                    const listItem = document.querySelector(`.cctv-list-item[data-id="${cctv.id}"]`);
                    const marker = markers[cctv.id];

                    if (match) {
                        if (listItem) listItem.classList.remove('hidden');
                        if (marker) marker.addTo(map);
                        if (cctv.realTimeStatus === 'active') activeCount++;
                        matchCount++;
                    } else {
                        if (listItem) listItem.classList.add('hidden');
                        if (marker) map.removeLayer(marker);
                    }
                });

                const countEl = document.getElementById('active-count');
                if (selectedStatus === 'inactive') {
                    countEl.textContent = `${matchCount} OFFLINE`;
                    countEl.className = 'text-rose-400 font-bold';
                } else if (selectedStatus === 'active') {
                    countEl.textContent = `${matchCount} ONLINE`;
                    countEl.className = 'text-emerald-400 font-bold';
                } else {
                    countEl.textContent = `${activeCount} ONLINE`;
                    countEl.className = 'text-slate-400 font-bold';
                }
            }

            // Realtime clock in modal footer
            function updateModalClock() {
                const now = new Date();
                const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
                const clockEl = document.getElementById('footer-time');
                if (clockEl) {
                    clockEl.textContent = timeString;
                }
            }
            setInterval(updateModalClock, 1000);
            updateModalClock();

            // Toggle Sidebar for Mobile
            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                const fab = document.getElementById('mobile-sidebar-toggle');
                
                // Toggle visibility on mobile
                sidebar.classList.toggle('mobile-hidden');
                
                // Hide FAB when sidebar is open
                fab.classList.toggle('mobile-hidden');
                
                setTimeout(() => { map.invalidateSize(); }, 350);
            }

            // Inject keyframes style programmatically for ping animation on markers
            const style = document.createElement('style');
            style.type = 'text/css';
            style.innerHTML = `
                @keyframes ping {
                    0% { transform: scale(1); opacity: 1; }
                    70%, 100% { transform: scale(2.2); opacity: 0; }
                }
            `;
            document.getElementsByTagName('head')[0].appendChild(style);

            // Initialize Map on load
            window.onload = () => {
                initMap();
                fetchRealTimeStatus();
            };

            // Fetch Real-time CCTV status
            async function fetchRealTimeStatus() {
                try {
                    const response = await fetch('/api/cctvs/status');
                    const statuses = await response.json();
                    
                    cctvs.forEach(cctv => {
                        const statusContainer = document.getElementById(`cctv-status-${cctv.id}`);
                        const isOnline = statuses[cctv.id] === 'online';
                        
                        cctv.realTimeStatus = isOnline ? 'active' : 'inactive';
                        
                        if (statusContainer) {
                            if (isOnline) {
                                statusContainer.innerHTML = `
                                    <span class="text-[10px] text-emerald-400 font-medium">Online</span>
                                    <span class="h-2 w-2 rounded-full bg-emerald-500 shadow-md shadow-emerald-500/50"></span>
                                `;
                            } else {
                                statusContainer.innerHTML = `
                                    <span class="text-[10px] text-slate-500 font-medium">Offline</span>
                                    <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                                `;
                            }
                        }
                    });
                    
                    // Re-render markers to update colors
                    cctvs.forEach(cctv => {
                        const lat = parseFloat(cctv.latitude);
                        const lng = parseFloat(cctv.longitude);
                        if (!isNaN(lat) && !isNaN(lng)) {
                            const status = cctv.realTimeStatus;
                            let statusColor = status === 'active' ? '#10b981' : '#ef4444';
                            let ringColor = status === 'active' ? 'rgba(16, 185, 129, 0.4)' : 'rgba(239, 68, 68, 0.2)';
                            
                            let markerHtml = '';
                            const customMarkerUrl = {!! json_encode($mapMarkerUrl) !!};
                            
                            if (customMarkerUrl) {
                                markerHtml = `
                                    <div style="display: flex; align-items: center; justify-content: center; width: 48px; height: 48px;">
                                        <div style="position: absolute; width: 48px; height: 48px; border-radius: 50%; background: ${ringColor}; border: 2px solid ${statusColor}; animation: ${status === 'active' ? 'ping 1.8s infinite' : 'none'}; opacity: 0.8;"></div>
                                        <img src="${customMarkerUrl}" style="position: relative; width: 36px; height: 36px; border-radius: 50%; border: 2px solid #090d16; box-shadow: 0 0 8px ${statusColor}; object-fit: cover; background: white;" />
                                    </div>
                                `;
                            } else {
                                markerHtml = `
                                    <div style="display: flex; align-items: center; justify-content: center; width: 24px; height: 24px;">
                                        <div style="position: absolute; width: 20px; height: 20px; border-radius: 50%; background: ${ringColor}; border: 1.5px solid ${statusColor}; animation: ${status === 'active' ? 'ping 1.8s infinite' : 'none'}; opacity: 0.8;"></div>
                                        <div style="position: relative; width: 10px; height: 10px; border-radius: 50%; background: ${statusColor}; border: 1.5px solid #090d16; box-shadow: 0 0 6px ${statusColor};"></div>
                                    </div>
                                `;
                            }
    
                            const markerIcon = L.divIcon({
                                className: 'custom-marker',
                                html: markerHtml,
                                iconSize: customMarkerUrl ? [48, 48] : [24, 24],
                                iconAnchor: customMarkerUrl ? [24, 24] : [12, 12]
                            });
                            
                            if (markers[cctv.id]) {
                                markers[cctv.id].setIcon(markerIcon);
                            }
                        }
                    });
                    
                    // Trigger filterCctvs to update the count display correctly based on online status
                    filterCctvs();
                    
                } catch (error) {
                    console.error("Failed to fetch real-time status:", error);
                } finally {
                    // Poll again after 5 seconds
                    setTimeout(fetchRealTimeStatus, 5000);
                }
            }
        </script>
    </body>
</html>
