<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\SeatingTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AdminSeatingChartTest extends TestCase
{
    use RefreshDatabase;

    private function loginAsAdmin(): void
    {
        Config::set('wedding.admin.password_hash', bcrypt('secret'));
        $this->post(route('admin.login'), ['password' => 'secret']);
    }

    public function test_seating_index_requires_admin(): void
    {
        Config::set('wedding.admin.password_hash', bcrypt('secret'));
        $this->get(route('admin.seating.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_create_table(): void
    {
        $this->loginAsAdmin();

        $this->post(route('admin.seating.store'), [
            'label' => 'Family',
            'capacity' => 8,
        ])->assertRedirect(route('admin.seating.index'));

        $this->assertDatabaseHas('seating_tables', [
            'label' => 'Family',
            'capacity' => 8,
        ]);
    }

    public function test_admin_can_assign_and_unassign_guest(): void
    {
        $this->loginAsAdmin();

        $table = SeatingTable::query()->create(['label' => 'Main']);
        $guest = Guest::query()->create(['name' => 'Assignable', 'token' => 'tok-seat']);

        $this->post(route('admin.seating.assign', $table), ['guest_id' => $guest->id])
            ->assertRedirect(route('admin.seating.index'));
        $this->assertSame($table->id, $guest->fresh()->seating_table_id);

        $this->post(route('admin.seating.unassign', $guest))
            ->assertRedirect(route('admin.seating.index'));
        $this->assertNull($guest->fresh()->seating_table_id);
    }

    public function test_delete_table_unassigns_guests(): void
    {
        $this->loginAsAdmin();

        $table = SeatingTable::query()->create(['label' => 'To delete']);
        $guest = Guest::query()->create([
            'name' => 'Seat Guest',
            'token' => 'tok-del-seat',
            'seating_table_id' => $table->id,
        ]);

        $this->delete(route('admin.seating.destroy', $table))
            ->assertRedirect(route('admin.seating.index'));

        $this->assertDatabaseMissing('seating_tables', ['id' => $table->id]);
        $this->assertNull($guest->fresh()->seating_table_id);
    }

    public function test_occupied_seats_counts_main_guest_and_companions(): void
    {
        $table = SeatingTable::query()->create(['label' => 'Counted']);
        Guest::query()->create([
            'name' => 'A',
            'token' => 'tok-a',
            'rsvp_status' => 'yes',
            'guests_count' => 3,
            'seating_table_id' => $table->id,
        ]);
        Guest::query()->create([
            'name' => 'B',
            'token' => 'tok-b',
            'rsvp_status' => 'no',
            'seating_table_id' => $table->id,
        ]);
        Guest::query()->create([
            'name' => 'Pending',
            'token' => 'tok-p',
            'seating_table_id' => $table->id,
        ]);

        $table->load('guests');
        $this->assertSame(4, $table->occupiedSeats());
    }
}
