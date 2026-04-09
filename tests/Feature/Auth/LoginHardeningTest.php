<?php

namespace Tests\Feature\Auth;

use App\Models\Branch;
use App\Models\District;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_user_cannot_sign_in_through_web_login(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $user = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.login@rgc.test');
        $user->forceFill(['status' => 'inactive'])->save();

        $this->post(route('login.attempt'), [
            'email' => 'branch.login@rgc.test',
            'password' => 'ChangeMe123!',
        ])
            ->assertSessionHasErrors([
                'email' => 'Your account is inactive. Please contact church leadership.',
            ]);

        $this->assertGuest();
    }

    public function test_login_page_does_not_display_seeded_admin_credentials(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('superadmin@rgc.or.tz')
            ->assertDontSee('regionaladmin@rgc.or.tz')
            ->assertDontSee('districtadmin@rgc.or.tz')
            ->assertDontSee('branchadmin@rgc.or.tz')
            ->assertDontSee('Local QA access');
    }

    public function test_super_admin_created_role_accounts_can_open_their_expected_dashboards(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();

        $accounts = [
            [$this->makeUser('regional_admin', $region, $district, $branch, 'regional.login@rgc.test'), 'Regional Admin Workspace'],
            [$this->makeUser('district_admin', $region, $district, $branch, 'district.login@rgc.test'), 'District Admin Workspace'],
            [$this->makeUser('branch_admin', $region, $district, $branch, 'branch.dashboard@rgc.test'), 'Branch Admin Workspace'],
        ];

        $this->post(route('login.attempt'), [
            'email' => 'superadmin@rgc.or.tz',
            'password' => 'ChangeMe123!',
        ])->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Manage users');

        $this->post(route('logout'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('status', 'Signed out successfully. Sign in again when you are ready.');
        $this->assertGuest();

        foreach ($accounts as [$account, $expectedHeading]) {
            $this->post(route('login.attempt'), [
                'email' => $account->email,
                'password' => 'ChangeMe123!',
            ])->assertRedirect(route('dashboard'));

            $this->get(route('dashboard'))
                ->assertOk()
                ->assertSee($expectedHeading);

            $this->post(route('logout'))
                ->assertRedirect(route('login'))
                ->assertSessionHas('status', 'Signed out successfully. Sign in again when you are ready.');
            $this->assertGuest();
        }
    }

    public function test_stale_login_csrf_token_redirects_back_to_login_with_friendly_message(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->withCookie('XSRF-TOKEN', 'stale-token')
            ->post(route('login.attempt'), [
                '_token' => 'stale-token',
                'email' => 'superadmin@rgc.or.tz',
                'password' => 'ChangeMe123!',
            ]);

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email' => 'Your session expired. Please sign in again.',
            ]);
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
