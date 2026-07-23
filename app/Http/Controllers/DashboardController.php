<?php

namespace App\Http\Controllers;

use App\Models\Cctv;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_cctv' => Cctv::count(),
            'active_cctv' => Cctv::where('status', 'active')->count(),
            'inactive_cctv' => Cctv::where('status', 'inactive')->count(),
            'total_users' => User::count(),
        ];

        $cctvs = Cctv::all();

        return view('dashboard', compact('stats', 'cctvs'));
    }
}
