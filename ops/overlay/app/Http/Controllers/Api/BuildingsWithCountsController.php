<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Cctv;

class BuildingsWithCountsController extends Controller
{
    public function __invoke()
    {
        $buildings = Building::select('id','name','lat','lng')->get();
        $counts = Cctv::selectRaw('building_id, status, count(*) as total')
            ->groupBy('building_id','status')->get()->groupBy('building_id');
        $payload = $buildings->map(function($b) use ($counts){
            $g = $counts->get($b->id, collect());
            $online = (int) optional($g->firstWhere('status','online'))->total;
            $offline = (int) optional($g->firstWhere('status','offline'))->total;
            $maint = (int) optional($g->firstWhere('status','maintenance'))->total;
            return [
                'id' => $b->id,
                'name' => $b->name,
                'lat' => $b->lat,
                'lng' => $b->lng,
                'counts' => ['online'=>$online,'offline'=>$offline,'maintenance'=>$maint],
            ];
        });
        return response()->json($payload);
    }
}

