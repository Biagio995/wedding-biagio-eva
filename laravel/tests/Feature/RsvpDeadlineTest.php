<?php

namespace Tests\Feature;

use App\Http\Controllers\WeddingController;
use App\Models\Guest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class RsvpDeadlineTest extends TestCase
{
    use RefreshDatabase;

    public function test_attend_page_hides_deadline_notice_when_not_configured(): void
    {
        Config::set('wedding.rsvp.deadline', null);

        $response = $this->get(route('wedding.attend'))->assertOk();

        $response->assertDontSee('Please respond by', false);
        $response->assertDontSee('The RSVP deadline has passed.', false);
    }

    public function test_attend_page_shows_upcoming_deadline_when_configured(): void
    {
        Carbon::setTestNow('2027-05-01 12:00:00');
        Config::set('wedding.rsvp.deadline', '2027-06-01');

        $response = $this->get(route('wedding.attend'))->assertOk();

        $response->assertSee('Please respond by', false);
        $response->assertDontSee('The RSVP deadline has passed.', false);
    }

    public function test_attend_page_soft_closes_after_deadline(): void
    {
        Carbon::setTestNow('2027-06-10 12:00:00');
        Config::set('wedding.rsvp.deadline', '2027-06-01');

        $response = $this->get(route('wedding.attend'))->assertOk();

        $response->assertSee('The RSVP deadline has passed.', false);
        $response->assertSee('You can still reply', false);
    }

    public function test_rsvp_still_accepts_submissions_after_deadline(): void
    {
        Carbon::setTestNow('2027-06-10 12:00:00');
        Config::set('wedding.rsvp.deadline', '2027-06-01');

        $guest = Guest::query()->create([
            'name' => 'Late Guest',
            'token' => 'tok-late',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'yes',
                'guests_count' => 1,
            ])
            ->assertRedirect(route('wedding.attend'))
            ->assertSessionHas('wedding_success');

        $guest->refresh();
        $this->assertSame('yes', $guest->rsvp_status);
    }
}
