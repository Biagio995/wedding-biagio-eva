<?php

namespace App\Mail;

use App\Models\Guest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GuestInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Guest $guest)
    {
        $this->locale($guest->mailLocale());
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('You have been invited to our wedding'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.guest-invitation',
        );
    }
}
