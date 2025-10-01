<?php

namespace App\Observers;

use App\Events\CctvStatusUpdated;
use App\Models\Cctv;

class CctvObserver
{
    public function updated(Cctv $cctv): void
    {
        if ($cctv->wasChanged('status')) {
            event(new CctvStatusUpdated($cctv));
        }
    }
}

