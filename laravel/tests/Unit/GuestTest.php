<?php

namespace Tests\Unit;

use App\Models\Guest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_gets_unique_token_when_omitted(): void
    {
        $a = Guest::query()->create(['name' => 'A']);
        $b = Guest::query()->create(['name' => 'B']);

        $this->assertNotEmpty($a->fresh()->token);
        $this->assertNotEmpty($b->fresh()->token);
        $this->assertNotSame($a->token, $b->token);
        $this->assertSame(48, strlen($a->token));
    }

    public function test_explicit_token_is_preserved(): void
    {
        $guest = Guest::query()->create([
            'name' => 'C',
            'token' => 'custom-fixed-token',
        ]);

        $this->assertSame('custom-fixed-token', $guest->fresh()->token);
    }
}
