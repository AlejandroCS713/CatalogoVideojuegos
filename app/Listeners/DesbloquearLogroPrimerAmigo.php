<?php

namespace App\Listeners;

use App\Events\AmigoAgregado;
use App\Jobs\NotificarLogroDesbloqueado;
use App\Models\users\Logro;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DesbloquearLogroPrimerAmigo implements ShouldQueue
{
    public function handle(AmigoAgregado $event)
    {
        $user = $event->user;

        // Verifica si ya tiene el logro
        if (!$user->logros()->where('nombre', 'Primer Amigo')->exists()) {
            // Asigna el logro
            $logro = Logro::firstOrCreate(['nombre' => 'Primer Amigo', 'descripcion' => 'Has agregado tu primer amigo']);
            $user->logros()->attach($logro->id);
            NotificarLogroDesbloqueado::dispatch($user, $logro);
        }
    }
}
