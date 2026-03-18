<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\Branch;
use App\Models\District;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_all_users_in_web_dashboard(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $this->makeUser('regional_admin', $region, $district, $branch, 'regional.view@rgc.test');
        $this->makeUser('district_admin', $region, $district, $branch, 'district.view@rgc.test');

        $superAdmin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $this->actingAs($superAdmin, 'web')
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('regional.view@rgc.test')
            ->assertSee('district.view@rgc.test');
    }

    public function test_super_admin_can_create_a_user_from_web_dashboard(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $superAdmin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $this->actingAs($superAdmin, 'web')
            ->post(route('admin.users.store'), [
                'name' => 'Regional Leader',
                'email' => 'regional.leader@rgc.test',
                'phone' => '0712345678',
                'password' => 'NewLeader123!',
                'password_confirmation' => 'NewLeader123!',
                'role' => 'regional_admin',
                'region_id' => $region->id,
                'district_id' => $district->id,
                'branch_id' => $branch->id,
            ])
            ->assertRedirect(route('admin.users.index'));

        $created = User::query()->where('email', 'regional.leader@rgc.test')->firstOrFail();

        $this->assertTrue($created->hasSystemRole('regional_admin'));
        $this->assertSame($region->id, $created->region_id);
        $this->assertTrue(Hash::check('NewLeader123!', $created->password));
    }

    public function test_super_admin_can_update_another_users_password_and_role(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $superAdmin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $target = $this->makeUser('district_admin', $region, $district, $branch, 'district.manage@rgc.test');

        $this->actingAs($superAdmin, 'web')
            ->put(route('admin.users.update', $target), [
                'name' => 'Updated District Manager',
                'email' => 'district.manage@rgc.test',
                'phone' => '0755000000',
                'password' => 'ResetUser123!',
                'password_confirmation' => 'ResetUser123!',
                'role' => 'regional_admin',
                'region_id' => $region->id,
                'district_id' => $district->id,
                'branch_id' => $branch->id,
            ])
            ->assertRedirect(route('admin.users.edit', $target));

        $target->refresh();

        $this->assertSame('Updated District Manager', $target->name);
        $this->assertTrue($target->hasSystemRole('regional_admin'));
        $this->assertTrue(Hash::check('ResetUser123!', $target->password));
    }

    public function test_super_admin_can_delete_an_admin_account_from_web_dashboard(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $superAdmin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $target = $this->makeUser('regional_admin', $region, $district, $branch, 'regional.delete@rgc.test');

        $this->actingAs($superAdmin, 'web')
            ->delete(route('admin.users.destroy', $target))
            ->assertRedirect(route('admin.users.index'));

        $this->assertSoftDeleted('users', [
            'id' => $target->id,
            'email' => 'regional.delete@rgc.test',
        ]);
    }

    public function test_super_admin_can_change_their_own_password_from_dashboard(): void
    {
        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $this->actingAs($superAdmin, 'web')
            ->put(route('account.password.update'), [
                'current_password' => 'ChangeMe123!',
                'password' => 'MyNewSecure123!',
                'password_confirmation' => 'MyNewSecure123!',
            ])
            ->assertRedirect(route('account.password.edit'));

        $superAdmin->refresh();
        $this->assertTrue(Hash::check('MyNewSecure123!', $superAdmin->password));
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
