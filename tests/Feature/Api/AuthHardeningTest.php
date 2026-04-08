<?php

namespace Tests\Feature\Api;

use App\Models\Branch;
use App\Models\District;
use App\Models\Region;
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

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $user = $this->makeUser('district_admin', $region, $district, $branch, 'district.api@rgc.test');
        $user->forceFill(['status' => 'inactive'])->save();

        $this->postJson('/api/auth/login', [
            'email' => 'district.api@rgc.test',
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

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $user = $this->makeUser('regional_admin', $region, $district, $branch, 'regional.api@rgc.test');
        $user->forceFill([
            'status' => 'inactive',
            'api_token' => hash('sha256', 'inactive-role-token'),
            'api_token_expires_at' => now()->addHour(),
        ])->save();

        $this->withHeader('Authorization', 'Bearer inactive-role-token')
            ->getJson('/api/me')
            ->assertStatus(403)
            ->assertJson([
            'message' => 'Your account is inactive. Please contact church leadership.',
        ]);
    }

    public function test_api_login_uses_configured_token_expiry_window(): void
    {
        $this->seed(DatabaseSeeder::class);
        config(['auth.api_token_expire_minutes' => 30]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'superadmin@rgc.or.tz',
            'password' => 'ChangeMe123!',
        ])->assertOk();

        $user = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $this->assertNotNull($user->api_token_expires_at);
        $this->assertTrue($user->api_token_expires_at->between(now()->addMinutes(29), now()->addMinutes(31)));
        $this->assertNotEmpty($response->json('expires_at'));
    }

    private function darHeadquartersContext(): array
    {
        $region = Region::query()->where('name', 'Dar es Salaam')->firstOrFail();
        $district = District::query()->where('region_id', $region->id)->where('name', 'Temeke')->firstOrFail();
        $branch = Branch::query()->where('name', 'Toangoma')->firstOrFail();

        return [$region, $district, $branch];
    }

    private function makeUser(string $role, Region $region, District $district, Branch $branch, string $email): User
    {
        $user = User::query()->create([
            'name' => ucwords(str_replace(['@rgc.test', '.'], ['', ' '], $email)),
            'email' => $email,
            'password' => 'ChangeMe123!',
            'role' => $role,
            'status' => 'active',
            'region_id' => $region->id,
            'district_id' => $district->id,
            'branch_id' => $branch->id,
            'church_id' => $branch->id,
            'email_verified_at' => now(),
        ]);

        $user->syncRoles([$role]);

        return $user;
    }
}
