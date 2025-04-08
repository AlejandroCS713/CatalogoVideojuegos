<?php

namespace App\Jobs;

use App\Mail\AdminBulkEmail;
use App\Models\users\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendBulkEmailToUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $timeout = 120;


    protected User $user;
    protected string $messageContent;

    public function __construct(User $user, string $messageContent)
    {
        $this->user = $user;
        $this->messageContent = $messageContent;
    }

    public function handle(): void
    {
        try {
            $email = new AdminBulkEmail($this->messageContent);

            Mail::to($this->user->email)->send($email);
            Log::info("Enviado a  {$this->user->email}: " );
        } catch (Throwable $exception) {
            Log::error("Error enviando email encolado a {$this->user->email}: " . $exception->getMessage());
        }
    }

    public function failed(Throwable $exception): void
    {

        Log::critical("Job SendBulkEmailToUser falló definitivamente para el usuario {$this->user->id} ({$this->user->email}): " . $exception->getMessage());
    }
}
