<?php

namespace App\Http\Controllers;

use App\Models\Cctv;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $cctvQuery = Cctv::query();

        if ($user && $user->hasRole('viewer') && $user->cctvGroups()->exists()) {
            $cctvQuery->whereIn('cctv_group_id', $user->cctvGroups->pluck('id'));
        }

        $stats = [
            'total_cctv' => (clone $cctvQuery)->count(),
            'active_cctv' => (clone $cctvQuery)->where('status', 'active')->count(),
            'inactive_cctv' => (clone $cctvQuery)->where('status', 'inactive')->count(),
            'total_users' => User::count(),
        ];

        $cctvs = $cctvQuery->get();

        return view('dashboard', compact('stats', 'cctvs'));
    }
}
