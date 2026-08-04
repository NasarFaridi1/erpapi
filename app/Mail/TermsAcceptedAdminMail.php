<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TermsAcceptedAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $record;

    public function __construct($record)
    {
        $this->record = $record;
    }

    public function build()
    {
        // PDF absolute path in /public
        $pdfFullPath = public_path($this->record->pdf_path);

        return $this->subject('Institute Accepted Terms & Conditions')
            ->view('emails.terms_accepted_admin')
            ->with([
                'record' => $this->record,
                'institute' => $this->record->institute,
            ])
            ->attach($pdfFullPath, [
                'as' => 'TermsAcceptance.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
