<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Room;
use App\Models\Cctv;

class SummaryController extends Controller
{
    public function __invoke()
    {
        $total_buildings = Building::count();
        $total_rooms = Room::count();
        $cctv_online = Cctv::where('status','online')->count();
        $cctv_offline = Cctv::where('status','offline')->count();
        $cctv_maintenance = Cctv::where('status','maintenance')->count();
        return response()->json(compact('total_buildings','total_rooms','cctv_online','cctv_offline','cctv_maintenance'));
    }
}

