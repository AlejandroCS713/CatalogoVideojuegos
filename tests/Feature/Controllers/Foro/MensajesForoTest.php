<?php

use App\Models\Foro\Foro;
use App\Models\Foro\MensajeForo;
use App\Models\Foro\RespuestaForo;
use App\Models\users\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
beforeEach(function () {
    $this->user = User::factory()->create();
    $this->foro = Foro::factory()->create();

    $role = Role::firstOrCreate(['name' => 'user']);
    $this->user->assignRole($role);

    Sanctum::actingAs($this->user);
});

it('can store a new mensaje foro and redirects to foro show page', function () {
    $postData = [
        'contenido' => 'Este es un mensaje de prueba',
        'foro_id' => $this->foro->id,
    ];

    $response = $this->post(route('mensajes.store', ['foro' => $this->foro->id]), $postData);

    $response->assertRedirect(route('foro.show', $this->foro->id));
    $response->assertSessionHas('success', '¡Mensaje enviado!');

    $this->assertDatabaseHas('mensaje_foros', [
        'contenido' => $postData['contenido'],
        'foro_id' => $this->foro->id,
        'usuario_id' => $this->user->id,
    ]);
});

it('validates request data when storing a mensaje foro', function () {
    $response = $this->post(route('mensajes.store', ['foro' => $this->foro->id]), []);

    $response->assertSessionHasErrors(['contenido']);
});

it('can delete a mensaje foro and its respuestas', function () {
    $mensaje = MensajeForo::factory()->create([
        'foro_id' => $this->foro->id,
        'usuario_id' => $this->user->id,
    ]);

    RespuestaForo::factory()->count(3)->create([
        'mensaje_id' => $mensaje->id,
        'usuario_id' => $this->user->id,
    ]);

    $response = $this->delete(route('mensaje.destroy', $mensaje));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Mensaje eliminado correctamente.');

    $this->assertDatabaseMissing('mensaje_foros', ['id' => $mensaje->id]);
    $this->assertDatabaseMissing('respuesta_foros', ['mensaje_id' => $mensaje->id]);
});

it('returns 404 when trying to delete a non-existent mensaje foro', function () {
    $response = $this->delete(route('mensaje.destroy', 9999));

    $response->assertNotFound();
});
