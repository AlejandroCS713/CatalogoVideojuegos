<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LogroNotification extends Notification {
    use Queueable;

    public $logro;

    public function __construct($logro) {
        $this->logro = $logro;
    }

    public function via($notifiable) {
        return ['mail', 'database'];
    }

    public function toMail($notifiable) {
        return (new MailMessage)
            ->subject('¡Has desbloqueado un logro!')
            ->greeting('¡Felicidades!')
            ->line("Has desbloqueado el logro: {$this->logro->nombre}")
            ->line($this->logro->descripcion)
            ->line('¡Sigue jugando y desbloquea más logros!')
            ->action('Ver logros', url('/perfil/logros'))
            ->line('Gracias por ser parte de la comunidad.');
    }
    public function toDatabase($notifiable) {
        return [
            'logro_nombre' => $this->logro->nombre,
            'mensaje' => '¡Has desbloqueado el logro: ' . $this->logro->nombre . '!',
        ];
    }
}
