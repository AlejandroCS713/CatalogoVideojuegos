
<?php

use App\Models\users\User;

it('can access the videogame index page', function () {
    $response = $this->get(route('videojuegos.index'));
    $response->assertStatus(200);
});
it('can access the videogame show page', function () {
    $videojuego = \App\Models\games\Videojuego::factory()->create();
    $response = $this->get(route('videojuegos.show', ['id' => $videojuego->id]));
    $response->assertStatus(200);
});

