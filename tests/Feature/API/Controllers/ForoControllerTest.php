<?php

use App\Models\Foro\Foro;
use App\Models\users\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'user']);
});
it('returns a paginated list of forums', function () {
    Foro::factory()->count(3)->create();

    $response = $this->getJson('/api/foros');

    $response->assertStatus(200)
        ->assertJsonStructure(['data']);
});
it('creates a new forum when authenticated', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $user->assignRole('user');

    $this->actingAs($user);

    $payload = [
        'titulo' => 'Nuevo foro',
        'descripcion' => 'Este es un foro de prueba'
    ];

    $response = $this->postJson('/api/foros', $payload);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => ['id', 'titulo', 'descripcion']
        ]);
});

it('returns unauthorized when creating forum without authentication', function () {
    $payload = [
        'titulo' => 'Test',
        'descripcion' => 'Not allowed',
    ];

    $this->postJson('/api/foros', $payload)->assertStatus(401);
});

it('shows details of a specific forum', function () {
    $foro = Foro::factory()->create();

    $this->getJson("/api/foros/{$foro->id}")
        ->assertStatus(200)
        ->assertJsonStructure(['data' => ['id', 'titulo']]);
});

it('updates a forum when authenticated', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $foro = Foro::factory()->create(['usuario_id' => $user->id]);

    $this->putJson("/api/foros/{$foro->id}", [
        'titulo' => 'Updated Title',
        'descripcion' => 'Updated Description',
        'videojuegosConRoles' => [],
    ])->assertStatus(200)
        ->assertJsonFragment(['titulo' => 'Updated Title']);
});

it('deletes a forum when authenticated', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $foro = Foro::factory()->create(['usuario_id' => $user->id]);

    $this->deleteJson("/api/foros/{$foro->id}")
        ->assertStatus(200)
        ->assertJson(['message' => 'Foro eliminado correctamente.']);

    $this->assertDatabaseMissing('foros', ['id' => $foro->id]);
});

it('does not allow deleting forum without authentication', function () {
    $foro = Foro::factory()->create();

    $this->deleteJson("/api/foros/{$foro->id}")->assertStatus(401);
});

it('fails validation if titulo is missing', function () {
    Sanctum::actingAs(User::factory()->create()->assignRole('user'));

    $response = $this->postJson('/api/foros', [
        'descripcion' => 'Valid description',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('titulo');
});

it('fails if videojuego id is not numeric', function () {
    Sanctum::actingAs(User::factory()->create([
        'email_verified_at' => now(),
    ])->assignRole('user'));

    $response = $this->postJson('/api/foros', [
        'titulo' => 'Título válido',
        'descripcion' => 'Descripción válida',
        'videojuegosConRoles' => ['abc' => 'principal']
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('videojuegosConRoles');
});

it('prevents creating forum if email not verified', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $user->assignRole('user');

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/foros', [
        'titulo' => 'Título',
        'descripcion' => 'Descripción'
    ]);

    $response->assertStatus(403);
});

it('prevents updating forum you do not own', function () {
    Sanctum::actingAs(User::factory()->create());

    $foro = Foro::factory()->create();

    $this->putJson("/api/foros/{$foro->id}", [
        'titulo' => 'Hackeado',
    ])->assertStatus(403);
});

it('syncs videojuegos with roles correctly', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    Sanctum::actingAs($user->assignRole('user'));

    $videojuego = \App\Models\games\Videojuego::factory()->create();

    $payload = [
        'titulo' => 'Foro con videojuegos',
        'descripcion' => 'Test de sync',
        'videojuegosConRoles' => [
            $videojuego->id => 'principal'
        ]
    ];

    $response = $this->postJson('/api/foros', $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('foro_videojuego', [
        'videojuego_id' => $videojuego->id,
        'rol_videojuego' => 'principal',
    ]);
});
