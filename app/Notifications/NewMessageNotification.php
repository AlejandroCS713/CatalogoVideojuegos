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
            ->subject(__('You have a new message'))
            ->greeting(__('Hello!'))
            ->line(__('You have received a new message in your account.'))
            ->line(__('Message: ') . $this->message->message)
            ->action(__('View message'), $chatUrl)
            ->line(__('Feel free to reply to your friend!'));
    }
    public function toArray($notifiable)
    {
        return [
            'message' => $this->message->message,
            'sender_id' => $this->message->sender_id,
        ];
    }
}
