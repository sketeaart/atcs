<?php

namespace App\Events;

use App\Models\Cctv;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CctvStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;

    public function __construct(Cctv $cctv)
    {
        $this->payload = [
            'id' => $cctv->id,
            'status' => $cctv->status,
            'lat' => $cctv->lat,
            'lng' => $cctv->lng,
        ];
    }

    public function broadcastOn(): Channel
    {
        return new Channel('cctv.status');
    }
}

