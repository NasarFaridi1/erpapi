<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrainingReminderMail extends Mailable
{
    use SerializesModels;

    public $student;
    public $training;

    public function __construct($student, $training)
    {
        $this->student = $student;
        $this->training = $training;
    }

    public function build()
    {
        return $this->subject('Training Reminder - Starting Tomorrow')
                    ->view('emails.training_reminder');
    }
}
