<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class CompanyRegisterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $company;

    public function __construct($user, $company)
    {
        $this->user = $user;
        $this->company = $company;
    }

    public function build()
    {
        return $this->subject('Welcome to SIA Professional – Company Account Created')
                    ->view('emails.company-register');
    }
}
