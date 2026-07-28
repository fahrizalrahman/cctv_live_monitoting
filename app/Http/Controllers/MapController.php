<?php

namespace App\Http\Controllers;

use App\Models\Cctv;
use App\Models\CctvGroup;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        $cctvs = Cctv::with('group')->get();
        $groups = CctvGroup::with('cctvs')->orderBy('name')->get();
        return view('welcome', compact('cctvs', 'groups'));
    }
}
