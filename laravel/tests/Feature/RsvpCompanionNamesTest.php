<?php

namespace Tests\Feature;

use App\Http\Controllers\WeddingController;
use App\Models\Guest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RsvpCompanionNamesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_companion_names_saved_when_attending_with_multiple_guests(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Main Guest',
            'token' => 'tok-companions',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'yes',
                'guests_count' => 3,
                'companion_names' => "Alice Red\nBob Blue",
            ])
            ->assertRedirect(route('wedding.show'))
            ->assertSessionHas('wedding_success');

        $guest->refresh();
        $this->assertSame(['Alice Red', 'Bob Blue'], $guest->companion_names);
    }

    public function test_companion_names_array_input_is_normalised(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Array Guest',
            'token' => 'tok-arr',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'yes',
                'guests_count' => 4,
                'companion_names' => ['  Alice  ', '', 'Bob', 'Alice'],
            ])
            ->assertRedirect(route('wedding.show'));

        $guest->refresh();
        $this->assertSame(['Alice', 'Bob'], $guest->companion_names);
    }

    public function test_companion_names_cleared_when_declining(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Will Decline',
            'token' => 'tok-decline-comp',
            'rsvp_status' => 'yes',
            'guests_count' => 2,
            'companion_names' => ['Previous'],
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'no',
                'companion_names' => "Should be ignored",
            ])
            ->assertRedirect(route('wedding.show'));

        $guest->refresh();
        $this->assertNull($guest->companion_names);
    }

    public function test_companion_names_rejected_when_exceeding_guests_count_minus_one(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Over Limit',
            'token' => 'tok-over',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'yes',
                'guests_count' => 2,
                'companion_names' => "Alice\nBob\nCharlie",
            ])
            ->assertSessionHasErrors('companion_names');

        $guest->refresh();
        $this->assertNull($guest->companion_names);
    }

    public function test_single_attendee_cannot_list_companions(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Alone',
            'token' => 'tok-alone',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post('/w/rsvp', [
                'rsvp_status' => 'yes',
                'guests_count' => 1,
                'companion_names' => 'Partner',
            ])
            ->assertSessionHasErrors('companion_names');
    }
}
