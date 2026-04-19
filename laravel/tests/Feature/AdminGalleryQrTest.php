<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AdminGalleryQrTest extends TestCase
{
    use RefreshDatabase;

    private function loginAsAdmin(): void
    {
        Config::set('wedding.admin.password_hash', bcrypt('secret'));
        $this->post(route('admin.login'), ['password' => 'secret']);
    }

    public function test_route_requires_admin_authentication(): void
    {
        Config::set('wedding.admin.password_hash', bcrypt('secret'));

        $this->get(route('admin.gallery.qr.card'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_see_printable_card_with_gallery_qr(): void
    {
        $this->loginAsAdmin();

        $response = $this->get(route('admin.gallery.qr.card'))->assertOk();

        $response->assertSee('Share your photos with us', false);
        /** Embedded QR rendered as data URI so the card prints standalone. */
        $response->assertSee('data:image/png;base64,', false);
    }
}
