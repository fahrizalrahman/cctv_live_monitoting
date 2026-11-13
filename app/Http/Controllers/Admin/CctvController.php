<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cctv;
use App\Models\CctvGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;

class CctvController extends Controller
{
    private function getFilteredQuery(Request $request)
    {
        $query = Cctv::with('group');
        if ($request->filled('cctv_group_id')) {
            $query->where('cctv_group_id', $request->cctv_group_id);
        }
        
        if ($request->filled('status')) {
            $allCctvs = Cctv::all(['id', 'ip', 'port', 'status']);
            $matchedIds = [];
            
            foreach ($allCctvs as $cctv) {
                $isOnline = false;
                if ($cctv->status === 'active') {
                    $port = !empty($cctv->port) ? $cctv->port : 554;
                    // Use a short timeout of 0.2s for faster backend filtering
                    $fp = @fsockopen($cctv->ip, $port, $errno, $errstr, 0.2);
                    if ($fp) {
                        $isOnline = true;
                        @fclose($fp);
                    }
                }
                
                if ($request->status === 'online' && $isOnline) {
                    $matchedIds[] = $cctv->id;
                } elseif ($request->status === 'offline' && !$isOnline) {
                    $matchedIds[] = $cctv->id;
                }
            }
            $query->whereIn('id', $matchedIds);
        }
        
        if ($request->filled('visibility')) {
            if ($request->visibility === 'visible') {
                $query->where('is_visible', true);
            } elseif ($request->visibility === 'hidden') {
                $query->where('is_visible', false);
            }
        }
        
        return $query;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('manage cctvs');
        
        $query = $this->getFilteredQuery($request);
        
        $cctvs = $query->latest()->paginate(10)->withQueryString();
        $groups = CctvGroup::orderBy('name')->get();
        
        return view('admin.cctvs.index', compact('cctvs', 'groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('manage cctvs');
        $groups = CctvGroup::all();
        return view('admin.cctvs.create', compact('groups'));
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
            'cctv_group_id' => 'nullable|exists:cctv_groups,id',
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
        $groups = CctvGroup::all();
        return view('admin.cctvs.edit', compact('cctv', 'groups'));
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
            'cctv_group_id' => 'nullable|exists:cctv_groups,id',
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
     * Synchronize CCTV streams from database to go2rtc config and ensure service is running.
     */
    protected function syncGo2Rtc()
    {
        self::ensureGo2RtcSynced(true);
    }

    /**
     * Ensure all RTSP streams from database are synchronized to go2rtc live in memory
     * via PUT /api/streams without restarting the service or touching server-owned config.
     */
    public static function ensureGo2RtcSynced(bool $forceSync = false): void
    {
        try {
            $cctvs = Cctv::orderBy('id')->get(['id', 'stream_url']);

            $yamlContent = "api:\n  origin: '*'\n\nstreams:\n";
            $streamsMap = [];

            // Khusus 4 kamera RW 13 Cisalak: override sementara dengan ONVIF langsung ke IP kamera (primer) & NVR (sekunder)
            $onvifOverrides = [
                172 => [
                    'onvif://admin:Admin%20123@10.12.0.26:80',
                    'onvif://admin:Admin%20123@10.11.0.8:80?channel=1'
                ],
                173 => [
                    'onvif://admin:Admin%20123@10.12.0.27:80',
                    'onvif://admin:Admin%20123@10.11.0.8:80?channel=2'
                ],
                174 => [
                    'onvif://admin:Admin%20123@10.12.0.28:80',
                    'onvif://admin:Admin%20123@10.11.0.8:80?channel=3'
                ],
                175 => [
                    'onvif://admin:Admin%20123@10.12.0.29:80',
                    'onvif://admin:Admin%20123@10.11.0.8:80?channel=4'
                ],
            ];

            foreach ($cctvs as $cctv) {
                // Jika termasuk salah satu dari 4 kamera RW 13 Cisalak, generate stream ONVIF
                if (isset($onvifOverrides[$cctv->id])) {
                    $sources = $onvifOverrides[$cctv->id];
                    $yamlContent .= "  cctv_{$cctv->id}:\n";
                    foreach ($sources as $s) {
                        $yamlContent .= "    - \"{$s}\"\n";
                    }
                    $streamsMap["cctv_{$cctv->id}"] = $sources;
                    continue;
                }

                $rawUrl = trim($cctv->stream_url ?? '');
                if ($rawUrl === '') {
                    continue;
                }

                // Strip accidental ffmpeg: prefix or trailing fragments if present
                if (str_starts_with(strtolower($rawUrl), 'ffmpeg:')) {
                    $rawUrl = substr($rawUrl, 7);
                }
                if (str_contains($rawUrl, '#')) {
                    $rawUrl = explode('#', $rawUrl)[0];
                }

                if (str_starts_with(strtolower($rawUrl), 'rtsp://') || str_starts_with(strtolower($rawUrl), 'onvif://')) {
                    // Encode spaces (e.g. password "Admin 123" -> "Admin%20123") so URL parser doesn't fail
                    $rawUrl = str_replace(' ', '%20', $rawUrl);
                    $escapedUrl = str_replace('"', '\"', $rawUrl);

                    $yamlContent .= "  cctv_{$cctv->id}: \"{$escapedUrl}\"\n";
                    $streamsMap["cctv_{$cctv->id}"] = $rawUrl;
                }
            }

            $hashPath = sys_get_temp_dir() . '/cctv_go2rtc_synced_v8_' . md5(base_path()) . '.hash';
            $runtimeYamlPath = sys_get_temp_dir() . '/cctv_go2rtc_runtime_' . md5(base_path()) . '.yaml';
            $newHash = md5($yamlContent);
            $lastHash = file_exists($hashPath) ? trim((string) @file_get_contents($hashPath)) : '';

            $configChanged = $forceSync || ($newHash !== $lastHash);

            if ($configChanged) {
                @file_put_contents($runtimeYamlPath, $yamlContent);
            }

            // Also sync to C:\go2rtc\go2rtc.yaml on Windows if that directory exists
            if (PHP_OS_FAMILY === 'Windows' && is_dir('C:\\go2rtc')) {
                $winYamlPath = 'C:\\go2rtc\\go2rtc.yaml';
                $currentWinYaml = file_exists($winYamlPath) ? (string) @file_get_contents($winYamlPath) : '';
                if ($currentWinYaml !== $yamlContent) {
                    @file_put_contents($winYamlPath, $yamlContent);
                    $configChanged = true;
                }
            }

            $isRunning = self::isGo2RtcRunning();

            if (!$isRunning) {
                @file_put_contents($runtimeYamlPath, $yamlContent);
                self::startGo2RtcProcess();
                for ($i = 0; $i < 10; $i++) {
                    usleep(100000);
                    if (self::isGo2RtcRunning()) {
                        $isRunning = true;
                        break;
                    }
                }
                $configChanged = true;
            }

            if ($isRunning && !$configChanged && count($streamsMap) >= 10) {
                // Verify go2rtc actually has the streams registered in memory (in case go2rtc was restarted)
                try {
                    $resp = Http::timeout(1)->get('http://127.0.0.1:1984/api/streams');
                    if ($resp->successful() && substr_count($resp->body(), '"cctv_') < 10) {
                        $configChanged = true;
                    }
                } catch (\Exception $e) {
                }
            }

            if ($isRunning && $configChanged) {
                // Dynamically register all streams in go2rtc memory via PUT /api/streams WITHOUT restarting go2rtc
                foreach (array_chunk($streamsMap, 30, true) as $chunk) {
                    try {
                        Http::pool(function (\Illuminate\Http\Client\Pool $pool) use ($chunk) {
                            foreach ($chunk as $name => $sources) {
                                $query = 'name=' . urlencode($name);
                                foreach ((array) $sources as $src) {
                                    $query .= '&src=' . urlencode($src);
                                }
                                $pool->timeout(2)->put('http://127.0.0.1:1984/api/streams?' . $query);
                            }
                        });
                    } catch (\Exception $e) {
                    }
                }

                @file_put_contents($hashPath, $newHash);
            }
        } catch (\Exception $e) {
            // Do not break page load if go2rtc sync fails
        }
    }

    private static function isGo2RtcRunning(): bool
    {
        $fp = @fsockopen('127.0.0.1', 1984, $errno, $errstr, 0.3);
        if ($fp) {
            @fclose($fp);
            return true;
        }
        return false;
    }

    private static function startGo2RtcProcess(): void
    {
        try {
            if (PHP_OS_FAMILY === 'Windows') {
                $bin = file_exists('C:\\go2rtc\\go2rtc.exe') ? 'C:\\go2rtc\\go2rtc.exe' : base_path('go2rtc.exe');
                $cfg = file_exists('C:\\go2rtc\\go2rtc.yaml') ? 'C:\\go2rtc\\go2rtc.yaml' : base_path('go2rtc.yaml');
                if (file_exists($bin)) {
                    @pclose(@popen("start /B \"\" \"{$bin}\" -config \"{$cfg}\"", 'r'));
                }
            } else {
                $bin = base_path('go2rtc');
                $runtimeCfg = sys_get_temp_dir() . '/cctv_go2rtc_runtime_' . md5(base_path()) . '.yaml';
                $cfg = file_exists($runtimeCfg) ? $runtimeCfg : base_path('go2rtc.yaml');
                $log = sys_get_temp_dir() . '/cctv_go2rtc_' . md5(base_path()) . '.log';
                if (file_exists($bin)) {
                    @chmod($bin, 0755);
                    @exec('nohup ' . escapeshellarg($bin) . ' -config ' . escapeshellarg($cfg) . ' >> ' . escapeshellarg($log) . ' 2>&1 &');
                }
            }
        } catch (\Exception $e) {
            // Ignore process spawn errors
        }
    }

    public function toggleVisibility(Cctv $cctv)
    {
        $cctv->update(['is_visible' => !$cctv->is_visible]);
        $status = $cctv->is_visible ? 'visible on public map' : 'hidden from public map';
        return redirect()->back()->with('success', "CCTV {$cctv->name} is now {$status}.");
    }

    public function bulkVisibility(Request $request)
    {
        Gate::authorize('manage cctvs');

        $action = $request->input('action');
        if (!in_array($action, ['show', 'hide'])) {
            return redirect()->back()->with('error', 'Invalid bulk action.');
        }

        $query = $this->getFilteredQuery($request);
        // Extract IDs instead of running a bulk update directly to ensure we only update what was matched
        $ids = $query->pluck('cctvs.id')->toArray();

        if (count($ids) > 0) {
            Cctv::whereIn('id', $ids)->update(['is_visible' => ($action === 'show')]);
            $statusStr = $action === 'show' ? 'shown on public map' : 'hidden from public map';
            return redirect()->back()->with('success', count($ids) . " CCTV(s) have been successfully {$statusStr}.");
        }

        return redirect()->back()->with('error', 'No CCTVs matched the current filters to update.');
    }
}
