<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cctv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CctvStatusController extends Controller
{
    /**
     * Check real-time status of all active CCTVs concurrently using non-blocking sockets.
     */
    public function index()
    {
        // Cache the status for 2 seconds to prevent overwhelming the server while keeping it real-time
        $statuses = Cache::remember('cctv_realtime_statuses', 2, function () {
            $cctvs = Cctv::all(['id', 'ip', 'port', 'status']);
            $sockets = [];
            $results = [];

            foreach ($cctvs as $cctv) {
                if ($cctv->status !== 'active') {
                    $results[$cctv->id] = 'offline';
                    continue;
                }

                $host = $cctv->ip;
                $port = !empty($cctv->port) ? $cctv->port : 554;
                
                // Use a reliable synchronous check with a very short timeout (1 second)
                // This is safer and more reliable across all OS environments (Windows, Mac, Linux)
                // compared to async streams which can be buggy on some XAMPP versions.
                $fp = @fsockopen($host, $port, $errno, $errstr, 1);
                
                if ($fp) {
                    $results[$cctv->id] = 'online';
                    @fclose($fp);
                } else {
                    $results[$cctv->id] = 'offline';
                }
            }

            return $results;
        });

        return response()->json($statuses);
    }
}
