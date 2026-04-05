<?php

namespace App\Console\Commands;

use App\Mail\RsvpReminderMail;
use App\Models\Guest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendRsvpRemindersCommand extends Command
{
    protected $signature = 'wedding:send-rsvp-reminders';

    protected $description = 'Send RSVP reminder emails to guests who have not responded (requires email).';

    public function handle(): int
    {
        if (! config('wedding.rsvp_reminders.enabled', false)) {
            $this->comment('RSVP reminders are disabled (wedding.rsvp_reminders.enabled).');

            return self::SUCCESS;
        }

        $cooldownDays = max(1, (int) config('wedding.rsvp_reminders.cooldown_days', 14));

        $query = Guest::query()
            ->whereNull('rsvp_status')
            ->whereNotNull('email')
            ->where(function ($q) use ($cooldownDays): void {
                $q->whereNull('rsvp_reminder_sent_at')
                    ->orWhere('rsvp_reminder_sent_at', '<', now()->subDays($cooldownDays));
            });

        $sent = 0;

        foreach ($query->cursor() as $guest) {
            try {
                Mail::to($guest->email)->send(new RsvpReminderMail($guest));
                $guest->forceFill(['rsvp_reminder_sent_at' => now()])->save();
                $sent++;
            } catch (\Throwable $e) {
                report($e);
                $this->error("Failed for guest {$guest->id}: {$e->getMessage()}");
            }
        }

        $this->info("Sent {$sent} RSVP reminder(s).");

        return self::SUCCESS;
    }
}
