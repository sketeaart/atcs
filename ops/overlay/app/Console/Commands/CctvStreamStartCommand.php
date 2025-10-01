<?php

namespace App\Console\Commands;

use App\Jobs\StartCctvStreamJob;
use App\Models\Cctv;
use Illuminate\Console\Command;

class CctvStreamStartCommand extends Command
{
    protected $signature = 'cctv:stream-start {id? : CCTV id or omit for all}';
    protected $description = 'Start RTSP→HLS streaming for a CCTV or all CCTVs';

    public function handle(): int
    {
        $id = $this->argument('id');
        if ($id) {
            StartCctvStreamJob::dispatch((int)$id);
            $this->info("Dispatched start job for CCTV #{$id}");
            return self::SUCCESS;
        }
        Cctv::query()->pluck('id')->each(fn($cid)=> StartCctvStreamJob::dispatch((int)$cid));
        $this->info('Dispatched start jobs for all CCTVs.');
        return self::SUCCESS;
    }
}

