<?php

namespace Tests\Feature;

use App\Http\Controllers\WeddingController;
use App\Models\Guest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_pages_load_without_laravel_user_account_us29(): void
    {
        $this->get('/')->assertOk();
        $this->get('/w')->assertOk();
        $this->get('/gallery')->assertOk();
        $this->get('/gallery/album')->assertOk();
    }

    public function test_wedding_page_renders_public_content_us29(): void
    {
        $this->get('/w')
            ->assertOk()
            ->assertSee('Countdown', false);
    }

    public function test_rsvp_saves_using_session_not_user_model_us29(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Token Guest',
            'token' => 'tok-us29-rsvp',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'yes',
                'guests_count' => 1,
                'notes' => null,
            ])
            ->assertRedirect(route('wedding.attend'));

        $guest->refresh();
        $this->assertSame('yes', $guest->rsvp_status);
    }
}
