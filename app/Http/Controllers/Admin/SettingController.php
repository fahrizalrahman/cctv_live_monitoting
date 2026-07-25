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
        return view('admin.settings.index', compact('appLogo', 'appName', 'mapMarkerIcon'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'nullable|string|max:255',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'map_marker_icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->has('app_name')) {
            Setting::updateOrCreate(
                ['key' => 'app_name'],
                ['value' => $request->app_name]
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
