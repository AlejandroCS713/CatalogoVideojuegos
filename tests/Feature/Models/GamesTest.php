<?php

use App\Models\games\Genero;
use App\Models\games\Multimedia;
use App\Models\games\Plataforma;
use App\Models\games\Precio;
use App\Models\games\Videojuego;
use App\Models\users\User;
use Illuminate\Support\Facades\DB;

it('verifies that multimedia can be added to a video game', function () {
    $videojuego = Videojuego::create([
        'nombre' => 'Juego con multimedia',
        'descripcion' => 'Descripción del juego',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 85,
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test'
    ]);

    $multimedia = Multimedia::create([
        'videojuego_id' => $videojuego->id,
        'tipo' => 'video',
        'url' => 'https://example.com/trailer.mp4'
    ]);

    $videojuego->load('multimedia');
    expect($videojuego->multimedia->count())->toBe(1);
    expect($videojuego->multimedia->first()->url)->toBe('https://example.com/trailer.mp4');
});

it('verifies that a video game can be associated with multiple platforms', function () {
    $videojuego = Videojuego::create([
        'nombre' => 'Juego en múltiples plataformas',
        'descripcion' => 'Descripción del juego',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 90,
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test'
    ]);

    $plataforma1 = Plataforma::create(['nombre' => 'PC']);
    $plataforma2 = Plataforma::create(['nombre' => 'PS5']);

    $videojuego->plataformas()->attach([$plataforma1->id, $plataforma2->id]);

    expect($videojuego->plataformas->count())->toBe(2);
    expect($videojuego->plataformas->pluck('nombre'))->toContain('PC');
    expect($videojuego->plataformas->pluck('nombre'))->toContain('PS5');
});

it('verifies that a price can be associated with a video game and platform', function () {
    $videojuego = Videojuego::create([
        'nombre' => 'Juego con precio',
        'descripcion' => 'Descripción del juego',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 95,
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test'
    ]);

    $plataforma = Plataforma::create(['nombre' => 'PC']);

    $precio = Precio::create([
        'videojuego_id' => $videojuego->id,
        'plataforma_id' => $plataforma->id,
        'precio' => 59.99
    ]);

    $videojuego->load('precios');
    expect($videojuego->precios->first()->precio)->toBe(59.99);
});

it('verifies that a price is correctly associated with a video game and platform', function () {
    $videojuego = Videojuego::create([
        'nombre' => 'Juego con precio',
        'descripcion' => 'Descripción del juego',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 95,
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test'
    ]);

    $plataforma = Plataforma::create(['nombre' => 'PC']);
    $precio = Precio::create([
        'videojuego_id' => $videojuego->id,
        'plataforma_id' => $plataforma->id,
        'precio' => 59.99
    ]);

    expect($videojuego->precios->first()->precio)->toBe(59.99);
});

it('verifies that relationships can be correctly detached', function () {
    $videojuego = Videojuego::create([
        'nombre' => 'Juego para desvincular',
        'descripcion' => 'Descripción del juego',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 80,
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test'
    ]);
    $genero = Genero::create(['nombre' => 'Acción']);

    $videojuego->generos()->attach($genero->id);

    expect($videojuego->generos->count())->toBe(1);

    $videojuego->generos()->detach($genero->id);

    $videojuego->load('generos');

    expect($videojuego->generos->count())->toBe(0);
});

it('verifies that a genre can be created and associated with a video game', function () {
    $videojuego = Videojuego::create([
        'nombre' => 'Juego de prueba',
        'descripcion' => 'Descripción del juego',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 80,
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test'
    ]);

    $genero = Genero::create(['nombre' => 'Acción']);

    $videojuego->generos()->attach($genero->id);

    expect($videojuego->generos->count())->toBe(1);
    expect($videojuego->generos->first()->nombre)->toBe('Acción');
});

it('verifies that a genre can be updated', function () {
    $genero = Genero::create(['nombre' => 'Acción']);

    $genero->nombre = 'Aventura';
    $genero->save();

    expect($genero->nombre)->toBe('Aventura');
});

it('verifies that a genre can be deleted', function () {
    $genero = Genero::create(['nombre' => 'Acción']);

    $genero->delete();

    expect(Genero::find($genero->id))->toBeNull();
});

it('verifies that multimedia associated with a video game can be deleted', function () {
    $videojuego = Videojuego::create([
        'nombre' => 'Juego con multimedia',
        'descripcion' => 'Descripción del juego',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 85,
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test'
    ]);

    $multimedia = Multimedia::create([
        'videojuego_id' => $videojuego->id,
        'tipo' => 'video',
        'url' => 'https://example.com/trailer.mp4'
    ]);

    $multimedia->delete();

    expect(Multimedia::find($multimedia->id))->toBeNull();
});

