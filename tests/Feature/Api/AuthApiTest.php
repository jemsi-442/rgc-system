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
                'user' => ['id', 'email'],
            ])
            ->assertJson([
                'token_type' => 'Bearer',
            ]);

        $token = $response->json('access_token');
        $user = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $this->assertNotEmpty($token);
        $this->assertSame(hash('sha256', $token), $user->fresh()->getRawOriginal('api_token'));
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

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->withHeader('Accept', 'application/json')
            ->getJson('/api/me')
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthorized. Invalid token.',
            ]);
    }
}
