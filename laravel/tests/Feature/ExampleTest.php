<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_home_is_wedding_landing(): void
    {
        Config::set('wedding.event.title', 'Test Wedding Title');

        $this->get('/')
            ->assertOk()
            ->assertSee('Test Wedding Title', false);
    }
}
