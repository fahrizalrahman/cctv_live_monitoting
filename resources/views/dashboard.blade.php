@extends('layouts.app')

@section('page_title', 'Live Monitoring Dashboard')

@section('content')
<!-- Dashboard Welcome Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-slate-200">System Dashboard</h2>
        <p class="text-xs text-slate-500 mt-1">Real-time camera metrics, active system users, and stream status logs.</p>
    </div>
</div>

<!-- Stats Cards Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
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
            <span class="block text-3xl font-extrabold text-emerald-400 mt-2 flex items-center gap-2">
                {{ $stats['active_cctv'] }}
                <span class="flex h-2.5 w-2.5 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </span>
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
            <span class="block text-3xl font-extrabold text-rose-500 mt-2">{{ $stats['inactive_cctv'] }}</span>
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
<div class="bg-[#0d1321]/30 border border-slate-800 rounded-3xl p-6 shadow-xl mb-8">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-200">Interactive CCTV Live Grid</h2>
            <p class="text-xs text-slate-500 mt-1">Select layout and assign CCTV feeds to monitor concurrently.</p>
        </div>

        <!-- Layout Controls -->
        <div class="flex items-center bg-[#090d16] p-1 border border-slate-800 rounded-xl gap-1">
            <button onclick="changeLayout(1)" id="btn-layout-1" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-600 text-white transition-all">
                <i data-lucide="square" class="w-3.5 h-3.5"></i>
                <span>1x1 View</span>
            </button>
            <button onclick="changeLayout(4)" id="btn-layout-4" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-400 hover:text-slate-200 transition-all">
                <i data-lucide="layout-grid" class="w-3.5 h-3.5"></i>
                <span>2x2 View</span>
            </button>
            <button onclick="changeLayout(9)" id="btn-layout-9" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-400 hover:text-slate-200 transition-all">
                <i data-lucide="grid-3x3" class="w-3.5 h-3.5"></i>
                <span>3x3 View</span>
            </button>
        </div>
    </div>

    <!-- The Grid Container -->
    <div id="cctv-grid" class="grid grid-cols-1 gap-6 transition-all duration-300">
        <!-- Render 9 slots but toggle visibility based on layout -->
        @for($i = 0; $i < 9; $i++)
            <div id="slot-{{ $i }}" class="cctv-slot bg-[#070b12] border border-slate-800/80 rounded-2xl overflow-hidden aspect-video flex flex-col relative group transition-all hover:border-indigo-500/50">
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

                <!-- Video player area -->
                <div id="player-container-{{ $i }}" class="hidden w-full h-full relative">
                    <video id="video-{{ $i }}" class="w-full h-full object-cover" autoplay muted playsinline controls></video>
                    
                    <!-- Info overlay -->
                    <div class="absolute top-4 left-4 bg-[#090d16]/80 backdrop-blur-md border border-slate-800 rounded-lg px-3 py-1.5 text-[10px] text-slate-300 font-mono pointer-events-none">
                        <div class="font-semibold text-slate-100 flex items-center gap-1.5">
                            <span class="h-1.5 w-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                            <span id="label-name-{{ $i }}">Camera</span>
                        </div>
                        <div class="text-[9px] text-slate-500 mt-0.5" id="label-details-{{ $i }}">IP: 0.0.0.0</div>
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
@endsection

@push('scripts')
<script>
    let currentSlotsCount = 1;
    let activePlayers = {}; // Keep references to active Hls instances to destroy them properly

    function changeLayout(slotsCount) {
        currentSlotsCount = slotsCount;
        
        // Update grid layout css classes
        const gridContainer = document.getElementById('cctv-grid');
        gridContainer.className = 'grid gap-6 transition-all duration-300';
        
        if (slotsCount === 1) {
            gridContainer.classList.add('grid-cols-1');
        } else if (slotsCount === 4) {
            gridContainer.classList.add('grid-cols-1', 'md:grid-cols-2');
        } else if (slotsCount === 9) {
            gridContainer.classList.add('grid-cols-1', 'md:grid-cols-2', 'lg:grid-cols-3');
        }

        // Toggle visibility of slots
        for (let i = 0; i < 9; i++) {
            const slot = document.getElementById(`slot-${i}`);
            if (i < slotsCount) {
                slot.classList.remove('hidden');
            } else {
                slot.classList.add('hidden');
                unloadSlot(i); // Free memory and stop video streams for hidden slots
            }
        }

        // Update active class on buttons
        const layouts = [1, 4, 9];
        layouts.forEach(num => {
            const btn = document.getElementById(`btn-layout-${num}`);
            if (num === slotsCount) {
                btn.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-600 text-white transition-all';
            } else {
                btn.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-400 hover:text-slate-200 transition-all';
            }
        });
    }

    function loadCctvIntoSlot(slotId, cctvId) {
        if (!cctvId) return;

        const selector = document.getElementById(`selector-${slotId}`);
        const selectElement = selector.querySelector('select');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        
        const rawUrl = selectedOption.getAttribute('data-url');
        const cctvName = selectedOption.getAttribute('data-name');
        const cctvIp = selectedOption.getAttribute('data-ip');
        const cctvPort = selectedOption.getAttribute('data-port');

        // Convert RTSP to HLS dynamic path
        let streamUrl = rawUrl;
        if (rawUrl && rawUrl.toLowerCase().startsWith('rtsp://')) {
            if (window.location.protocol === 'https:') {
                streamUrl = `/stream/api/stream.m3u8?src=cctv_${cctvId}`;
            } else {
                streamUrl = `http://${window.location.hostname}:1984/api/stream.m3u8?src=cctv_${cctvId}`;
            }
        }

        const playerContainer = document.getElementById(`player-container-${slotId}`);
        const video = document.getElementById(`video-${slotId}`);
        const labelName = document.getElementById(`label-name-${slotId}`);
        const labelDetails = document.getElementById(`label-details-${slotId}`);

        // Update labels
        labelName.textContent = cctvName;
        labelDetails.textContent = `IP: ${cctvIp}:${cctvPort}`;

        // Unload first if already playing something in this slot
        unloadSlot(slotId);

        // Hide selector, show player
        selector.classList.add('hidden');
        playerContainer.classList.remove('hidden');

        // Play stream
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

    function unloadSlot(slotId) {
        // Destroy Hls player instance
        if (activePlayers[slotId]) {
            activePlayers[slotId].destroy();
            delete activePlayers[slotId];
        }

        const video = document.getElementById(`video-${slotId}`);
        if (video) {
            video.pause();
            video.removeAttribute('src');
            video.load();
        }

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
    });
</script>
@endpush
