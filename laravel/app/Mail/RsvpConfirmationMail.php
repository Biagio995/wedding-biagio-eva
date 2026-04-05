<?php

namespace App\Mail;

use App\Models\Guest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RsvpConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Guest $guest) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('RSVP received — :event', ['event' => config('wedding.event.title')]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.rsvp-confirmation',
        );
    }
}
