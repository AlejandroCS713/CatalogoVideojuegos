<?php

namespace App\Listeners;

use App\Events\PerfilActualizado;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SincronizarDatosExternos
{

    public function __construct()
    {

    }

    public function handle(PerfilActualizado $event)
    {

        Log::info("Perfil del usuario {$event->user->email} actualizado. Cambios: " . json_encode($event->cambiosRealizados));

        if (in_array('name', $event->cambiosRealizados)) {
            Log::info("El apodo del usuario {$event->user->email} ha cambiado a {$event->user->name}.");
        }

        if (in_array('password', $event->cambiosRealizados)) {
            Log::info("La contraseña del usuario {$event->user->email} ha sido actualizada.");
        }


    }
}
