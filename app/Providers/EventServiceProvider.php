<?php

namespace App\Providers;

use App\Events\AmigoAgregado;
use App\Events\PerfilActualizado;
use App\Events\PrimerMensajeEnviado;
use App\Listeners\DesbloquearLogroPrimerAmigo;
use App\Listeners\SincronizarDatosExternos;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AmigoAgregado::class => [
            DesbloquearLogroPrimerAmigo::class,
        ],
        PrimerMensajeEnviado::class =>[
            PrimerMensajeEnviado::class,
        ],PerfilActualizado::class => [
            SincronizarDatosExternos::class,
        ],
    ];

    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
