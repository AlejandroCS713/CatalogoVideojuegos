<?php

use App\Models\Foro\Foro;
use App\Models\Foro\MensajeForo;
use App\Models\users\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    $role = Role::firstOrCreate(['name' => 'user']);
    $this->user->assignRole($role);

    $this->foro = Foro::factory()->create();

    $this->mensaje = MensajeForo::factory()->create([
        'foro_id' => $this->foro->id,
        'usuario_id' => $this->user->id,
    ]);

    Sanctum::actingAs($this->user);
});
it('can store a new response for a message with valid data', function () {
    $response = $this->post(route('respuestas.store', $this->mensaje->id), [
        'contenido' => 'Esta es una nueva respuesta de prueba.',
    ]);

    $response->assertRedirect(route('foro.show', $this->foro->id))
        ->assertSessionHas('success', '¡Respuesta enviada!');

    $this->assertDatabaseHas('respuestas_foro', [
        'contenido' => 'Esta es una nueva respuesta de prueba.',
        'mensaje_id' => $this->mensaje->id,
        'usuario_id' => $this->user->id,
    ]);
});

it('returns validation errors when storing an invalid response', function () {
    $response = $this->post(route('respuestas.store', $this->mensaje->id), [
        'contenido' => '',
    ]);

    $response->assertSessionHasErrors(['contenido']);
    $response->assertStatus(302);
    $this->assertDatabaseCount('respuestas_foro', 0);
});


it('returns 404 if storing a response for a non-existent message', function () {
    $nonExistentMessageId = 99999;

    $response = $this->post(route('respuestas.store', $nonExistentMessageId), [
        'contenido' => 'Contenido para mensaje inexistente.',
    ]);

    $response->assertNotFound();
    $this->assertDatabaseCount('respuestas_foro', 0);
});
