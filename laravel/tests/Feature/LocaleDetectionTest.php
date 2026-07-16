<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleDetectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_uses_browser_language_on_first_visit(): void
    {
        $this->withHeader('Accept-Language', 'de-DE,de;q=0.9')
            ->get('/')
            ->assertOk()
            ->assertSee('Tage', false);

        $this->assertSame('de', session('locale'));
    }

    public function test_browser_english_is_used_on_first_visit(): void
    {
        $this->withHeader('Accept-Language', 'en-US,en;q=0.9')
            ->get('/')
            ->assertOk()
            ->assertSee('Days', false);

        $this->assertSame('en', session('locale'));
    }

    public function test_falls_back_to_first_configured_locale_when_browser_unsupported(): void
    {
        $this->withHeader('Accept-Language', 'fr-FR,fr;q=0.9')
            ->get('/')
            ->assertOk()
            ->assertSee('Giorni', false);

        $this->assertSame('it', session('locale'));
    }

    public function test_session_locale_overrides_browser_preference(): void
    {
        $this->withSession(['locale' => 'el'])
            ->withHeader('Accept-Language', 'de-DE,de;q=0.9')
            ->get('/')
            ->assertOk()
            ->assertSee('Ημέρες', false);
    }

    public function test_explicit_locale_switch_persists(): void
    {
        $this->withHeader('Accept-Language', 'en-US,en;q=0.9')
            ->get('/locale/de')
            ->assertRedirect();

        $this->get('/')
            ->assertOk()
            ->assertSee('Tage', false);
    }
}
