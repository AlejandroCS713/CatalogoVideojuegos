<?php

use App\Events\AmigoAgregado;
use App\Events\PrimerMensajeEnviado;
use App\Jobs\NotificarLogroDesbloqueado;
use App\Listeners\DesbloquearLogroPrimerAmigo;
use App\Listeners\DesbloquearLogroPrimerMensaje;
use App\Models\users\Logro;
use App\Models\users\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

describe('DesbloquearLogroPrimerAmigo Listener', function () {
    beforeEach(function () {
        $this->primerAmigoLogro = Logro::firstOrCreate([
            'nombre' => 'Primer Amigo',
            'descripcion' => 'Has agregado tu primer amigo'
        ]);

        Event::fake();
        Queue::fake();
    });

    it('asserts DesbloquearLogroPrimerAmigo is listening for AmigoAgregado', function () {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        event(new AmigoAgregado($user1, $user2));

        Event::assertListening(
            AmigoAgregado::class,
            DesbloquearLogroPrimerAmigo::class
        );
    });

    it('asserts DesbloquearLogroPrimerMensaje is listening for PrimerMensajeEnviado', function () {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        event(new PrimerMensajeEnviado($sender, $receiver));

        Event::assertListening(
            PrimerMensajeEnviado::class,
            DesbloquearLogroPrimerMensaje::class
        );
    });

    it('unlocks "First Friend" achievement and notifies users when they do not have it', function () {
        $user = User::factory()->create();
        $friend = User::factory()->create();

        expect($user->logros()->where('nombre', 'Primer Amigo')->exists())->toBeFalse();
        expect($friend->logros()->where('nombre', 'Primer Amigo')->exists())->toBeFalse();

        $listener = new DesbloquearLogroPrimerAmigo();
        $listener->handle(new AmigoAgregado($user, $friend));

        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $user->id,
            'logro_id' => $this->primerAmigoLogro->id,
        ]);
        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $friend->id,
            'logro_id' => $this->primerAmigoLogro->id,
        ]);

        Queue::assertPushed(NotificarLogroDesbloqueado::class, function ($job) use ($user) {
            return $job->user->is($user) && $job->logro->is($this->primerAmigoLogro);
        });
        Queue::assertPushed(NotificarLogroDesbloqueado::class, function ($job) use ($friend) {
            return $job->user->is($friend) && $job->logro->is($this->primerAmigoLogro);
        });
    });

    it('does not unlock "First Friend" achievement or notify if the user already has it', function () {
        $user = User::factory()->create();
        $friend = User::factory()->create();

        $user->logros()->attach($this->primerAmigoLogro->id);

        expect($user->logros()->where('nombre', 'Primer Amigo')->exists())->toBeTrue();
        expect($friend->logros()->where('nombre', 'Primer Amigo')->exists())->toBeFalse();

        $listener = new DesbloquearLogroPrimerAmigo();
        $listener->handle(new AmigoAgregado($user, $friend));

        $this->assertDatabaseCount('logro_usuario', 2);
        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $user->id,
            'logro_id' => $this->primerAmigoLogro->id,
        ]);
        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $friend->id,
            'logro_id' => $this->primerAmigoLogro->id,
        ]);

        Queue::assertNotPushed(NotificarLogroDesbloqueado::class, function ($job) use ($user) {
            return $job->user->is($user);
        });
        Queue::assertPushed(NotificarLogroDesbloqueado::class, function ($job) use ($friend) {
            return $job->user->is($friend) && $job->logro->is($this->primerAmigoLogro);
        });
    });

    it('does not unlock "First Friend" achievement or notify if the friend already has it', function () {
        $user = User::factory()->create();
        $friend = User::factory()->create();

        $friend->logros()->attach($this->primerAmigoLogro->id);

        expect($user->logros()->where('nombre', 'Primer Amigo')->exists())->toBeFalse();
        expect($friend->logros()->where('nombre', 'Primer Amigo')->exists())->toBeTrue();

        $listener = new DesbloquearLogroPrimerAmigo();
        $listener->handle(new AmigoAgregado($user, $friend));

        $this->assertDatabaseCount('logro_usuario', 2);
        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $friend->id,
            'logro_id' => $this->primerAmigoLogro->id,
        ]);
        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $user->id,
            'logro_id' => $this->primerAmigoLogro->id,
        ]);

        Queue::assertNotPushed(NotificarLogroDesbloqueado::class, function ($job) use ($friend) {
            return $job->user->is($friend);
        });
        Queue::assertPushed(NotificarLogroDesbloqueado::class, function ($job) use ($user) {
            return $job->user->is($user) && $job->logro->is($this->primerAmigoLogro);
        });
    });

    it('does not unlock "First Friend" achievement or notify if both users already have it', function () {
        $user = User::factory()->create();
        $friend = User::factory()->create();

        $user->logros()->attach($this->primerAmigoLogro->id);
        $friend->logros()->attach($this->primerAmigoLogro->id);

        expect($user->logros()->where('nombre', 'Primer Amigo')->exists())->toBeTrue();
        expect($friend->logros()->where('nombre', 'Primer Amigo')->exists())->toBeTrue();

        $listener = new DesbloquearLogroPrimerAmigo();
        $listener->handle(new AmigoAgregado($user, $friend));

        $this->assertDatabaseCount('logro_usuario', 2);
        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $user->id,
            'logro_id' => $this->primerAmigoLogro->id,
        ]);
        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $friend->id,
            'logro_id' => $this->primerAmigoLogro->id,
        ]);

        Queue::assertNotPushed(NotificarLogroDesbloqueado::class, function ($job) use ($user) {
            return $job->user->is($user);
        });
        Queue::assertNotPushed(NotificarLogroDesbloqueado::class, function ($job) use ($friend) {
            return $job->user->is($friend);
        });
    });
});

