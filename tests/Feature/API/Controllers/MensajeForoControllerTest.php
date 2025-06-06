<?php

namespace Tests\Feature\API\Controllers;

use App\Models\Foro\Foro;
use App\Models\Foro\MensajeForo;
use App\Models\users\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'user']);
    Role::firstOrCreate(['name' => 'moderador']);
    Role::firstOrCreate(['name' => 'admin']);
    $this->baseUrl = '/api';
});

it('returns a paginated list of messages for a specific forum', function () {
    $foro = Foro::factory()->create();
    MensajeForo::factory()->count(5)->create(['foro_id' => $foro->id]);

    $response = $this->getJson("{$this->baseUrl}/foros/{$foro->id}/mensajes");

    $response->assertOk()
        ->assertJsonCount(5, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'contenido', 'usuario']
            ],
            'meta' => [
                'total', 'per_page', 'current_page',
                'last_page', 'from', 'to', 'path', 'links'
            ]
        ]);
});

it('creates a new message when authenticated as a verified user with "user" role', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->assignRole('user');
    Sanctum::actingAs($user);

    $foro = Foro::factory()->create();
    $messageContent = 'Este es un nuevo mensaje de prueba.';

    $response = $this->postJson("{$this->baseUrl}/foros/{$foro->id}/mensajes", [
        'contenido' => $messageContent,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.contenido', $messageContent)
        ->assertJsonPath('data.usuario.id', $user->id);

    $this->assertDatabaseHas('mensaje_foros', [
        'contenido' => $messageContent,
        'foro_id' => $foro->id,
        'usuario_id' => $user->id,
    ]);
});

it('returns 401 when creating a message without authentication', function () {
    $foro = Foro::factory()->create();
    $this->postJson("{$this->baseUrl}/foros/{$foro->id}/mensajes", [
        'contenido' => 'Mensaje de invitado.',
    ])->assertUnauthorized();
});

it('fails validation if message content is missing', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->assignRole('user');
    Sanctum::actingAs($user);

    $foro = Foro::factory()->create();

    $response = $this->postJson("{$this->baseUrl}/foros/{$foro->id}/mensajes", [
        'contenido' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('contenido');
});

it('shows details of a specific message', function () {
    $mensaje = MensajeForo::factory()->create();

    $this->getJson("{$this->baseUrl}/mensajes/{$mensaje->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $mensaje->id)
        ->assertJsonPath('data.contenido', $mensaje->contenido)
        ->assertJsonStructure([
            'data' => [
                'id',
                'contenido',
                'usuario' => ['id', 'name'],
                'respuestas',
                'created_at',
                'updated_at'
            ]
        ]);
});

it('returns 404 when showing a non-existent message', function () {
    $this->getJson("{$this->baseUrl}/mensajes/9999")->assertNotFound();
});

it('updates a message when authenticated as the owner', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $mensaje = MensajeForo::factory()->create(['usuario_id' => $user->id]);
    $newContent = 'Contenido del mensaje actualizado.';

    $response = $this->putJson("{$this->baseUrl}/mensajes/{$mensaje->id}", [
        'contenido' => $newContent,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.contenido', $newContent);

    $this->assertDatabaseHas('mensaje_foros', [
        'id' => $mensaje->id,
        'contenido' => $newContent,
    ]);
});

it('updates a message when authenticated as a moderator and message is not owned by an admin', function () {
    $moderator = User::factory()->create();
    $moderator->assignRole('moderador');
    Sanctum::actingAs($moderator);

    $normalUser = User::factory()->create();
    $normalUser->assignRole('user');

    $mensaje = MensajeForo::factory()->create(['usuario_id' => $normalUser->id]);
    $newContent = 'Contenido actualizado por moderador.';

    $response = $this->putJson("{$this->baseUrl}/mensajes/{$mensaje->id}", [
        'contenido' => $newContent,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.contenido', $newContent);
});

it('returns 403 when a moderator tries to update a message owned by an admin', function () {
    $moderator = User::factory()->create();
    $moderator->assignRole('moderador');
    Sanctum::actingAs($moderator);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $mensaje = MensajeForo::factory()->create(['usuario_id' => $admin->id]);
    $originalContent = $mensaje->contenido;
    $newContent = 'Intento de actualización por moderador a mensaje de admin.';

    $response = $this->putJson("{$this->baseUrl}/mensajes/{$mensaje->id}", [
        'contenido' => $newContent,
    ]);

    $response->assertForbidden();
    $this->assertDatabaseHas('mensaje_foros', [
        'id' => $mensaje->id,
        'contenido' => $originalContent,
    ]);
});

it('returns 403 when a non-owner and non-moderator user tries to update a message', function () {
    $user = User::factory()->create();
    $user->assignRole('user');
    Sanctum::actingAs($user);

    $otherUser = User::factory()->create();
    $mensaje = MensajeForo::factory()->create(['usuario_id' => $otherUser->id]);
    $originalContent = $mensaje->contenido;
    $newContent = 'Intento de actualización de mensaje ajeno.';

    $response = $this->putJson("{$this->baseUrl}/mensajes/{$mensaje->id}", [
        'contenido' => $newContent,
    ]);

    $response->assertForbidden();
    $this->assertDatabaseHas('mensaje_foros', [
        'id' => $mensaje->id,
        'contenido' => $originalContent,
    ]);
});

it('an admin can update any message', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $user = User::factory()->create();
    $mensaje = MensajeForo::factory()->create(['usuario_id' => $user->id]);
    $newContent = 'Contenido actualizado por admin.';

    $response = $this->putJson("{$this->baseUrl}/mensajes/{$mensaje->id}", [
        'contenido' => $newContent,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.contenido', $newContent);
});

it('fails validation if updated message content is missing', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $mensaje = MensajeForo::factory()->create(['usuario_id' => $user->id]);

    $response = $this->putJson("{$this->baseUrl}/mensajes/{$mensaje->id}", [
        'contenido' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('contenido');
});

it('deletes a message when authenticated as the owner', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $mensaje = MensajeForo::factory()->create(['usuario_id' => $user->id]);

    $response = $this->deleteJson("{$this->baseUrl}/mensajes/{$mensaje->id}");

    $response->assertOk()
        ->assertJson(['message' => 'Mensaje eliminado correctamente.']);

    $this->assertDatabaseMissing('mensaje_foros', [
        'id' => $mensaje->id,
    ]);
});

it('deletes a message when authenticated as a moderator and message is not owned by an admin', function () {
    $moderator = User::factory()->create();
    $moderator->assignRole('moderador');
    Sanctum::actingAs($moderator);

    $normalUser = User::factory()->create();
    $normalUser->assignRole('user');

    $mensaje = MensajeForo::factory()->create(['usuario_id' => $normalUser->id]);

    $response = $this->deleteJson("{$this->baseUrl}/mensajes/{$mensaje->id}");

    $response->assertOk()
        ->assertJson(['message' => 'Mensaje eliminado correctamente.']);
});

it('returns 403 when a moderator tries to delete a message owned by an admin', function () {
    $moderator = User::factory()->create();
    $moderator->assignRole('moderador');
    Sanctum::actingAs($moderator);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $mensaje = MensajeForo::factory()->create(['usuario_id' => $admin->id]);

    $response = $this->deleteJson("{$this->baseUrl}/mensajes/{$mensaje->id}");

    $response->assertForbidden();
    $this->assertDatabaseHas('mensaje_foros', [
        'id' => $mensaje->id,
    ]);
});

it('returns 403 when a non-owner and non-moderator user tries to delete a message', function () {
    $user = User::factory()->create();
    $user->assignRole('user');
    Sanctum::actingAs($user);

    $otherUser = User::factory()->create();
    $mensaje = MensajeForo::factory()->create(['usuario_id' => $otherUser->id]);

    $response = $this->deleteJson("{$this->baseUrl}/mensajes/{$mensaje->id}");

    $response->assertForbidden();
    $this->assertDatabaseHas('mensaje_foros', [
        'id' => $mensaje->id,
    ]);
});

it('an admin can delete any message', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $user = User::factory()->create();
    $mensaje = MensajeForo::factory()->create(['usuario_id' => $user->id]);

    $response = $this->deleteJson("{$this->baseUrl}/mensajes/{$mensaje->id}");

    $response->assertOk()
        ->assertJson(['message' => 'Mensaje eliminado correctamente.']);
});

it('returns 401 when deleting a message without authentication', function () {
    $mensaje = MensajeForo::factory()->create();
    $this->deleteJson("{$this->baseUrl}/mensajes/{$mensaje->id}")->assertUnauthorized();
});
