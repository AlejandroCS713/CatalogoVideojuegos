<?php

namespace App\Listeners;

use App\Events\AmigoAgregado;
use App\Jobs\NotificarLogroDesbloqueado;
use App\Models\users\Logro;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class DesbloquearLogroPrimerAmigo implements ShouldQueue
{
    public function handle(AmigoAgregado $event)
    {
        $user = $event->user;

        if (!$user->logros()->where('nombre', 'Primer Amigo')->exists()) {
            $logro = Logro::firstOrCreate([
                'nombre' => 'Primer Amigo',
                'descripcion' => 'Has agregado tu primer amigo'
            ]);
            $user->logros()->attach($logro->id);
            NotificarLogroDesbloqueado::dispatch($user, $logro);
        }

        $friend = $event->friend;
        if (!$friend->logros()->where('nombre', 'Primer Amigo')->exists()) {
            $logro = Logro::firstOrCreate([
                'nombre' => 'Primer Amigo',
                'descripcion' => 'Has agregado tu primer amigo'
            ]);
            $friend->logros()->attach($logro->id);
            NotificarLogroDesbloqueado::dispatch($friend, $logro);
        }
    }
}
