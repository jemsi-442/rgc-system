<?php

namespace Tests\Feature\Permissions;

use App\Models\Announcement;
use App\Models\Branch;
use App\Models\District;
use App\Models\Expense;
use App\Models\Offering;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateDeletePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_branch_admin_can_update_an_announcement_in_their_own_branch(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.announcement.update@rgc.test');

        $announcement = Announcement::query()->create([
            'title' => 'Before Update',
            'body' => 'Original branch notice body.',
            'region_id' => $region->id,
            'district_id' => $district->id,
            'church_id' => $branch->id,
            'created_by' => $branchAdmin->id,
        ]);

        $this->actingAs($branchAdmin)
            ->put(route('announcements.update', $announcement), [
                'title' => 'After Update',
                'body' => 'Updated branch notice body.',
            ])
            ->assertRedirect(route('announcements.index'));

        $this->assertDatabaseHas('announcements', [
            'id' => $announcement->id,
            'title' => 'After Update',
            'body' => 'Updated branch notice body.',
        ]);
    }

    public function test_member_cannot_delete_an_announcement_even_in_their_own_branch(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $member = $this->makeUser('member', $region, $district, $branch, 'member.announcement.delete@rgc.test');
        $author = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.announcement.author@rgc.test');

        $announcement = Announcement::query()->create([
            'title' => 'Protected Notice',
            'body' => 'Members must not delete this notice.',
            'region_id' => $region->id,
            'district_id' => $district->id,
            'church_id' => $branch->id,
            'created_by' => $author->id,
        ]);

        $this->actingAs($member)
            ->delete(route('announcements.destroy', $announcement))
            ->assertForbidden();

        $this->assertDatabaseHas('announcements', [
            'id' => $announcement->id,
            'title' => 'Protected Notice',
        ]);
    }

    public function test_branch_admin_cannot_delete_an_offering_from_another_branch(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.offering.delete@rgc.test');
        [, , $outsideBranch] = $this->makeBranchInAnotherRegion();

        $offering = Offering::query()->create([
            'church_id' => $outsideBranch->id,
            'amount' => 88000,
            'date' => '2026-03-17',
            'recorded_by' => 'Outside Recorder',
        ]);

        $this->actingAs($branchAdmin)
            ->delete(route('offerings.destroy', $offering))
            ->assertForbidden();

        $this->assertDatabaseHas('offerings', [
            'id' => $offering->id,
            'church_id' => $outsideBranch->id,
            'amount' => 88000,
        ]);
    }

    public function test_branch_admin_cannot_delete_an_expense_from_another_branch(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.expense.delete@rgc.test');
        [$otherRegion, $otherDistrict, $outsideBranch] = $this->makeBranchInAnotherRegion();
        $outsideRecorder = $this->makeUser('branch_admin', $otherRegion, $otherDistrict, $outsideBranch, 'outside.expense.recorder@rgc.test');

        $expense = Expense::query()->create([
            'church_id' => $outsideBranch->id,
            'recorded_by' => $outsideRecorder->id,
            'date' => '2026-03-17',
            'amount' => 25000,
            'description' => 'Outside branch transport',
        ]);

        $this->actingAs($branchAdmin)
            ->delete(route('expenses.destroy', $expense))
            ->assertForbidden();

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'church_id' => $outsideBranch->id,
            'amount' => 25000,
        ]);
    }

    public function test_branch_admin_cannot_escalate_a_user_role_via_api_update(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.user.update@rgc.test');
        $member = $this->makeUser('member', $region, $district, $branch, 'member.user.update@rgc.test');

        $this->apiAs($branchAdmin)
            ->putJson('/api/users/' . $member->id, [
                'name' => $member->name,
                'email' => $member->email,
                'phone' => $member->phone,
                'password' => 'ChangeMe123!',
                'role' => 'district_admin',
                'region_id' => $region->id,
                'district_id' => $district->id,
                'branch_id' => $branch->id,
            ])
            ->assertStatus(403)
            ->assertJson([
                'message' => 'Unauthorized role assignment.',
            ]);

        $member->refresh();
        $this->assertSame('member', $member->normalizedRoleName());
    }

    public function test_only_super_admin_can_delete_users_via_api(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $superAdmin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $regionalAdmin = $this->makeUser('regional_admin', $region, $district, $branch, 'regional.user.delete@rgc.test');
        $target = $this->makeUser('member', $region, $district, $branch, 'member.user.delete@rgc.test');

        $this->apiAs($regionalAdmin)
            ->deleteJson('/api/users/' . $target->id)
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'email' => 'member.user.delete@rgc.test',
        ]);

        $this->apiAs($superAdmin)
            ->deleteJson('/api/users/' . $target->id)
            ->assertOk()
            ->assertJson([
                'message' => 'User deleted.',
            ]);

        $this->assertSoftDeleted('users', [
            'id' => $target->id,
            'email' => 'member.user.delete@rgc.test',
        ]);
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
        $branch = $this->makeBranch($region->name . ' Permission Branch', $region, $district);

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
