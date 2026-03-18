<?php

namespace Tests\Feature\Locale;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LocaleSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_switch_to_kiswahili(): void
    {
        $this->from(route('home'))
            ->post(route('locale.update'), ['locale' => 'sw'])
            ->assertRedirect(route('home'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Utawala wa Kitaifa wa Kanisa')
            ->assertSeeText('Mwanzo');
    }

    public function test_user_can_switch_back_to_english(): void
    {
        $this->from(route('home'))
            ->post(route('locale.update'), ['locale' => 'sw'])
            ->assertRedirect(route('home'));

        $this->from(route('home'))
            ->post(route('locale.update'), ['locale' => 'en'])
            ->assertRedirect(route('home'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('National Church Governance')
            ->assertSeeText('Home');
    }

    public function test_authenticated_user_locale_preference_is_persisted_and_reused(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
            'role' => 'member',
            'status' => 'active',
            'locale' => 'sw',
        ]);

        $this->actingAs($user)
            ->withSession(['locale' => null])
            ->get(route('home'))
            ->assertOk()
            ->assertSeeText('Utawala wa Kitaifa wa Kanisa');

        $this->actingAs($user)
            ->from(route('home'))
            ->post(route('locale.update'), ['locale' => 'en'])
            ->assertRedirect(route('home'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'locale' => 'en',
        ]);

        $this->actingAs($user->fresh())
            ->withSession(['locale' => null])
            ->get(route('home'))
            ->assertOk()
            ->assertSeeText('National Church Governance');
    }
}
