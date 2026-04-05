<?php

namespace App\Mail;

use App\Models\Guest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RsvpReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Guest $guest) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Reminder: RSVP for :event', ['event' => __(config('wedding.event.title'))]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.rsvp-reminder',
        );
    }
}
