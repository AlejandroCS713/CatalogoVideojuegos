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


it('assigns admin role and permissions to the user using app:manage-admin', function () {
    $user = User::factory()->create([
        'email' => 'test_user_admin@example.com',
    ]);

    Permission::firstOrCreate(['name' => 'crear juegos']);
    Permission::firstOrCreate(['name' => 'editar juegos']);
    Permission::firstOrCreate(['name' => 'eliminar juegos']);

    $this->artisan('app:manage-admin', ['email' => $user->email])
        ->expectsOutput("User {$user->email} now has the admin role and associated permissions.")
        ->assertExitCode(0);

    $user->refresh();

    expect($user->hasRole('admin'))->toBeTrue();

    expect($user->hasPermissionTo('crear juegos'))->toBeTrue();
    expect($user->hasPermissionTo('editar juegos'))->toBeTrue();
    expect($user->hasPermissionTo('eliminar juegos'))->toBeTrue();
});

it('revokes admin role and permissions from the user using app:manage-admin --revoke', function () {
    $user = User::factory()->create([
        'email' => 'test_user_remove_admin@example.com',
    ]);
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    $permissions = ['crear juegos', 'editar juegos', 'eliminar juegos'];

    foreach ($permissions as $permissionName) {
        Permission::firstOrCreate(['name' => $permissionName]);
    }

    $user->assignRole($adminRole);
    $user->givePermissionTo($permissions);

    expect($user->hasRole('admin'))->toBeTrue();
    expect($user->hasPermissionTo('crear juegos'))->toBeTrue();

    $this->artisan('app:manage-admin', ['email' => $user->email, '--revoke' => true])
        ->expectsOutput("User {$user->email} no longer has the admin role and its associated permissions have been revoked.")
        ->assertExitCode(0);

    $user->refresh();

    expect($user->hasRole('admin'))->toBeFalse();

    expect($user->hasPermissionTo('crear juegos'))->toBeFalse();
    expect($user->hasPermissionTo('editar juegos'))->toBeFalse();
    expect($user->hasPermissionTo('eliminar juegos'))->toBeFalse();
});

it('informs if trying to assign admin role to an already admin user', function () {
    $user = User::factory()->create([
        'email' => 'existing_admin@example.com',
    ]);
    $user->assignRole('admin');

    $this->artisan('app:manage-admin', ['email' => $user->email])
        ->expectsOutput("User {$user->email} already has the admin role.")
        ->assertExitCode(0);

    $user->refresh();
    expect($user->hasRole('admin'))->toBeTrue();
});

it('informs if trying to revoke admin role from a non-admin user', function () {
    $user = User::factory()->create([
        'email' => 'non_admin_user@example.com',
    ]);

    $this->artisan('app:manage-admin', ['email' => $user->email, '--revoke' => true])
        ->expectsOutput("User {$user->email} does not have the admin role to revoke.")
        ->assertExitCode(0);

    $user->refresh();
    expect($user->hasRole('admin'))->toBeFalse();
});

it('fails if the user email is not found', function () {
    $nonExistentEmail = 'nonexistent@example.com';

    $this->artisan('app:manage-admin', ['email' => $nonExistentEmail])
        ->expectsOutput("User with email {$nonExistentEmail} not found.")
        ->assertExitCode(1);
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
