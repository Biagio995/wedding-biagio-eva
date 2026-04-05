<?php

namespace Tests\Feature;

use App\Models\Photo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\TestCase;

class AdminPhotoModerationTest extends TestCase
{
    use RefreshDatabase;

    private function configureAdminPassword(string $plain = 'secret'): void
    {
        Config::set('wedding.admin.password_hash', bcrypt($plain));
    }

    public function test_photo_moderation_requires_authentication_us21(): void
    {
        $this->configureAdminPassword();

        $this->get(route('admin.photos.index'))
            ->assertRedirect(route('admin.login'));

        $photo = Photo::query()->create([
            'guest_id' => null,
            'file_path' => 'gallery/x.jpg',
            'original_filename' => 'x.jpg',
            'approved' => false,
        ]);

        $this->post(route('admin.photos.approve', ['photo' => $photo->id]))
            ->assertRedirect(route('admin.login'));

        $this->delete(route('admin.photos.destroy', ['photo' => $photo->id]))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_approve_photo_us21(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('gallery/x.jpg', 'fake-image');

        $photo = Photo::query()->create([
            'guest_id' => null,
            'file_path' => 'gallery/x.jpg',
            'original_filename' => 'x.jpg',
            'approved' => false,
        ]);

        $this->configureAdminPassword();
        $this->post(route('admin.login'), ['password' => 'secret']);

        $this->post(route('admin.photos.approve', ['photo' => $photo->id]))
            ->assertRedirect();

        $photo->refresh();
        $this->assertTrue($photo->approved);
    }

    public function test_moderation_page_lists_pending_photos_us21(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('gallery/a.jpg', 'a');
        Storage::disk('public')->put('gallery/b.jpg', 'b');

        Photo::query()->create([
            'guest_id' => null,
            'file_path' => 'gallery/a.jpg',
            'original_filename' => 'a.jpg',
            'approved' => false,
        ]);

        $this->configureAdminPassword();
        $this->post(route('admin.login'), ['password' => 'secret']);

        $this->get(route('admin.photos.index', ['status' => 'pending']))
            ->assertOk()
            ->assertSee(__('Photo moderation'), false)
            ->assertSee(__('Approve'), false);
    }

    public function test_admin_can_delete_photo_and_storage_file_us22(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('gallery/remove.jpg', 'image-bytes');

        $photo = Photo::query()->create([
            'guest_id' => null,
            'file_path' => 'gallery/remove.jpg',
            'original_filename' => 'remove.jpg',
            'approved' => true,
        ]);

        $this->configureAdminPassword();
        $this->post(route('admin.login'), ['password' => 'secret']);

        $this->delete(route('admin.photos.destroy', ['photo' => $photo->id]))
            ->assertRedirect();

        $this->assertDatabaseMissing('photos', ['id' => $photo->id]);
        Storage::disk('public')->assertMissing('gallery/remove.jpg');
    }

    public function test_photo_archive_requires_authentication_us23(): void
    {
        $this->configureAdminPassword();

        $this->get(route('admin.photos.archive'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_download_all_photos_as_zip_us23(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('gallery/a.jpg', 'aaa');
        Storage::disk('public')->put('gallery/b.jpg', 'bbb');

        Photo::query()->create([
            'guest_id' => null,
            'file_path' => 'gallery/a.jpg',
            'original_filename' => 'a.jpg',
            'approved' => false,
        ]);
        Photo::query()->create([
            'guest_id' => null,
            'file_path' => 'gallery/b.jpg',
            'original_filename' => 'b.jpg',
            'approved' => true,
        ]);

        $this->configureAdminPassword();
        $this->post(route('admin.login'), ['password' => 'secret']);

        $response = $this->get(route('admin.photos.archive'));
        $response->assertOk();
        $base = $response->baseResponse;
        $this->assertInstanceOf(BinaryFileResponse::class, $base);
        $path = $base->getFile()->getPathname();
        $this->assertStringStartsWith("PK\x03\x04", (string) file_get_contents($path));
    }

    public function test_archive_redirects_when_no_photos_us23(): void
    {
        $this->configureAdminPassword();
        $this->post(route('admin.login'), ['password' => 'secret']);

        $this->get(route('admin.photos.archive'))
            ->assertRedirect(route('admin.photos.index'))
            ->assertSessionHas('error');
    }
}
