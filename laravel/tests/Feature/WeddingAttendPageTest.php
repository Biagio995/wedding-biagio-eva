<?php

namespace Tests\Feature;

use App\Http\Controllers\WeddingController;
use App\Models\Guest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class WeddingAttendPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_attend_page_renders_for_anonymous_visitors(): void
    {
        Config::set('wedding.admin.password_hash', null);

        $this->get(route('wedding.attend'))
            ->assertOk()
            ->assertSee('Will you attend?')
            ->assertSee('Your name');
    }

    public function test_attend_page_shows_personalised_form_for_recognised_guest(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Luca Attendee',
            'email' => 'luca@example.test',
            'token' => 'tok-attend',
            'rsvp_status' => 'yes',
            'guests_count' => 2,
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->get(route('wedding.attend'))
            ->assertOk()
            ->assertSee('Luca Attendee')
            ->assertSee('luca@example.test');
    }

    public function test_attend_page_renders_embedded_maps_when_configured(): void
    {
        Config::set('wedding.event.maps_embed_url', 'https://maps.google.test/reception-embed');
        Config::set('wedding.event.maps_church_embed_url', 'https://maps.google.test/church-embed');

        $response = $this->get(route('wedding.attend'))->assertOk();
        $response->assertSee('How to get there');
        $response->assertSee('https://maps.google.test/reception-embed', false);
        $response->assertSee('https://maps.google.test/church-embed', false);
    }

    public function test_home_page_no_longer_has_rsvp_form_but_links_to_attend(): void
    {
        Config::set('wedding.admin.password_hash', null);

        $response = $this->get('/w')->assertOk();
        $response->assertDontSee('id="wedding-rsvp-form"', false);
        $response->assertSee(route('wedding.attend'), false);
    }

    public function test_rsvp_submission_redirects_to_attend_page(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Redirect Test',
            'token' => 'tok-redir',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'yes',
                'guests_count' => 1,
            ])
            ->assertRedirect(route('wedding.attend'));
    }

    public function test_navbar_shows_attend_link(): void
    {
        $response = $this->get('/w')->assertOk();
        $response->assertSee('href="'.route('wedding.attend').'"', false);
    }
}
