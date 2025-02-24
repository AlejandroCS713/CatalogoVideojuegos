<?php

use App\Http\Requests\Users\SendMessageRequest;
use App\Http\Requests\users\StoreUserAdminRequest;
use App\Http\Requests\Users\UpdateAvatarRequest;
use App\Http\Requests\users\UpdateUserAdminRequest;
use App\Models\users\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

it('validates send message request with valid data', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    $requestData = [
        'receiver_id' => $receiver->id,
        'message' => 'Este es un mensaje válido',
    ];

    $request = new SendMessageRequest();
    $validator = Validator::make($requestData, $request->rules());

    expect($validator->passes())->toBeTrue();
});

it('fails send message request with invalid data', function () {
    $sender = User::factory()->create();

    $requestData = [
        'receiver_id' => 99999,
        'message' => '',
    ];

    $request = new SendMessageRequest();
    $validator = Validator::make($requestData, $request->rules());

    expect($validator->fails())->toBeTrue();

    expect($validator->errors()->get('receiver_id'))->toEqual(['The selected receiver id is invalid.']);
    expect($validator->errors()->get('message'))->toEqual(['The message field is required.']);
});

it('fails store user admin request with invalid data', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $requestData = [
        'nombre' => '',
        'descripcion' => 12345,
        'fecha_lanzamiento' => 'fecha-invalida',
        'rating_usuario' => 20,
        'rating_criticas' => -5,
        'desarrollador' => str_repeat('A', 300),
        'publicador' => str_repeat('B', 300),
        'plataformas' => 'no-array',
        'generos' => 'no-array',
    ];

    $request = new StoreUserAdminRequest();
    $validator = Validator::make($requestData, $request->rules());

    expect($request->authorize())->toBeFalse();
    expect($validator->fails())->toBeTrue();

    expect($validator->errors()->get('nombre'))->toEqual(['The nombre field is required.']);
    expect($validator->errors()->get('descripcion'))->toEqual(['The descripcion field must be a string.']);
    expect($validator->errors()->get('fecha_lanzamiento'))->toEqual(['The fecha lanzamiento field must be a valid date.']);
    expect($validator->errors()->get('rating_usuario'))->toEqual(['The rating usuario field must not be greater than 10.']);
    expect($validator->errors()->get('rating_criticas'))->toEqual(['The rating criticas field must be at least 0.']);
    expect($validator->errors()->get('desarrollador'))->toEqual(['The desarrollador field must not be greater than 255 characters.']);
    expect($validator->errors()->get('publicador'))->toEqual(['The publicador field must not be greater than 255 characters.']);
    expect($validator->errors()->get('plataformas'))->toEqual(['The plataformas field must be an array.']);
    expect($validator->errors()->get('generos'))->toEqual(['The generos field must be an array.']);
});

it('fails update avatar request with invalid data', function () {
    $requestData = [
        'avatar' => null,
    ];

    $request = new UpdateAvatarRequest();
    $validator = Validator::make($requestData, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->get('avatar'))->toEqual(['The avatar field is required.']);
});

it('passes update avatar request with valid data', function () {
    $requestData = [
        'avatar' => 'https://example.com/avatar.jpg',
    ];

    $request = new UpdateAvatarRequest();
    $validator = Validator::make($requestData, $request->rules());

    expect($validator->passes())->toBeTrue();
});

it('fails update user admin request with invalid data', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $requestData = [
        'nombre' => '',
        'descripcion' => 12345,
        'fecha_lanzamiento' => 'fecha-invalida',
        'rating_usuario' => 20,
        'rating_criticas' => -5,
        'desarrollador' => str_repeat('A', 300),
        'publicador' => str_repeat('B', 300),
        'plataformas' => 'no-array',
        'generos' => 'no-array',
    ];

    $request = new UpdateUserAdminRequest();
    $validator = Validator::make($requestData, $request->rules());

    expect($request->authorize())->toBeFalse();
    expect($validator->fails())->toBeTrue();

    expect($validator->errors()->get('nombre'))->toEqual(['The nombre field is required.']);
    expect($validator->errors()->get('descripcion'))->toEqual(['The descripcion field must be a string.']);
    expect($validator->errors()->get('fecha_lanzamiento'))->toEqual(['The fecha lanzamiento field must be a valid date.']);
    expect($validator->errors()->get('rating_usuario'))->toEqual(['The rating usuario field must not be greater than 10.']);
    expect($validator->errors()->get('rating_criticas'))->toEqual(['The rating criticas field must be at least 0.']);
    expect($validator->errors()->get('desarrollador'))->toEqual(['The desarrollador field must not be greater than 255 characters.']);
    expect($validator->errors()->get('publicador'))->toEqual(['The publicador field must not be greater than 255 characters.']);
    expect($validator->errors()->get('plataformas'))->toEqual(['The plataformas field must be an array.']);
    expect($validator->errors()->get('generos'))->toEqual(['The generos field must be an array.']);
});
