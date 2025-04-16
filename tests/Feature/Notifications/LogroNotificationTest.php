<?php

use App\Models\users\User;
use App\Notifications\LogroNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Notifiable;

it('sends a mail notification with the correct content', function () {
    $logro = (object) [
        'nombre' => 'Logro Épico',
        'descripcion' => 'Has alcanzado un nivel increíble de habilidad.',
    ];

    $notifiable = new class extends User {
        use Notifiable;

        protected $fillable = ['email'];
    };
    $notifiable->email = 'test@example.com';

    Notification::fake();

    $notifiable->notify(new LogroNotification($logro));

    Notification::assertSentTo(
        [$notifiable],
        LogroNotification::class
    );

    Notification::assertSentTo(
        [$notifiable],
        LogroNotification::class,
        function ($notification, $channels) use ($logro, $notifiable) {
            return in_array('mail', $channels);
        }
    );
});

it('creates a database notification with the correct data', function () {
    $logro = (object) [
        'nombre' => 'Logro Secreto',
        'descripcion' => 'Descubriste algo que pocos han visto.',
    ];

    $notifiable = new class extends User {
        use Notifiable;

        protected $fillable = ['email'];
    };
    $notifiable->email = 'test@example.com';

    Notification::fake();

    $notifiable->notify(new LogroNotification($logro));

    Notification::assertSentTo(
        [$notifiable],
        LogroNotification::class,
        function ($notification, $channels) use ($logro, $notifiable) {
            if (in_array('database', $channels)) {
                return $notification->toDatabase($notifiable) === [
                        'logro_nombre' => $logro->nombre,
                        'mensaje' => '¡Has desbloqueado el logro: ' . $logro->nombre . '!',
                    ];
            }
            return false;
        }
    );
});

it('sends the notification via both mail and database', function () {
    $logro = (object) [
        'nombre' => 'Logro Comunitario',
        'descripcion' => 'Participaste activamente con otros jugadores.',
    ];

    $notifiable = new class extends User {
        use Notifiable;

        protected $fillable = ['email'];
    };
    $notifiable->email = 'test@example.com';

    Notification::fake();

    $notifiable->notify(new LogroNotification($logro));

    Notification::assertSentTo(
        [$notifiable],
        LogroNotification::class,
        function ($notification, $channels) {
            return in_array('mail', $channels) && in_array('database', $channels);
        }
    );
});

it('creates a database notification with a different logro', function () {
    $logro = (object) [
        'nombre' => 'Cazador de Secretos',
        'descripcion' => 'Encontraste todos los secretos ocultos.',
    ];

    $notifiable = new class extends User {
        use Notifiable;

        protected $fillable = ['email'];
    };
    $notifiable->email = 'test@example.com';

    Notification::fake();

    $notifiable->notify(new LogroNotification($logro));

    Notification::assertSentTo(
        [$notifiable],
        LogroNotification::class,
        function ($notification, $channels) use ($logro, $notifiable) {
            if (in_array('database', $channels)) {
                $databaseData = $notification->toDatabase($notifiable);
                expect($databaseData['logro_nombre'])->toBe($logro->nombre);
                expect($databaseData['mensaje'])->toBe('¡Has desbloqueado el logro: ' . $logro->nombre . '!');
                return true;
            }
            return false;
        }
    );
});

it('sends a mail notification with a different logro', function () {
    $logro = (object) [
        'nombre' => 'Maestro Jugador',
        'descripcion' => 'Has alcanzado el máximo nivel.',
    ];

    $notifiable = new class extends User {
        use Notifiable;

        protected $fillable = ['email'];
    };
    $notifiable->email = 'test@example.com';

    Notification::fake();

    $notifiable->notify(new LogroNotification($logro));

    Notification::assertSentTo(
        [$notifiable],
        LogroNotification::class,
        function ($notification, $channels) use ($logro, $notifiable) {
            if (in_array('mail', $channels)) {
                $mailMessage = $notification->toMail($notifiable);
                $rendered = $mailMessage->render();

                expect(str_contains($rendered, "Has desbloqueado el logro: {$logro->nombre}"))->toBeTrue();
                expect(str_contains($rendered, $logro->descripcion))->toBeTrue();
                return true;
            }
            return false;
        }
    );
});
