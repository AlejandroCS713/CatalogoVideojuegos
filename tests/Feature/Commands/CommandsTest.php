<?php

use App\Listeners\DesbloquearLogroPrimerAmigo;
use App\Models\games\Videojuego;
use App\Models\users\Logro;
use App\Models\users\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
it('deletes videojuegos later or equal to the given date in ascending order', function () {
    $videojuego1 = Videojuego::create([
        'nombre' => 'Juego Futuro',
        'fecha_lanzamiento' => '2026-03-15',
    ]);

    $videojuego2 = Videojuego::create([
        'nombre' => 'Juego Más Futuro',
        'fecha_lanzamiento' => '2027-05-20',
    ]);

    $this->artisan('games:delete-ancient')
        ->expectsQuestion('¿Cuál es la fecha límite para eliminar los juegos? (Formato: yyyy-mm-dd)', '2026-03-15')
        ->expectsChoice('¿En qué orden quieres eliminar los juegos?', 'Ascendente', ['Ascendente', 'Descendente'])
        ->expectsConfirmation('¿Estás seguro que deseas eliminar 2 juegos?', 'yes')
        ->assertExitCode(0);

    $videojuego1 = Videojuego::find($videojuego1->id);
    expect($videojuego1)->toBeNull();

    $videojuego2 = Videojuego::find($videojuego2->id);
    expect($videojuego2)->toBeNull();
});
/**
it('deletes videojuegos earlier or equal to the given date in descending order', function () {
    $videojuego1 = Videojuego::create([
        'nombre' => 'Juego Más Futuro',
        'fecha_lanzamiento' => '1980-01-02',
    ]);


    $this->artisan('games:delete-ancient')
        ->expectsQuestion('¿Cuál es la fecha límite para eliminar los juegos? (Formato: yyyy-mm-dd)', '1980-01-02')
        ->expectsChoice('¿En qué orden quieres eliminar los juegos?', 'Descendente', ['Ascendente', 'Descendente'])
        ->expectsConfirmation('¿Estás seguro que deseas eliminar 1 juegos?', 'yes')
        ->assertExitCode(0);

    $videojuego1 = Videojuego::find($videojuego1->id);
    expect($videojuego1)->toBeNull();

});
 * */

it('assigns admin role and permissions to the user', function () {
    $user = User::where('email', 'user40@example.com')->first();  // Aseguramos que el usuario ya existe

    // Ejecutar el comando 'make:admin' para asignar el rol de admin
    $this->artisan('make:admin', ['email' => 'user40@example.com'])
        ->expectsOutput("El usuario {$user->email} ahora tiene el rol de admin y los permisos necesarios.")
        ->assertExitCode(0);

    // Verificar que el usuario ahora tiene el rol de admin
    $this->assertTrue($user->hasRole('admin'));

    // Verificar que el usuario tiene los permisos necesarios
    $this->assertTrue($user->hasPermissionTo('crear juegos'));
    $this->assertTrue($user->hasPermissionTo('editar juegos'));
    $this->assertTrue($user->hasPermissionTo('eliminar juegos'));
});

it('removes admin role and permissions from the user', function () {
    $user = User::where('email', 'user40@example.com')->first();  // Aseguramos que el usuario ya existe

    // Ejecutar el comando 'remove:admin' para eliminar el rol de admin y revocar permisos
    $this->artisan('remove:admin', ['email' => 'user40@example.com'])
        ->expectsOutput("El usuario {$user->email} ya no es un admin.")
        ->assertExitCode(0);

    // Verificar que el usuario ya no tiene el rol de admin
    $this->assertFalse($user->hasRole('admin'));

    // Verificar que los permisos han sido revocados
    $this->assertFalse($user->hasPermissionTo('crear juegos'));
    $this->assertFalse($user->hasPermissionTo('editar juegos'));
    $this->assertFalse($user->hasPermissionTo('eliminar juegos'));
});

