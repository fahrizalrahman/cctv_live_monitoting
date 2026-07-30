@extends('layouts.app')

@section('page_title', 'Interactive Dashboard')

@php
    $watermarkLogo = \App\Models\Setting::where('key', 'watermark_logo')->first();
@endphp

@section('content')
<!-- Dashboard Wrapper (Flex Col Full Height) -->
<div class="flex flex-col flex-1 h-full min-h-0">
    <!-- Dashboard Welcome Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4 shrink-0">
        <div>
            <h2 class="text-xl font-bold text-slate-200">System Dashboard</h2>
            <p class="text-xs text-slate-500 mt-1">Real-time camera metrics, active system users, and stream status logs.</p>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4 shrink-0">
    <!-- Card 1 -->
    <div class="bg-[#0d1321]/60 border border-slate-800 p-6 rounded-2xl flex items-center justify-between shadow-lg hover:border-slate-700 transition-all">
        <div>
            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Total CCTV</span>
            <span class="block text-3xl font-extrabold text-slate-100 mt-2">{{ $stats['total_cctv'] }}</span>
        </div>
        <div class="p-3.5 bg-indigo-600/10 rounded-xl border border-indigo-500/20 text-indigo-400">
            <i data-lucide="video" class="w-6 h-6"></i>
        </div>
    </div>

    <!-- Card 2 -->
    <div class="bg-[#0d1321]/60 border border-slate-800 p-6 rounded-2xl flex items-center justify-between shadow-lg hover:border-slate-700 transition-all">
        <div>
            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Active Streams</span>
            <span id="active-streams-count" class="block text-3xl font-extrabold text-emerald-400 mt-2 flex items-center gap-2">
                <span class="text-lg text-slate-400">Checking...</span>
            </span>
        </div>
        <div class="p-3.5 bg-emerald-600/10 rounded-xl border border-emerald-500/20 text-emerald-400">
            <i data-lucide="check-circle" class="w-6 h-6"></i>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="bg-[#0d1321]/60 border border-slate-800 p-6 rounded-2xl flex items-center justify-between shadow-lg hover:border-slate-700 transition-all">
        <div>
            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Offline Streams</span>
            <span id="offline-streams-count" class="block text-3xl font-extrabold text-rose-500 mt-2">
                <span class="text-lg text-slate-400">Checking...</span>
            </span>
        </div>
        <div class="p-3.5 bg-rose-600/10 rounded-xl border border-rose-500/20 text-rose-400">
            <i data-lucide="alert-triangle" class="w-6 h-6"></i>
        </div>
    </div>

    <!-- Card 4 -->
    <div class="bg-[#0d1321]/60 border border-slate-800 p-6 rounded-2xl flex items-center justify-between shadow-lg hover:border-slate-700 transition-all">
        <div>
            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Registered Users</span>
            <span class="block text-3xl font-extrabold text-slate-100 mt-2">{{ $stats['total_users'] }}</span>
        </div>
        <div class="p-3.5 bg-purple-600/10 rounded-xl border border-purple-500/20 text-purple-400">
            <i data-lucide="users" class="w-6 h-6"></i>
        </div>
    </div>
    </div>

    <!-- CCTV Live Grid Section -->
    <div class="bg-[#0d1321]/30 border border-slate-800 rounded-3xl p-4 shadow-xl flex-1 flex flex-col min-h-0">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-4 shrink-0">
            <div>
                <h2 class="text-xl font-bold text-slate-200">Interactive CCTV Live Grid</h2>
                <p class="text-xs text-slate-500 mt-1">Select layout and assign CCTV feeds to monitor concurrently.</p>
            </div>

        <!-- Layout Controls -->
        <div class="flex items-center bg-[#090d16] p-1 border border-slate-800 rounded-xl gap-1">
            <button onclick="changeLayout(1)" id="btn-layout-1" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-600 text-white transition-all">
                <i data-lucide="square" class="w-3.5 h-3.5"></i>
                <span>1 View</span>
            </button>
            <button onclick="changeLayout(2)" id="btn-layout-2" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-400 hover:text-slate-200 transition-all">
                <i data-lucide="columns" class="w-3.5 h-3.5"></i>
                <span>2 View</span>
            </button>
            <button onclick="changeLayout(3)" id="btn-layout-3" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-400 hover:text-slate-200 transition-all">
                <i data-lucide="gallery-horizontal" class="w-3.5 h-3.5"></i>
                <span>3 View</span>
            </button>
        </div>
        </div>

        <!-- The Grid Container -->
        <div id="cctv-grid" class="flex-1 overflow-x-auto overflow-y-hidden flex snap-x snap-mandatory gap-4 pb-2 transition-all duration-300 custom-scrollbar">
            <!-- Render 9 slots but toggle visibility based on layout -->
            @for($i = 0; $i < 9; $i++)
                <div id="slot-{{ $i }}" class="cctv-slot bg-[#070b12] border border-slate-800/80 rounded-2xl overflow-hidden h-full flex flex-col relative group transition-all hover:border-indigo-500/50 shrink-0 snap-center">
                    <!-- Dropdown Selector (if no stream) -->
                    <div id="selector-{{ $i }}" class="absolute inset-0 flex flex-col items-center justify-center p-6 bg-[#070b12] z-10">
                    <div class="p-3 bg-slate-800/40 rounded-full text-slate-500 mb-3 group-hover:text-indigo-400 group-hover:bg-indigo-600/10 transition-colors">
                        <i data-lucide="plus" class="w-6 h-6"></i>
                    </div>
                    <span class="block text-xs font-semibold text-slate-500 mb-4">Slot #{{ $i + 1 }} - Select CCTV</span>
                    
                    <select onchange="loadCctvIntoSlot({{ $i }}, this.value)" class="bg-[#0c1220] border border-slate-800 rounded-xl text-xs text-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600 transition-all max-w-[200px]">
                        <option value="">-- Choose Camera --</option>
                        @foreach($cctvs as $cctv)
                            <option value="{{ $cctv->id }}" data-url="{{ $cctv->stream_url }}" data-name="{{ $cctv->name }}" data-ip="{{ $cctv->ip }}" data-port="{{ $cctv->port }}">
                                {{ $cctv->name }} ({{ $cctv->ip }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Player Container (hidden by default) -->
                <div id="player-container-{{ $i }}" class="absolute inset-0 flex flex-col hidden z-20 bg-black">
                    <!-- Watermark Logo -->
                    @if($watermarkLogo && $watermarkLogo->value)
                        <style>
                            .custom-watermark-dashboard {
                                position: absolute;
                                top: 6px;
                                right: 6px;
                                height: 16px;
                                z-index: 40;
                                pointer-events: none;
                            }
                            @media (min-width: 768px) {
                                .custom-watermark-dashboard {
                                    top: 12px;
                                    right: 12px;
                                    height: 28px;
                                }
                            }
                        </style>
                        <img src="{{ Storage::url($watermarkLogo->value) }}" alt="Watermark" class="custom-watermark-dashboard opacity-70 drop-shadow-md object-contain" />
                    @endif
                    
                    <!-- Loading Indicator -->
                    <div id="player-loading-{{ $i }}" class="absolute inset-0 flex flex-col items-center justify-center bg-[#070b12]/90 z-20">
                        <i data-lucide="loader-2" class="w-6 h-6 text-indigo-500 animate-spin mb-2"></i>
                        <span class="block text-[10px] font-semibold text-slate-300">Menghubungkan...</span>
                    </div>
                    
                    <div class="flex-1 relative overflow-hidden bg-black flex items-center justify-center">
                        <video id="video-{{ $i }}" class="w-full h-full object-fill relative z-10 hidden" autoplay muted playsinline></video>
                        <iframe id="iframe-{{ $i }}" class="w-full h-full border-0 hidden relative z-10" allow="fullscreen; autoplay"></iframe>
                    </div>

                    <!-- Info overlay -->
                    <div class="absolute bottom-4 left-4 z-30 pointer-events-none">
                        <div class="flex items-center gap-2 font-mono text-xs font-semibold text-white drop-shadow-md bg-black/40 px-2 py-1 rounded-md border border-white/10 backdrop-blur-sm">
                            <span class="h-1.5 w-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                            <span id="label-name-{{ $i }}">Camera</span>
                        </div>
                    </div>

                    <!-- Close/Unload button -->
                    <button onclick="unloadSlot({{ $i }})" class="absolute top-4 right-4 bg-rose-600/20 text-rose-400 hover:bg-rose-600 hover:text-white border border-rose-500/30 p-1.5 rounded-lg transition-all shadow-md">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            </div>
        @endfor
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Custom scrollbar for horizontal scrolling grid */
    .custom-scrollbar::-webkit-scrollbar {
        height: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 0.4);
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(79, 70, 229, 0.5); /* indigo-600 with opacity */
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(79, 70, 229, 0.8);
    }
</style>
@endpush

@push('scripts')
<script>
    let currentItemsPerView = 1;
    let activePlayers = {}; // Keep references to active Hls instances to destroy them properly

    function changeLayout(itemsPerView) {
        currentItemsPerView = itemsPerView;
        
        // Update active class on buttons
        const layouts = [1, 2, 3];
        layouts.forEach(num => {
            const btn = document.getElementById(`btn-layout-${num}`);
            if (num === itemsPerView) {
                btn.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-600 text-white transition-all';
            } else {
                btn.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-400 hover:text-slate-200 transition-all';
            }
        });

        // Update width of all 9 slots and ensure they are all visible to act as a slider
        for (let i = 0; i < 9; i++) {
            const slot = document.getElementById(`slot-${i}`);
            slot.classList.remove('hidden');
            
            // Set inline styles for responsive width based on selection
            if (itemsPerView === 1) {
                slot.style.width = '100%';
            } else if (itemsPerView === 2) {
                slot.style.width = window.innerWidth < 768 ? '100%' : 'calc(50% - 8px)';
            } else if (itemsPerView === 3) {
                if (window.innerWidth < 768) {
                    slot.style.width = '100%';
                } else if (window.innerWidth < 1024) {
                    slot.style.width = 'calc(50% - 8px)';
                } else {
                    slot.style.width = 'calc(33.333% - 10.66px)';
                }
            }
        }
    }
    
    // Add window resize listener to update layout dynamically
    window.addEventListener('resize', () => {
        changeLayout(currentItemsPerView);
    });

    function loadCctvIntoSlot(slotId, cctvId) {
        if (!cctvId) return;

        const selector = document.getElementById(`selector-${slotId}`);
        const selectElement = selector.querySelector('select');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        
        const rawUrl = selectedOption.getAttribute('data-url');
        const cctvName = selectedOption.getAttribute('data-name');
        const cctvIp = selectedOption.getAttribute('data-ip');
        const cctvPort = selectedOption.getAttribute('data-port');

        // Convert RTSP to proper go2rtc path
        let streamUrl = rawUrl;
        let isMp4 = false;
        
        if (rawUrl && rawUrl.toLowerCase().startsWith('rtsp://')) {
            streamUrl = `/go2rtc/api/stream.mp4?src=cctv_${cctvId}`;
            isMp4 = true;
        }

        const playerContainer = document.getElementById(`player-container-${slotId}`);
        const video = document.getElementById(`video-${slotId}`);
        const iframe = document.getElementById(`iframe-${slotId}`);
        const labelName = document.getElementById(`label-name-${slotId}`);
        const labelDetails = document.getElementById(`label-details-${slotId}`);
        const loadingAlert = document.getElementById(`player-loading-${slotId}`);

        // Update labels
        labelName.textContent = cctvName;

        // Unload first if already playing something in this slot
        unloadSlot(slotId);

        // Hide selector, show player
        selector.classList.add('hidden');
        playerContainer.classList.remove('hidden');
        loadingAlert.classList.remove('hidden');

        // Play stream
        if (isMp4) {
            // Use WebRTC ultra-low latency iframe player
            video.classList.add('hidden');
            iframe.classList.remove('hidden');
            iframe.src = streamUrl.replace('/api/stream.mp4', '/stream.html');
            iframe.onload = function() { 
                loadingAlert.classList.add('hidden'); 
                try {
                    const style = document.createElement('style');
                    style.innerHTML = '.info, #info { display: none !important; } video { object-fit: fill !important; width: 100% !important; height: 100% !important; } body { margin: 0; padding: 0; overflow: hidden; }';
                    iframe.contentWindow.document.head.appendChild(style);
                } catch(e) {}
            };
        } else {
            // HLS / HTTP native stream
            iframe.classList.add('hidden');
            video.classList.remove('hidden');
            
            // Hide loading when video starts playing
            video.onplaying = function() { loadingAlert.classList.add('hidden'); };
            video.onloadeddata = function() { loadingAlert.classList.add('hidden'); };
            video.onerror = function() { loadingAlert.classList.add('hidden'); };

            if (Hls.isSupported()) {
                const hls = new Hls();
                hls.loadSource(streamUrl);
                hls.attachMedia(video);
                hls.on(Hls.Events.MANIFEST_PARSED, function() {
                    video.play().catch(e => console.log('Autoplay blocked:', e));
                });
                activePlayers[slotId] = hls;
            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                video.src = streamUrl;
                video.addEventListener('loadedmetadata', function() {
                    video.play().catch(e => console.log('Autoplay blocked:', e));
                });
            }
        }
    }

    function unloadSlot(slotId) {
        const video = document.getElementById(`video-${slotId}`);
        const iframe = document.getElementById(`iframe-${slotId}`);
        
        if (activePlayers[slotId]) {
            activePlayers[slotId].destroy();
            delete activePlayers[slotId];
        }
        
        if (video) {
            video.pause();
            video.removeAttribute('src');
            video.load();
        }
        
        iframe.removeAttribute('src');

        // Show selector, hide player container
        const selector = document.getElementById(`selector-${slotId}`);
        const playerContainer = document.getElementById(`player-container-${slotId}`);
        
        if (selector && playerContainer) {
            selector.classList.remove('hidden');
            playerContainer.classList.add('hidden');
            
            // Reset selector value
            const selectElement = selector.querySelector('select');
            if (selectElement) selectElement.value = "";
        }
    }

    // Initialize layout to 1x1
    document.addEventListener("DOMContentLoaded", () => {
        changeLayout(1);
        fetchDashboardStats();
    });

    async function fetchDashboardStats() {
        try {
            const response = await fetch('/api/cctvs/status');
            const statuses = await response.json();
            
            let onlineCount = 0;
            let offlineCount = 0;
            
            for (const status of Object.values(statuses)) {
                if (status === 'online') {
                    onlineCount++;
                } else {
                    offlineCount++;
                }
            }
            
            const activeEl = document.getElementById('active-streams-count');
            if (activeEl) {
                activeEl.innerHTML = `
                    ${onlineCount}
                    <span class="flex h-2.5 w-2.5 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                `;
            }
            
            const offlineEl = document.getElementById('offline-streams-count');
            if (offlineEl) {
                offlineEl.innerHTML = offlineCount;
            }

            // Highlight active video card
            document.querySelectorAll('.cctv-slot').forEach(slot => {
                slot.classList.remove('border-emerald-500/50', 'shadow-[0_0_15px_rgba(16,185,129,0.1)]');
            });
            Object.keys(activePlayers).forEach(slotId => {
                const selectEl = document.querySelector(`#selector-${slotId} select`);
                const cctvId = selectEl ? selectEl.value : null;
                if (cctvId && statuses[cctvId] === 'online') {
                    document.getElementById(`slot-${slotId}`).classList.add('border-emerald-500/50', 'shadow-[0_0_15px_rgba(16,185,129,0.1)]');
                }
            });
            
        } catch (error) {
            console.error("Failed to fetch real-time status for dashboard:", error);
        } finally {
            // Poll again after 5 seconds
            setTimeout(fetchDashboardStats, 5000);
        }
    }
</script>
@endpush
