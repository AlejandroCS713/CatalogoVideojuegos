<?php

namespace App\Listeners;

use App\Events\LogroDesbloqueado;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AsignarLogro implements ShouldQueue {
    use InteractsWithQueue;

    public function handle(LogroDesbloqueado $event) {
        $event->user->logros()->syncWithoutDetaching([$event->logro->id]);
    }
}
