<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InstituteTermsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $institute;
    public $token;

    public function __construct($institute, $token)
    {
        $this->institute = $institute;
        $this->token = $token;
    }

    public function build()
    {
       $url = 'https://siaprofessional.nexteck.uk/verification/' . $this->token;

        return $this->subject('Accept Terms & Conditions')
            ->view('emails.institute_terms')
            ->with([
                'institute' => $this->institute,
                'url' => $url
            ]);
    }
}
