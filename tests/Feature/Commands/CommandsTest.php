<?php

use App\Listeners\DesbloquearLogroPrimerAmigo;
use App\Models\games\Videojuego;
use App\Models\users\Logro;
use App\Models\users\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'user']);
    Role::firstOrCreate(['name' => 'moderador']);
    Permission::firstOrCreate(['name' => 'crear juegos']);
    Permission::firstOrCreate(['name' => 'editar juegos']);
    Permission::firstOrCreate(['name' => 'eliminar juegos']);
});

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

it('assigns admin role and permissions to the user', function () {
    $user = User::factory()->create([
        'email' => 'user40@example.com',
    ]);

    $this->artisan('make:admin', ['email' => 'user40@example.com'])
        ->expectsOutput("El usuario {$user->email} ahora tiene el rol de admin y los permisos necesarios.")
        ->assertExitCode(0);

    $user->refresh();

    $this->assertTrue($user->hasRole('admin'));
    $this->assertTrue($user->hasPermissionTo('crear juegos'));
    $this->assertTrue($user->hasPermissionTo('editar juegos'));
    $this->assertTrue($user->hasPermissionTo('eliminar juegos'));
});

it('removes admin role and permissions from the user', function () {
    $user = User::factory()->create([
        'email' => 'user40@example.com',
    ]);
    $user->assignRole('admin');
    $user->givePermissionTo(['crear juegos', 'editar juegos', 'eliminar juegos']);

    $this->artisan('remove:admin', ['email' => 'user40@example.com'])
        ->expectsOutput("El usuario {$user->email} ya no es un admin.")
        ->assertExitCode(0);

    $user->refresh();

    $this->assertFalse($user->hasRole('admin'));
    $this->assertFalse($user->hasPermissionTo('crear juegos'));
    $this->assertFalse($user->hasPermissionTo('editar juegos'));
    $this->assertFalse($user->hasPermissionTo('eliminar juegos'));
});

it('fails if user not found', function () {
    $email = 'nonexistent@example.com';

    $this->artisan('app:moderate-user', ['email' => $email])
        ->assertExitCode(Command::FAILURE)
        ->expectsOutput("Usuario con el correo {$email} no encontrado.");
});

it('assigns moderator role to user', function () {
    $user = User::factory()->create(['email' => 'assign@example.com']);

    $this->assertFalse($user->hasRole('moderador'));

    $this->artisan('app:moderate-user', ['email' => $user->email])
        ->assertExitCode(Command::SUCCESS)
        ->expectsOutput("El usuario {$user->email} ahora tiene el rol de moderador.");

    $user->refresh();
    $this->assertTrue($user->hasRole('moderador'));
});

it('does not reassign moderator role if already present', function () {
    $user = User::factory()->create(['email' => 'already_moderator@example.com']);
    $user->assignRole('moderador');

    $this->assertTrue($user->hasRole('moderador'));

    $this->artisan('app:moderate-user', ['email' => $user->email])
        ->assertExitCode(Command::SUCCESS)
        ->expectsOutput("El usuario {$user->email} ya tiene el rol de moderador.");

    $user->refresh();
    $this->assertTrue($user->hasRole('moderador'));
});

it('revokes moderator role from user', function () {
    $user = User::factory()->create(['email' => 'revoke@example.com']);
    $user->assignRole('moderador');

    $this->assertTrue($user->hasRole('moderador'));

    $this->artisan('app:moderate-user', [
        'email' => $user->email,
        '--revoke' => true,
    ])
        ->assertExitCode(Command::SUCCESS)
        ->expectsOutput("El usuario {$user->email} ya no tiene el rol de moderador.");

    $user->refresh();
    $this->assertFalse($user->hasRole('moderador'));
});

it('does not revoke moderator role if not present', function () {
    $user = User::factory()->create(['email' => 'not_moderator@example.com']);

    $this->assertFalse($user->hasRole('moderador'));

    $this->artisan('app:moderate-user', [
        'email' => $user->email,
        '--revoke' => true,
    ])
        ->assertExitCode(Command::SUCCESS)
        ->expectsOutput("El usuario {$user->email} no tiene el rol de moderador para revocar.");

    $user->refresh();
    $this->assertFalse($user->hasRole('moderador'));
});
