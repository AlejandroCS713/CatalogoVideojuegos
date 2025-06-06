<?php

use App\Listeners\DesbloquearLogroPrimerAmigo;
use App\Models\games\Videojuego;
use App\Models\users\Logro;
use App\Models\users\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
/**
it('assigns admin role and permissions to the user', function () {
    $user = User::where('email', 'user40@example.com')->first();

    $this->artisan('make:admin', ['email' => 'user40@example.com'])
        ->expectsOutput("El usuario {$user->email} ahora tiene el rol de admin y los permisos necesarios.")
        ->assertExitCode(0);

    $this->assertTrue($user->hasRole('admin'));

    $this->assertTrue($user->hasPermissionTo('crear juegos'));
    $this->assertTrue($user->hasPermissionTo('editar juegos'));
    $this->assertTrue($user->hasPermissionTo('eliminar juegos'));
});

it('removes admin role and permissions from the user', function () {
    $user = User::where('email', 'user40@example.com')->first();

    $this->artisan('remove:admin', ['email' => 'user40@example.com'])
        ->expectsOutput("El usuario {$user->email} ya no es un admin.")
        ->assertExitCode(0);

    $this->assertFalse($user->hasRole('admin'));

    $this->assertFalse($user->hasPermissionTo('crear juegos'));
    $this->assertFalse($user->hasPermissionTo('editar juegos'));
    $this->assertFalse($user->hasPermissionTo('eliminar juegos'));
});
 **/

