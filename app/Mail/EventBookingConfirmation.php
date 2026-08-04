<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\EventBooking;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventBookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $event;
    public $booking;

    public function __construct(User $user, Event $event, EventBooking $booking)
    {
        $this->user = $user;
        $this->event = $event;
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->subject('Event Booking Confirmation')
            ->view('emails.event-booking-confirmation');
    }
}
