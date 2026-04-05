<?php

namespace Tests\Feature;

use App\Models\Guest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AdminGuestTest extends TestCase
{
    use RefreshDatabase;

    private function configureAdminPassword(string $plain = 'secret'): void
    {
        Config::set('wedding.admin.password_hash', bcrypt($plain));
    }

    public function test_admin_login_returns_503_when_password_not_configured(): void
    {
        Config::set('wedding.admin.password_hash', null);

        $this->get(route('admin.login'))->assertStatus(503);
    }

    public function test_guest_create_redirects_to_login_when_unauthenticated(): void
    {
        $this->configureAdminPassword();

        $this->post(route('admin.guests.store'), [
            'name' => 'New Guest',
        ])->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_login_and_create_guest_us16(): void
    {
        $this->configureAdminPassword();

        $this->post(route('admin.login'), ['password' => 'secret'])
            ->assertRedirect(route('admin.guests.create'));

        $this->post(route('admin.guests.store'), [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
        ])->assertRedirect(route('admin.guests.create'));

        $this->assertDatabaseHas('guests', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
        ]);

        $guest = Guest::query()->where('email', 'ada@example.com')->first();
        $this->assertNotNull($guest);
        $this->assertNotEmpty($guest->token);
    }

    public function test_create_guest_accepts_optional_token_us16(): void
    {
        $this->configureAdminPassword();
        $this->post(route('admin.login'), ['password' => 'secret']);

        $this->post(route('admin.guests.store'), [
            'name' => 'Custom',
            'token' => 'my-invite-99',
        ])->assertRedirect(route('admin.guests.create'));

        $this->assertDatabaseHas('guests', [
            'name' => 'Custom',
            'token' => 'my-invite-99',
        ]);
    }

    public function test_create_guest_validates_name_required(): void
    {
        $this->configureAdminPassword();
        $this->post(route('admin.login'), ['password' => 'secret']);

        $this->post(route('admin.guests.store'), [
            'name' => '',
        ])->assertSessionHasErrors('name');
    }

    public function test_create_guest_rejects_duplicate_token(): void
    {
        Guest::query()->create([
            'name' => 'Existing',
            'email' => null,
            'token' => 'taken-token',
        ]);

        $this->configureAdminPassword();
        $this->post(route('admin.login'), ['password' => 'secret']);

        $this->post(route('admin.guests.store'), [
            'name' => 'Other',
            'token' => 'taken-token',
        ])->assertSessionHasErrors('token');
    }

    public function test_qr_endpoint_requires_admin_session_us17(): void
    {
        $guest = Guest::query()->create([
            'name' => 'A',
            'email' => null,
        ]);

        $this->configureAdminPassword();

        $this->get(route('admin.guests.qr', ['guest' => $guest->id]))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_qr_returns_png_us17(): void
    {
        Config::set('app.url', 'http://wedding.test');
        $this->configureAdminPassword();

        $guest = Guest::query()->create([
            'name' => 'QR Guest',
            'email' => null,
        ]);

        $this->post(route('admin.login'), ['password' => 'secret']);

        $response = $this->get(route('admin.guests.qr', ['guest' => $guest->id]));
        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/png');
        $binary = $response->getContent();
        $this->assertIsString($binary);
        $this->assertStringStartsWith("\x89PNG\r\n\x1a\n", $binary);
    }

    public function test_admin_qr_download_sets_attachment_us17(): void
    {
        Config::set('app.url', 'http://wedding.test');
        $this->configureAdminPassword();

        $guest = Guest::query()->create([
            'name' => 'Download Me',
            'email' => null,
        ]);

        $this->post(route('admin.login'), ['password' => 'secret']);

        $response = $this->get(route('admin.guests.qr', [
            'guest' => $guest->id,
            'download' => 1,
        ]));
        $response->assertOk();
        $disposition = $response->headers->get('Content-Disposition');
        $this->assertIsString($disposition);
        $this->assertStringContainsString('attachment', $disposition);
        $this->assertStringContainsString('.png', $disposition);
    }

    public function test_admin_qr_unknown_guest_returns_404(): void
    {
        $this->configureAdminPassword();
        $this->post(route('admin.login'), ['password' => 'secret']);

        $this->get(route('admin.guests.qr', ['guest' => 999999]))
            ->assertNotFound();
    }

    public function test_csv_import_requires_auth_us18(): void
    {
        $this->configureAdminPassword();
        $csv = "name,email\nTest,test@example.com\n";
        $file = UploadedFile::fake()->createWithContent('guests.csv', $csv);

        $this->post(route('admin.guests.import.store'), ['file' => $file])
            ->assertRedirect(route('admin.login'));
    }

    public function test_csv_import_creates_guests_us18(): void
    {
        $this->configureAdminPassword();
        $this->post(route('admin.login'), ['password' => 'secret']);

        $csv = "name,email\nAda,ada@example.com\nBob,bob@example.com\n";
        $file = UploadedFile::fake()->createWithContent('guests.csv', $csv);

        $this->post(route('admin.guests.import.store'), ['file' => $file])
            ->assertRedirect(route('admin.guests.import'))
            ->assertSessionHas('import_result', fn (array $r) => $r['created'] === 2 && $r['errors'] === []);

        $this->assertDatabaseHas('guests', ['name' => 'Ada', 'email' => 'ada@example.com']);
        $this->assertDatabaseHas('guests', ['name' => 'Bob', 'email' => 'bob@example.com']);
        $this->assertSame(2, Guest::query()->count());
    }

    public function test_csv_import_rejects_missing_name_column_us18(): void
    {
        $this->configureAdminPassword();
        $this->post(route('admin.login'), ['password' => 'secret']);

        $csv = "full_name,email\nAda,ada@example.com\n";
        $file = UploadedFile::fake()->createWithContent('bad.csv', $csv);

        $this->post(route('admin.guests.import.store'), ['file' => $file])
            ->assertRedirect(route('admin.guests.import'))
            ->assertSessionHas('import_result', fn (array $r) => $r['created'] === 0 && $r['errors'] !== []);

        $this->assertDatabaseCount('guests', 0);
    }

    public function test_csv_import_skips_invalid_row_us18(): void
    {
        $this->configureAdminPassword();
        $this->post(route('admin.login'), ['password' => 'secret']);

        $csv = "name,email\nValid,valid@example.com\n,invalid@example.com\n";
        $file = UploadedFile::fake()->createWithContent('mixed.csv', $csv);

        $this->post(route('admin.guests.import.store'), ['file' => $file])
            ->assertRedirect(route('admin.guests.import'))
            ->assertSessionHas('import_result', fn (array $r) => $r['created'] === 1 && count($r['errors']) === 1);

        $this->assertSame(1, Guest::query()->count());
    }

    public function test_csv_import_accepts_semicolon_delimiter_us18(): void
    {
        $this->configureAdminPassword();
        $this->post(route('admin.login'), ['password' => 'secret']);

        $csv = "name;email\nAda;ada@example.com\n";
        $file = UploadedFile::fake()->createWithContent('guests.csv', $csv);

        $this->post(route('admin.guests.import.store'), ['file' => $file])
            ->assertRedirect(route('admin.guests.import'));

        $this->assertDatabaseHas('guests', ['name' => 'Ada', 'email' => 'ada@example.com']);
    }
}
