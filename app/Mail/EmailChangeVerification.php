<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailChangeVerification extends Mailable
{
    use SerializesModels;

    public $user;
    public $newEmail;
    public $token;

    public function __construct(User $user, $newEmail, $token)
    {
        $this->user = $user;
        $this->newEmail = $newEmail;
        $this->token = $token;
    }

    public function build()
    {
        return $this->subject('Konfirmasi Perubahan Email')
                    ->view('emails.email_change_verification');
    }
}
