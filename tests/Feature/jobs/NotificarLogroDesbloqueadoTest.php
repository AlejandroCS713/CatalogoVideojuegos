<?php
use App\Jobs\NotificarLogroDesbloqueado;
use App\Models\users\User;
use App\Notifications\LogroNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
it('sends logro notification when job is handled', function () {
    Notification::fake();

    $user = User::factory()->create();
    $logro = 'Nuevo logro desbloqueado';

    $job = new NotificarLogroDesbloqueado($user, $logro);

    $job->handle();

    Notification::assertSentTo(
        $user, LogroNotification::class,
        function ($notification) use ($logro) {
            return $notification->logro === $logro;
        }
    );
});

it('job is pushed to queue', function () {
    $user = User::factory()->create();

    $logro = 'Nuevo logro desbloqueado';

    Queue::fake();

    NotificarLogroDesbloqueado::dispatch($user, $logro);

    Queue::assertPushed(NotificarLogroDesbloqueado::class);
});
