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

    public function test_guest_list_has_edit_link_per_row(): void
    {
        $this->configureAdminPassword();

        $guest = Guest::query()->create([
            'name' => 'Row Actions',
            'rsvp_status' => null,
        ]);

        $this->post(route('admin.login'), ['password' => 'secret']);

        $this->get(route('admin.guests.index'))
            ->assertOk()
            ->assertSee(route('admin.guests.edit', ['guest' => $guest, 'rsvp' => 'all']), false);
    }

    public function test_admin_can_update_guest(): void
    {
        $this->configureAdminPassword();

        $guest = Guest::query()->create([
            'name' => 'Bob RSVP',
            'email' => 'bob@example.com',
            'rsvp_status' => 'yes',
            'guests_count' => 3,
            'notes' => 'Vegetarian',
            'rsvp_reminder_sent_at' => now(),
        ]);

        $this->post(route('admin.login'), ['password' => 'secret']);

        $this->put(route('admin.guests.update', $guest), [
            'name' => 'Bob RSVP',
            'email' => 'bob@example.com',
            'token' => $guest->token,
            'rsvp_status' => '',
            'guests_count' => null,
            'notes' => null,
            'return_rsvp' => 'yes',
        ])
            ->assertRedirect(route('admin.guests.index', ['rsvp' => 'yes']))
            ->assertSessionHas('status');

        $guest->refresh();
        $this->assertNull($guest->rsvp_status);
        $this->assertNull($guest->guests_count);
        $this->assertNull($guest->notes);
        $this->assertNull($guest->rsvp_reminder_sent_at);
    }

    public function test_admin_can_delete_guest(): void
    {
        $this->configureAdminPassword();

        $guest = Guest::query()->create([
            'name' => 'To Delete',
            'rsvp_status' => 'no',
        ]);

        $this->post(route('admin.login'), ['password' => 'secret']);

        $this->from(route('admin.guests.index'))
            ->delete(route('admin.guests.destroy', ['guest' => $guest, 'rsvp' => 'all']))
            ->assertRedirect(route('admin.guests.index', ['rsvp' => 'all']))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('guests', ['id' => $guest->id]);
    }

    public function test_destroy_guest_requires_authentication(): void
    {
        $this->configureAdminPassword();

        $guest = Guest::query()->create([
            'name' => 'Secret',
            'rsvp_status' => 'no',
        ]);

        $this->delete(route('admin.guests.destroy', $guest))
            ->assertRedirect(route('admin.login'));

        $this->assertDatabaseHas('guests', ['id' => $guest->id]);
    }
}
