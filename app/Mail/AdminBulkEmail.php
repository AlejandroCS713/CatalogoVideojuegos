<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminBulkEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $messageContent;

    public function __construct(string $messageContent)
    {
        $this->messageContent = $messageContent;
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Un mensaje importante de la administración',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.bulk',
            with: [
                'messageBody' => $this->messageContent,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
