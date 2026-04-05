<?php

namespace Tests\Feature;

use App\Mail\RsvpReminderMail;
use App\Models\Guest;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RsvpReminderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_command_sends_reminder_when_enabled_and_guest_eligible(): void
    {
        Config::set('wedding.rsvp_reminders.enabled', true);
        Config::set('wedding.rsvp_reminders.cooldown_days', 14);

        $guest = Guest::query()->create([
            'name' => 'Remind Me',
            'email' => 'guest@example.com',
            'token' => 'tok-remind-1',
            'rsvp_status' => null,
            'rsvp_reminder_sent_at' => null,
        ]);

        Artisan::call('wedding:send-rsvp-reminders');

        Mail::assertSent(RsvpReminderMail::class, function (RsvpReminderMail $mail) use ($guest): bool {
            return $mail->guest->is($guest);
        });

        $guest->refresh();
        $this->assertNotNull($guest->rsvp_reminder_sent_at);
    }

    public function test_command_sends_nothing_when_disabled(): void
    {
        Config::set('wedding.rsvp_reminders.enabled', false);

        Guest::query()->create([
            'name' => 'No Send',
            'email' => 'nosend@example.com',
            'token' => 'tok-remind-off',
            'rsvp_status' => null,
        ]);

        Artisan::call('wedding:send-rsvp-reminders');

        Mail::assertNothingSent();
    }

    public function test_command_skips_guest_without_email(): void
    {
        Config::set('wedding.rsvp_reminders.enabled', true);

        Guest::query()->create([
            'name' => 'No Email',
            'email' => null,
            'token' => 'tok-no-email',
            'rsvp_status' => null,
        ]);

        Artisan::call('wedding:send-rsvp-reminders');

        Mail::assertNothingSent();
    }

    public function test_command_skips_guest_who_already_rsvp(): void
    {
        Config::set('wedding.rsvp_reminders.enabled', true);

        Guest::query()->create([
            'name' => 'Already',
            'email' => 'yes@example.com',
            'token' => 'tok-rsvp-yes',
            'rsvp_status' => 'yes',
        ]);

        Artisan::call('wedding:send-rsvp-reminders');

        Mail::assertNothingSent();
    }

    public function test_command_respects_cooldown_after_recent_reminder(): void
    {
        Config::set('wedding.rsvp_reminders.enabled', true);
        Config::set('wedding.rsvp_reminders.cooldown_days', 14);

        Guest::query()->create([
            'name' => 'Cooling',
            'email' => 'cool@example.com',
            'token' => 'tok-cooldown',
            'rsvp_status' => null,
            'rsvp_reminder_sent_at' => Carbon::now()->subDays(5),
        ]);

        Artisan::call('wedding:send-rsvp-reminders');

        Mail::assertNothingSent();
    }

    public function test_command_sends_again_after_cooldown_elapsed(): void
    {
        Config::set('wedding.rsvp_reminders.enabled', true);
        Config::set('wedding.rsvp_reminders.cooldown_days', 14);

        $guest = Guest::query()->create([
            'name' => 'Second wave',
            'email' => 'again@example.com',
            'token' => 'tok-cooldown-ok',
            'rsvp_status' => null,
            'rsvp_reminder_sent_at' => Carbon::now()->subDays(20),
        ]);

        Artisan::call('wedding:send-rsvp-reminders');

        Mail::assertSent(RsvpReminderMail::class, function (RsvpReminderMail $mail) use ($guest): bool {
            return $mail->guest->is($guest);
        });
    }
}
