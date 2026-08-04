<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\JobApplication;

class JobApplicationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $application;

    public function __construct(JobApplication $application)
    {
        $this->application = $application;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Job Application Submitted')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new job application has been submitted.')
            ->line('Job Title: ' . $this->application->job->title)
            ->line('Applicant: ' . $this->application->user->name)
            ->action('View Application', url('/applications/' . $this->application->id))
            ->line('Thank you for using our platform!');
    }
}
