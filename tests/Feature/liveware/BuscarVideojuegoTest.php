<?php

use App\Livewire\BuscarVideojuego;
use App\Models\Foro\Foro;
use App\Models\games\Videojuego;
use App\Models\users\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'user']);

    $this->user = User::factory()->create(['email_verified_at' => now()]);
    $this->user->assignRole('user');

    $this->foro = Foro::factory()->create();

    Livewire::actingAs($this->user);
});

it('renders successfully with default state', function () {
    Livewire::test(BuscarVideojuego::class)
        ->assertStatus(200)
        ->assertSet('searchTerm', '')
        ->assertSet('videojuegosConRol', []);
});

it('updates search term and shows results', function () {
    Videojuego::factory()->create(['nombre' => 'Cyberpunk 2077']);
    Videojuego::factory()->create(['nombre' => 'Red Dead Redemption 2']);
    Videojuego::factory()->create(['nombre' => 'Grand Theft Auto V']);

    $component = Livewire::test(BuscarVideojuego::class)
        ->set('searchTerm', 'Cybe');

    $component->assertSee('Cyberpunk 2077')
        ->assertDontSee('Red Dead Redemption 2');
});

it('shows no results for search term less than 2 characters', function () {
    Videojuego::factory()->create(['nombre' => 'Test Game']);

    Livewire::test(BuscarVideojuego::class)
        ->set('searchTerm', 'T')
        ->assertDontSee('Test Game');
});

it('can select a video game', function () {
    $videojuego1 = Videojuego::factory()->create(['nombre' => 'Selected Game 1']);
    $videojuego2 = Videojuego::factory()->create(['nombre' => 'Selected Game 2']);

    $component = Livewire::test(BuscarVideojuego::class)
        ->call('seleccionarVideojuego', $videojuego1->id)
        ->assertSet('videojuegosConRol', [$videojuego1->id => 'principal'])
        ->assertSet('searchTerm', '');

    $component->call('seleccionarVideojuego', $videojuego2->id)
        ->assertSet('videojuegosConRol', [
            $videojuego1->id => 'principal',
            $videojuego2->id => 'principal'
        ]);

    $component->call('seleccionarVideojuego', $videojuego1->id)
        ->assertSet('videojuegosConRol', [
            $videojuego1->id => 'principal',
            $videojuego2->id => 'principal'
        ]);
});

it('dispatches event after selecting a video game', function () {
    $videojuego = Videojuego::factory()->create();

    Livewire::test(BuscarVideojuego::class)
        ->call('seleccionarVideojuego', $videojuego->id)
        ->assertDispatched('videojuegosConRolSeleccionados', [$videojuego->id => 'principal']);
});

it('can eliminate a selected video game', function () {
    $videojuego1 = Videojuego::factory()->create(['nombre' => 'Game To Keep']);
    $videojuego2 = Videojuego::factory()->create(['nombre' => 'Game To Remove']);

    $component = Livewire::test(BuscarVideojuego::class)
        ->call('seleccionarVideojuego', $videojuego1->id)
        ->call('seleccionarVideojuego', $videojuego2->id)
        ->assertSet('videojuegosConRol', [
            $videojuego1->id => 'principal',
            $videojuego2->id => 'principal'
        ])
        ->call('eliminarVideojuego', $videojuego2->id)
        ->assertSet('videojuegosConRol', [$videojuego1->id => 'principal']);
});

it('dispatches event after eliminating a video game', function () {
    $videojuego1 = Videojuego::factory()->create();
    $videojuego2 = Videojuego::factory()->create();

    Livewire::test(BuscarVideojuego::class)
        ->call('seleccionarVideojuego', $videojuego1->id)
        ->call('seleccionarVideojuego', $videojuego2->id)
        ->call('eliminarVideojuego', $videojuego1->id)
        ->assertDispatched('videojuegosConRolSeleccionados', [$videojuego2->id => 'principal']);
});

it('can initialize selected games via listener', function () {
    $videojuego1 = Videojuego::factory()->create();
    $videojuego2 = Videojuego::factory()->create();

    $initialGames = [
        $videojuego1->id => 'principal',
        $videojuego2->id => 'secundario'
    ];

    Livewire::test(BuscarVideojuego::class)
        ->dispatch('inicializarJuegosConRol', $initialGames)
        ->assertSet('videojuegosConRol', $initialGames);
});

it('dispatches event when videojuegosConRol property is updated directly', function () {
    $videojuego = Videojuego::factory()->create();

    Livewire::test(BuscarVideojuego::class)
        ->set('videojuegosConRol', [$videojuego->id => 'principal'])
        ->assertDispatched('videojuegosConRolSeleccionados', [$videojuego->id => 'principal']);
});

it('displays selected games in the view with their roles', function () {
    $videojuego1 = Videojuego::factory()->create(['nombre' => 'Juego Principal']);
    $videojuego2 = Videojuego::factory()->create(['nombre' => 'Juego Secundario']);

    $initialGames = [
        $videojuego1->id => 'principal',
        $videojuego2->id => 'secundario'
    ];

    Livewire::test(BuscarVideojuego::class)
        ->set('videojuegosConRol', $initialGames)
        ->assertSee('Juego Principal')
        ->assertSee('Juego Secundario');
});