it('verifies that the URL of multimedia can be updated', function () {
    $videojuego = Videojuego::create([
        'nombre' => 'Juego con multimedia',
        'descripcion' => 'Descripción del juego',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 85,
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test'
    ]);

    $multimedia = Multimedia::create([
        'videojuego_id' => $videojuego->id,
        'tipo' => 'video',
        'url' => 'https://example.com/trailer.mp4'
    ]);

    $multimedia->url = 'https://example.com/updated-trailer.mp4';
    $multimedia->save();

    expect($multimedia->url)->toBe('https://example.com/updated-trailer.mp4');
});

it('verifies that a video game can be detached from a platform', function () {
    $videojuego = Videojuego::create([
        'nombre' => 'Juego en plataformas',
        'descripcion' => 'Descripción del juego',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 90,
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test'
    ]);

    $plataforma1 = Plataforma::create(['nombre' => 'PC']);
    $plataforma2 = Plataforma::create(['nombre' => 'PS5']);

    $videojuego->plataformas()->attach([$plataforma1->id, $plataforma2->id]);

    $videojuego->plataformas()->detach($plataforma1->id);

    expect($videojuego->plataformas->pluck('nombre'))->not()->toContain('PC');
});

it('verifies that the price of a video game on a platform can be updated', function () {
    $videojuego = Videojuego::create([
        'nombre' => 'Juego con precio',
        'descripcion' => 'Descripción del juego',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 95,
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test'
    ]);

    $plataforma = Plataforma::create(['nombre' => 'PC']);

    $precio = Precio::create([
        'videojuego_id' => $videojuego->id,
        'plataforma_id' => $plataforma->id,
        'precio' => 59.99
    ]);

    $precio->precio = 49.99;
    $precio->save();

    expect($precio->precio)->toBe(49.99);
});

it('verifies that the price of a video game on a platform can be deleted', function () {
    $videojuego = Videojuego::create([
        'nombre' => 'Juego con precio',
        'descripcion' => 'Descripción del juego',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 95,
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test'
    ]);

    $plataforma = Plataforma::create(['nombre' => 'PC']);

    $precio = Precio::create([
        'videojuego_id' => $videojuego->id,
        'plataforma_id' => $plataforma->id,
        'precio' => 59.99
    ]);

    $precio->delete();

    expect(Precio::find($precio->id))->toBeNull();
});

it('verifies that a price cannot be created without a price value', function () {
    $videojuego = Videojuego::create([
        'nombre' => 'Juego sin precio',
        'descripcion' => 'Descripción del juego',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 90,
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test'
    ]);

    $plataforma = Plataforma::create(['nombre' => 'PC']);

    $this->expectException(\Illuminate\Database\QueryException::class);

    Precio::create([
        'videojuego_id' => $videojuego->id,
        'plataforma_id' => $plataforma->id,
    ]);

    DB::table('videojuegos')->where('id', $videojuego->id)->delete();
    DB::table('plataformas')->where('id', $plataforma->id)->delete();
});

it('verifies that multimedia cannot be created without type and url', function () {
    $videojuego = Videojuego::create([
        'nombre' => 'Juego sin multimedia',
        'descripcion' => 'Descripción del juego',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 90,
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test'
    ]);

    $this->expectException(\Illuminate\Database\QueryException::class);

    Multimedia::create([
        'videojuego_id' => $videojuego->id,
    ]);

    DB::table('videojuegos')->where('id', $videojuego->id)->delete();
});

it('verifies that a price cannot be created without a platform', function () {
    $videojuego = Videojuego::create([
        'nombre' => 'Juego sin plataforma',
        'descripcion' => 'Descripción del juego',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 85,
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test'
    ]);

    $this->expectException(\Illuminate\Database\QueryException::class);

    Precio::create([
        'videojuego_id' => $videojuego->id,
        'precio' => 59.99,
    ]);
});

it('verifies that a video game can have multiple genres', function () {
    $videojuego = Videojuego::create([
        'nombre' => 'Juego con múltiples géneros',
        'descripcion' => 'Descripción del juego',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 85,
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test'
    ]);

    $genero1 = Genero::create(['nombre' => 'Acción']);
    $genero2 = Genero::create(['nombre' => 'Aventura']);

    $videojuego->generos()->attach([$genero1->id, $genero2->id]);

    $videojuego->load('generos');
    expect($videojuego->generos->count())->toBe(2);
    expect($videojuego->generos->pluck('nombre'))->toContain('Acción');
    expect($videojuego->generos->pluck('nombre'))->toContain('Aventura');
});

