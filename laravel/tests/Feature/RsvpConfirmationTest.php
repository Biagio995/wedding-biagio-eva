<?php

namespace Tests\Feature;

use App\Http\Controllers\WeddingController;
use App\Models\Guest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RsvpConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_attend_page_shows_dedicated_thank_you_card_after_rsvp(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Happy Guest',
            'email' => 'happy@example.test',
            'token' => 'tok-conf',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->followingRedirects()
            ->post('/w/rsvp', [
                'rsvp_status' => 'yes',
                'guests_count' => 2,
                'companion_names' => ['Plus One'],
            ])
            ->assertOk()
            ->assertSee('Thank you', false)
            ->assertSee('Happy Guest', false)
            ->assertSee('Plus One', false)
            ->assertSee('Add to calendar', false)
            ->assertSee('Change my answer', false);
    }

    public function test_attend_page_shows_update_wording_for_edits(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Editor',
            'token' => 'tok-edit',
            'rsvp_status' => 'yes',
            'guests_count' => 1,
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->followingRedirects()
            ->post('/w/rsvp', [
                'rsvp_status' => 'no',
            ])
            ->assertOk()
            ->assertSee('Your RSVP has been updated.', false);
    }

    public function test_attend_page_without_submission_does_not_show_confirmation_card(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Idle',
            'token' => 'tok-idle',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->get(route('wedding.attend'))
            ->assertOk()
            ->assertDontSee('id="rsvp-confirmation"', false);
    }
}
