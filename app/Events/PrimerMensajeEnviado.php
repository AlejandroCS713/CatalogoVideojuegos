<?php
namespace App\Events;

use App\Models\users\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrimerMensajeEnviado
{
    use Dispatchable, SerializesModels;

    public $sender;
    public $receiver;

    public function __construct(User $sender, User $receiver)
    {
        $this->sender = $sender;
        $this->receiver = $receiver;
    }
}
