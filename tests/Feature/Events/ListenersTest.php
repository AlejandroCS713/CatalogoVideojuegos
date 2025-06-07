<?php

use App\Events\PerfilActualizado;
use App\Listeners\SincronizarDatosExternos;
use App\Models\users\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

describe('SincronizarDatosExternos Listener', function () {
    beforeEach(function () {
        Event::fake();
    });

    it('logs only name change when name is updated', function () {
        $user = User::factory()->create(['name' => 'OldName']);
        $user->name = 'UpdatedName';

        $cambios = ['name'];
        $event = new PerfilActualizado($user, $cambios);
        $listener = new SincronizarDatosExternos();

        Log::shouldReceive('info')
            ->once()
            ->with("Perfil del usuario {$user->email} actualizado. Cambios: " . json_encode($cambios));

        Log::shouldReceive('info')
            ->once()
            ->with("El apodo del usuario {$user->email} ha cambiado a {$user->name}.");

        Log::shouldNotReceive('info')
            ->with("La contraseña del usuario {$user->email} ha sido actualizada.");
        Log::shouldNotReceive('info')
            ->with("El avatar del usuario {$user->email} ha cambiado a {$user->avatar}.");


        $listener->handle($event);
    });

    it('logs only password change when password is updated', function () {
        $user = User::factory()->create(['email' => 'pass_test@example.com']);
        $user->password = 'new_password_hash';

        $cambios = ['password'];
        $event = new PerfilActualizado($user, $cambios);
        $listener = new SincronizarDatosExternos();

        Log::shouldReceive('info')
            ->once()
            ->with("Perfil del usuario {$user->email} actualizado. Cambios: " . json_encode($cambios));

        Log::shouldReceive('info')
            ->once()
            ->with("La contraseña del usuario {$user->email} ha sido actualizada.");

        Log::shouldNotReceive('info')
            ->with("El apodo del usuario {$user->email} ha cambiado a {$user->name}.");
        Log::shouldNotReceive('info')
            ->with("El avatar del usuario {$user->email} ha cambiado a {$user->avatar}.");

        $listener->handle($event);
    });

    it('logs general message when no specific changes are provided', function () {
        $user = User::factory()->create();
        $cambios = [];
        $event = new PerfilActualizado($user, $cambios);
        $listener = new SincronizarDatosExternos();

        Log::shouldReceive('info')
            ->once()
            ->with("Perfil del usuario {$user->email} actualizado. Cambios: " . json_encode($cambios));

        Log::shouldNotReceive('info')
            ->with("El apodo del usuario {$user->email} ha cambiado a {$user->name}.");
        Log::shouldNotReceive('info')
            ->with("La contraseña del usuario {$user->email} ha sido actualizada.");
        Log::shouldNotReceive('info')
            ->with("El avatar del usuario {$user->email} ha cambiado a {$user->avatar}.");

        $listener->handle($event);
    });
});
