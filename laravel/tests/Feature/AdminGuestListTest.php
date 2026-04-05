<?php

namespace Tests\Feature;

use App\Models\Guest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AdminGuestListTest extends TestCase
{
    use RefreshDatabase;

    private function configureAdminPassword(string $plain = 'secret'): void
    {
        Config::set('wedding.admin.password_hash', bcrypt($plain));
    }

    public function test_guest_list_requires_authentication_us20(): void
    {
        $this->configureAdminPassword();

        $this->get(route('admin.guests.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_guest_list_shows_rsvp_status_us20(): void
    {
        $this->configureAdminPassword();

        Guest::query()->create([
            'name' => 'Alice Yes',
            'email' => 'a@example.com',
            'rsvp_status' => 'yes',
            'guests_count' => 2,
        ]);
        Guest::query()->create([
            'name' => 'Carol Pending',
            'email' => null,
            'rsvp_status' => null,
            'guests_count' => null,
        ]);

        $this->post(route('admin.login'), ['password' => 'secret']);

        $this->get(route('admin.guests.index'))
            ->assertOk()
            ->assertSee('Alice Yes', false)
            ->assertSee('Carol Pending', false)
            ->assertSee(__('Guest list'), false);
    }

    public function test_guest_list_filters_by_rsvp_us20(): void
    {
        $this->configureAdminPassword();

        Guest::query()->create([
            'name' => 'Only Yes',
            'email' => null,
            'rsvp_status' => 'yes',
            'guests_count' => 1,
        ]);
        Guest::query()->create([
            'name' => 'Only Pending',
            'email' => null,
            'rsvp_status' => null,
            'guests_count' => null,
        ]);

        $this->post(route('admin.login'), ['password' => 'secret']);

        $this->get(route('admin.guests.index', ['rsvp' => 'pending']))
            ->assertOk()
            ->assertSee('Only Pending', false)
            ->assertDontSee('Only Yes', false);
    }
}
