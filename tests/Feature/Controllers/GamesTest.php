<?php

use App\Models\games\Multimedia;
use App\Models\games\Videojuego;
use App\Models\users\Logro;
use App\Models\users\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

it('does not allow user to store multimedia without file', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $videojuego = Videojuego::factory()->create();

    $requestData = [
        'type' => 'image',
        'url' => null,
    ];

    $response = $this->post(route('multimedia.store', ['videojuego' => $videojuego->id]), $requestData);

    $response->assertSessionHasErrors('url');

    $user->delete();
    $videojuego->delete();
});
