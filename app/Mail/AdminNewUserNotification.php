<?php
// app/Mail/AdminNewUserNotification.php

use App\Models\User;

class AdminNewUserNotification extends Mailable
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function envelope()
    {
        return new Envelope(
            subject: '🔔 New User Registration Pending Approval',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.admin-new-user',
        );
    }
}