it('verifies that a platform can be deleted', function () {
    $plataforma = Plataforma::create(['nombre' => 'PC']);

    $plataforma->delete();

    expect(Plataforma::find($plataforma->id))->toBeNull();
});

it('verifies that multiple genres can be associated and one can be removed', function () {
    $videojuego = Videojuego::create([
        'nombre' => 'Juego con múltiples géneros',
        'descripcion' => 'Descripción del juego',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 85,
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test'
    ]);

    $genero1 = Genero::create(['nombre' => 'Acción']);
    $genero2 = Genero::create(['nombre' => 'Aventura']);

    $videojuego->generos()->attach([$genero1->id, $genero2->id]);

    $videojuego->generos()->detach($genero1->id);

    $videojuego->load('generos');
    expect($videojuego->generos->count())->toBe(1);
    expect($videojuego->generos->first()->nombre)->toBe('Aventura');
});

it('verifies that a genre cannot be associated without a name', function () {
    $videojuego = Videojuego::create([
        'nombre' => 'Juego sin género',
        'descripcion' => 'Descripción del juego',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 80,
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test'
    ]);

    $this->expectException(\Illuminate\Database\QueryException::class);

    Genero::create([]);
});

it('verifies that scopeTopRatedAAA returns the top 10 AAA games by user rating', function () {
    Videojuego::factory()->count(15)->sequence(
        ['publicador' => 'Nintendo'],
        ['publicador' => 'Sony'],
        ['publicador' => 'Ubisoft'],
        ['publicador' => 'EA'],
        ['publicador' => 'Microsoft'],
        ['publicador' => 'Otro'],
        ['publicador' => 'Otro']
    )->create([
        'rating_usuario' => fn() => rand(1, 5)
    ]);

    $topAAA = Videojuego::topRatedAAA()->get();

    expect($topAAA->count())->toBeLessThanOrEqual(10);
    expect($topAAA->pluck('publicador')->unique())->not()->toContain('Otro');
});

it('verifies that scopeExclusiveGames returns exclusive games', function () {
    $videojuego = Videojuego::factory()->create();
    $plataforma = Plataforma::factory()->create();

    $videojuego->plataformas()->attach($plataforma->id);

    $exclusivos = Videojuego::exclusiveGames()->get();

    expect($exclusivos->pluck('id'))->toContain($videojuego->id);
});

it('verifies that scopeNewest orders games by release date descending', function () {
    $old = Videojuego::factory()->create(['fecha_lanzamiento' => now()->subYears(5)]);
    $new = Videojuego::factory()->create(['fecha_lanzamiento' => now()]);

    $ordered = Videojuego::newest()->get();

    expect($ordered->first()->id)->toBe($new->id);
});

it('verifies that scopeOldest orders games by release date ascending', function () {
    $old = Videojuego::factory()->create(['fecha_lanzamiento' => now()->subYears(5)]);
    $new = Videojuego::factory()->create(['fecha_lanzamiento' => now()]);

    $ordered = Videojuego::oldest()->get();

    expect($ordered->first()->id)->toBe($old->id);
});

it('verifies that scopeAlphabetically orders games alphabetically', function () {
    Videojuego::factory()->create(['nombre' => 'Zelda']);
    Videojuego::factory()->create(['nombre' => 'Animal Crossing']);

    $ordered = Videojuego::alphabetically()->pluck('nombre');

    expect($ordered->first())->toBe('Animal Crossing');
});

it('verifies that scopeReverseAlphabetically orders games alphabetically in reverse', function () {
    Videojuego::factory()->create(['nombre' => 'Zelda']);
    Videojuego::factory()->create(['nombre' => 'Animal Crossing']);

    $ordered = Videojuego::reverseAlphabetically()->pluck('nombre');

    expect($ordered->first())->toBe('Zelda');
});

it('verifies that scopeHighlyRatedNewExclusiveGames returns best rated new exclusive games', function () {
    $videojuego = Videojuego::factory()->create([
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 5.0,
    ]);

    $plataforma = Plataforma::factory()->create();
    $videojuego->plataformas()->attach($plataforma->id);

    $result = Videojuego::highlyRatedNewExclusiveGames()->get();

    expect($result->pluck('id'))->toContain($videojuego->id);
});
