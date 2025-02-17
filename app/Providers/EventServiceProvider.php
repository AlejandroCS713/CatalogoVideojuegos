<?php

namespace App\Providers;

use App\Events\AmigoAgregado;
use App\Events\LogroDesbloqueado;
use App\Listeners\AsignarLogro;
use App\Listeners\DesbloquearLogroPrimerAmigo;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        LogroDesbloqueado::class => [
            AsignarLogro::class,
        ],
        AmigoAgregado::class => [
            DesbloquearLogroPrimerAmigo::class,
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
