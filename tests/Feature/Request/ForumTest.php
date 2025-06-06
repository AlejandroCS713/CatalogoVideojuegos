<?php

use App\Http\Requests\Foro\ForoRequest;
use App\Http\Requests\Foro\MensajeForoRequest;
use App\Http\Requests\Foro\RespuestaForoRequest;
use App\Models\games\Videojuego;
use Illuminate\Support\Facades\Validator;

it('validates foro creation request', function () {
    $videojuegos = Videojuego::factory()->createMany([
        ['id' => 1, 'nombre' => 'Videojuego 1'],
        ['id' => 2, 'nombre' => 'Videojuego 2'],
    ]);

    $requestData = [
        'titulo' => 'Nuevo Foro',
        'descripcion' => 'Descripción del foro',
        'imagen' => null,
        'videojuegos' => [1, 2],
    ];

    $request = new ForoRequest();
    $validator = Validator::make($requestData, $request->rules());

    expect($validator->passes())->toBeTrue();

    Videojuego::whereIn('id', [1, 2])->delete();
});

it('validates foro message request', function () {
    $requestData = [
        'contenido' => 'Este es un mensaje en el foro.',
        'imagen' => null,
        'foro_id' => 1,
    ];

    $request = new MensajeForoRequest();
    $validator = Validator::make($requestData, $request->rules());

    expect($validator->passes())->toBeTrue();
});

it('validates foro response request', function () {
    $requestData = [
        'contenido' => 'Esta es una respuesta en el foro.',
        'imagen' => null,
        'mensaje_id' => 1,
    ];

    $request = new RespuestaForoRequest();
    $validator = Validator::make($requestData, $request->rules());

    expect($validator->passes())->toBeTrue();
});
