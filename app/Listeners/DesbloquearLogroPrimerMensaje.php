<?php
namespace App\Listeners;

use App\Events\PrimerMensajeEnviado;
use App\Jobs\NotificarLogroDesbloqueado;
use App\Models\users\Logro;

class DesbloquearLogroPrimerMensaje
{
    public function handle(PrimerMensajeEnviado $event)
    {
        $user = $event->sender;

        if (!$user->logros()->where('nombre', 'Primer Mensaje')->exists()) {
            $logro = Logro::firstOrCreate([
                'nombre' => 'Primer Mensaje',
                'descripcion' => 'Has enviado tu primer mensaje a un amigo'
            ]);

            $user->logros()->attach($logro->id);

            NotificarLogroDesbloqueado::dispatch($user, $logro);
        }

        $friend = $event->receiver;

        if (!$friend->logros()->where('nombre', 'Primer Mensaje')->exists()) {
            $logro = Logro::firstOrCreate([
                'nombre' => 'Primer Mensaje',
                'descripcion' => 'Has recibido tu primer mensaje de un amigo'
            ]);

            $friend->logros()->attach($logro->id);

            NotificarLogroDesbloqueado::dispatch($friend, $logro);
        }
    }
}
