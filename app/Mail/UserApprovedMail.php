<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Your MTU Account Has Been Approved!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user-approved',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
