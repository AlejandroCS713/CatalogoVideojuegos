<?php

use App\Models\Forum\Foro;
use App\Models\Forum\MensajeForo;
use App\Models\Forum\RespuestaForo;
use App\Models\games\Videojuego;
use App\Models\users\User;

it('verifica que se puede crear un foro y relacionarlo con un usuario y videojuegos', function () {
    $user = User::find(40);

    $foro = Foro::create([
        'titulo' => 'Nuevo Foro',
        'descripcion' => 'Descripción del foro',
        'usuario_id' => $user->id
    ]);

    expect($foro->usuario->id)->toBe($user->id);

    expect($foro->titulo)->toBe('Nuevo Foro');
    expect($foro->descripcion)->toBe('Descripción del foro');

    expect($foro->videojuegos)->toBeEmpty();
});

it('verifica que se pueden asignar videojuegos a un foro', function () {
    $foro = Foro::create([
        'titulo' => 'Foro de Videojuegos',
        'descripcion' => 'Un foro sobre videojuegos',
        'usuario_id' => 40
    ]);

    $videojuego1 = Videojuego::create([
        'nombre' => 'Juego 1',
        'fecha_lanzamiento' => now(),
    ]);

    $videojuego2 = Videojuego::create([
        'nombre' => 'Juego 2',
        'fecha_lanzamiento' => now(),
    ]);

    $foro->videojuegos()->attach([$videojuego1->id, $videojuego2->id]);

    expect($foro->videojuegos->count())->toBe(2);
    expect($foro->videojuegos->pluck('nombre'))->toContain('Juego 1');
    expect($foro->videojuegos->pluck('nombre'))->toContain('Juego 2');
});

it('verifica que se puede crear un mensaje en un foro y asociarlo a un usuario', function () {
    $user = User::find(40);
    $foro = Foro::create([
        'titulo' => 'Foro de Mensajes',
        'descripcion' => 'Descripción del foro',
        'usuario_id' => $user->id
    ]);

    $mensaje = MensajeForo::create([
        'contenido' => 'Este es un mensaje de prueba',
        'foro_id' => $foro->id,
        'usuario_id' => $user->id
    ]);

    expect($mensaje->foro->id)->toBe($foro->id);

    expect($mensaje->usuario->id)->toBe($user->id);

    expect($mensaje->contenido)->toBe('Este es un mensaje de prueba');
});

it('verifica que se puede crear una respuesta en un mensaje de foro y asociarla al usuario', function () {
    $user = User::find(40);
    $foro = Foro::create([
        'titulo' => 'Foro de Respuestas',
        'descripcion' => 'Un foro para respuestas',
        'usuario_id' => $user->id
    ]);
    $mensaje = MensajeForo::create([
        'contenido' => 'Este es el mensaje principal',
        'foro_id' => $foro->id,
        'usuario_id' => $user->id
    ]);

    $respuesta = RespuestaForo::create([
        'contenido' => 'Esta es una respuesta al mensaje',
        'mensaje_id' => $mensaje->id,
        'usuario_id' => $user->id
    ]);

    expect($respuesta->mensaje->id)->toBe($mensaje->id);

    expect($respuesta->usuario->id)->toBe($user->id);

    expect($respuesta->contenido)->toBe('Esta es una respuesta al mensaje');
});
