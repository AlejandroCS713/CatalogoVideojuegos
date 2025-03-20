<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification
{
    use Queueable;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $senderId = $this->message->sender_id;

        $chatUrl = url("/chat/{$senderId}");

        return (new MailMessage)
            ->subject('Tienes un nuevo mensaje')
            ->greeting('¡Hola!')
            ->line('Has recibido un nuevo mensaje en tu cuenta.')
            ->line('Mensaje: ' . $this->message->message)
            ->action('Ver mensaje', $chatUrl)
            ->line('¡No dudes en responder a tu amigo!');
    }
    public function toArray($notifiable)
    {
        return [
            'message' => $this->message->message,
            'sender_id' => $this->message->sender_id,
        ];
    }
}
