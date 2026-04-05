<?php

namespace App\Mail;

use App\Models\Guest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RsvpAdminNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Guest $guest,
        public bool $isUpdate,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->isUpdate
            ? __('RSVP updated — :name (:event)', [
                'name' => $this->guest->name,
                'event' => __(config('wedding.event.title')),
            ])
            : __('New RSVP — :name (:event)', [
                'name' => $this->guest->name,
                'event' => __(config('wedding.event.title')),
            ]);

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.rsvp-admin-notification',
        );
    }
}
