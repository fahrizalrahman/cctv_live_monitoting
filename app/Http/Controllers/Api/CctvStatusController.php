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
                
                // Open a non-blocking async socket connection
                $socket = @stream_socket_client("tcp://$host:$port", $errno, $errstr, 1, STREAM_CLIENT_ASYNC_CONNECT);
                if ($socket) {
                    stream_set_blocking($socket, false);
                    $sockets[$cctv->id] = $socket;
                } else {
                    $results[$cctv->id] = 'offline';
                }
            }

            // Wait for sockets to connect or fail with a total timeout of 1 second
            if (!empty($sockets)) {
                $read = [];
                $write = $sockets;
                $except = $sockets;
                
                if (@stream_select($read, $write, $except, 1) > 0) {
                    foreach ($write as $id => $socket) {
                        $sockCheck = socket_import_stream($socket);
                        if ($sockCheck) {
                            $error = socket_get_option($sockCheck, SOL_SOCKET, SO_ERROR);
                            if ($error === 0) {
                                $results[$id] = 'online';
                            } else {
                                $results[$id] = 'offline';
                            }
                        } else {
                            $results[$id] = 'online'; // Fallback
                        }
                    }
                    foreach ($except as $id => $socket) {
                        $results[$id] = 'offline';
                    }
                }

                // Close all sockets and mark remaining as offline
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
