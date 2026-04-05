<?php

namespace Tests\Feature;

use App\Http\Controllers\WeddingController;
use App\Mail\RsvpAdminNotificationMail;
use App\Mail\RsvpConfirmationMail;
use App\Models\Guest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WeddingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_wedding_page_loads_without_session(): void
    {
        $response = $this->get('/w');

        $response->assertOk();
        $response->assertSee(config('wedding.event.title'), false);
    }

    public function test_event_additional_notes_section_when_configured(): void
    {
        Config::set('wedding.event.additional_notes', "Parking behind the venue.\nCeremony starts at 16:00.");

        $this->get('/w')
            ->assertOk()
            ->assertSee('Parking behind the venue.', false)
            ->assertSee('Additional details', false);
    }

    public function test_wedding_page_includes_countdown_markup(): void
    {
        Config::set('wedding.event.date', '2035-12-01 15:00:00');

        $response = $this->get('/w');

        $response->assertOk();
        $response->assertSee('id="wedding-countdown"', false);
        $response->assertSee('data-countdown-target', false);
        $response->assertSee('data-cd="days"', false);
    }

    public function test_valid_token_redirects_to_w_and_sets_session(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Test Guest',
            'token' => 'secure-token-123',
        ]);

        $response = $this->get('/w/'.$guest->token);

        $response->assertRedirect(route('wedding.show'));
        $this->assertEquals($guest->id, session(WeddingController::SESSION_WEDDING_GUEST_ID));
    }

    public function test_token_query_on_w_redirects_and_sets_session(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Query Token Guest',
            'token' => 'query-token-xyz',
        ]);

        $response = $this->get('/w?token='.$guest->token);

        $response->assertRedirect(route('wedding.show'));
        $this->assertEquals($guest->id, session(WeddingController::SESSION_WEDDING_GUEST_ID));
    }

    public function test_invalid_token_redirects_with_flash(): void
    {
        $response = $this->get('/w/not-a-real-token');

        $response->assertRedirect(route('wedding.show'));
        $response->assertSessionHas('wedding_error');
    }

    public function test_rsvp_updates_guest_when_session_present(): void
    {
        $guest = Guest::query()->create([
            'name' => 'RSVP User',
            'token' => 'tok-rsvp',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'yes',
                'guests_count' => 2,
                'notes' => 'Veggie meal',
            ])
            ->assertRedirect(route('wedding.show'))
            ->assertSessionHas('wedding_success', __('Thank you — your response has been saved.'))
            ->assertSessionHas('wedding_confirmation_email_sent', false);

        $guest->refresh();
        $this->assertSame('yes', $guest->rsvp_status);
        $this->assertSame(2, $guest->guests_count);
        $this->assertSame('Veggie meal', $guest->notes);
        Mail::assertNothingSent();
    }

    public function test_rsvp_sends_confirmation_email_when_enabled_and_guest_has_email_us08(): void
    {
        Config::set('wedding.rsvp.send_confirmation_email', true);

        $guest = Guest::query()->create([
            'name' => 'Mail Guest',
            'email' => 'mail-guest@example.test',
            'token' => 'tok-mail-us08',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'yes',
                'guests_count' => 1,
            ])
            ->assertSessionHas('wedding_success')
            ->assertSessionHas('wedding_confirmation_email_sent', true);

        Mail::assertSent(RsvpConfirmationMail::class, function (RsvpConfirmationMail $mail) use ($guest): bool {
            return $mail->guest->is($guest);
        });
    }

    public function test_rsvp_does_not_send_confirmation_when_disabled(): void
    {
        Config::set('wedding.rsvp.send_confirmation_email', false);

        $guest = Guest::query()->create([
            'name' => 'No Mail',
            'email' => 'has@email.test',
            'token' => 'tok-no-mail',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'no',
            ])
            ->assertSessionHas('wedding_confirmation_email_sent', false);

        Mail::assertNothingSent();
    }

    public function test_rsvp_does_not_send_confirmation_when_guest_email_missing(): void
    {
        Config::set('wedding.rsvp.send_confirmation_email', true);

        $guest = Guest::query()->create([
            'name' => 'No Email',
            'email' => null,
            'token' => 'tok-no-addr',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'yes',
                'guests_count' => 1,
            ])
            ->assertSessionHas('wedding_confirmation_email_sent', false);

        Mail::assertNothingSent();
    }

    public function test_guest_can_edit_rsvp_after_first_submit_us07(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Editor',
            'token' => 'tok-edit-us07',
        ]);

        $sid = WeddingController::SESSION_WEDDING_GUEST_ID;

        $this->withSession([$sid => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'yes',
                'guests_count' => 2,
            ])
            ->assertSessionHas('wedding_success', __('Thank you — your response has been saved.'));

        $guest->refresh();
        $this->assertSame(2, $guest->guests_count);

        $this->withSession([$sid => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'yes',
                'guests_count' => 4,
                'notes' => 'Two more guests confirmed later.',
            ])
            ->assertSessionHas('wedding_success', __('Your RSVP has been updated.'));

        $guest->refresh();
        $this->assertSame('yes', $guest->rsvp_status);
        $this->assertSame(4, $guest->guests_count);
        $this->assertSame('Two more guests confirmed later.', $guest->notes);
    }

    public function test_access_via_token_allows_rsvp_edit_in_same_session(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Token Editor',
            'token' => 'tok-session-edit',
        ]);

        $this->get('/w/'.$guest->token)->assertRedirect(route('wedding.show'));

        $this->post('/w/rsvp', [
            'rsvp_status' => 'yes',
            'guests_count' => 1,
        ])->assertSessionHas('wedding_success', __('Thank you — your response has been saved.'));

        $guest->refresh();
        $this->assertSame('yes', $guest->rsvp_status);

        $this->post('/w/rsvp', [
            'rsvp_status' => 'no',
        ])->assertSessionHas('wedding_success', __('Your RSVP has been updated.'));

        $guest->refresh();
        $this->assertSame('no', $guest->rsvp_status);
        $this->assertNull($guest->guests_count);
    }

    public function test_rsvp_no_clears_guests_count(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Declines',
            'token' => 'tok-no',
            'rsvp_status' => 'yes',
            'guests_count' => 3,
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'no',
            ])
            ->assertRedirect(route('wedding.show'))
            ->assertSessionHas('wedding_success');

        $guest->refresh();
        $this->assertSame('no', $guest->rsvp_status);
        $this->assertNull($guest->guests_count);
    }

    public function test_rsvp_validation_requires_guests_count_when_yes(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Needs count',
            'token' => 'tok-count',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'yes',
            ])
            ->assertSessionHasErrors('guests_count');
    }

    public function test_rsvp_validation_rejects_invalid_status(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Bad status',
            'token' => 'tok-bad',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'maybe',
                'guests_count' => 1,
            ])
            ->assertSessionHasErrors('rsvp_status');
    }

    public function test_rsvp_validation_rejects_guests_count_above_max(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Too many',
            'token' => 'tok-max',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'yes',
                'guests_count' => 51,
            ])
            ->assertSessionHasErrors('guests_count');
    }

    public function test_rsvp_notes_free_text_persist_and_show_on_page(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Notes Guest',
            'token' => 'tok-notes',
        ]);

        $detail = 'Nut allergy — please offer a vegan main.';

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'yes',
                'guests_count' => 1,
                'notes' => $detail,
            ])
            ->assertRedirect(route('wedding.show'))
            ->assertSessionHas('wedding_success');

        $guest->refresh();
        $this->assertSame($detail, $guest->notes);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->get('/w')
            ->assertOk()
            ->assertSee('Nut allergy', false);
    }

    public function test_rsvp_notes_validation_rejects_over_max_length(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Long notes',
            'token' => 'tok-long',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'yes',
                'guests_count' => 1,
                'notes' => str_repeat('a', 2001),
            ])
            ->assertSessionHasErrors('notes');
    }

    public function test_rsvp_empty_notes_trims_to_null_in_database(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Clear notes',
            'token' => 'tok-clear',
            'notes' => 'Old note',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'no',
                'notes' => "   \n  ",
            ])
            ->assertRedirect(route('wedding.show'));

        $guest->refresh();
        $this->assertNull($guest->notes);
    }

    public function test_rsvp_open_without_token_requires_name(): void
    {
        $this->post('/w/rsvp', [
            'rsvp_status' => 'yes',
            'guests_count' => 1,
        ])
            ->assertSessionHasErrors('name');
    }

    public function test_rsvp_open_without_token_creates_guest(): void
    {
        $this->post('/w/rsvp', [
            'name' => 'Walk-in Guest',
            'email' => 'walkin@example.test',
            'rsvp_status' => 'yes',
            'guests_count' => 2,
            'notes' => 'Table near exit',
        ])
            ->assertRedirect(route('wedding.show'))
            ->assertSessionHas('wedding_success', __('Thank you — your response has been saved.'));

        $guest = Guest::query()->where('name', 'Walk-in Guest')->first();
        $this->assertNotNull($guest);
        $this->assertSame('walkin@example.test', $guest->email);
        $this->assertSame('yes', $guest->rsvp_status);
        $this->assertSame(2, $guest->guests_count);
        $this->assertSame('Table near exit', $guest->notes);
        $this->assertNotEmpty($guest->token);
    }

    public function test_rsvp_sends_admin_notification_when_configured_us24(): void
    {
        Config::set('wedding.rsvp.notify_admin_email', 'admin-notify@example.test');
        Config::set('wedding.rsvp.send_confirmation_email', false);

        $guest = Guest::query()->create([
            'name' => 'Notified Guest',
            'email' => null,
            'token' => 'tok-us24',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'no',
            ])
            ->assertSessionHas('wedding_success');

        Mail::assertSent(RsvpAdminNotificationMail::class, function (RsvpAdminNotificationMail $mail) use ($guest): bool {
            return $mail->guest->is($guest) && $mail->isUpdate === false;
        });
    }

    public function test_rsvp_admin_notification_marks_update_us24(): void
    {
        Config::set('wedding.rsvp.notify_admin_email', 'admin@example.test');
        Config::set('wedding.rsvp.send_confirmation_email', false);

        $guest = Guest::query()->create([
            'name' => 'Editor Notify',
            'token' => 'tok-us24b',
            'rsvp_status' => 'yes',
            'guests_count' => 1,
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'no',
            ]);

        Mail::assertSent(RsvpAdminNotificationMail::class, function (RsvpAdminNotificationMail $mail): bool {
            return $mail->isUpdate === true;
        });
    }

    public function test_rsvp_skips_invalid_admin_notify_email_us24(): void
    {
        Config::set('wedding.rsvp.notify_admin_email', 'not-an-email');
        Config::set('wedding.rsvp.send_confirmation_email', false);

        $guest = Guest::query()->create([
            'name' => 'X',
            'token' => 'tok-us24c',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'yes',
                'guests_count' => 1,
            ]);

        Mail::assertNothingSent();
    }

    public function test_gallery_route_is_separate_from_wedding(): void
    {
        $this->get('/gallery')->assertOk();
        $this->get('/w')->assertOk();
    }
}
