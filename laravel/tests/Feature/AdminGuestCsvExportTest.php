<?php

namespace Tests\Feature;

use App\Models\Guest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AdminGuestCsvExportTest extends TestCase
{
    use RefreshDatabase;

    private function loginAsAdmin(): void
    {
        Config::set('wedding.admin.password_hash', bcrypt('secret'));
        $this->post(route('admin.login'), ['password' => 'secret']);
    }

    public function test_export_requires_admin_session(): void
    {
        Config::set('wedding.admin.password_hash', bcrypt('secret'));
        $this->get(route('admin.guests.export'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_export_streams_csv_with_all_guests(): void
    {
        Guest::query()->create([
            'name' => 'Alice Alpha',
            'email' => 'alice@example.test',
            'token' => 'tok-a',
            'rsvp_status' => 'yes',
            'guests_count' => 2,
            'companion_names' => ['Mark'],
            'notes' => 'Vegan',
        ]);
        Guest::query()->create([
            'name' => 'Bob Beta',
            'token' => 'tok-b',
            'rsvp_status' => 'no',
        ]);

        $this->loginAsAdmin();

        $response = $this->get(route('admin.guests.export'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $disposition = $response->headers->get('Content-Disposition');
        $this->assertIsString($disposition);
        $this->assertStringContainsString('attachment', $disposition);
        $this->assertStringContainsString('.csv', $disposition);

        $body = $response->streamedContent();
        // BOM present
        $this->assertStringStartsWith("\xEF\xBB\xBF", $body);
        // Header row
        $this->assertStringContainsString('id,name,email,token,rsvp_status,guests_count,companion_names,notes', $body);
        // Rows
        $this->assertStringContainsString('Alice Alpha', $body);
        $this->assertStringContainsString('alice@example.test', $body);
        $this->assertStringContainsString('Mark', $body);
        $this->assertStringContainsString('Bob Beta', $body);
    }

    public function test_export_filter_only_includes_matching_status(): void
    {
        Guest::query()->create(['name' => 'Yes', 'token' => 't-yes', 'rsvp_status' => 'yes', 'guests_count' => 1]);
        Guest::query()->create(['name' => 'No', 'token' => 't-no', 'rsvp_status' => 'no']);
        Guest::query()->create(['name' => 'Pending', 'token' => 't-pending']);

        $this->loginAsAdmin();

        $body = $this->get(route('admin.guests.export', ['rsvp' => 'yes']))
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('Yes', $body);
        $this->assertStringNotContainsString(",No,", $body);
        $this->assertStringNotContainsString(',Pending,', $body);
    }
}
