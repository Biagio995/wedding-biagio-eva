<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Photo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_page_loads(): void
    {
        $response = $this->get('/gallery');

        $response->assertOk();
        $response->assertSee('Wedding gallery', false);
        $response->assertSee('multi-select', false);
    }

    public function test_token_recognizes_guest_and_persists_in_session(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Ada Lovelace',
            'token' => 'test-token-ada',
        ]);

        $response = $this->get('/gallery?token=test-token-ada');

        $response->assertOk();
        $response->assertSee('Ada Lovelace', false);

        $this->assertEquals($guest->id, session('gallery_guest_id'));
    }

    public function test_upload_redirects_and_stores_photos_with_guest(): void
    {
        Storage::fake('public');

        $guest = Guest::query()->create([
            'name' => 'Test',
            'token' => 'tok',
        ]);

        $this->withSession(['gallery_guest_id' => $guest->id])
            ->post('/gallery', [
                'photos' => [
                    UploadedFile::fake()->image('party.jpg', 600, 400),
                ],
            ])
            ->assertRedirect(route('gallery.show'));

        $this->assertDatabaseHas('photos', [
            'guest_id' => $guest->id,
        ]);
    }

    public function test_upload_multiple_stores_each_photo_us10(): void
    {
        Storage::fake('public');

        $guest = Guest::query()->create([
            'name' => 'Multi',
            'token' => 'tok-multi',
        ]);

        $this->withSession(['gallery_guest_id' => $guest->id])
            ->post('/gallery', [
                'photos' => [
                    UploadedFile::fake()->image('a.jpg', 100, 100),
                    UploadedFile::fake()->image('b.jpg', 100, 100),
                    UploadedFile::fake()->image('c.jpg', 100, 100),
                ],
            ])
            ->assertRedirect(route('gallery.show'));

        $this->assertSame(3, Photo::query()->where('guest_id', $guest->id)->count());
    }

    public function test_photo_associates_guest_id_when_known_us12(): void
    {
        Storage::fake('public');

        $guest = Guest::query()->create([
            'name' => 'Traceable',
            'token' => 'tok-us12',
        ]);

        $this->withSession(['gallery_guest_id' => $guest->id])
            ->post('/gallery', [
                'photos' => [UploadedFile::fake()->image('one.jpg', 400, 300)],
            ])
            ->assertRedirect(route('gallery.show'));

        $photo = Photo::query()->first();
        $this->assertNotNull($photo);
        $this->assertSame($guest->id, $photo->guest_id);
        $this->assertTrue($photo->guest()->is($guest));
    }

    public function test_photo_guest_id_null_when_upload_anonymous_us12(): void
    {
        Storage::fake('public');

        $this->post('/gallery', [
            'photos' => [UploadedFile::fake()->image('anon.jpg', 200, 200)],
        ])->assertRedirect(route('gallery.show'));

        $photo = Photo::query()->first();
        $this->assertNotNull($photo);
        $this->assertNull($photo->guest_id);
    }

    public function test_upload_ajax_returns_json_redirect_us10(): void
    {
        Storage::fake('public');

        $guest = Guest::query()->create([
            'name' => 'Ajax',
            'token' => 'tok-ajax',
        ]);

        $response = $this->withSession(['gallery_guest_id' => $guest->id])
            ->withHeaders([
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ])
            ->post('/gallery', [
                'photos' => [
                    UploadedFile::fake()->image('x.jpg', 200, 200),
                ],
            ]);

        $response->assertOk();
        $response->assertJson([
            'redirect' => route('gallery.show'),
        ]);

        $this->assertSame(1, Photo::query()->where('guest_id', $guest->id)->count());
    }

    public function test_upload_validates_photo_mimes(): void
    {
        Storage::fake('public');

        $guest = Guest::query()->create([
            'name' => 'Test',
            'token' => 'tok2',
        ]);

        $this->withSession(['gallery_guest_id' => $guest->id])
            ->post('/gallery', [
                'photos' => [
                    UploadedFile::fake()->create('notes.pdf', 120, 'application/pdf'),
                ],
            ])
            ->assertSessionHasErrors('photos.0');

        $this->assertDatabaseCount('photos', 0);
    }

    public function test_upload_rejects_non_image_disguised_as_jpeg_us26(): void
    {
        Storage::fake('public');

        $guest = Guest::query()->create([
            'name' => 'Secure',
            'token' => 'tok-sec',
        ]);

        $this->withSession(['gallery_guest_id' => $guest->id])
            ->post('/gallery', [
                'photos' => [
                    UploadedFile::fake()->createWithContent('vacation.jpg', '<?php echo "x";'),
                ],
            ])
            ->assertSessionHasErrors('photos.0');

        $this->assertDatabaseCount('photos', 0);
    }

    public function test_upload_rate_limited_per_ip_us27(): void
    {
        Storage::fake('public');
        Config::set('gallery.upload.rate_limit.max_per_minute', 2);

        $guest = Guest::query()->create([
            'name' => 'Rate',
            'token' => 'tok-rate',
        ]);

        $session = ['gallery_guest_id' => $guest->id];

        $this->withSession($session)
            ->post('/gallery', [
                'photos' => [UploadedFile::fake()->image('a.jpg', 40, 40)],
            ])
            ->assertRedirect(route('gallery.show'));

        $this->withSession($session)
            ->post('/gallery', [
                'photos' => [UploadedFile::fake()->image('b.jpg', 40, 40)],
            ])
            ->assertRedirect(route('gallery.show'));

        $this->withSession($session)
            ->post('/gallery', [
                'photos' => [UploadedFile::fake()->image('c.jpg', 40, 40)],
            ])
            ->assertStatus(429);
    }

    public function test_upload_requires_at_least_one_photo(): void
    {
        $guest = Guest::query()->create([
            'name' => 'Test',
            'token' => 'tok3',
        ]);

        $this->withSession(['gallery_guest_id' => $guest->id])
            ->post('/gallery', [])
            ->assertSessionHasErrors('photos');
    }

    public function test_upload_rejects_more_than_twenty_files(): void
    {
        Storage::fake('public');

        $guest = Guest::query()->create([
            'name' => 'Many',
            'token' => 'tok-many',
        ]);

        $files = [];
        for ($i = 0; $i < 21; $i++) {
            $files[] = UploadedFile::fake()->image("p{$i}.jpg", 10, 10);
        }

        $this->withSession(['gallery_guest_id' => $guest->id])
            ->post('/gallery', ['photos' => $files])
            ->assertSessionHasErrors('photos');

        $this->assertDatabaseCount('photos', 0);
    }

    public function test_gallery_upload_route_redirects_with_query_string(): void
    {
        $this->get('/gallery/upload?token=abc')
            ->assertRedirect('/gallery?token=abc');
    }

    public function test_public_album_page_loads_us13(): void
    {
        $this->get(route('gallery.album'))
            ->assertOk()
            ->assertSee('Shared photos', false)
            ->assertSee('album-grid', false);
    }

    public function test_public_feed_json_pagination_us13(): void
    {
        Storage::fake('public');
        Config::set('gallery.public_feed.per_page', 2);
        Config::set('gallery.public_feed.only_approved', false);

        for ($i = 0; $i < 3; $i++) {
            $path = "gallery/p{$i}.jpg";
            Storage::disk('public')->put($path, 'fake-image');
            Photo::query()->create([
                'guest_id' => null,
                'file_path' => $path,
                'original_filename' => "p{$i}.jpg",
                'approved' => false,
            ]);
        }

        $first = $this->getJson(route('gallery.feed'));
        $first->assertOk();
        $first->assertJsonCount(2, 'data');
        $this->assertNotNull($first->json('next_page_url'));

        $second = $this->getJson($first->json('next_page_url'));
        $second->assertOk();
        $second->assertJsonCount(1, 'data');
        $this->assertNull($second->json('next_page_url'));
    }

    public function test_public_feed_only_approved_when_configured_us13(): void
    {
        Storage::fake('public');
        Config::set('gallery.public_feed.only_approved', true);
        Config::set('gallery.public_feed.per_page', 10);

        Storage::disk('public')->put('gallery/a.jpg', 'x');
        Photo::query()->create([
            'guest_id' => null,
            'file_path' => 'gallery/a.jpg',
            'original_filename' => 'a.jpg',
            'approved' => false,
        ]);

        $this->getJson(route('gallery.feed'))
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_public_feed_filters_by_date_us14(): void
    {
        Storage::fake('public');
        Config::set('gallery.public_feed.only_approved', false);
        Config::set('gallery.public_feed.per_page', 10);

        Storage::disk('public')->put('gallery/a.jpg', 'x');
        Storage::disk('public')->put('gallery/b.jpg', 'x');

        $this->travelTo(Carbon::parse('2026-04-05 14:00:00'));
        Photo::query()->create([
            'guest_id' => null,
            'file_path' => 'gallery/a.jpg',
            'original_filename' => 'a.jpg',
            'approved' => false,
        ]);

        $this->travelTo(Carbon::parse('2026-04-06 14:00:00'));
        Photo::query()->create([
            'guest_id' => null,
            'file_path' => 'gallery/b.jpg',
            'original_filename' => 'b.jpg',
            'approved' => false,
        ]);

        $this->travelBack();

        $this->getJson(route('gallery.feed', ['date' => '2026-04-05']))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.alt', 'a.jpg');
    }

    public function test_public_feed_next_page_retains_date_query_us14(): void
    {
        Storage::fake('public');
        Config::set('gallery.public_feed.only_approved', false);
        Config::set('gallery.public_feed.per_page', 2);

        for ($i = 0; $i < 3; $i++) {
            $path = "gallery/p{$i}.jpg";
            Storage::disk('public')->put($path, 'x');
            $this->travelTo(
                Carbon::parse('2026-04-05 10:00:00')->addMinutes($i),
            );
            Photo::query()->create([
                'guest_id' => null,
                'file_path' => $path,
                'original_filename' => "p{$i}.jpg",
                'approved' => false,
            ]);
        }
        $this->travelBack();

        $first = $this->getJson(route('gallery.feed', ['date' => '2026-04-05']));
        $first->assertOk();
        $first->assertJsonCount(2, 'data');
        $next = $first->json('next_page_url');
        $this->assertIsString($next);
        $this->assertStringContainsString('date=2026-04-05', $next);

        $second = $this->getJson($next);
        $second->assertOk();
        $second->assertJsonCount(1, 'data');
        $this->assertNull($second->json('next_page_url'));
    }

    public function test_public_feed_invalid_date_query_ignored_us14(): void
    {
        Storage::fake('public');
        Config::set('gallery.public_feed.only_approved', false);
        Config::set('gallery.public_feed.per_page', 10);

        Storage::disk('public')->put('gallery/a.jpg', 'x');
        $this->travelTo(Carbon::parse('2026-04-05 12:00:00'));
        Photo::query()->create([
            'guest_id' => null,
            'file_path' => 'gallery/a.jpg',
            'original_filename' => 'a.jpg',
            'approved' => false,
        ]);
        $this->travelBack();

        $this->getJson(route('gallery.feed', ['date' => 'not-a-date']))
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_public_album_ignores_date_query_us14(): void
    {
        $this->get(route('gallery.album', ['date' => '2026-01-01']))
            ->assertOk()
            ->assertSee('Shared photos', false)
            ->assertSee('album-grid', false);
    }

    public function test_public_feed_includes_download_url_us15(): void
    {
        Storage::fake('public');
        Config::set('gallery.public_feed.only_approved', false);
        Config::set('gallery.public_feed.per_page', 10);

        Storage::disk('public')->put('gallery/a.jpg', 'x');
        $photo = Photo::query()->create([
            'guest_id' => null,
            'file_path' => 'gallery/a.jpg',
            'original_filename' => 'a.jpg',
            'approved' => false,
        ]);

        $this->getJson(route('gallery.feed'))
            ->assertOk()
            ->assertJsonPath('data.0.download_url', route('gallery.photo.download', ['photo' => $photo->id]));
    }

    public function test_guest_can_download_public_photo_us15(): void
    {
        Storage::fake('public');
        Config::set('gallery.public_feed.only_approved', false);

        $content = 'fake-image-bytes';
        Storage::disk('public')->put('gallery/party.jpg', $content);
        $photo = Photo::query()->create([
            'guest_id' => null,
            'file_path' => 'gallery/party.jpg',
            'original_filename' => 'party.jpg',
            'approved' => false,
        ]);

        $response = $this->get(route('gallery.photo.download', ['photo' => $photo->id]));
        $response->assertOk();
        $response->assertDownload('party.jpg');
        $this->assertSame($content, $response->streamedContent());
    }

    public function test_download_not_found_when_only_approved_and_photo_pending_us15(): void
    {
        Storage::fake('public');
        Config::set('gallery.public_feed.only_approved', true);

        Storage::disk('public')->put('gallery/a.jpg', 'x');
        $photo = Photo::query()->create([
            'guest_id' => null,
            'file_path' => 'gallery/a.jpg',
            'original_filename' => 'a.jpg',
            'approved' => false,
        ]);

        $this->get(route('gallery.photo.download', ['photo' => $photo->id]))
            ->assertNotFound();
    }

    public function test_download_not_found_when_file_missing_us15(): void
    {
        Storage::fake('public');
        Config::set('gallery.public_feed.only_approved', false);

        $photo = Photo::query()->create([
            'guest_id' => null,
            'file_path' => 'gallery/missing.jpg',
            'original_filename' => 'missing.jpg',
            'approved' => false,
        ]);

        $this->get(route('gallery.photo.download', ['photo' => $photo->id]))
            ->assertNotFound();
    }

    public function test_public_feed_sets_cache_control_us30(): void
    {
        Config::set('gallery.http_cache.feed_max_age', 45);

        $response = $this->getJson(route('gallery.feed'));
        $response->assertOk();
        $cc = $response->headers->get('Cache-Control');
        $this->assertNotNull($cc);
        $this->assertStringContainsString('max-age=45', $cc);
        $this->assertStringContainsString('public', $cc);
    }

    public function test_public_download_sets_cache_headers_us30(): void
    {
        Storage::fake('public');
        Config::set('gallery.public_feed.only_approved', false);
        Config::set('gallery.http_cache.download_max_age', 120);

        Storage::disk('public')->put('gallery/p.jpg', 'bytes');
        $photo = Photo::query()->create([
            'guest_id' => null,
            'file_path' => 'gallery/p.jpg',
            'original_filename' => 'p.jpg',
            'approved' => false,
        ]);

        $response = $this->get(route('gallery.photo.download', ['photo' => $photo->id]));
        $response->assertOk();
        $cc = $response->headers->get('Cache-Control');
        $this->assertNotNull($cc);
        $this->assertStringContainsString('max-age=120', $cc);
        $this->assertStringContainsString('immutable', $cc);
        $this->assertStringContainsString('public', $cc);
    }
}
