<?php

use App\Events\PerfilActualizado;
use App\Listeners\SincronizarDatosExternos;
use App\Models\users\User;
use App\Notifications\PerfilActualizadoNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

describe('SincronizarDatosExternos Listener', function () {
    beforeEach(function () {
        Event::fake();
        Notification::fake();
    });
    it('logs only name change when name is updated', function () {
        $user = User::factory()->create(['name' => 'OldName', 'email' => 'name_test@example.com']);
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

        Log::shouldReceive('info')
            ->once()
            ->with(Mockery::on(function ($message) use ($user) {
                return str_contains($message, "Notificación de perfil actualizado enviada al correo de {$user->email}.");
            }));

        Log::shouldNotReceive('info')
            ->with("La contraseña del usuario {$user->email} ha sido actualizada.");
        Log::shouldNotReceive('info')
            ->with("El avatar del usuario {$user->email} ha cambiado a {$user->avatar}.");

        $listener->handle($event);

        Notification::assertSentTo(
            $user,
            PerfilActualizadoNotification::class,
            function ($notification) use ($user, $cambios) {
                return $notification->user->is($user) && $notification->cambiosRealizados === $cambios;
            }
        );
    });

    it('logs only password change when password is updated', function () {
        $user = User::factory()->create(['email' => 'pass_test@example.com']);
        $user->password = 'new_password_hash_placeholder';

        $cambios = ['password'];
        $event = new PerfilActualizado($user, $cambios);
        $listener = new SincronizarDatosExternos();

        Log::shouldReceive('info')
            ->once()
            ->with("Perfil del usuario {$user->email} actualizado. Cambios: " . json_encode($cambios));

        Log::shouldReceive('info')
            ->once()
            ->with("La contraseña del usuario {$user->email} ha sido actualizada.");

        Log::shouldReceive('info')
            ->once()
            ->with(Mockery::on(function ($message) use ($user) {
                return str_contains($message, "Notificación de perfil actualizado enviada al correo de {$user->email}.");
            }));

        Log::shouldNotReceive('info')
            ->with("El apodo del usuario {$user->email} ha cambiado a {$user->name}.");
        Log::shouldNotReceive('info')
            ->with("El avatar del usuario {$user->email} ha cambiado a {$user->avatar}.");

        $listener->handle($event);

        Notification::assertSentTo(
            $user,
            PerfilActualizadoNotification::class,
            function ($notification) use ($user, $cambios) {
                return $notification->user->is($user) && $notification->cambiosRealizados === $cambios;
            }
        );
    });

    it('logs general message when no specific changes are provided', function () {
        $user = User::factory()->create(['email' => 'general_test@example.com']);
        $cambios = [];

        $event = new PerfilActualizado($user, $cambios);
        $listener = new SincronizarDatosExternos();

        Log::shouldReceive('info')
            ->once()
            ->with("Perfil del usuario {$user->email} actualizado. Cambios: " . json_encode($cambios));

        Log::shouldReceive('info')
            ->once()
            ->with(Mockery::on(function ($message) use ($user) {
                return str_contains($message, "Notificación de perfil actualizado enviada al correo de {$user->email}.");
            }));

        Log::shouldNotReceive('info')
            ->with("El apodo del usuario {$user->email} ha cambiado a {$user->name}.");
        Log::shouldNotReceive('info')
            ->with("La contraseña del usuario {$user->email} ha sido actualizada.");
        Log::shouldNotReceive('info')
            ->with("El avatar del usuario {$user->email} ha cambiado a {$user->avatar}.");

        $listener->handle($event);

        Notification::assertSentTo(
            $user,
            PerfilActualizadoNotification::class,
            function ($notification) use ($user, $cambios) {
                return $notification->user->is($user) && $notification->cambiosRealizados === $cambios;
            }
        );
    });
});

