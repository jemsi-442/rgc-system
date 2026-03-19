<?php

namespace Tests\Feature\Dashboard;

use App\Models\Announcement;
use App\Models\Branch;
use App\Models\District;
use App\Models\OfferingPayment;
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
        $sameDistrictOtherBranch = $this->makeBranch('Temeke Overflow Branch', $darRegion, $temekeDistrict);

        $member = $this->makeUser('member', $darRegion, $temekeDistrict, $hqBranch, 'member.dashboard@rgc.test');
        $sameDistrictAuthor = $this->makeUser('branch_admin', $darRegion, $temekeDistrict, $sameDistrictOtherBranch, 'same.district.author@rgc.test');
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
            'title' => 'Same District Other Branch Notice',
            'body' => 'This should stay inside the other branch only.',
            'region_id' => $darRegion->id,
            'district_id' => $temekeDistrict->id,
            'church_id' => $sameDistrictOtherBranch->id,
            'created_by' => $sameDistrictAuthor->id,
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
            ->assertDontSee('Same District Other Branch Notice')
            ->assertDontSee('Outside Region Notice');
    }

    public function test_member_dashboard_shows_give_now_shortcut(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        $member = $this->makeUser('member', $darRegion, $temekeDistrict, $hqBranch, 'member.dashboard.giving@rgc.test');

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Give now')
            ->assertSee('Ready to give to your branch?');
    }

    public function test_branch_admin_dashboard_shows_recent_payment_alerts(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $darRegion, $temekeDistrict, $hqBranch, 'branch.dashboard.alerts@rgc.test');

        OfferingPayment::query()->create([
            'church_id' => $hqBranch->id,
            'user_id' => $branchAdmin->id,
            'amount' => 45000,
            'currency' => 'TZS',
            'offering_date' => now()->toDateString(),
            'payer_name' => 'Dashboard Payer',
            'description' => 'Dashboard payment alert',
            'status' => 'completed',
            'paid_at' => now()->subHour(),
            'receipt_emailed_at' => now()->subHour(),
        ]);

        $this->actingAs($branchAdmin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Payment alerts')
            ->assertSee('New collection received')
            ->assertSee('Dashboard payment alert')
            ->assertSee('Receipt emailed');
    }

    public function test_branch_admin_can_mark_payment_alert_as_reviewed(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $darRegion, $temekeDistrict, $hqBranch, 'branch.dashboard.review@rgc.test');

        $payment = OfferingPayment::query()->create([
            'church_id' => $hqBranch->id,
            'user_id' => $branchAdmin->id,
            'amount' => 32000,
            'currency' => 'TZS',
            'offering_date' => now()->toDateString(),
            'payer_name' => 'Review Me',
            'description' => 'Reviewable payment alert',
            'status' => 'completed',
            'paid_at' => now()->subMinutes(10),
        ]);

        $this->actingAs($branchAdmin)
            ->patch(route('offerings.payments.review', $payment))
            ->assertRedirect();

        $payment->refresh();

        $this->assertNotNull($payment->reviewed_at);
        $this->assertSame($branchAdmin->id, $payment->reviewed_by);

        $this->actingAs($branchAdmin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('No payment alerts right now.');
    }
    public function test_branch_admin_can_mark_all_payment_alerts_as_reviewed(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $darRegion, $temekeDistrict, $hqBranch, 'branch.dashboard.bulk.review@rgc.test');

        OfferingPayment::query()->create([
            'church_id' => $hqBranch->id,
            'user_id' => $branchAdmin->id,
            'amount' => 12000,
            'currency' => 'TZS',
            'offering_date' => now()->toDateString(),
            'payer_name' => 'Bulk One',
            'description' => 'Bulk alert one',
            'status' => 'completed',
            'paid_at' => now()->subMinutes(8),
        ]);

        OfferingPayment::query()->create([
            'church_id' => $hqBranch->id,
            'user_id' => $branchAdmin->id,
            'amount' => 18000,
            'currency' => 'TZS',
            'offering_date' => now()->toDateString(),
            'payer_name' => 'Bulk Two',
            'description' => 'Bulk alert two',
            'status' => 'pending',
        ]);

        $this->actingAs($branchAdmin)
            ->patch(route('offerings.payments.review-all'))
            ->assertRedirect();

        $this->assertSame(2, OfferingPayment::query()->whereNotNull('reviewed_at')->count());

        $this->actingAs($branchAdmin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('No payment alerts right now.');
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

    public function test_member_dashboard_shows_selected_branch_announcements_only_for_targeted_branch(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        $selectedBranch = $this->makeBranch('Temeke Dashboard Selected Branch', $darRegion, $temekeDistrict);
        $superAdmin = $this->makeUser('super_admin', $darRegion, $temekeDistrict, $hqBranch, 'super.dashboard.selected@rgc.test');
        $hqMember = $this->makeUser('member', $darRegion, $temekeDistrict, $hqBranch, 'member.dashboard.hq@rgc.test');
        $targetMember = $this->makeUser('member', $darRegion, $temekeDistrict, $selectedBranch, 'member.dashboard.targeted@rgc.test');

        $announcement = Announcement::query()->create([
            'title' => 'Selected Branch Dashboard Notice',
            'body' => 'Only one branch dashboard should see this.',
            'created_by' => $superAdmin->id,
            'is_global' => false,
        ]);

        $announcement->targetBranches()->sync([$selectedBranch->id]);

        $this->actingAs($targetMember)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Selected Branch Dashboard Notice')
            ->assertSee('Branch');

        $this->actingAs($hqMember)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Selected Branch Dashboard Notice');
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
