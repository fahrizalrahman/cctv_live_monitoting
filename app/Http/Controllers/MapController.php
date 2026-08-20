<?php

namespace App\Http\Controllers;

use App\Models\Cctv;
use App\Models\CctvGroup;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $cctvQuery = Cctv::with('group')->where('is_visible', true);
        $groupQuery = CctvGroup::with(['cctvs' => function ($query) {
            $query->where('is_visible', true);
        }])->orderBy('name');

        if ($user && $user->hasRole('viewer') && $user->cctvGroups()->exists()) {
            $groupIds = $user->cctvGroups->pluck('id');
            $cctvQuery->whereIn('cctv_group_id', $groupIds);
            $groupQuery->whereIn('id', $groupIds);
        }

        $cctvs = $cctvQuery->get();
        $groups = $groupQuery->get();
        
        return view('welcome', compact('cctvs', 'groups'));
    }
}
