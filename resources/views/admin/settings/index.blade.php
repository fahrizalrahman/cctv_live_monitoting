@extends('layouts.app')

@section('page_title', 'App Settings')

@section('content')
<div class="bg-[#0d1321]/30 border border-slate-800 rounded-3xl p-6 shadow-xl w-full">
    <div class="flex items-center gap-3 mb-6">
        <div class="p-2 bg-indigo-600/20 text-indigo-400 rounded-xl border border-indigo-500/20">
            <i data-lucide="settings" class="w-5 h-5"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold text-slate-200">Application Settings</h2>
            <p class="text-xs text-slate-500 mt-1">Configure global application settings and branding.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- App Name -->
        <div>
            <label for="app_name" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Application Name</label>
            <input type="text" id="app_name" name="app_name" value="{{ old('app_name', $appName->value ?? 'CCTV MONITOR') }}"
                   class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
            <span class="block text-[10px] text-slate-500 mt-1">This name will appear on the dashboard, maps, and sidebar.</span>
            @error('app_name')
                <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Running Text -->
        <div>
            <label for="running_text" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Running Text (Marquee)</label>
            <input type="text" id="running_text" name="running_text" value="{{ old('running_text', $runningText->value ?? '') }}" placeholder="Contoh: Selamat datang di sistem pantau CCTV terpadu..."
                   class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
            <span class="block text-[10px] text-slate-500 mt-1">Text to scroll at the bottom of the public pages. Leave empty to hide.</span>
            @error('running_text')
                <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- App Logo -->
        <div>
            <label for="app_logo" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Application Logo</label>
            
            @if($appLogo && $appLogo->value)
                <div class="mb-4">
                    <p class="text-xs text-slate-500 mb-2">Current Logo:</p>
                    <div class="p-4 bg-[#090d16] border border-slate-800 rounded-xl inline-block">
                        <img src="{{ Storage::url($appLogo->value) }}" alt="App Logo" class="h-12 object-contain" />
                    </div>
                </div>
            @endif

            <input type="file" id="app_logo" name="app_logo" accept="image/*"
                   class="block w-full text-sm text-slate-400
                          file:mr-4 file:py-2.5 file:px-4
                          file:rounded-xl file:border-0
                          file:text-xs file:font-semibold
                          file:bg-indigo-600/10 file:text-indigo-400
                          hover:file:bg-indigo-600/20 transition-all cursor-pointer" />
            <span class="block text-[10px] text-slate-500 mt-2">Recommended size: 200x50px (PNG or SVG). Max size 2MB.</span>
            @error('app_logo')
                <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Watermark Logo -->
        <div class="pt-4 border-t border-slate-800/50">
            <label for="watermark_logo" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Video Watermark Logo</label>
            
            @if(isset($watermarkLogo) && $watermarkLogo->value)
                <div class="mb-4">
                    <p class="text-xs text-slate-500 mb-2">Current Watermark:</p>
                    <div class="p-4 bg-[#090d16] border border-slate-800 rounded-xl inline-block relative overflow-hidden">
                        <!-- Add a video-like background to preview how it looks -->
                        <div class="absolute inset-0 bg-slate-800/30"></div>
                        <img src="{{ Storage::url($watermarkLogo->value) }}" alt="Watermark Logo" class="h-10 object-contain relative z-10 opacity-70" />
                    </div>
                </div>
            @endif

            <input type="file" id="watermark_logo" name="watermark_logo" accept="image/*"
                   class="block w-full text-sm text-slate-400
                          file:mr-4 file:py-2.5 file:px-4
                          file:rounded-xl file:border-0
                          file:text-xs file:font-semibold
                          file:bg-sky-600/10 file:text-sky-400
                          hover:file:bg-sky-600/20 transition-all cursor-pointer" />
            <span class="block text-[10px] text-slate-500 mt-2">Semi-transparent PNG recommended. Will be displayed in the top right corner of live videos. Max size 2MB.</span>
            @error('watermark_logo')
                <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Map Center Coordinates & Zoom -->
        <div class="pt-4 border-t border-slate-800/50">
            <h3 class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Map Center & Zoom</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="map_center_latitude" class="block text-[10px] text-slate-500 mb-1">Default Latitude</label>
                    <input type="text" id="map_center_latitude" name="map_center_latitude" value="{{ old('map_center_latitude', $mapCenterLat->value ?? '-6.4025') }}"
                           class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 font-mono focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
                    @error('map_center_latitude')
                        <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="map_center_longitude" class="block text-[10px] text-slate-500 mb-1">Default Longitude</label>
                    <input type="text" id="map_center_longitude" name="map_center_longitude" value="{{ old('map_center_longitude', $mapCenterLng->value ?? '106.8186') }}"
                           class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 font-mono focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
                    @error('map_center_longitude')
                        <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="map_zoom_level" class="block text-[10px] text-slate-500 mb-1">Default Zoom Level</label>
                    <input type="number" id="map_zoom_level" name="map_zoom_level" value="{{ old('map_zoom_level', $mapZoomLevel->value ?? '12') }}" min="1" max="20"
                           class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 font-mono focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
                    @error('map_zoom_level')
                        <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <span class="block text-[10px] text-slate-500 mt-2">Set the default coordinates and zoom level (1-20) where the map will focus when first loaded.</span>
        </div>

        <!-- Map Marker Icon -->
        <div class="pt-4 border-t border-slate-800/50">
            <label for="map_marker_icon" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Map Marker Icon</label>
            
            @if(isset($mapMarkerIcon) && $mapMarkerIcon->value)
                <div class="mb-4">
                    <p class="text-xs text-slate-500 mb-2">Current Marker:</p>
                    <div class="p-2 bg-[#090d16] border border-slate-800 rounded-xl inline-block">
                        <img src="{{ Storage::url($mapMarkerIcon->value) }}" alt="Marker Icon" class="h-8 object-contain" />
                    </div>
                </div>
            @endif

            <input type="file" id="map_marker_icon" name="map_marker_icon" accept="image/*"
                   class="block w-full text-sm text-slate-400
                          file:mr-4 file:py-2.5 file:px-4
                          file:rounded-xl file:border-0
                          file:text-xs file:font-semibold
                          file:bg-emerald-600/10 file:text-emerald-400
                          hover:file:bg-emerald-600/20 transition-all cursor-pointer" />
            <span class="block text-[10px] text-slate-500 mt-2">Custom icon for CCTV points on the map. Recommended size: 32x32px.</span>
            @error('map_marker_icon')
                <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-6 border-t border-slate-800 flex justify-end gap-3">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-indigo-600/20 transition-all flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Save Settings</span>
            </button>
        </div>
    </form>
</div>
@endsection
