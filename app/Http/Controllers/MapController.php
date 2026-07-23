<?php

namespace App\Http\Controllers;

use App\Models\Cctv;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        $cctvs = Cctv::all();
        return view('welcome', compact('cctvs'));
    }
}
