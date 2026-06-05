<?php

namespace Tests\Unit;

use App\Services\BrowserLocaleResolver;
use Illuminate\Http\Request;
use Tests\TestCase;

class BrowserLocaleResolverTest extends TestCase
{
    private BrowserLocaleResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new BrowserLocaleResolver;
    }

    public function test_resolves_primary_language_tag(): void
    {
        $request = Request::create('/', 'GET', server: [
            'HTTP_ACCEPT_LANGUAGE' => 'it-IT,it;q=0.9,en;q=0.8',
        ]);

        $this->assertSame('it', $this->resolver->resolve($request, ['it', 'el', 'de']));
    }

    public function test_resolves_greek_region_tag(): void
    {
        $request = Request::create('/', 'GET', server: [
            'HTTP_ACCEPT_LANGUAGE' => 'el-GR,el;q=0.9,en;q=0.8',
        ]);

        $this->assertSame('el', $this->resolver->resolve($request, ['it', 'el', 'de']));
    }

    public function test_returns_null_when_no_supported_language(): void
    {
        $request = Request::create('/', 'GET', server: [
            'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.9,fr;q=0.8',
        ]);

        $this->assertNull($this->resolver->resolve($request, ['it', 'el', 'de']));
    }

    public function test_respects_quality_ordering(): void
    {
        $request = Request::create('/', 'GET', server: [
            'HTTP_ACCEPT_LANGUAGE' => 'en;q=0.5,de-DE,de;q=0.9,it;q=0.8',
        ]);

        $this->assertSame('de', $this->resolver->resolve($request, ['it', 'el', 'de']));
    }
}
