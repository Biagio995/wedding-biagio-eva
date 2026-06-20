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

    public function test_approve_uploads_photo_to_google_drive_when_configured(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('gallery/x.jpg', 'fake-image');

        Config::set('gallery.google_drive.apps_script_url', 'https://script.google.com/macros/s/test/exec');
        Config::set('gallery.google_drive.secret', 'apps-script-secret');
        Config::set('gallery.google_drive.folder_id', 'folder-id');

        \Illuminate\Support\Facades\Http::fake([
            'script.google.com/macros/s/test/exec' => \Illuminate\Support\Facades\Http::response([
                'ok' => true,
                'id' => 'drive-file-123',
            ]),
        ]);

        $photo = Photo::query()->create([
            'guest_id' => null,
            'file_path' => 'gallery/x.jpg',
            'original_filename' => 'x.jpg',
            'approved' => false,
        ]);

        $this->configureAdminPassword();
        $this->post(route('admin.login'), ['password' => 'secret']);

        $this->post(route('admin.photos.approve', ['photo' => $photo->id]))
            ->assertRedirect()
            ->assertSessionHas('status', __('Photo approved and uploaded to Google Drive.'));

        $photo->refresh();
        $this->assertTrue($photo->approved);
        $this->assertSame('drive-file-123', $photo->google_drive_file_id);
        $this->assertNotNull($photo->synced_to_google_drive_at);
    }

    public function test_moderation_page_lists_pending_photos_us21(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('gallery/a.jpg', 'a');
        Storage::disk('public')->put('gallery/b.jpg', 'b');

        $photo = Photo::query()->create([
            'guest_id' => null,
            'file_path' => 'gallery/a.jpg',
            'original_filename' => 'a.jpg',
            'approved' => false,
        ]);

        Photo::query()->create([
            'guest_id' => null,
            'file_path' => 'gallery/b.jpg',
            'original_filename' => 'b.jpg',
            'approved' => false,
        ]);

        $this->configureAdminPassword();
        $this->post(route('admin.login'), ['password' => 'secret']);

        $this->get(route('admin.photos.index', ['status' => 'pending']))
            ->assertOk()
            ->assertSee(__('Photo moderation'), false)
            ->assertSee(__('Approve'), false)
            ->assertSee(route('admin.photos.show', ['photo' => $photo->id]), false);
    }

    public function test_admin_can_preview_photo_file_us21(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('gallery/x.jpg', 'preview-bytes');

        $photo = Photo::query()->create([
            'guest_id' => null,
            'file_path' => 'gallery/x.jpg',
            'original_filename' => 'x.jpg',
            'approved' => false,
        ]);

        $this->configureAdminPassword();
        $this->post(route('admin.login'), ['password' => 'secret']);

        $response = $this->get(route('admin.photos.show', ['photo' => $photo->id]));
        $response->assertOk();
        $this->assertSame('preview-bytes', $response->streamedContent());
    }

    public function test_admin_photo_preview_requires_authentication_us21(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('gallery/x.jpg', 'x');

        $photo = Photo::query()->create([
            'guest_id' => null,
            'file_path' => 'gallery/x.jpg',
            'original_filename' => 'x.jpg',
            'approved' => false,
        ]);

        $this->configureAdminPassword();

        $this->get(route('admin.photos.show', ['photo' => $photo->id]))
            ->assertRedirect(route('admin.login'));
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

    public function test_admin_delete_removes_photo_from_google_drive_when_configured(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('gallery/remove.jpg', 'image-bytes');

        Config::set('gallery.google_drive.apps_script_url', 'https://script.google.com/macros/s/test/exec');
        Config::set('gallery.google_drive.secret', 'apps-script-secret');

        \Illuminate\Support\Facades\Http::fake([
            'script.google.com/macros/s/test/exec' => \Illuminate\Support\Facades\Http::response([
                'ok' => true,
            ]),
        ]);

        $photo = Photo::query()->create([
            'guest_id' => null,
            'file_path' => 'gallery/remove.jpg',
            'original_filename' => 'remove.jpg',
            'approved' => true,
            'google_drive_file_id' => 'drive-file-123',
        ]);

        $this->configureAdminPassword();
        $this->post(route('admin.login'), ['password' => 'secret']);

        $this->delete(route('admin.photos.destroy', ['photo' => $photo->id]))
            ->assertRedirect()
            ->assertSessionHas('status', __('Photo removed.'));

        \Illuminate\Support\Facades\Http::assertSent(function (\Illuminate\Http\Client\Request $request): bool {
            $data = $request->data();

            return $request->url() === 'https://script.google.com/macros/s/test/exec'
                && ($data['action'] ?? null) === 'delete'
                && ($data['fileId'] ?? null) === 'drive-file-123';
        });
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
