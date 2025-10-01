<?php

namespace App\Jobs;

use App\Models\Cctv;
use App\Services\FfmpegStreamService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StartCctvStreamJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $cctvId;

    public function __construct(int $cctvId)
    {
        $this->onQueue('streaming');
        $this->cctvId = $cctvId;
    }

    public function handle(FfmpegStreamService $service): void
    {
        $cctv = Cctv::find($this->cctvId);
        if (!$cctv) return;
        $service->start($cctv);
    }
}

