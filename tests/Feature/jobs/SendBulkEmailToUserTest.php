<?php
use App\Jobs\SendBulkEmailToUser;
use App\Models\users\User;
use Illuminate\Support\Facades\Queue;
use App\Mail\AdminBulkEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

it('job is pushed to queue', function () {
    $user = User::factory()->create([
        'email' => 'unique-email-' . Str::random(10) . '@example.com',
    ]);
    $message = 'Este es un mensaje de prueba para todos los usuarios.';

    Queue::fake();

    SendBulkEmailToUser::dispatch($user, $message);

    Queue::assertPushed(SendBulkEmailToUser::class, function ($job) use ($user, $message) {
        return $job->user->id === $user->id && $job->messageContent === $message;
    });
});



it('sends bulk email when job is handled', function () {
    Mail::fake();

    $user = User::factory()->create([
        'email' => 'unique-email-' . Str::random(10) . '@example.com',
    ]);
    $message = 'Mensaje masivo de prueba desde el admin.';

    $job = new SendBulkEmailToUser($user, $message);
    $job->handle();

    Mail::assertSent(AdminBulkEmail::class, function ($mail) use ($user, $message) {
        return $mail->hasTo($user->email)
            && $mail->messageContent === $message;
    });
});

it('logs critical error when job fails', function () {
    Log::shouldReceive('critical')
        ->once()
        ->withArgs(function ($message) {
            return str_contains($message, 'Job SendBulkEmailToUser falló definitivamente');
        });

    $user = User::factory()->create();
    $job = new SendBulkEmailToUser($user, 'Mensaje');

    $job->failed(new Exception('Simulated failure'));
});
it('handles job failure', function () {
    Mail::shouldReceive('to')
        ->once()
        ->andThrow(new Exception('Error en el envío del correo'));

    $user = User::factory()->create([
        'email' => 'unique-email-' . Str::random(10) . '@example.com',
    ]);
    $message = 'Mensaje de prueba con fallo en el envío';

    $job = new SendBulkEmailToUser($user, $message);

    Log::shouldReceive('error')
        ->once()
        ->with("Error enviando email encolado a {$user->email}: Error en el envío del correo");

    $job->handle();

    $this->assertTrue(true);
});

it('calls failed method on failure', function () {
    $exception = new Exception('Error al enviar correo');

    $user = User::factory()->create([
        'email' => 'unique-email-' . Str::random(10) . '@example.com',
    ]);
    $message = 'Este es un mensaje de prueba para manejar fallos.';

    $job = new SendBulkEmailToUser($user, $message);

    Log::shouldReceive('critical')
        ->once()
        ->with("Job SendBulkEmailToUser falló definitivamente para el usuario {$user->id} ({$user->email}): {$exception->getMessage()}");

    $job->failed($exception);

    $this->assertTrue(true);
});
