<?php

use App\Models\games\Genero;
use App\Models\games\Multimedia;
use App\Models\games\Plataforma;
use App\Models\games\Precio;
use App\Models\games\Reseña;
use App\Models\games\Videojuego;
use App\Models\users\User;
use Illuminate\Support\Facades\DB;
it('verifica que se puede agregar multimedia a un videojuego', function () {
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
it('verifica que un videojuego se puede asociar a varias plataformas', function () {
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
it('verifica que se puede asociar un precio a un videojuego y plataforma', function () {
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

it('verifica que un usuario puede dejar una reseña para un videojuego', function () {
    $user = User::find(40);
    $videojuego = Videojuego::create([
        'nombre' => 'Juego con reseña',
        'descripcion' => 'Descripción del juego',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 90,
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test'
    ]);

    $resena = Reseña::create([
        'usuario_id' => $user->id,
        'videojuego_id' => $videojuego->id,
        'texto' => 'Este juego es increíble',
        'calificacion' => 5
    ]);

    expect($resena->usuario->id)->toBe($user->id);

    expect($resena->videojuego->id)->toBe($videojuego->id);

    expect($resena->texto)->toBe('Este juego es increíble');
    expect($resena->calificacion)->toBe(5);
});
it('verifica que un precio se asocia correctamente a un videojuego y plataforma', function () {
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

it('verifica que se pueden desvincular relaciones correctamente', function () {
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

it('verifica que se puede crear un género y asociarlo a un videojuego', function () {
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

it('verifica que un género se puede actualizar', function () {
    $genero = Genero::create(['nombre' => 'Acción']);

    $genero->nombre = 'Aventura';
    $genero->save();

    expect($genero->nombre)->toBe('Aventura');
});

it('verifica que se puede eliminar un género', function () {
    $genero = Genero::create(['nombre' => 'Acción']);

    $genero->delete();

    expect(Genero::find($genero->id))->toBeNull();
});

it('verifica que se puede eliminar un multimedia asociado a un videojuego', function () {
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

it('verifica que se puede actualizar la URL de un multimedia', function () {
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

it('verifica que un videojuego se puede desvincular de una plataforma', function () {
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

it('verifica que se puede actualizar el precio de un videojuego en una plataforma', function () {
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

it('verifica que se puede eliminar el precio de un videojuego en una plataforma', function () {
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
it('verifica que un precio no se puede crear sin precio', function () {
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

    // Intentamos crear un precio sin especificar el valor de 'precio'
    $this->expectException(\Illuminate\Database\QueryException::class);

    Precio::create([
        'videojuego_id' => $videojuego->id,
        'plataforma_id' => $plataforma->id,
    ]);

    DB::table('videojuegos')->where('id', $videojuego->id)->delete();
    DB::table('plataformas')->where('id', $plataforma->id)->delete();
});

it('verifica que multimedia no se puede crear sin tipo y url', function () {
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

it('verifica que no se puede crear un precio sin plataforma', function () {
    $videojuego = Videojuego::create([
        'nombre' => 'Juego sin plataforma',
        'descripcion' => 'Descripción del juego',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 85,
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test'
    ]);

    // Intentamos crear un precio sin asociar una plataforma
    $this->expectException(\Illuminate\Database\QueryException::class);

    Precio::create([
        'videojuego_id' => $videojuego->id,
        'precio' => 59.99,
    ]);
});

it('verifica que un videojuego puede tener múltiples géneros', function () {
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

it('verifica que se puede eliminar una plataforma', function () {
    $plataforma = Plataforma::create(['nombre' => 'PC']);

    $plataforma->delete();

    expect(Plataforma::find($plataforma->id))->toBeNull();
});

it('verifica que se puede asociar múltiples géneros y eliminar uno', function () {
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


it('verifica que un género no se puede asociar sin nombre', function () {
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

    $genero = Genero::create([]);
});
