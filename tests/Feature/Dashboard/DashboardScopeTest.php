<?php

namespace Tests\Feature\Dashboard;

use App\Models\Announcement;
use App\Models\Branch;
use App\Models\District;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DashboardScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_dashboard_only_shows_branch_notices_from_their_own_branch(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        [$otherRegion, $otherDistrict, $otherBranch] = $this->makeBranchInAnotherRegion();

        $member = $this->makeUser('member', $darRegion, $temekeDistrict, $hqBranch, 'member.dashboard@rgc.test');
        $otherAuthor = $this->makeUser('branch_admin', $otherRegion, $otherDistrict, $otherBranch, 'other.author@rgc.test');

        Announcement::query()->create([
            'title' => 'Toangoma Notice',
            'body' => 'Visible only inside the headquarters branch scope.',
            'region_id' => $darRegion->id,
            'district_id' => $temekeDistrict->id,
            'church_id' => $hqBranch->id,
            'created_by' => $member->id,
        ]);

        Announcement::query()->create([
            'title' => 'Outside Region Notice',
            'body' => 'This should not appear on the member dashboard.',
            'region_id' => $otherRegion->id,
            'district_id' => $otherDistrict->id,
            'church_id' => $otherBranch->id,
            'created_by' => $otherAuthor->id,
        ]);

        $response = $this->actingAs($member)->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertSee('Member Workspace')
            ->assertSee('My branch notices')
            ->assertSee('Toangoma Notice')
            ->assertDontSee('Outside Region Notice');
    }

    public function test_regional_admin_dashboard_only_lists_branches_from_their_region(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        $ilalaDistrict = District::query()
            ->where('region_id', $darRegion->id)
            ->where('id', '!=', $temekeDistrict->id)
            ->orderBy('name')
            ->firstOrFail();

        $regionalAdmin = $this->makeUser('regional_admin', $darRegion, $temekeDistrict, $hqBranch, 'regional.dashboard@rgc.test');
        $regionBranch = $this->makeBranch('Kigamboni Branch', $darRegion, $ilalaDistrict);
        [, , $outsideBranch] = $this->makeBranchInAnotherRegion();

        $response = $this->actingAs($regionalAdmin)->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertSee('Regional Admin Workspace')
            ->assertSee('Branches in your region')
            ->assertSee('Toangoma')
            ->assertSee('Kigamboni Branch')
            ->assertDontSee($outsideBranch->name);
    }


    public function test_expired_announcement_is_archived_by_command_and_removed_from_dashboard(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        $member = $this->makeUser('member', $darRegion, $temekeDistrict, $hqBranch, 'member.dashboard.archive@rgc.test');

        $expired = Announcement::query()->create([
            'title' => 'Expired Dashboard Notice',
            'body' => 'This should leave the dashboard after archival.',
            'region_id' => $darRegion->id,
            'district_id' => $temekeDistrict->id,
            'church_id' => $hqBranch->id,
            'created_by' => $member->id,
            'is_global' => false,
            'is_pinned' => true,
            'pinned_at' => now()->subDays(2),
            'expires_at' => now()->subDay(),
        ]);

        Announcement::query()->create([
            'title' => 'Active Dashboard Notice',
            'body' => 'This one should remain visible.',
            'region_id' => $darRegion->id,
            'district_id' => $temekeDistrict->id,
            'church_id' => $hqBranch->id,
            'created_by' => $member->id,
            'is_global' => false,
            'expires_at' => now()->addDays(3),
        ]);

        $this->artisan('announcements:archive-expired')
            ->expectsOutput('Archived 1 expired announcements.')
            ->assertExitCode(0);

        $expired->refresh();
        $this->assertNotNull($expired->archived_at);
        $this->assertFalse($expired->is_pinned);
        $this->assertNull($expired->pinned_at);

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Active Dashboard Notice')
            ->assertDontSee('Expired Dashboard Notice');

        $this->actingAs($member)
            ->get(route('announcements.index', ['archived' => 1]))
            ->assertOk()
            ->assertSee('Expired Dashboard Notice')
            ->assertSee('Archived');
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
        $branch = $this->makeBranch($region->name . ' Mission Branch', $region, $district);

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
