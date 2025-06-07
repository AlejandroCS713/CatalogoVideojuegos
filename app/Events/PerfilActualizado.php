<?php

namespace App\Events;

use App\Models\users\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PerfilActualizado
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $cambiosRealizados;


    public function __construct(User $user, array $cambiosRealizados = [])
    {
        $this->user = $user;
        $this->cambiosRealizados = $cambiosRealizados;
    }
}
