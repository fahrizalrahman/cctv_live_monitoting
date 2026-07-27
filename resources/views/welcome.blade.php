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
        </style>
    </head>
    <body class="antialiased bg-[#090d16] text-slate-200 min-h-screen overflow-hidden flex flex-col relative">

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
        <aside class="absolute top-4 right-4 bottom-4 w-80 bg-[#0d1321]/90 backdrop-blur-xl border border-slate-800/80 p-5 flex flex-col z-30 shadow-2xl rounded-3xl">
            <!-- Sidebar Header & Action -->
            <div class="flex items-center justify-between mb-5 pb-4 border-b border-slate-800/50">
                <span class="text-sm font-bold text-slate-200">Daftar CCTV</span>
                
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

            <!-- Search Bar -->
            <div class="mb-5">
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

            <!-- List Count -->
            <div class="flex justify-between items-center text-[10px] font-bold text-slate-500 uppercase mb-3 px-1">
                <span>CCTV Terdaftar</span>
                <span id="active-count" class="text-indigo-400">{{ count($cctvs) }} Online</span>
            </div>

            <!-- CCTV List Scrollable -->
            <div id="cctv-list" class="flex-1 overflow-y-auto space-y-2 pr-1">
                @forelse($cctvs as $cctv)
                    <button onclick="focusOnCctv({{ $cctv->id }})" 
                            data-id="{{ $cctv->id }}"
                            data-name="{{ $cctv->name }}"
                            data-ip="{{ $cctv->ip }}"
                            class="cctv-list-item w-full text-left bg-[#090d16]/40 hover:bg-slate-800/40 border border-slate-850 p-3 rounded-xl flex items-center justify-between gap-3 group transition-all">
                        <div class="min-w-0">
                            <span class="block text-xs font-bold text-slate-300 group-hover:text-slate-100 truncate transition-colors">{{ $cctv->name }}</span>
                            <span class="block text-[9px] text-slate-500 font-mono mt-0.5 truncate">IP: {{ $cctv->ip }}</span>
                        </div>
                        <div class="shrink-0 flex items-center gap-2">
                            @if($cctv->status === 'active')
                                <span class="text-[10px] text-emerald-400 font-medium">Online</span>
                                <span class="h-2 w-2 rounded-full bg-emerald-500 shadow-md shadow-emerald-500/50"></span>
                            @else
                                <span class="text-[10px] text-slate-500 font-medium">Offline</span>
                                <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                            @endif
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
            <div class="aspect-video bg-[#070b12] overflow-hidden flex items-center justify-center relative">
                <video id="modal-video" class="w-full h-full object-cover" autoplay muted playsinline controls></video>
                <iframe id="modal-video-frame" class="w-full h-full border-0 hidden" allow="fullscreen; autoplay"></iframe>
                <div id="modal-player-error" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-[#070b12]/90 p-4 text-center z-10">
                    <i data-lucide="video-off" class="w-8 h-8 text-rose-500 mb-2"></i>
                    <span class="block text-xs font-semibold text-slate-300">Gagal Memutar Live Stream</span>
                    <span class="block text-[10px] text-slate-500 mt-1">Harap periksa kecocokan link HLS/M3U8 atau konfigurasikan transcoder RTSP Anda.</span>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-[#131b2e] px-6 py-3 flex items-center justify-between text-xs text-slate-400 font-medium border-t border-slate-800/80">
                <span class="flex items-center gap-1.5 text-emerald-400">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Live</span>
                </span>
                <span id="footer-time" class="font-mono text-slate-500">17:36:03</span>
            </div>
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
                        // Custom Marker design
                        const statusColor = cctv.status === 'active' ? '#10b981' : '#ef4444';
                        const ringColor = cctv.status === 'active' ? 'rgba(16, 185, 129, 0.4)' : 'rgba(239, 68, 68, 0.2)';
                        
                        let markerHtml = '';
                        const customMarkerUrl = {!! json_encode($mapMarkerUrl) !!};
                        
                        if (customMarkerUrl) {
                            markerHtml = `
                                <div style="display: flex; align-items: center; justify-content: center; width: 48px; height: 48px;">
                                    <div style="position: absolute; width: 48px; height: 48px; border-radius: 50%; background: ${ringColor}; border: 2px solid ${statusColor}; animation: ping 1.8s infinite; opacity: 0.8;"></div>
                                    <img src="${customMarkerUrl}" style="position: relative; width: 36px; height: 36px; border-radius: 50%; border: 2px solid #090d16; box-shadow: 0 0 8px ${statusColor}; object-fit: cover; background: white;" />
                                </div>
                            `;
                        } else {
                            markerHtml = `
                                <div style="display: flex; align-items: center; justify-content: center; width: 24px; height: 24px;">
                                    <div style="position: absolute; width: 20px; height: 20px; border-radius: 50%; background: ${ringColor}; border: 1.5px solid ${statusColor}; animation: ping 1.8s infinite; opacity: 0.8;"></div>
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

                        const marker = L.marker([lat, lng], { icon: markerIcon }).addTo(map);
                        
                        // Marker Click Event
                        marker.on('click', () => {
                            showCctvInModal(cctv);
                        });

                        markers[cctv.id] = marker;
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

                if (cctv.stream_url && cctv.stream_url.toLowerCase().startsWith('rtsp://')) {
                    // Use go2rtc MP4 stream (MSE) which relies purely on HTTP (no WebSockets needed)
                    iframe.classList.add('hidden');
                    video.classList.remove('hidden');
                    video.src = `{{ url('/') }}/go2rtc/api/stream.mp4?src=cctv_${cctv.id}`;
                    
                    video.addEventListener('loadedmetadata', function() {
                        video.play().catch(e => console.log('Autoplay blocked:', e));
                    });
                    
                    video.addEventListener('error', function() {
                        // MEDIA_ERR_SRC_NOT_SUPPORTED usually means H.265 codec in Chrome
                        errorAlert.classList.remove('hidden');
                    });
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
                let activeCount = 0;

                cctvs.forEach(cctv => {
                    const match = cctv.name.toLowerCase().includes(query) || cctv.ip.toLowerCase().includes(query);
                    const listItem = document.querySelector(`.cctv-list-item[data-id="${cctv.id}"]`);
                    const marker = markers[cctv.id];

                    if (match) {
                        if (listItem) listItem.classList.remove('hidden');
                        if (marker) marker.addTo(map);
                        activeCount++;
                    } else {
                        if (listItem) listItem.classList.add('hidden');
                        if (marker) map.removeLayer(marker);
                    }
                });

                document.getElementById('active-count').textContent = `${activeCount} Online`;
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
            window.onload = initMap;
        </script>
    </body>
</html>
