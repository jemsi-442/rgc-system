<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\Branch;
use App\Models\District;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAccountStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_deactivate_another_account_and_that_user_cannot_sign_in(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $superAdmin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $target = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.status@rgc.test');

        $this->actingAs($superAdmin)
            ->put(route('admin.users.update', $target), [
                'name' => $target->name,
                'email' => $target->email,
                'phone' => $target->phone,
                'role' => 'branch_admin',
                'status' => 'inactive',
                'region_id' => $target->region_id,
                'district_id' => $target->district_id,
                'branch_id' => $target->effectiveBranchId(),
            ])
            ->assertRedirect(route('admin.users.edit', $target));

        $this->assertSame('inactive', $target->fresh()->status);

        $this->post(route('logout'));

        $this->post(route('login.attempt'), [
            'email' => 'branch.status@rgc.test',
            'password' => 'ChangeMe123!',
        ])
            ->assertSessionHasErrors([
                'email' => 'Your account is inactive. Please contact church leadership.',
            ]);
    }

    public function test_super_admin_cannot_deactivate_their_own_account_from_user_management_screen(): void
    {
        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $this->actingAs($superAdmin)
            ->from(route('admin.users.edit', $superAdmin))
            ->put(route('admin.users.update', $superAdmin), [
                'name' => $superAdmin->name,
                'email' => $superAdmin->email,
                'phone' => $superAdmin->phone,
                'role' => 'super_admin',
                'status' => 'inactive',
                'region_id' => $superAdmin->region_id,
                'district_id' => $superAdmin->district_id,
                'branch_id' => $superAdmin->effectiveBranchId(),
            ])
            ->assertRedirect(route('admin.users.edit', $superAdmin))
            ->assertSessionHasErrors([
                'status' => 'You cannot deactivate your own account from this screen.',
            ]);

        $this->assertSame('active', $superAdmin->fresh()->status);
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
