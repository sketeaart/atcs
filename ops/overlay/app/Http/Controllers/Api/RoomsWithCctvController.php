<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;

class RoomsWithCctvController extends Controller
{
    public function __invoke()
    {
        $rooms = Room::with(['building:id,name','cctvs' => function($q){ $q->select('id','building_id','room_id','status','lat','lng'); }])->get(['id','building_id','name']);
        $payload = [];
        foreach ($rooms as $room) {
            foreach ($room->cctvs as $c) {
                $payload[] = [
                    'id' => $c->id,
                    'building_id' => $c->building_id,
                    'room_id' => $room->id,
                    'building_name' => optional($room->building)->name,
                    'name' => $room->name,
                    'status' => $c->status,
                    'lat' => $c->lat,
                    'lng' => $c->lng,
                ];
            }
        }
        return response()->json($payload);
    }
}

