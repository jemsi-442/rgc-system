<?php

namespace Tests\Unit;

use App\Models\Branch;
use App\Models\District;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserScopeHelperTest extends TestCase
{
    use RefreshDatabase;

    public function test_regional_admin_can_only_manage_districts_inside_their_region(): void
    {
        $this->seed(DatabaseSeeder::class);

        $dar = Region::query()->where('name', 'Dar es Salaam')->firstOrFail();
        $temeke = District::query()->where('region_id', $dar->id)->where('name', 'Temeke')->firstOrFail();
        $branch = Branch::query()->where('name', 'Toangoma')->firstOrFail();
        $outsideDistrict = District::query()->where('region_id', '!=', $dar->id)->orderBy('name')->firstOrFail();

        $regionalAdmin = $this->makeUser('regional_admin', $dar, $temeke, $branch, 'regional.scope.helper@rgc.test');

        $this->assertTrue($regionalAdmin->canManageDistrict($temeke->id));
        $this->assertFalse($regionalAdmin->canManageDistrict($outsideDistrict->id));
    }

    public function test_branch_admin_cannot_manage_region_or_district_scope_helpers(): void
    {
        $this->seed(DatabaseSeeder::class);

        $dar = Region::query()->where('name', 'Dar es Salaam')->firstOrFail();
        $temeke = District::query()->where('region_id', $dar->id)->where('name', 'Temeke')->firstOrFail();
        $branch = Branch::query()->where('name', 'Toangoma')->firstOrFail();

        $branchAdmin = $this->makeUser('branch_admin', $dar, $temeke, $branch, 'branch.scope.helper@rgc.test');

        $this->assertFalse($branchAdmin->canManageRegion($dar->id));
        $this->assertFalse($branchAdmin->canManageDistrict($temeke->id));
        $this->assertTrue($branchAdmin->canManageBranch($branch->id));
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
