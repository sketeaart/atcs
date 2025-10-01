<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;

class RoomsWithCctvController extends Controller
{
    public function __invoke()
    {
        $rooms = Room::with(['cctvs' => function($q){ $q->select('id','room_id','status','lat','lng'); }])->get();
        $payload = [];
        foreach ($rooms as $room) {
            foreach ($room->cctvs as $c) {
                $payload[] = [
                    'id' => $c->id,
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

