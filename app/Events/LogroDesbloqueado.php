<?php

namespace App\Events;

use App\Models\users\Logro;
use App\Models\users\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LogroDesbloqueado {
    use Dispatchable, SerializesModels;

    public $user, $logro;

    public function __construct(User $user, Logro $logro) {
        $this->user = $user;
        $this->logro = $logro;
    }
}
