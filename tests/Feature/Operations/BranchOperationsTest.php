<?php

namespace Tests\Feature\Operations;

use App\Models\Announcement;
use App\Models\Branch;
use App\Models\BranchMessage;
use App\Models\District;
use App\Models\Expense;
use App\Models\Offering;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class BranchOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_post_branch_chat_messages_and_feed_only_returns_their_branch_messages(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        [$otherRegion, $otherDistrict, $otherBranch] = $this->makeBranchInAnotherRegion();

        $member = $this->makeUser('member', $darRegion, $temekeDistrict, $hqBranch, 'member.chat@rgc.test');
        $otherMember = $this->makeUser('member', $otherRegion, $otherDistrict, $otherBranch, 'other.chat@rgc.test');

        BranchMessage::query()->create([
            'church_id' => $otherBranch->id,
            'user_id' => $otherMember->id,
            'message' => 'Outside branch message',
        ]);

        $this->actingAs($member)
            ->post(route('messages.store'), [
                'message' => 'Inside branch message',
            ])
            ->assertRedirect();

        $this->actingAs($member)
            ->getJson(route('messages.feed'))
            ->assertOk()
            ->assertJsonFragment(['message' => 'Inside branch message'])
            ->assertJsonMissing(['message' => 'Outside branch message']);

        $this->assertDatabaseHas('branch_messages', [
            'church_id' => $hqBranch->id,
            'user_id' => $member->id,
            'message' => 'Inside branch message',
        ]);
    }

    public function test_branch_admin_can_create_announcements_scoped_to_their_branch(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.announcements@rgc.test');

        $this->actingAs($branchAdmin)
            ->post(route('announcements.store'), [
                'title' => 'Branch Admin Notice',
                'body' => 'Operational update for the branch.',
            ])
            ->assertRedirect(route('announcements.index'));

        $this->assertDatabaseHas('announcements', [
            'title' => 'Branch Admin Notice',
            'region_id' => $region->id,
            'district_id' => $district->id,
            'church_id' => $branch->id,
            'created_by' => $branchAdmin->id,
        ]);
    }

    public function test_member_cannot_create_announcements(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $member = $this->makeUser('member', $region, $district, $branch, 'member.announcements@rgc.test');

        $this->actingAs($member)
            ->post(route('announcements.store'), [
                'title' => 'Blocked Notice',
                'body' => 'Members should not be able to post this.',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('announcements', [
            'title' => 'Blocked Notice',
        ]);
    }

    public function test_branch_admin_can_record_offerings_and_expenses_for_their_branch(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.finance@rgc.test');

        $this->actingAs($branchAdmin)
            ->post(route('offerings.store'), [
                'offering_date' => '2026-03-18',
                'amount' => '45000',
                'description' => 'Sunday service',
            ])
            ->assertRedirect(route('offerings.index'));

        $this->actingAs($branchAdmin)
            ->post(route('expenses.store'), [
                'expense_date' => '2026-03-18',
                'amount' => '12000',
                'category' => 'Transport',
                'description' => 'Regional coordination visit',
            ])
            ->assertRedirect(route('expenses.index'));

        $this->assertDatabaseHas('offerings', [
            'church_id' => $branch->id,
            'recorded_by' => $branchAdmin->name,
            'amount' => 45000,
        ]);

        $this->assertDatabaseHas('expenses', [
            'church_id' => $branch->id,
            'recorded_by' => $branchAdmin->id,
            'amount' => 12000,
            'description' => 'Transport: Regional coordination visit',
        ]);
    }

    public function test_branch_admin_cannot_update_an_offering_from_another_branch(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.finance.lock@rgc.test');
        [$otherRegion, $otherDistrict, $otherBranch] = $this->makeBranchInAnotherRegion();

        $offering = Offering::query()->create([
            'church_id' => $otherBranch->id,
            'amount' => 90000,
            'date' => '2026-03-17',
            'recorded_by' => 'Outside Recorder',
        ]);

        $this->actingAs($branchAdmin)
            ->patch(route('offerings.update', $offering), [
                'offering_date' => '2026-03-18',
                'amount' => '100000',
                'description' => 'Should be blocked',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('offerings', [
            'id' => $offering->id,
            'church_id' => $otherBranch->id,
            'amount' => 90000,
        ]);
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
        $branch = $this->makeBranch($region->name . ' Operations Branch', $region, $district);

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
