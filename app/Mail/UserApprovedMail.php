<?php
// app/Mail/UserApprovedMail.php

use App\Models\User;

class UserApprovedMail extends Mailable
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function envelope()
    {
        return new Envelope(
            subject: '✅ Your MTU Account Has Been Approved!',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.user-approved',
        );
    }
}
