<?php

namespace Tests\Feature\SuperAdmin;

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

        $superAdmin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $target = User::query()->where('email', 'branchadmin@rgc.or.tz')->firstOrFail();

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
            'email' => 'branchadmin@rgc.or.tz',
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
}
