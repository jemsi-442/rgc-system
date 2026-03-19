<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_rejects_inactive_users(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->where('email', 'districtadmin@rgc.or.tz')->firstOrFail();
        $user->forceFill(['status' => 'inactive'])->save();

        $this->postJson('/api/auth/login', [
            'email' => 'districtadmin@rgc.or.tz',
            'password' => 'ChangeMe123!',
        ])
            ->assertStatus(403)
            ->assertJson([
                'message' => 'Your account is inactive. Please contact church leadership.',
            ]);
    }

    public function test_api_token_auth_rejects_inactive_users(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->where('email', 'regionaladmin@rgc.or.tz')->firstOrFail();
        $user->forceFill([
            'status' => 'inactive',
            'api_token' => hash('sha256', 'inactive-role-token'),
        ])->save();

        $this->withHeader('Authorization', 'Bearer inactive-role-token')
            ->getJson('/api/me')
            ->assertStatus(403)
            ->assertJson([
                'message' => 'Your account is inactive. Please contact church leadership.',
            ]);
    }
}
