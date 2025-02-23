<?php

use App\Http\Requests\Forum\ForoRequest;
use App\Http\Requests\Forum\MensajeForoRequest;
use App\Http\Requests\Forum\RespuestaForoRequest;
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

it('fails foro creation request with invalid data', function () {
    $requestData = [
        'titulo' => '',
        'descripcion' => 'Descripción del foro',
        'imagen' => null,
        'videojuegos' => [999],
    ];

    $request = new ForoRequest();
    $validator = Validator::make($requestData, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->get('titulo'))->toEqual(['The titulo field is required.']);
    expect($validator->errors()->get('videojuegos.0'))->toEqual(['The selected videojuegos.0 is invalid.']);
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

it('fails foro message request with invalid data', function () {
    $requestData = [
        'contenido' => '',
        'imagen' => null,
        'foro_id' => 999,
    ];

    $request = new MensajeForoRequest();
    $validator = Validator::make($requestData, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->get('contenido'))->toEqual(['The contenido field is required.']);
    expect($validator->errors()->get('foro_id'))->toEqual(['The selected foro id is invalid.']);
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

it('fails foro response request with invalid data', function () {
    $requestData = [
        'contenido' => '',
        'imagen' => null,
        'mensaje_id' => 999,
    ];

    $request = new RespuestaForoRequest();
    $validator = Validator::make($requestData, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->get('contenido'))->toEqual(['The contenido field is required.']);
    expect($validator->errors()->get('mensaje_id'))->toEqual(['The selected mensaje id is invalid.']);
});
