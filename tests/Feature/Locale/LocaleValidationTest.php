<?php

namespace Tests\Feature\Locale;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_validation_message_is_displayed_in_kiswahili_when_locale_is_sw(): void
    {
        $this->withSession(['locale' => 'sw'])
            ->from(route('login'))
            ->post(route('login.attempt'), [
                'email' => '',
                'password' => '',
            ])
            ->assertRedirect(route('login'));

        $this->withSession(['locale' => 'sw'])
            ->get(route('login'))
            ->assertOk();

        $response = $this->withSession(['locale' => 'sw'])
            ->from(route('login'))
            ->post(route('login.attempt'), [
                'email' => '',
                'password' => '',
            ]);

        $response->assertSessionHasErrors(['email', 'password']);
        $this->followRedirects($response)
            ->assertSeeText('barua pepe ni lazima.')
            ->assertSeeText('Kuingia Salama');
    }
}
