<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $reason;

    public function __construct(User $user, $reason)
    {
        $this->user = $user;
        $this->reason = $reason;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '❌ Registration Update - MTU Academic Portal',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user-rejected',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
