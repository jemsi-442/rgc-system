<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertHeader('x-frame-options', 'SAMEORIGIN')
            ->assertHeader('x-content-type-options', 'nosniff')
            ->assertHeader('referrer-policy', 'strict-origin-when-cross-origin');
    }
}
