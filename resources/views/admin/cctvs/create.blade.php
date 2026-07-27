@extends('layouts.app')

@section('page_title', 'Create CCTV')

@section('content')
<div class="bg-[#0d1321]/30 border border-slate-800 rounded-3xl p-6 shadow-xl w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.cctvs.index') }}" class="p-2 bg-slate-800/40 text-slate-400 hover:text-slate-200 rounded-xl border border-slate-800 transition-all">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-slate-200">Register CCTV Device</h2>
            <p class="text-xs text-slate-500 mt-1">Configure CCTV network credentials, location markers, and streaming formats.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.cctvs.store') }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column: Fields -->
            <div class="space-y-4">
                <!-- CCTV Name -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">CCTV Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Bundaran HI Main Camera" required
                           class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- IP & Port -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-2">
                        <label for="ip" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">IP Address / Host</label>
                        <input type="text" id="ip" name="ip" value="{{ old('ip') }}" placeholder="e.g. 192.168.1.100" required
                               class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
                        @error('ip')
                            <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="port" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Port</label>
                        <input type="number" id="port" name="port" value="{{ old('port', 80) }}" required
                               class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
                        @error('port')
                            <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Channel & Status -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="channel" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Channel</label>
                        <input type="number" id="channel" name="channel" value="{{ old('channel', 1) }}" required
                               class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
                        @error('channel')
                            <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="status" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Status</label>
                        <select id="status" name="status" required
                                class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm">
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Username & Password -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="username" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">CCTV Username</label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="admin"
                               class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
                        @error('username')
                            <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">CCTV Password</label>
                        <input type="password" id="password" name="password" placeholder="••••••••"
                               class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
                        @error('password')
                            <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Stream URL -->
                <div>
                    <label for="stream_url" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">RTSP / HLS Live Stream URL</label>
                    <input type="text" id="stream_url" name="stream_url" value="{{ old('stream_url') }}" placeholder="e.g. http://server.com/live/stream.m3u8" required
                           class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
                    <span class="block text-[10px] text-slate-500 mt-1">Note: Browsers stream HLS (.m3u8) natively. For standard RTSP, you will need a transcoder/converter setup.</span>
                    @error('stream_url')
                        <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Right Column: Map Coordinates Picker -->
            <div class="space-y-4">
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Geographic Coordinates (Map Picker)</span>
                    <div id="minimap" class="w-full h-[280px] bg-[#070b12] border border-slate-800 rounded-2xl overflow-hidden z-10"></div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        @php
                            $mapCenterLat = \App\Models\Setting::where('key', 'map_center_latitude')->first();
                            $defaultLat = $mapCenterLat && $mapCenterLat->value ? $mapCenterLat->value : '-6.4025';
                        @endphp
                        <label for="latitude" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Latitude</label>
                        <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $defaultLat) }}" required
                               class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 font-mono focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
                        @error('latitude')
                            <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        @php
                            $mapCenterLng = \App\Models\Setting::where('key', 'map_center_longitude')->first();
                            $defaultLng = $mapCenterLng && $mapCenterLng->value ? $mapCenterLng->value : '106.8186';
                        @endphp
                        <label for="longitude" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Longitude</label>
                        <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $defaultLng) }}" required
                               class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 font-mono focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
                        @error('longitude')
                            <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-6 border-t border-slate-800 flex justify-end gap-3">
            <a href="{{ route('admin.cctvs.index') }}" class="px-5 py-2.5 bg-slate-800/40 text-slate-300 border border-slate-850 hover:bg-slate-800 rounded-xl text-xs font-semibold transition-all">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-indigo-600/20 transition-all">
                Save CCTV Device
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Default coordinates
        const defaultLat = parseFloat(document.getElementById('latitude').value) || -6.4025;
        const defaultLng = parseFloat(document.getElementById('longitude').value) || 106.8186;

        // Initialize Map
        const map = L.map('minimap').setView([defaultLat, defaultLng], 12);

        // Google Maps Satellite Hybrid (Earth style)
        L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            attribution: '&copy; Google Maps',
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            maxZoom: 20
        }).addTo(map);

        // Create draggable marker
        let marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

        // Function to update input coordinates
        function updateCoordinates(lat, lng) {
            document.getElementById('latitude').value = parseFloat(lat).toFixed(8);
            document.getElementById('longitude').value = parseFloat(lng).toFixed(8);
        }

        // Initialize coordinates input
        updateCoordinates(defaultLat, defaultLng);

        // Map Click Event
        map.on('click', (e) => {
            const { lat, lng } = e.latlng;
            marker.setLatLng([lat, lng]);
            updateCoordinates(lat, lng);
        });

        // Marker Drag Event
        marker.on('dragend', () => {
            const position = marker.getLatLng();
            updateCoordinates(position.lat, position.lng);
        });

        // Add manual input listeners
        function handleManualInput() {
            const lat = parseFloat(document.getElementById('latitude').value);
            const lng = parseFloat(document.getElementById('longitude').value);
            if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng], map.getZoom());
            }
        }

        document.getElementById('latitude').addEventListener('input', handleManualInput);
        document.getElementById('longitude').addEventListener('input', handleManualInput);
    });
</script>
@endpush
