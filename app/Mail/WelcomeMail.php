<?php

namespace App\Mail;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Swift_Image;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password = null;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $password = null)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.welcome')
            ->subject('Please Verify Your Email Address')
            ->with([
                'user' => $this->user,
                'password' => $this->password,
            ]);
    }
}
