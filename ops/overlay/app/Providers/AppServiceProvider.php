<?php

namespace App\Providers;

use App\Models\Cctv;
use App\Observers\CctvObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Cctv::observe(CctvObserver::class);
    }
}

