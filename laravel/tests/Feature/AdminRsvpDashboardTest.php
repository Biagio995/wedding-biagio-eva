<?php

namespace Tests\Feature;

use App\Models\Guest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AdminRsvpDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function configureAdminPassword(string $plain = 'secret'): void
    {
        Config::set('wedding.admin.password_hash', bcrypt($plain));
    }

    public function test_rsvp_dashboard_requires_authentication_us19(): void
    {
        $this->configureAdminPassword();

        $this->get(route('admin.rsvp.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_rsvp_dashboard_shows_statistics_us19(): void
    {
        $this->configureAdminPassword();

        Guest::query()->create([
            'name' => 'Alice Yes',
            'email' => 'a@example.com',
            'rsvp_status' => 'yes',
            'guests_count' => 3,
        ]);
        Guest::query()->create([
            'name' => 'Bob No',
            'email' => null,
            'rsvp_status' => 'no',
            'guests_count' => null,
        ]);
        Guest::query()->create([
            'name' => 'Carol Pending',
            'email' => null,
            'rsvp_status' => null,
            'guests_count' => null,
        ]);

        $this->post(route('admin.login'), ['password' => 'secret']);

        $this->get(route('admin.rsvp.dashboard'))
            ->assertOk()
            ->assertSee(__('RSVP dashboard'), false)
            ->assertSee(__('People attending'), false)
            ->assertSee(__('Awaiting reply'), false)
            ->assertSee(__('View guest list with RSVP status'), false);
    }
}
