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
