@extends('layouts.app')

@section('page_title', 'Edit CCTV')

@section('content')
<div class="bg-[#0d1321]/30 border border-slate-800 rounded-3xl p-6 shadow-xl w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.cctvs.index') }}" class="p-2 bg-slate-800/40 text-slate-400 hover:text-slate-200 rounded-xl border border-slate-800 transition-all">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-slate-200">Modify CCTV Device</h2>
            <p class="text-xs text-slate-500 mt-1">Update CCTV device details, coordinates, or connection details.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.cctvs.update', $cctv->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column: Fields -->
            <div class="space-y-4">
                <!-- CCTV Name & Group -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">CCTV Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $cctv->name) }}" placeholder="e.g. Bundaran HI Main Camera" required
                               class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
                        @error('name')
                            <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="cctv_group_id" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Group <span class="text-slate-500 font-normal capitalize">(Optional)</span></label>
                        <select id="cctv_group_id" name="cctv_group_id"
                                class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm">
                            <option value="">No Group</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ old('cctv_group_id', $cctv->cctv_group_id) == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                        @error('cctv_group_id')
                            <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- IP & Port -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-2">
                        <label for="ip" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">IP Address / Host</label>
                        <input type="text" id="ip" name="ip" value="{{ old('ip', $cctv->ip) }}" required
                               class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
                        @error('ip')
                            <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="port" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Port</label>
                        <input type="number" id="port" name="port" value="{{ old('port', $cctv->port) }}" required
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
                        <input type="number" id="channel" name="channel" value="{{ old('channel', $cctv->channel) }}" required
                               class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
                        @error('channel')
                            <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="status" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Status</label>
                        <select id="status" name="status" required
                                class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm">
                            <option value="active" {{ old('status', $cctv->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $cctv->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                        <input type="text" id="username" name="username" value="{{ old('username', $cctv->username) }}" placeholder="admin"
                               class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
                        @error('username')
                            <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">CCTV Password</label>
                        <input type="password" id="password" name="password" placeholder="Leave blank to keep unchanged"
                               class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
                        @error('password')
                            <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Stream URL -->
                <div>
                    <label for="stream_url" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">RTSP / HLS Live Stream URL</label>
                    <input type="text" id="stream_url" name="stream_url" value="{{ old('stream_url', $cctv->stream_url) }}" required
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
                        <label for="latitude" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Latitude</label>
                        <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $cctv->latitude) }}" required
                               class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 font-mono focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
                        @error('latitude')
                            <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="longitude" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Longitude</label>
                        <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $cctv->longitude) }}" required
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
                Update CCTV Device
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Prepopulated coordinates from DB
        const savedLat = {{ $cctv->latitude }};
        const savedLng = {{ $cctv->longitude }};
        const defaultZoom = {{ \App\Models\Setting::where('key', 'map_zoom_level')->first()->value ?? 12 }};

        // Initialize Map
        const map = L.map('minimap').setView([savedLat, savedLng], defaultZoom);

        // Google Maps Satellite Hybrid (Earth style)
        L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            attribution: '&copy; Google Maps',
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            maxZoom: 20
        }).addTo(map);

        // Create draggable marker at saved position
        let marker = L.marker([savedLat, savedLng], { draggable: true }).addTo(map);

        // Function to update input coordinates
        function updateCoordinates(lat, lng) {
            document.getElementById('latitude').value = parseFloat(lat).toFixed(8);
            document.getElementById('longitude').value = parseFloat(lng).toFixed(8);
        }

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
