<?php

namespace Tests\Feature\Api;

use App\Models\Branch;
use App\Models\District;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_regional_admin_only_receives_users_from_their_region_in_the_api_index(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        $ilalaDistrict = District::query()
            ->where('region_id', $darRegion->id)
            ->where('id', '!=', $temekeDistrict->id)
            ->orderBy('name')
            ->firstOrFail();

        $regionalAdmin = $this->makeUser('regional_admin', $darRegion, $temekeDistrict, $hqBranch, 'regional.api@rgc.test');
        $regionalStaff = $this->makeUser('member', $darRegion, $ilalaDistrict, $this->makeBranch('Dar Scope Branch', $darRegion, $ilalaDistrict), 'dar.scope@rgc.test');
        [$outsideRegion, $outsideDistrict, $outsideBranch] = $this->makeBranchInAnotherRegion();
        $outsideUser = $this->makeUser('member', $outsideRegion, $outsideDistrict, $outsideBranch, 'outside.scope@rgc.test');

        $response = $this->apiAs($regionalAdmin)->getJson('/api/users');

        $response
            ->assertOk()
            ->assertJsonFragment(['email' => $regionalAdmin->email])
            ->assertJsonFragment(['email' => $regionalStaff->email])
            ->assertJsonMissing(['email' => $outsideUser->email]);
    }

    public function test_branch_admin_cannot_assign_a_higher_role_through_the_user_api(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $darRegion, $temekeDistrict, $hqBranch, 'branch.admin.api@rgc.test');

        $response = $this->apiAs($branchAdmin)->postJson('/api/users', [
            'name' => 'Blocked District Admin',
            'email' => 'blocked.district@rgc.test',
            'phone' => '255700000001',
            'password' => 'ChangeMe123!',
            'role' => 'district_admin',
            'region_id' => $darRegion->id,
            'district_id' => $temekeDistrict->id,
            'branch_id' => $hqBranch->id,
        ]);

        $response
            ->assertStatus(403)
            ->assertJson([
                'message' => 'Unauthorized role assignment.',
            ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'blocked.district@rgc.test',
        ]);
    }

    public function test_branch_admin_cannot_create_a_user_outside_their_branch_scope(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $darRegion, $temekeDistrict, $hqBranch, 'branch.scope.api@rgc.test');
        [$outsideRegion, $outsideDistrict, $outsideBranch] = $this->makeBranchInAnotherRegion();

        $response = $this->apiAs($branchAdmin)->postJson('/api/users', [
            'name' => 'Blocked Outside Member',
            'email' => 'blocked.outside@rgc.test',
            'phone' => '255700000002',
            'password' => 'ChangeMe123!',
            'role' => 'member',
            'region_id' => $outsideRegion->id,
            'district_id' => $outsideDistrict->id,
            'branch_id' => $outsideBranch->id,
        ]);

        $response
            ->assertStatus(403)
            ->assertJson([
                'message' => 'Cannot create user outside your governance scope.',
            ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'blocked.outside@rgc.test',
        ]);
    }

    public function test_member_cannot_access_the_user_index_api(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        $member = $this->makeUser('member', $darRegion, $temekeDistrict, $hqBranch, 'member.api@rgc.test');

        $this->apiAs($member)
            ->getJson('/api/users')
            ->assertForbidden();
    }

    private function apiAs(User $user)
    {
        $rawToken = 'token-for-user-' . $user->id;
        $user->forceFill([
            'api_token' => hash('sha256', $rawToken),
        ])->save();

        return $this->withHeader('Authorization', 'Bearer ' . $rawToken)
            ->withHeader('Accept', 'application/json');
    }

    private function darHeadquartersContext(): array
    {
        $region = Region::query()->where('name', 'Dar es Salaam')->firstOrFail();
        $district = District::query()->where('region_id', $region->id)->where('name', 'Temeke')->firstOrFail();
        $branch = Branch::query()->where('name', 'Toangoma')->firstOrFail();

        return [$region, $district, $branch];
    }

    private function makeBranchInAnotherRegion(): array
    {
        $region = Region::query()->where('name', '!=', 'Dar es Salaam')->orderBy('name')->firstOrFail();
        $district = District::query()->where('region_id', $region->id)->orderBy('name')->firstOrFail();
        $branch = $this->makeBranch($region->name . ' Scope Branch', $region, $district);

        return [$region, $district, $branch];
    }

    private function makeBranch(string $name, Region $region, District $district): Branch
    {
        return Branch::query()->create([
            'name' => $name,
            'type' => 'local',
            'slug' => Str::slug($name),
            'region_id' => $region->id,
            'district_id' => $district->id,
            'status' => 'active',
        ]);
    }

    private function makeUser(string $role, Region $region, District $district, Branch $branch, string $email): User
    {
        $user = User::query()->create([
            'name' => Str::headline(str_replace(['@rgc.test', '.'], ['', ' '], $email)),
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
