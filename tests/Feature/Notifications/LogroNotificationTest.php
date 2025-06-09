<?php

use App\Models\users\User;
use App\Notifications\LogroNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Notifiable;


beforeEach(function () {
    Notification::fake();

});
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

it('sends a mail notification with a different logro', function () {
    $logro = (object) [
        'nombre' => 'Maestro Jugador',
        'descripcion' => 'Has alcanzado el máximo nivel.',
    ];

    $notifiable = User::factory()->create(['email' => 'test@example.com']);

    app()->setLocale('es');

    $notifiable->notify(new LogroNotification($logro));

    Notification::assertSentTo(
        [$notifiable],
        LogroNotification::class,
        function ($notification, $channels) use ($logro, $notifiable) {
            if (in_array('mail', $channels)) {
                $mailMessage = $notification->toMail($notifiable);
                $rendered = $mailMessage->render();

                expect(str_contains($rendered, __('You have unlocked the achievement: ') . $logro->nombre))->toBeTrue();
                expect(str_contains($rendered, $logro->descripcion))->toBeTrue();
                return true;
            }
            return false;
        }
    );
});

it('creates a database notification with the correct data', function () {
    $logro = (object) [
        'nombre' => 'Logro Secreto',
        'descripcion' => 'Descubriste algo que pocos han visto.',
    ];

    $notifiable = User::factory()->create(['email' => 'test_logro_correct_data@example.com']);

    app()->setLocale('es');

    $notifiable->notify(new LogroNotification($logro));

    Notification::assertSentTo(
        [$notifiable],
        LogroNotification::class,
        function ($notification, $channels) use ($logro, $notifiable) {
            if (!in_array('database', $channels)) {
                return false;
            }

            $databaseData = $notification->toDatabase($notifiable);

            $expectedMessagePart = __('You have unlocked the achievement: ');
            $expectedMessage = $expectedMessagePart . $logro->nombre . '!';

            expect($databaseData['logro_nombre'])->toBe($logro->nombre);
            expect($databaseData['mensaje'])->toBe($expectedMessage);

            return true;
        }
    );
});

it('creates a database notification with a different logro', function () {
    $logro = (object) [
        'nombre' => 'Cazador de Secretos',
        'descripcion' => 'Encontraste todos los secretos ocultos.',
    ];

    $notifiable = User::factory()->create(['email' => 'test_logro_different_data@example.com']);

    app()->setLocale('es');

    $notifiable->notify(new LogroNotification($logro));

    Notification::assertSentTo(
        [$notifiable],
        LogroNotification::class,
        function ($notification, $channels) use ($logro, $notifiable) {
            if (!in_array('database', $channels)) {
                return false;
            }

            $databaseData = $notification->toDatabase($notifiable);

            $expectedMessagePart = __('You have unlocked the achievement: ');
            $expectedMessage = $expectedMessagePart . $logro->nombre . '!';

            expect($databaseData['logro_nombre'])->toBe($logro->nombre);
            expect($databaseData['mensaje'])->toBe($expectedMessage);
            return true;
        }
    );
});
