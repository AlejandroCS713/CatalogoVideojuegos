<?php

use App\Models\games\Videojuego;
use App\Models\users\User;
use Illuminate\Support\Facades\Hash;

it('can access the home route', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
it('can access the login page', function () {
    $response = $this->get('login');

    $response->assertStatus(200);
});
it('can access the registered page', function () {
    $response = $this->get('register');
    $response->assertStatus(200);
});
it('can access the videogame index page', function () {
    $response = $this->get(route('videojuegos.index'));
    $response->assertStatus(200);
});
it('can access the videogame show page', function () {
    $videojuego = \App\Models\games\Videojuego::factory()->create();

    $response = $this->get(route('videojuegos.show', ['id' => $videojuego->id]));

    $response->assertStatus(200);
});
it('allows a user to log in', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('welcome'));

    $this->assertAuthenticatedAs($user);

    $user->delete();

    $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
});

it('allows a user to register', function () {
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = $this->post('/register', $userData);

    $response->assertRedirect(route('login'));

    $this->assertDatabaseHas('users', ['email' => $userData['email']]);

    \App\Models\users\User::where('email', $userData['email'])->delete();

    $this->assertDatabaseMissing('users', ['email' => $userData['email']]);
});

it('can access the home page and see top-rated games', function () {
    $videojuegos = Videojuego::factory()->count(3)->create([
        'nombre' => 'Juego de Prueba',
        'fecha_lanzamiento' => '2023-01-01',
        'rating_usuario' => 95,
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);

    $response->assertSee('Juego de Prueba');

    Videojuego::whereIn('id', $videojuegos->pluck('id'))->delete();
});
it('can access the foroum index page', function () {
    $response = $this->get(route('forum.index'));
    $response->assertStatus(200);
});

it('can access the forum show page', function () {
    $foro = \App\Models\Forum\Foro::factory()->create();

    $response = $this->get(route('forum.show', ['foro' => $foro->id]));

    $response->assertStatus(200);
});
it('allows registered users to access the forum creation page', function () {
    $user = \App\Models\users\User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('forum.create'));

    $response->assertStatus(200);

    $user->delete();
});

