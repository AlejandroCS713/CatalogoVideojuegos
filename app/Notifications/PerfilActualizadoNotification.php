<?php

namespace App\Notifications;

use App\Models\users\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PerfilActualizadoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;
    public $cambiosRealizados;

    public function __construct(User $user, array $cambiosRealizados)
    {
        $this->user = $user;
        $this->cambiosRealizados = $cambiosRealizados;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }
    public function toMail($notifiable)
    {
        $subject = __('Your profile has been updated!');
        $greeting = __('Hello,') . ' ' . $this->user->name . '!';
        $lines = [__('We wanted to inform you that your profile has been updated.'), ''];

        if (in_array('name', $this->cambiosRealizados)) {
            $lines[] = __('Your nickname has been changed.');
        }
        if (in_array('password', $this->cambiosRealizados)) {
            $lines[] = __('Your password has been updated.');
        }
        if (in_array('avatar', $this->cambiosRealizados)) {
            $lines[] = __('Your avatar has been updated.');
        }

        $lines[] = '';
        $lines[] = __('If you did not make these changes, please contact support immediately.');
        $lines[] = __('Thank you for being part of the community.');


        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting($greeting);

        foreach ($lines as $line) {
            $mailMessage->line($line);
        }

        return $mailMessage;
    }

    public function toArray($notifiable)
    {
        return [
            'changes' => $this->cambiosRealizados,
            'message' => __('Profile updated for user: ') . $this->user->email,
        ];
    }
}