describe('Achievement Listeners', function () {

    beforeEach(function () {

        $this->primerAmigoLogro = Logro::firstOrCreate([
            'nombre' => 'Primer Amigo',
            'descripcion' => 'Has agregado tu primer amigo'
        ]);

        $this->primerMensajeLogro = Logro::firstOrCreate([
            'nombre' => 'Primer Mensaje',
            'descripcion' => 'Has enviado o recibido tu primer mensaje'
        ]);

        Event::fake();
        Queue::fake();
    });

    it('asserts DesbloquearLogroPrimerAmigo is listening when AmigoAgregado is dispatched', function () {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        event(new AmigoAgregado($user1, $user2));

        Event::assertListening(
            AmigoAgregado::class,
            DesbloquearLogroPrimerAmigo::class
        );
    });

    it('unlocks "First Friend" achievement and notifies user when they do not have it', function () {
        $user = User::factory()->create();
        $friend = User::factory()->create();

        expect($user->logros()->where('logro_id', $this->primerAmigoLogro->id)->exists())->toBeFalse();
        expect($friend->logros()->where('logro_id', $this->primerAmigoLogro->id)->exists())->toBeFalse();

        $listener = new DesbloquearLogroPrimerAmigo();
        $listener->handle(new AmigoAgregado($user, $friend));

        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $user->id,
            'logro_id' => $this->primerAmigoLogro->id,
        ]);
        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $friend->id,
            'logro_id' => $this->primerAmigoLogro->id,
        ]);

        Queue::assertPushed(NotificarLogroDesbloqueado::class, function ($job) use ($user) {
            return $job->user->is($user) && $job->logro->is($this->primerAmigoLogro);
        });
        Queue::assertPushed(NotificarLogroDesbloqueado::class, function ($job) use ($friend) {
            return $job->user->is($friend) && $job->logro->is($this->primerAmigoLogro);
        });
    });

    it('does not unlock "First Friend" achievement or notify if user already has it', function () {
        $user = User::factory()->create();
        $friend = User::factory()->create();

        $user->logros()->attach($this->primerAmigoLogro->id);

        expect($user->logros()->where('logro_id', $this->primerAmigoLogro->id)->exists())->toBeTrue();
        expect($friend->logros()->where('logro_id', $this->primerAmigoLogro->id)->exists())->toBeFalse();

        $listener = new DesbloquearLogroPrimerAmigo();
        $listener->handle(new AmigoAgregado($user, $friend));

        $this->assertDatabaseCount('logro_usuario', 2);
        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $user->id,
            'logro_id' => $this->primerAmigoLogro->id,
        ]);
        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $friend->id,
            'logro_id' => $this->primerAmigoLogro->id,
        ]);

        Queue::assertNotPushed(NotificarLogroDesbloqueado::class, function ($job) use ($user) {
            return $job->user->is($user);
        });
        Queue::assertPushed(NotificarLogroDesbloqueado::class, function ($job) use ($friend) {
            return $job->user->is($friend) && $job->logro->is($this->primerAmigoLogro);
        });
    });

    it('does not unlock "First Friend" achievement or notify if friend already has it', function () {
        $user = User::factory()->create();
        $friend = User::factory()->create();

        $friend->logros()->attach($this->primerAmigoLogro->id);

        expect($user->logros()->where('logro_id', $this->primerAmigoLogro->id)->exists())->toBeFalse();
        expect($friend->logros()->where('logro_id', $this->primerAmigoLogro->id)->exists())->toBeTrue();

        $listener = new DesbloquearLogroPrimerAmigo();
        $listener->handle(new AmigoAgregado($user, $friend));

        $this->assertDatabaseCount('logro_usuario', 2);
        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $friend->id,
            'logro_id' => $this->primerAmigoLogro->id,
        ]);
        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $user->id,
            'logro_id' => $this->primerAmigoLogro->id,
        ]);

        Queue::assertNotPushed(NotificarLogroDesbloqueado::class, function ($job) use ($friend) {
            return $job->user->is($friend);
        });
        Queue::assertPushed(NotificarLogroDesbloqueado::class, function ($job) use ($user) {
            return $job->user->is($user) && $job->logro->is($this->primerAmigoLogro);
        });
    });

    it('does not unlock "First Friend" achievement or notify if both users already have it', function () {
        $user = User::factory()->create();
        $friend = User::factory()->create();

        $user->logros()->attach($this->primerAmigoLogro->id);
        $friend->logros()->attach($this->primerAmigoLogro->id);

        expect($user->logros()->where('logro_id', $this->primerAmigoLogro->id)->exists())->toBeTrue();
        expect($friend->logros()->where('logro_id', $this->primerAmigoLogro->id)->exists())->toBeTrue();

        $listener = new DesbloquearLogroPrimerAmigo();
        $listener->handle(new AmigoAgregado($user, $friend));

        $this->assertDatabaseCount('logro_usuario', 2);
        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $user->id,
            'logro_id' => $this->primerAmigoLogro->id,
        ]);
        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $friend->id,
            'logro_id' => $this->primerAmigoLogro->id,
        ]);

        Queue::assertNotPushed(NotificarLogroDesbloqueado::class, function ($job) use ($user) {
            return $job->user->is($user);
        });
        Queue::assertNotPushed(NotificarLogroDesbloqueado::class, function ($job) use ($friend) {
            return $job->user->is($friend);
        });
    });


    it('asserts DesbloquearLogroPrimerMensaje is listening when PrimerMensajeEnviado is dispatched', function () {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        event(new PrimerMensajeEnviado($sender, $receiver));

        Event::assertListening(
            PrimerMensajeEnviado::class,
            DesbloquearLogroPrimerMensaje::class
        );
    });

    it('unlocks "First Message" achievement and notifies both users when they do not have it', function () {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        expect($sender->logros()->where('logro_id', $this->primerMensajeLogro->id)->exists())->toBeFalse();
        expect($receiver->logros()->where('logro_id', $this->primerMensajeLogro->id)->exists())->toBeFalse();

        $listener = new DesbloquearLogroPrimerMensaje();
        $listener->handle(new PrimerMensajeEnviado($sender, $receiver));

        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $sender->id,
            'logro_id' => $this->primerMensajeLogro->id,
        ]);
        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $receiver->id,
            'logro_id' => $this->primerMensajeLogro->id,
        ]);

        Queue::assertPushed(NotificarLogroDesbloqueado::class, function ($job) use ($sender) {
            return $job->user->is($sender) && $job->logro->is($this->primerMensajeLogro);
        });
        Queue::assertPushed(NotificarLogroDesbloqueado::class, function ($job) use ($receiver) {
            return $job->user->is($receiver) && $job->logro->is($this->primerMensajeLogro);
        });
    });

    it('does not unlock "First Message" achievement or notify if sender already has it', function () {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $sender->logros()->attach($this->primerMensajeLogro->id);

        expect($sender->logros()->where('logro_id', $this->primerMensajeLogro->id)->exists())->toBeTrue();
        expect($receiver->logros()->where('logro_id', $this->primerMensajeLogro->id)->exists())->toBeFalse();

        $listener = new DesbloquearLogroPrimerMensaje();
        $listener->handle(new PrimerMensajeEnviado($sender, $receiver));

        $this->assertDatabaseCount('logro_usuario', 2);
        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $sender->id,
            'logro_id' => $this->primerMensajeLogro->id,
        ]);
        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $receiver->id,
            'logro_id' => $this->primerMensajeLogro->id,
        ]);

        Queue::assertNotPushed(NotificarLogroDesbloqueado::class, function ($job) use ($sender) {
            return $job->user->is($sender);
        });
        Queue::assertPushed(NotificarLogroDesbloqueado::class, function ($job) use ($receiver) {
            return $job->user->is($receiver) && $job->logro->is($this->primerMensajeLogro);
        });
    });

    it('does not unlock "First Message" achievement or notify if receiver already has it', function () {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $receiver->logros()->attach($this->primerMensajeLogro->id);

        expect($sender->logros()->where('logro_id', $this->primerMensajeLogro->id)->exists())->toBeFalse();
        expect($receiver->logros()->where('logro_id', $this->primerMensajeLogro->id)->exists())->toBeTrue();

        $listener = new DesbloquearLogroPrimerMensaje();
        $listener->handle(new PrimerMensajeEnviado($sender, $receiver));

        $this->assertDatabaseCount('logro_usuario', 2);
        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $sender->id,
            'logro_id' => $this->primerMensajeLogro->id,
        ]);
        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $receiver->id,
            'logro_id' => $this->primerMensajeLogro->id,
        ]);

        Queue::assertNotPushed(NotificarLogroDesbloqueado::class, function ($job) use ($receiver) {
            return $job->user->is($receiver);
        });
        Queue::assertPushed(NotificarLogroDesbloqueado::class, function ($job) use ($sender) {
            return $job->user->is($sender) && $job->logro->is($this->primerMensajeLogro);
        });
    });

    it('does not unlock "First Message" achievement or notify if both users already have it', function () {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $sender->logros()->attach($this->primerMensajeLogro->id);
        $receiver->logros()->attach($this->primerMensajeLogro->id);

        expect($sender->logros()->where('logro_id', $this->primerMensajeLogro->id)->exists())->toBeTrue();
        expect($receiver->logros()->where('logro_id', $this->primerMensajeLogro->id)->exists())->toBeTrue();

        $listener = new DesbloquearLogroPrimerMensaje();
        $listener->handle(new PrimerMensajeEnviado($sender, $receiver));

        $this->assertDatabaseCount('logro_usuario', 2);
        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $sender->id,
            'logro_id' => $this->primerMensajeLogro->id,
        ]);
        $this->assertDatabaseHas('logro_usuario', [
            'user_id' => $receiver->id,
            'logro_id' => $this->primerMensajeLogro->id,
        ]);

        Queue::assertNotPushed(NotificarLogroDesbloqueado::class, function ($job) use ($sender) {
            return $job->user->is($sender);
        });
        Queue::assertNotPushed(NotificarLogroDesbloqueado::class, function ($job) use ($receiver) {
            return $job->user->is($receiver);
        });
    });
});
