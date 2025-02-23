<?php


use App\Models\games\Videojuego;

it('can access the home route', function () {
$response = $this->get('/');
$response->assertStatus(200);
});

it('can access the home page and see top-rated games', function () {
    $videojuegos = Videojuego::factory()->count(6)->create([
        'nombre' => 'Juego de Prueba',
        'fecha_lanzamiento' => '2023-01-01',
        'rating_usuario' => 95,
    ]);
    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertSee('Juego de Prueba');
    Videojuego::whereIn('id', $videojuegos->pluck('id'))->delete();
});
