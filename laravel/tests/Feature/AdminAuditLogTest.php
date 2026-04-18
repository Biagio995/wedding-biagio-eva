<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Guest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AdminAuditLogTest extends TestCase
{
    use RefreshDatabase;

    private function configureAdmin(): void
    {
        Config::set('wedding.admin.password_hash', bcrypt('secret'));
    }

    public function test_successful_login_is_logged(): void
    {
        $this->configureAdmin();

        $this->post(route('admin.login'), ['password' => 'secret']);

        $this->assertSame(1, AuditLog::query()->where('action', 'admin.login.success')->count());
    }

    public function test_failed_login_is_logged(): void
    {
        $this->configureAdmin();

        $this->post(route('admin.login'), ['password' => 'wrong']);

        $this->assertSame(1, AuditLog::query()->where('action', 'admin.login.failed')->count());
        $this->assertSame(0, AuditLog::query()->where('action', 'admin.login.success')->count());
    }

    public function test_guest_create_logs_audit_entry(): void
    {
        $this->configureAdmin();
        $this->post(route('admin.login'), ['password' => 'secret']);

        $this->post(route('admin.guests.store'), [
            'name' => 'Audit Target',
            'email' => null,
        ]);

        $entry = AuditLog::query()->where('action', 'guest.created')->first();
        $this->assertNotNull($entry);
        $this->assertSame(Guest::class, $entry->subject_type);
        $this->assertIsArray($entry->meta);
        $this->assertSame('Audit Target', $entry->meta['name'] ?? null);
    }

    public function test_audit_index_requires_admin(): void
    {
        $this->configureAdmin();
        $this->get(route('admin.audit.index'))->assertRedirect(route('admin.login'));
    }

    public function test_audit_index_displays_entries(): void
    {
        $this->configureAdmin();
        $this->post(route('admin.login'), ['password' => 'secret']);

        AuditLog::query()->create([
            'action' => 'test.event',
            'meta' => ['hello' => 'world'],
        ]);

        $this->get(route('admin.audit.index'))
            ->assertOk()
            ->assertSee('test.event')
            ->assertSee('hello');
    }

    public function test_audit_index_filters_by_action(): void
    {
        $this->configureAdmin();
        $this->post(route('admin.login'), ['password' => 'secret']);

        AuditLog::query()->create(['action' => 'guest.created']);
        AuditLog::query()->create(['action' => 'photo.deleted']);

        $response = $this->get(route('admin.audit.index', ['action' => 'guest']));
        $response->assertOk();
        // Filter renders the matching row as a <code> element inside the table.
        $response->assertSee('<code>guest.created</code>', false);
        $response->assertDontSee('<code>photo.deleted</code>', false);
    }
}
