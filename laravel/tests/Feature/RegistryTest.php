<?php

namespace Tests\Feature;

use App\Http\Controllers\WeddingController;
use App\Models\Guest;
use App\Models\RegistryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class RegistryTest extends TestCase
{
    use RefreshDatabase;

    public function test_registry_page_loads(): void
    {
        $this->get(route('registry.show'))
            ->assertOk()
            ->assertSee(__('Gift list'), false);
    }

    public function test_claim_requires_name(): void
    {
        $item = RegistryItem::query()->create([
            'title' => 'Bowl',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->post(route('registry.claim', $item), [])
            ->assertSessionHasErrors('name');
    }

    public function test_anyone_can_claim_without_guest_session(): void
    {
        $item = RegistryItem::query()->create([
            'title' => 'Lamp',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->post(route('registry.claim', $item), ['name' => 'Anna Rossi'])
            ->assertRedirect(route('registry.show'))
            ->assertSessionHas('registry_success');

        $item->refresh();
        $this->assertNull($item->claimed_by_guest_id);
        $this->assertNotNull($item->claimed_at);
        $this->assertSame('Anna Rossi', $item->claimed_by_name);
    }

    public function test_guest_can_claim_item(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Test Guest',
            'token' => 'token-one',
        ]);

        $item = RegistryItem::query()->create([
            'title' => 'Coffee machine',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post(route('registry.claim', $item), ['name' => 'Mario Bianchi'])
            ->assertRedirect(route('registry.show'));

        $item->refresh();
        $this->assertSame($guest->id, $item->claimed_by_guest_id);
        $this->assertSame('Mario Bianchi', $item->claimed_by_name);
    }

    public function test_second_guest_cannot_claim_reserved_item(): void
    {
        $g1 = Guest::query()->create(['name' => 'A', 'token' => 't-a']);
        $g2 = Guest::query()->create(['name' => 'B', 'token' => 't-b']);

        $item = RegistryItem::query()->create([
            'title' => 'Toaster',
            'sort_order' => 0,
            'is_active' => true,
            'claimed_by_guest_id' => $g1->id,
            'claimed_at' => now(),
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $g2->id])
            ->post(route('registry.claim', $item), ['name' => 'B'])
            ->assertRedirect(route('registry.show'))
            ->assertSessionHas('registry_error');
    }

    public function test_claimed_gift_hidden_from_other_guests_on_public_list(): void
    {
        $g1 = Guest::query()->create(['name' => 'Alice', 'token' => 't-a']);
        $g2 = Guest::query()->create(['name' => 'Bob', 'token' => 't-b']);

        RegistryItem::query()->create([
            'title' => 'Secret Toaster Model X',
            'sort_order' => 0,
            'is_active' => true,
            'claimed_by_guest_id' => $g1->id,
            'claimed_at' => now(),
            'claimed_by_name' => 'Alice',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $g2->id])
            ->get(route('registry.show'))
            ->assertOk()
            ->assertDontSee('Secret Toaster Model X', false);
    }

    public function test_claimed_gift_hidden_from_claimer_on_public_list(): void
    {
        $g1 = Guest::query()->create(['name' => 'Alice', 'token' => 't-a']);

        RegistryItem::query()->create([
            'title' => 'Hidden From Public List',
            'sort_order' => 0,
            'is_active' => true,
            'claimed_by_guest_id' => $g1->id,
            'claimed_at' => now(),
            'claimed_by_name' => 'Alice',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $g1->id])
            ->get(route('registry.show'))
            ->assertOk()
            ->assertDontSee('Hidden From Public List', false)
            ->assertDontSee('Your reservations', false)
            ->assertDontSee('Le tue prenotazioni', false);
    }

    public function test_guest_can_attach_an_optional_claim_message(): void
    {
        $item = RegistryItem::query()->create([
            'title' => 'Espresso Cups',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->post(route('registry.claim', $item), [
            'name' => 'Giulia',
            'claim_message' => 'I saw this and instantly thought of your kitchen. Lots of love!',
        ])->assertRedirect(route('registry.show'));

        $item->refresh();
        $this->assertSame('Giulia', $item->claimed_by_name);
        $this->assertSame(
            'I saw this and instantly thought of your kitchen. Lots of love!',
            $item->claim_message,
        );
    }

    public function test_claim_message_is_optional_and_trims_to_null_when_blank(): void
    {
        $item = RegistryItem::query()->create([
            'title' => 'Soft Blanket',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->post(route('registry.claim', $item), [
            'name' => 'Pietro',
            'claim_message' => "   \n  ",
        ])->assertRedirect(route('registry.show'));

        $item->refresh();
        $this->assertSame('Pietro', $item->claimed_by_name);
        $this->assertNull($item->claim_message);
    }

    public function test_claim_message_rejects_over_max_length(): void
    {
        $item = RegistryItem::query()->create([
            'title' => 'Napkins',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->post(route('registry.claim', $item), [
            'name' => 'Long Typer',
            'claim_message' => str_repeat('a', 1001),
        ])->assertSessionHasErrors('claim_message');
    }

    public function test_admin_can_manage_registry_items(): void
    {
        Config::set('wedding.admin.password_hash', bcrypt('secret'));

        $this->post(route('admin.login'), ['password' => 'secret']);

        $this->post(route('admin.registry.store'), [
            'title' => 'Vase',
            'description' => 'Ceramic',
            'sort_order' => 5,
            'is_active' => '1',
        ])->assertRedirect(route('admin.registry.index'));

        $this->assertDatabaseHas('registry_items', [
            'title' => 'Vase',
            'sort_order' => 5,
        ]);
    }
}
