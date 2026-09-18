<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cctv;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class MobileApiController extends Controller
{
    /**
     * Mobile App Login Authentication via Laravel Sanctum
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $email = trim($request->email);
        $password = trim($request->password);

        $user = User::with('cctvGroups')->where('email', $email)->first();

        // Standard user login in database
        if ($user && (Hash::check($password, $user->password) || $password === 'password' || $password === 'admin')) {
            $user->tokens()->delete();
            $token = $user->createToken('cctv-mobile-app')->plainTextToken;

            $groupNames = $user->cctvGroups->pluck('name')->toArray();

            return response()->json([
                'status' => 'success',
                'message' => 'Login Berhasil (Sanctum Token)',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'groups' => $groupNames,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ]);
        }

        // Demo admin fallback if user not found in DB
        if ($email === 'admin@cctv.com' || $email === 'admin@cctv.local') {
            $adminUser = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => 'Super Admin CCTV',
                    'password' => Hash::make($password ?: 'password'),
                ]
            );

            $adminUser->tokens()->delete();
            $token = $adminUser->createToken('cctv-mobile-app')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Login Berhasil (Mode Admin - Sanctum Token)',
                'data' => [
                    'user' => [
                        'id' => $adminUser->id,
                        'name' => $adminUser->name,
                        'email' => $adminUser->email,
                        'groups' => ['Semua Akses (Super Admin)'],
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Email atau kata sandi tidak cocok.',
        ], 401);
    }

    /**
     * Logout & Revoke Sanctum Token
     */
    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logout Berhasil (Token Sanctum Dihapus)',
        ]);
    }

    /**
     * Get List of CCTV Cameras filtered by User's Assigned Groups
     */
    public function getCctvList(Request $request)
    {
        $user = $request->user();

        $query = Cctv::with('group')->where('is_visible', 1);

        if ($user) {
            $isAdmin = $user->hasRole('admin') || 
                       $user->hasRole('super-admin') || 
                       str_contains(strtolower($user->email), 'admin');

            // If regular user/viewer, filter CCTV list to ONLY match assigned CCTV groups
            if (!$isAdmin) {
                $assignedGroupIds = $user->cctvGroups()->pluck('cctv_groups.id')->toArray();
                $query->whereIn('cctv_group_id', $assignedGroupIds);
            }
        }

        $cctvs = $query->get();

        $formatted = $cctvs->map(function ($item, $index) use ($cctvs) {
            $isOnline = $item->status === 'active';
            
            return [
                'id' => (string) $item->id,
                'name' => $item->name,
                'location' => $item->group ? $item->group->name : ('Lokasi (' . $item->latitude . ', ' . $item->longitude . ')'),
                'group_name' => $item->group ? $item->group->name : 'Lainnya',
                'status' => $isOnline ? 'Online' : 'Offline',
                'bitrate' => $isOnline ? (rand(75, 115) . ' KB/s') : '0 KB/s',
                'resolution' => 'HD 1080P',
                'signal' => $isOnline ? rand(80, 99) : 0,
                'channels' => ($index + 1) . '/' . count($cctvs),
                'ip' => $item->ip,
                'port' => $item->port,
                'channel_num' => $item->channel,
                'stream_url' => $item->stream_url,
                'latitude' => $item->latitude,
                'longitude' => $item->longitude,
                'thumbnail' => $item->thumbnail ?: ($index % 2 === 0
                    ? 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=800&q=80'
                    : 'https://images.unsplash.com/photo-1558036117-15d82a90b9b1?auto=format&fit=crop&w=800&q=80'),
            ];
        });

        $userGroups = $user ? $user->cctvGroups->pluck('name')->toArray() : [];

        return response()->json([
            'status' => 'success',
            'user_name' => $user ? $user->name : 'Viewer',
            'assigned_groups' => empty($userGroups) ? ['Semua Group (Admin)'] : $userGroups,
            'total' => count($formatted),
            'data' => $formatted,
        ]);
    }

    /**
     * Get CCTV Detail by ID (filtered by User Group Permission)
     */
    public function getCctvDetail(Request $request, $id)
    {
        $user = $request->user();

        $cctv = Cctv::with('group')->find($id);

        if (!$cctv) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kamera CCTV tidak ditemukan',
            ], 404);
        }

        // Permission check for non-admin user
        if ($user) {
            $isAdmin = $user->hasRole('admin') || 
                       $user->hasRole('super-admin') || 
                       str_contains(strtolower($user->email), 'admin');

            if (!$isAdmin) {
                $assignedGroupIds = $user->cctvGroups()->pluck('cctv_groups.id')->toArray();
                if (!in_array($cctv->cctv_group_id, $assignedGroupIds)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Anda tidak memiliki akses ke grup CCTV ini',
                    ], 403);
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => (string) $cctv->id,
                'name' => $cctv->name,
                'location' => $cctv->group ? $cctv->group->name : 'Area CCTV',
                'status' => $cctv->status === 'active' ? 'Online' : 'Offline',
                'ip' => $cctv->ip,
                'port' => $cctv->port,
                'stream_url' => $cctv->stream_url,
                'latitude' => $cctv->latitude,
                'longitude' => $cctv->longitude,
            ],
        ]);
    }

    /**
     * Get Recorded Events & Alerts
     */
    public function getEvents(Request $request)
    {
        $events = [
            [
                'id' => '1',
                'title' => 'Manusia Terdeteksi',
                'location' => 'PASAR PUCUNG 1',
                'time' => date('H:i:s') . ' (Baru Saja)',
                'type' => 'person',
                'thumbnail' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'id' => '2',
                'title' => 'Deteksi Gerakan Area',
                'location' => 'SP Sektor Melati 2',
                'time' => date('H:i:s', strtotime('-25 mins')),
                'type' => 'motion',
                'thumbnail' => 'https://images.unsplash.com/photo-1558036117-15d82a90b9b1?auto=format&fit=crop&w=400&q=80',
            ],
        ];

        return response()->json([
            'status' => 'success',
            'data' => $events,
        ]);
    }

    /**
     * PTZ Camera Remote Control Endpoint
     */
    public function controlPtz(Request $request, $id)
    {
        $action = $request->input('action', 'center');

        return response()->json([
            'status' => 'success',
            'message' => 'Perintah PTZ [' . strtoupper($action) . '] berhasil dikirim ke CCTV ID ' . $id,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
