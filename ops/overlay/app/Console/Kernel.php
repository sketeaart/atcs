<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Health check or auto-restart logic can be added here.
        // $schedule->command('cctv:stream-start')->everyFiveMinutes();
    }
}

