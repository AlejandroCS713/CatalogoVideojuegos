<?php

use App\Models\users\Friend;
use App\Models\users\Logro;
use App\Models\users\Message;
use App\Models\users\User;

it('belongs to a user and a friend', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $friendship = Friend::create([
        'user_id' => $user1->id,
        'friend_id' => $user2->id,
        'status' => 'accepted'
    ]);

    expect($friendship->user->id)->toEqual($user1->id);
    expect($friendship->friend->id)->toEqual($user2->id);
});


it('belongs to many users', function () {
    $user = User::factory()->create();
    $logro = Logro::create([
        'nombre' => 'Primer Logro',
        'descripcion' => 'Este es el primer logro',
        'puntos' => 10
    ]);

    $logro->usuarios()->attach($user);

    expect($user->logros->contains($logro))->toBeTrue();

    $logro->usuarios()->detach($user);
    $logro->delete();
    $user->delete();
});


it('belongs to sender and receiver', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    $message = Message::create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'message' => 'Hola, ¿cómo estás?'
    ]);

    expect($message->sender->id)->toEqual($sender->id);
    expect($message->receiver->id)->toEqual($receiver->id);
});


it('has friends', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    Friend::create([
        'user_id' => $user1->id,
        'friend_id' => $user2->id,
        'status' => 'accepted'
    ]);

    expect($user1->friends->contains($user2))->toBeTrue();
});

it('has logros', function () {
    $user = User::factory()->create();
    $logro = Logro::create([
        'nombre' => 'Logro de ejemplo',
        'descripcion' => 'Descripción del logro',
        'puntos' => 100
    ]);

    $user->logros()->attach($logro);

    expect($user->logros->contains($logro))->toBeTrue();

    $user->logros()->detach($logro);
    $logro->delete();
    $user->delete();
});

