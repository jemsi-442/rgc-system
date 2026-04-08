<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_rejects_invalid_credentials(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->postJson('/api/auth/login', [
            'email' => 'superadmin@rgc.or.tz',
            'password' => 'wrong-password',
        ])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'Invalid credentials.',
            ]);
    }

    public function test_api_login_error_message_is_localized_to_kiswahili_when_requested(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->withHeader('Accept-Language', 'sw')
            ->postJson('/api/auth/login', [
                'email' => 'superadmin@rgc.or.tz',
                'password' => 'wrong-password',
            ])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'Taarifa za kuingia si sahihi.',
            ]);
    }

    public function test_api_login_returns_a_bearer_token_and_persists_its_hash(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'superadmin@rgc.or.tz',
            'password' => 'ChangeMe123!',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'token_type',
                'access_token',
                'expires_at',
                'user' => ['id', 'email'],
            ])
            ->assertJson([
                'token_type' => 'Bearer',
            ]);

        $token = $response->json('access_token');
        $user = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $this->assertNotEmpty($token);
        $this->assertSame(hash('sha256', $token), $user->fresh()->getRawOriginal('api_token'));
        $this->assertNotNull($user->fresh()->api_token_expires_at);
    }

    public function test_api_me_returns_the_authenticated_user_for_a_valid_bearer_token(): void
    {
        $this->seed(DatabaseSeeder::class);

        $login = $this->postJson('/api/auth/login', [
            'email' => 'superadmin@rgc.or.tz',
            'password' => 'ChangeMe123!',
        ]);

        $token = $login->json('access_token');

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->withHeader('Accept', 'application/json')
            ->getJson('/api/me')
            ->assertOk()
            ->assertJsonFragment([
                'email' => 'superadmin@rgc.or.tz',
            ]);
    }

    public function test_api_missing_token_message_is_localized_to_kiswahili_when_requested(): void
    {
        $this->withHeader('Accept-Language', 'sw')
            ->getJson('/api/me')
            ->assertStatus(401)
            ->assertHeader('x-content-type-options', 'nosniff')
            ->assertJson([
                'message' => 'Hujaruhusiwa. Bearer token haipo.',
            ]);
    }

    public function test_api_token_is_revoked_after_web_password_change(): void
    {
        $this->seed(DatabaseSeeder::class);

        $login = $this->postJson('/api/auth/login', [
            'email' => 'superadmin@rgc.or.tz',
            'password' => 'ChangeMe123!',
        ]);

        $token = $login->json('access_token');
        $user = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $this->actingAs($user)
            ->put(route('account.password.update'), [
                'current_password' => 'ChangeMe123!',
                'password' => 'ChangedThroughWeb123!',
                'password_confirmation' => 'ChangedThroughWeb123!',
            ])
            ->assertRedirect(route('account.password.edit'));

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->withHeader('Accept', 'application/json')
            ->getJson('/api/me')
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthorized. Invalid token.',
            ]);
    }

    public function test_api_logout_revokes_the_token_and_blocks_further_access(): void
    {
        $this->seed(DatabaseSeeder::class);

        $login = $this->postJson('/api/auth/login', [
            'email' => 'superadmin@rgc.or.tz',
            'password' => 'ChangeMe123!',
        ]);

        $token = $login->json('access_token');

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->withHeader('Accept', 'application/json')
            ->postJson('/api/auth/logout')
            ->assertOk()
            ->assertJson([
                'message' => 'Logged out.',
            ]);

        $user = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $this->assertNull($user->fresh()->getRawOriginal('api_token'));
        $this->assertNull($user->fresh()->api_token_expires_at);

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->withHeader('Accept', 'application/json')
            ->getJson('/api/me')
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthorized. Invalid token.',
            ]);
    }

    public function test_api_rejects_an_expired_token_and_clears_it(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $user->forceFill([
            'api_token' => hash('sha256', 'expired-api-token'),
            'api_token_expires_at' => now()->subMinute(),
        ])->save();

        $this->withHeader('Authorization', 'Bearer expired-api-token')
            ->withHeader('Accept', 'application/json')
            ->getJson('/api/me')
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthorized. Token expired.',
            ]);

        $user->refresh();
        $this->assertNull($user->getRawOriginal('api_token'));
        $this->assertNull($user->api_token_expires_at);
    }

    public function test_api_logout_message_uses_the_authenticated_user_locale(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $user->forceFill(['locale' => 'sw'])->save();

        $login = $this->postJson('/api/auth/login', [
            'email' => 'superadmin@rgc.or.tz',
            'password' => 'ChangeMe123!',
        ]);

        $token = $login->json('access_token');

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/logout')
            ->assertOk()
            ->assertJson([
                'message' => 'Umetoka.',
            ]);
    }
}
