<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $appLogo = Setting::where('key', 'app_logo')->first();
        $appName = Setting::where('key', 'app_name')->first();
        $mapMarkerIcon = Setting::where('key', 'map_marker_icon')->first();
        $mapCenterLat = Setting::where('key', 'map_center_latitude')->first();
        $mapCenterLng = Setting::where('key', 'map_center_longitude')->first();
        $mapZoomLevel = Setting::where('key', 'map_zoom_level')->first();
        
        return view('admin.settings.index', compact('appLogo', 'appName', 'mapMarkerIcon', 'mapCenterLat', 'mapCenterLng', 'mapZoomLevel'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'nullable|string|max:255',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'map_marker_icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'map_center_latitude' => 'nullable|numeric|between:-90,90',
            'map_center_longitude' => 'nullable|numeric|between:-180,180',
            'map_zoom_level' => 'nullable|integer|between:1,20',
        ]);

        if ($request->has('app_name')) {
            Setting::updateOrCreate(
                ['key' => 'app_name'],
                ['value' => $request->app_name]
            );
        }

        if ($request->filled('map_center_latitude')) {
            Setting::updateOrCreate(
                ['key' => 'map_center_latitude'],
                ['value' => $request->map_center_latitude]
            );
        }

        if ($request->filled('map_center_longitude')) {
            Setting::updateOrCreate(
                ['key' => 'map_center_longitude'],
                ['value' => $request->map_center_longitude]
            );
        }

        if ($request->filled('map_zoom_level')) {
            Setting::updateOrCreate(
                ['key' => 'map_zoom_level'],
                ['value' => $request->map_zoom_level]
            );
        }

        if ($request->hasFile('app_logo')) {
            $file = $request->file('app_logo');
            $filename = time() . '_logo_' . $file->getClientOriginalName();
            $path = $file->storeAs('logos', $filename, 'public');
            Setting::updateOrCreate(['key' => 'app_logo'], ['value' => $path]);
        }

        if ($request->hasFile('map_marker_icon')) {
            $file = $request->file('map_marker_icon');
            $filename = time() . '_marker_' . $file->getClientOriginalName();
            $path = $file->storeAs('logos', $filename, 'public');
            Setting::updateOrCreate(['key' => 'map_marker_icon'], ['value' => $path]);
        }

        return redirect()->route('admin.settings.index')->with('success', 'App settings updated successfully.');
    }
}
