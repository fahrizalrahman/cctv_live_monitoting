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
        // Cache the status for 30 seconds to prevent overwhelming the server with rapid pings
        $statuses = Cache::remember('cctv_realtime_statuses', 30, function () {
            $cctvs = Cctv::all(['id', 'ip', 'port', 'status']);
            
            $sockets = [];
            $results = [];

            // Initialize results for all CCTVs to offline by default
            foreach ($cctvs as $cctv) {
                // If it's administratively inactive, we don't even ping it.
                if ($cctv->status !== 'active') {
                    $results[$cctv->id] = 'offline';
                    continue;
                }

                $host = $cctv->ip;
                $port = $cctv->port;
                
                // Open a non-blocking async socket connection
                $socket = @stream_socket_client("tcp://$host:$port", $errno, $errstr, 1, STREAM_CLIENT_ASYNC_CONNECT | STREAM_CLIENT_CONNECT);
                if ($socket) {
                    stream_set_blocking($socket, false);
                    $sockets[$cctv->id] = $socket;
                } else {
                    $results[$cctv->id] = 'offline';
                }
            }

            // Wait for sockets to connect or fail with a total timeout of 1 second (1,000,000 microseconds)
            if (!empty($sockets)) {
                $read = [];
                $write = $sockets;
                $except = $sockets;
                
                // 1 second timeout for all concurrent requests
                if (@stream_select($read, $write, $except, 1, 0) > 0) {
                    foreach ($write as $id => $socket) {
                        // Double check if connection is fully established
                        $error = 0;
                        $errlen = sizeof($error);
                        $sockCheck = socket_import_stream($socket);
                        
                        if ($sockCheck) {
                            $error = socket_get_option($sockCheck, SOL_SOCKET, SO_ERROR);
                            if ($error === 0) {
                                $results[$id] = 'online';
                            } else {
                                $results[$id] = 'offline';
                            }
                        } else {
                            // Fallback if socket extension is not fully compatible with stream
                            // If it's in the write array, it means it connected.
                            $results[$id] = 'online';
                        }
                    }
                    foreach ($except as $id => $socket) {
                        $results[$id] = 'offline';
                    }
                }

                // Any socket not in write array after select timed out
                foreach ($sockets as $id => $socket) {
                    if (!isset($results[$id])) {
                        $results[$id] = 'offline';
                    }
                    @fclose($socket);
                }
            }

            return $results;
        });

        return response()->json($statuses);
    }
}
