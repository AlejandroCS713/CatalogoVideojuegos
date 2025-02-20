<?php

use App\Models\games\Videojuego;
use Livewire\Livewire;
use App\Livewire\BuscarVideojuego;

it('can search for a video game by name', function () {
    $videojuego1 = Videojuego::factory()->create(['nombre' => 'Doom']);
    $videojuego2 = Videojuego::factory()->create(['nombre' => 'Minecraft']);

    Livewire::test(BuscarVideojuego::class)
        ->set('searchTerm', 'Doom')
        ->call('search')
        ->assertSee($videojuego1->nombre)
        ->assertDontSee($videojuego2->nombre);
});
