<?php

namespace App\Console\Commands;

use App\Jobs\StopCctvStreamJob;
use App\Models\Cctv;
use Illuminate\Console\Command;

class CctvStreamStopCommand extends Command
{
    protected $signature = 'cctv:stream-stop {id? : CCTV id or omit for all}';
    protected $description = 'Stop RTSP→HLS streaming for a CCTV or all CCTVs';

    public function handle(): int
    {
        $id = $this->argument('id');
        if ($id) {
            StopCctvStreamJob::dispatch((int)$id);
            $this->info("Dispatched stop job for CCTV #{$id}");
            return self::SUCCESS;
        }
        Cctv::query()->pluck('id')->each(fn($cid)=> StopCctvStreamJob::dispatch((int)$cid));
        $this->info('Dispatched stop jobs for all CCTVs.');
        return self::SUCCESS;
    }
}

