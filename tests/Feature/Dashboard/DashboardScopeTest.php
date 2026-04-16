<?php

namespace Tests\Feature\Dashboard;

use App\Models\Announcement;
use App\Models\Branch;
use App\Models\District;
use App\Models\Event;
use App\Models\Expense;
use App\Models\Offering;
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
            ->assertSee("Notices for {$hqBranch->name}")
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
            ->assertSee('Giving Snapshot');
    }

    public function test_member_dashboard_shows_personal_snapshot_highlight_and_upcoming_events(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        $member = $this->makeUser('member', $darRegion, $temekeDistrict, $hqBranch, 'member.dashboard.home@rgc.test');

        OfferingPayment::query()->create([
            'church_id' => $hqBranch->id,
            'user_id' => $member->id,
            'amount' => 25000,
            'currency' => 'TZS',
            'offering_date' => now()->toDateString(),
            'payer_name' => 'Member Home',
            'description' => 'Home dashboard contribution',
            'status' => 'completed',
            'paid_at' => now()->subHour(),
            'metadata' => ['payment_type' => 'thanksgiving'],
        ]);

        Announcement::query()->create([
            'title' => 'Pinned Branch Focus',
            'body' => 'This is the key update members should notice first.',
            'region_id' => $darRegion->id,
            'district_id' => $temekeDistrict->id,
            'church_id' => $hqBranch->id,
            'created_by' => $member->id,
            'is_pinned' => true,
            'pinned_at' => now(),
        ]);

        Event::query()->create([
            'title' => 'Friday Prayer Gathering',
            'description' => 'Branch prayer and fellowship evening.',
            'event_date' => now()->addDays(2)->setTime(18, 30),
            'region_id' => $darRegion->id,
            'district_id' => $temekeDistrict->id,
            'church_id' => $hqBranch->id,
            'created_by' => $member->id,
        ]);

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Today’s Encouragement')
            ->assertSee('Giving Snapshot')
            ->assertSee('Giving Journey')
            ->assertSee('Contribution Mix')
            ->assertSee('Pinned Branch Focus')
            ->assertSee('Upcoming Moments')
            ->assertSee('Friday Prayer Gathering')
            ->assertSee('Thanksgiving')
            ->assertSee('This month');
    }

    public function test_super_admin_dashboard_summarizes_regions_across_the_platform(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        $superAdmin = $this->makeUser('super_admin', $darRegion, $temekeDistrict, $hqBranch, 'super.dashboard.scope@rgc.test');
        $otherRegion = Region::query()->where('name', '!=', $darRegion->name)->orderBy('name')->firstOrFail();

        $this->actingAs($superAdmin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Super Admin Workspace')
            ->assertSee('Regions across the platform')
            ->assertSee($darRegion->name)
            ->assertSee($otherRegion->name)
            ->assertSee('active branch')
            ->assertSee('district');
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

    public function test_branch_admin_dashboard_lists_people_from_their_branch_only(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $darRegion, $temekeDistrict, $hqBranch, 'branch.dashboard.people@rgc.test');
        $branchMember = $this->makeUser('member', $darRegion, $temekeDistrict, $hqBranch, 'branch.dashboard.member@rgc.test');
        $sameDistrictOtherBranch = $this->makeBranch('Temeke Roster Branch', $darRegion, $temekeDistrict);
        $outsideUser = $this->makeUser('member', $darRegion, $temekeDistrict, $sameDistrictOtherBranch, 'branch.dashboard.outside@rgc.test');

        $this->actingAs($branchAdmin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Branch Admin Workspace')
            ->assertSee("People in {$hqBranch->name}")
            ->assertSee($branchAdmin->name)
            ->assertSee($branchMember->name)
            ->assertSee('Branch Admin')
            ->assertSee('Member')
            ->assertSee('Active')
            ->assertDontSee($outsideUser->name);
    }

    public function test_accountant_dashboard_focuses_on_branch_finance_instead_of_people_roster(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        $accountant = $this->makeUser('accountant', $darRegion, $temekeDistrict, $hqBranch, 'accountant.dashboard@rgc.test');

        Offering::query()->create([
            'church_id' => $hqBranch->id,
            'recorded_by' => $accountant->id,
            'amount' => 85000,
            'type' => 'offering',
            'service_name' => 'Sunday Service',
            'offering_date' => now()->toDateString(),
        ]);

        Expense::query()->create([
            'church_id' => $hqBranch->id,
            'recorded_by' => $accountant->id,
            'amount' => 15000,
            'category' => 'Transport',
            'expense_date' => now()->toDateString(),
            'description' => 'Fuel reimbursement',
        ]);

        OfferingPayment::query()->create([
            'church_id' => $hqBranch->id,
            'user_id' => $accountant->id,
            'amount' => 20000,
            'currency' => 'TZS',
            'offering_date' => now()->toDateString(),
            'payer_name' => 'Finance Prompt',
            'description' => 'Pending finance prompt',
            'status' => 'pending',
        ]);

        OfferingPayment::query()->create([
            'church_id' => $hqBranch->id,
            'user_id' => $accountant->id,
            'amount' => 50000,
            'currency' => 'TZS',
            'offering_date' => now()->toDateString(),
            'payer_name' => 'Confirmed Collection',
            'description' => 'Completed finance collection',
            'status' => 'completed',
            'paid_at' => now()->subMinutes(30),
        ]);

        $this->actingAs($accountant)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Accountant Workspace')
            ->assertSee("Finance desk for {$hqBranch->name}")
            ->assertSee('Pending payment requests')
            ->assertSee('Completed payments')
            ->assertSee('Offerings recorded')
            ->assertSee('Expenses recorded')
            ->assertSee('Offerings')
            ->assertSee('Expenses')
            ->assertDontSee('People in')
            ->assertDontSee('Events');
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

    public function test_regional_admin_cannot_review_payment_outside_their_region(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        [$otherRegion, $otherDistrict, $outsideBranch] = $this->makeBranchInAnotherRegion();
        $regionalAdmin = $this->makeUser('regional_admin', $darRegion, $temekeDistrict, $hqBranch, 'regional.dashboard.outside.review@rgc.test');
        $outsideRecorder = $this->makeUser('branch_admin', $otherRegion, $otherDistrict, $outsideBranch, 'outside.dashboard.payment@rgc.test');

        $payment = OfferingPayment::query()->create([
            'church_id' => $outsideBranch->id,
            'user_id' => $outsideRecorder->id,
            'amount' => 15000,
            'currency' => 'TZS',
            'offering_date' => now()->toDateString(),
            'payer_name' => 'Outside Region Payer',
            'description' => 'Outside region payment alert',
            'status' => 'completed',
            'paid_at' => now()->subMinutes(15),
        ]);

        $this->actingAs($regionalAdmin)
            ->patch(route('offerings.payments.review', $payment))
            ->assertForbidden();

        $payment->refresh();
        $this->assertNull($payment->reviewed_at);
        $this->assertNull($payment->reviewed_by);
    }
    public function test_regional_admin_dashboard_summarizes_districts_without_branch_only_shortcuts(): void
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
            ->assertSee('Districts in your region')
            ->assertSee($temekeDistrict->name)
            ->assertSee($ilalaDistrict->name)
            ->assertSee('active branch')
            ->assertDontSee('Send payment prompt')
            ->assertDontSee('Kigamboni Branch')
            ->assertDontSee($outsideBranch->name);
    }

    public function test_district_admin_dashboard_lists_only_their_district_branches_without_branch_only_shortcuts(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        $districtAdmin = $this->makeUser('district_admin', $darRegion, $temekeDistrict, $hqBranch, 'district.dashboard@rgc.test');
        $sameDistrictBranch = $this->makeBranch('Temeke South Branch', $darRegion, $temekeDistrict);
        $outsideDistrict = District::query()
            ->where('region_id', $darRegion->id)
            ->where('id', '!=', $temekeDistrict->id)
            ->orderBy('name')
            ->firstOrFail();
        $otherDistrictBranch = $this->makeBranch('Ilala District Branch', $darRegion, $outsideDistrict);
        [, , $outsideRegionBranch] = $this->makeBranchInAnotherRegion();

        $this->actingAs($districtAdmin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('District Admin Workspace')
            ->assertSee("Branches in {$temekeDistrict->name}")
            ->assertSee($hqBranch->name)
            ->assertSee($sameDistrictBranch->name)
            ->assertSee('Active')
            ->assertDontSee('Send payment prompt')
            ->assertDontSee($otherDistrictBranch->name)
            ->assertDontSee($outsideRegionBranch->name);
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
