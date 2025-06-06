<?php

namespace Tests\Feature\API\Controllers;

use App\Models\Foro\MensajeForo;
use App\Models\Foro\RespuestaForo;
use App\Models\users\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'user']);
    Role::firstOrCreate(['name' => 'moderador']);
    Role::firstOrCreate(['name' => 'admin']);
    $this->baseUrl = '/api';
});

it('returns a paginated list of responses for a specific message', function () {
    $mensaje = MensajeForo::factory()->create();
    RespuestaForo::factory()->count(5)->create(['mensaje_id' => $mensaje->id]);

    $response = $this->getJson("{$this->baseUrl}/mensajes/{$mensaje->id}/respuestas");

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

it('creates a new response when authenticated as a verified user with "user" role', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->assignRole('user');
    Sanctum::actingAs($user);

    $mensaje = MensajeForo::factory()->create();
    $responseContent = 'Esta es una nueva respuesta de prueba.';

    $response = $this->postJson("{$this->baseUrl}/mensajes/{$mensaje->id}/respuestas", [
        'contenido' => $responseContent,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.contenido', $responseContent)
        ->assertJsonPath('data.usuario.id', $user->id);

    $this->assertDatabaseHas('respuesta_foros', [
        'contenido' => $responseContent,
        'mensaje_id' => $mensaje->id,
        'usuario_id' => $user->id,
    ]);
});

it('returns 404 when getting responses for a non-existent message', function () {
    $this->getJson("{$this->baseUrl}/mensajes/9999/respuestas")->assertNotFound();
});

it('returns 401 when creating a response without authentication', function () {
    $mensaje = MensajeForo::factory()->create();
    $this->postJson("{$this->baseUrl}/mensajes/{$mensaje->id}/respuestas", [
        'contenido' => 'Respuesta de invitado.',
    ])->assertUnauthorized();
});

it('fails validation if response content is missing', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->assignRole('user');
    Sanctum::actingAs($user);

    $mensaje = MensajeForo::factory()->create();

    $response = $this->postJson("{$this->baseUrl}/mensajes/{$mensaje->id}/respuestas", [
        'contenido' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('contenido');
});

it('shows details of a specific response', function () {
    $respuesta = RespuestaForo::factory()->create();

    $this->getJson("{$this->baseUrl}/respuestas/{$respuesta->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $respuesta->id)
        ->assertJsonPath('data.contenido', $respuesta->contenido)
        ->assertJsonStructure([
            'data' => [
                'id',
                'contenido',
                'usuario' => ['id', 'name'],
                'created_at',
                'updated_at'
            ]
        ]);
});

it('returns 404 when showing a non-existent response', function () {
    $this->getJson("{$this->baseUrl}/respuestas/9999")->assertNotFound();
});

it('updates a response when authenticated as the owner', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $respuesta = RespuestaForo::factory()->create(['usuario_id' => $user->id]);
    $newContent = 'Contenido de la respuesta actualizado.';

    $response = $this->putJson("{$this->baseUrl}/respuestas/{$respuesta->id}", [
        'contenido' => $newContent,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.contenido', $newContent);

    $this->assertDatabaseHas('respuesta_foros', [
        'id' => $respuesta->id,
        'contenido' => $newContent,
    ]);
});

it('updates a response when authenticated as a moderator and response is not owned by an admin', function () {
    $moderator = User::factory()->create();
    $moderator->assignRole('moderador');
    Sanctum::actingAs($moderator);

    $normalUser = User::factory()->create();
    $normalUser->assignRole('user');

    $respuesta = RespuestaForo::factory()->create(['usuario_id' => $normalUser->id]);
    $newContent = 'Contenido actualizado por moderador.';

    $response = $this->putJson("{$this->baseUrl}/respuestas/{$respuesta->id}", [
        'contenido' => $newContent,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.contenido', $newContent);
});

it('an admin can update any response', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $user = User::factory()->create();
    $respuesta = RespuestaForo::factory()->create(['usuario_id' => $user->id]);
    $newContent = 'Contenido actualizado por admin.';

    $response = $this->putJson("{$this->baseUrl}/respuestas/{$respuesta->id}", [
        'contenido' => $newContent,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.contenido', $newContent);
});

it('fails validation if updated response content is missing', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $respuesta = RespuestaForo::factory()->create(['usuario_id' => $user->id]);

    $response = $this->putJson("{$this->baseUrl}/respuestas/{$respuesta->id}", [
        'contenido' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('contenido');
});

it('deletes a response when authenticated as the owner', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $respuesta = RespuestaForo::factory()->create(['usuario_id' => $user->id]);

    $response = $this->deleteJson("{$this->baseUrl}/respuestas/{$respuesta->id}");

    $response->assertOk()
        ->assertJson(['message' => 'Respuesta eliminada correctamente.']);

    $this->assertDatabaseMissing('respuesta_foros', [
        'id' => $respuesta->id,
    ]);
});

it('deletes a response when authenticated as a moderator and response is not owned by an admin', function () {
    $moderator = User::factory()->create();
    $moderator->assignRole('moderador');
    Sanctum::actingAs($moderator);

    $normalUser = User::factory()->create();
    $normalUser->assignRole('user');

    $respuesta = RespuestaForo::factory()->create(['usuario_id' => $normalUser->id]);

    $response = $this->deleteJson("{$this->baseUrl}/respuestas/{$respuesta->id}");

    $response->assertOk()
        ->assertJson(['message' => 'Respuesta eliminada correctamente.']);
});

it('an admin can delete any response', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $user = User::factory()->create();
    $respuesta = RespuestaForo::factory()->create(['usuario_id' => $user->id]);

    $response = $this->deleteJson("{$this->baseUrl}/respuestas/{$respuesta->id}");

    $response->assertOk()
        ->assertJson(['message' => 'Respuesta eliminada correctamente.']);
});

it('returns 401 when deleting a response without authentication', function () {
    $respuesta = RespuestaForo::factory()->create();
    $this->deleteJson("{$this->baseUrl}/respuestas/{$respuesta->id}")->assertUnauthorized();
});

it('returns 404 when deleting a non-existent response', function () {
    Sanctum::actingAs(User::factory()->create());
    $this->deleteJson("{$this->baseUrl}/respuestas/9999")->assertNotFound();
});

it('returns 404 when updating a non-existent response', function () {
    Sanctum::actingAs(User::factory()->create());
    $this->putJson("{$this->baseUrl}/respuestas/9999", ['contenido' => 'Intento de actualizar no existente.'])->assertNotFound();
});
