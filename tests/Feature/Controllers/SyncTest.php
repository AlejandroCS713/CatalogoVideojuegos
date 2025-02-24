<?php


use App\Events\AmigoAgregado;
use App\Jobs\NotificarLogroDesbloqueado;
use App\Listeners\DesbloquearLogroPrimerAmigo;
use App\Models\users\Friend;
use App\Models\users\Logro;
use App\Models\users\Message;
use App\Models\users\User;
use App\Notifications\LogroNotification;
use App\Notifications\NewMessageNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Bus;



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


it('sends LogroNotification manually', function () {
    Notification::fake();

    $user = User::factory()->create();

    $logro = Logro::create([
        'nombre' => 'Logro de Test ' . now()->timestamp,
        'descripcion' => 'Este es un logro de prueba',
    ]);

    $user->notify(new LogroNotification($logro));

    Notification::assertSentTo($user, LogroNotification::class);
});

it('sends a NewMessageNotification when a message is sent', function () {
    Notification::fake();

    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    Friend::create([
        'user_id' => $user1->id,
        'friend_id' => $user2->id,
        'status' => 'accepted',
    ]);

    Auth::login($user1);

    $message = Message::create([
        'sender_id' => $user1->id,
        'receiver_id' => $user2->id,
        'message' => '¡Hola!',
    ]);

    $isFriend = Friend::where(function ($query) use ($user1, $user2) {
        $query->where('user_id', $user1->id)
            ->where('friend_id', $user2->id);
    })->orWhere(function ($query) use ($user1, $user2) {
        $query->where('user_id', $user2->id)
            ->where('friend_id', $user1->id);
    })->where('status', 'accepted')->exists();

    if ($isFriend) {
        $user2->notify(new NewMessageNotification($message));
    }

    Notification::assertSentTo(
        $user2,
        NewMessageNotification::class,
        function ($notification) use ($message) {
            return $notification->message->message === $message->message
                && $notification->message->sender_id === $message->sender_id
                && $notification->message->receiver_id === $message->receiver_id;
        }
    );

    $this->assertDatabaseHas('messages', [
        'sender_id' => $user1->id,
        'receiver_id' => $user2->id,
        'message' => '¡Hola!',
    ]);
});
