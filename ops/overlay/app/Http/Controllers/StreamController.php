<?php

namespace App\Http\Controllers;

use App\Models\Cctv;

class StreamController extends Controller
{
    public function show(Cctv $cctv)
    {
        return view('pages.stream', compact('cctv'));
    }
}

