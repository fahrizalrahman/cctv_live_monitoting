<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cctv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;

class CctvController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('manage cctvs');
        $cctvs = Cctv::paginate(10);
        return view('admin.cctvs.index', compact('cctvs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('manage cctvs');
        return view('admin.cctvs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('manage cctvs');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip' => 'required|string|max:255',
            'port' => 'required|integer',
            'channel' => 'required|integer',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'stream_url' => 'required|string|max:1000',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'status' => 'required|string|in:active,inactive',
        ]);

        Cctv::create($validated);

        $this->syncGo2Rtc();

        return redirect()->route('admin.cctvs.index')->with('success', 'CCTV successfully created.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cctv $cctv)
    {
        Gate::authorize('manage cctvs');
        return view('admin.cctvs.edit', compact('cctv'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cctv $cctv)
    {
        Gate::authorize('manage cctvs');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip' => 'required|string|max:255',
            'port' => 'required|integer',
            'channel' => 'required|integer',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'stream_url' => 'required|string|max:1000',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'status' => 'required|string|in:active,inactive',
        ]);

        $cctv->update($validated);

        $this->syncGo2Rtc();

        return redirect()->route('admin.cctvs.index')->with('success', 'CCTV successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cctv $cctv)
    {
        Gate::authorize('manage cctvs');
        $cctv->delete();

        $this->syncGo2Rtc();

        return redirect()->route('admin.cctvs.index')->with('success', 'CCTV successfully deleted.');
    }

    /**
     * Synchronize CCTV streams from database to go2rtc config and restart the service.
     */
    protected function syncGo2Rtc()
    {
        try {
            $cctvs = Cctv::all();
            
            $yamlContent = "streams:\n";
            foreach ($cctvs as $cctv) {
                if (str_starts_with(strtolower($cctv->stream_url), 'rtsp://')) {
                    $yamlContent .= "  cctv_{$cctv->id}: \"{$cctv->stream_url}\"\n";
                }
            }
            
            file_put_contents(base_path('go2rtc.yaml'), $yamlContent);
            
            Http::timeout(2)->post('http://127.0.0.1:1984/api/restart');
        } catch (\Exception $e) {
            // Swallow connection resets from go2rtc restarting itself
        }
    }
}
