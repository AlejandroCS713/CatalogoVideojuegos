<?php

use App\Events\AmigoAgregado;
use App\Events\PrimerMensajeEnviado;
use App\Listeners\DesbloquearLogroPrimerAmigo;
use App\Listeners\DesbloquearLogroPrimerMensaje;
use App\Models\users\User;
use Illuminate\Support\Facades\Event;


it('calls DesbloquearLogroPrimerAmigo when AmigoAgregado is dispatched', function () {
    Event::fake();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    event(new AmigoAgregado($user1, $user2));

    Event::assertListening(
        AmigoAgregado::class,
        DesbloquearLogroPrimerAmigo::class
    );
});

it('calls DesbloquearLogroPrimerAmigo when PrimerMensajeEnviado is dispatched', function () {
    Event::fake();
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    event(new PrimerMensajeEnviado($sender, $receiver));

    Event::assertListening(
        PrimerMensajeEnviado::class,
        DesbloquearLogroPrimerMensaje::class
    );
});
