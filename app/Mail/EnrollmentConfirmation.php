<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnrollmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $training;
    public $enrollment;

    public function __construct($student, $training, $enrollment)
    {
        $this->student = $student;
        $this->training = $training;
        $this->enrollment = $enrollment;
    }

    public function build()
    {
        return $this->subject("Enrollment Confirmation for {$this->training->training_name}")
                    ->view('emails.enrollment_confirmation');
    }
}
