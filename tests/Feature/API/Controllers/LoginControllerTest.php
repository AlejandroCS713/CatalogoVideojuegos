<?php

use App\Models\users\User;
use Laravel\Sanctum\Sanctum;

it('permite iniciar sesión con credenciales válidas y devuelve un token', function () {
    $user = User::factory()->create([
        'email' => 'usuario@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'usuario@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['token']);
});

it('retorna error si las credenciales son incorrectas', function () {
    $user = User::factory()->create([
        'email' => 'usuario@example.com',
        'password' => bcrypt('correcta'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'usuario@example.com',
        'password' => 'incorrecta',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('email');
});

it('retorna error si faltan campos requeridos', function () {
    $response = $this->postJson('/api/login', []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email', 'password']);
});

it('permite cerrar sesión y revoca el token actual', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user, ['*']);

    $response = $this->postJson('/api/logout');

    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'Sesión cerrada correctamente.',
    ]);
});

it('retorna 401 al intentar cerrar sesión sin estar autenticado', function () {
    $response = $this->postJson('/api/logout');

    $response->assertStatus(401);
});
