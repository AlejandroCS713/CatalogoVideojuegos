<?php

namespace App\Jobs;

use App\Models\users\User;
use App\Notifications\LogroNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotificarLogroDesbloqueado implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user, $logro;

    public function __construct(User $user, $logro) {
        $this->user = $user;
        $this->logro = $logro;
    }

    public function handle() {
        $this->user->notify(new LogroNotification($this->logro));
    }
}
