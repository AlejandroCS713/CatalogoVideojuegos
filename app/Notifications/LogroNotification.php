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
            ->subject(__('You have unlocked an achievement!'))
            ->greeting(__('Congratulations!'))
            ->line(__('You have unlocked the achievement: ') . $this->logro->nombre)
            ->line($this->logro->descripcion)
            ->line(__('Keep playing and unlock more achievements!'))
            ->line(__('Go to your profile and see your new achievement.'))
            ->line(__('Thank you for being part of the community.'));
    }

    public function toDatabase($notifiable) {
        return [
            'logro_nombre' => $this->logro->nombre,
            'mensaje' => __('You have unlocked the achievement: ') . $this->logro->nombre . '!',
        ];
    }
}
