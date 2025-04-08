<?php
namespace App\Listeners;

use App\Events\PrimerMensajeEnviado;
use App\Jobs\NotificarLogroDesbloqueado;
use App\Models\users\Logro;

class DesbloquearLogroPrimerMensaje
{
    public function handle(PrimerMensajeEnviado $event)
    {
        $logro = Logro::where('nombre', 'Primer Mensaje')->first();

        if (!$logro) {
            return;
        }

        $user = $event->sender;

        if (!$user->logros()->where('logro_id', $logro->id)->exists()) {
            $user->logros()->attach($logro->id);
            NotificarLogroDesbloqueado::dispatch($user, $logro);
        }

        $friend = $event->receiver;

        if (!$friend->logros()->where('logro_id', $logro->id)->exists()) {
            $friend->logros()->attach($logro->id);
            NotificarLogroDesbloqueado::dispatch($friend, $logro);
        }
    }
}
