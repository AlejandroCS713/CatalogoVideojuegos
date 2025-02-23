<?php

use App\Http\Requests\Games\StoreMultimediaRequest;
use Illuminate\Support\Facades\Validator;
it('validates multimedia creation request', function () {
    $requestData = [
        'type' => 'image',
        'url' => \Illuminate\Http\UploadedFile::fake()->image('image.jpg'),
    ];

    $request = new StoreMultimediaRequest();

    $validator = Validator::make($requestData, $request->rules());

    expect($validator->passes())->toBeTrue();
});
it('fails multimedia creation request with invalid data', function () {
    $requestData = [
        'type' => '',
        'url' => \Illuminate\Http\UploadedFile::fake()->create('file.pdf', 100),
    ];

    $request = new StoreMultimediaRequest();

    $validator = Validator::make($requestData, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->get('type'))->toEqual(['The type field is required.']);
    expect($validator->errors()->get('url'))->toEqual(['The url field must be a file of type: jpg, jpeg, png, mp4.']);
});
