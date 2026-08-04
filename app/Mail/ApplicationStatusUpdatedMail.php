<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;

class ApplicationStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $recipientType;

    /**
     * Create a new message instance.
     */
    public function __construct(Application $application, $recipientType)
    {
        $this->application = $application;
        $this->recipientType = $recipientType;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Application Status Updated')
                    ->markdown('emails.application_status_updated')
                    ->with([
                        'application' => $this->application,
                        'recipientType' => $this->recipientType,
                    ]);
    }
}
