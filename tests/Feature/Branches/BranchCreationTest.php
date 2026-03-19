<?php

namespace Tests\Feature\Branches;

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
use Tests\TestCase;

class BranchCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_open_the_branch_creation_page_and_create_a_branch(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $region = Region::query()->where('name', 'Dar es Salaam')->firstOrFail();
        $district = District::query()->where('region_id', $region->id)->where('name', 'Temeke')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('branches.create'))
            ->assertOk();

        $response = $this->actingAs($admin)->post(route('branches.store'), [
            'name' => 'QA Test Branch',
            'branch_type' => 'local',
            'region_id' => $region->id,
            'district_id' => $district->id,
        ]);

        $response->assertRedirect(route('branches.index'));

        $this->assertDatabaseHas((new Branch())->getTable(), [
            'name' => 'QA Test Branch',
            'type' => 'local',
            'region_id' => $region->id,
            'district_id' => $district->id,
            'status' => 'active',
        ]);
    }

    public function test_branch_creation_rejects_a_district_outside_the_selected_region(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $region = Region::query()->where('name', 'Dar es Salaam')->firstOrFail();
        $invalidDistrict = District::query()->where('region_id', '!=', $region->id)->firstOrFail();

        $response = $this->actingAs($admin)
            ->from(route('branches.create'))
            ->post(route('branches.store'), [
                'name' => 'QA Invalid Branch',
                'branch_type' => 'local',
                'region_id' => $region->id,
                'district_id' => $invalidDistrict->id,
            ]);

        $response
            ->assertRedirect(route('branches.create'))
            ->assertSessionHasErrors(['district_id']);

        $this->assertDatabaseMissing((new Branch())->getTable(), [
            'name' => 'QA Invalid Branch',
        ]);
    }

    public function test_super_admin_can_view_branch_details_page_with_extended_stats(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $branch = Branch::query()->where('type', 'headquarters')->firstOrFail();

        Offering::query()->create([
            'church_id' => $branch->id,
            'amount' => 250000,
            'date' => now()->toDateString(),
            'recorded_by' => $admin->id,
        ]);

        Expense::query()->create([
            'church_id' => $branch->id,
            'amount' => 100000,
            'date' => now()->toDateString(),
            'description' => 'Sound repair',
            'recorded_by' => $admin->id,
        ]);

        Event::query()->create([
            'title' => 'Youth Revival',
            'event_date' => now()->addWeek(),
            'church_id' => $branch->id,
            'region_id' => $branch->region_id,
            'district_id' => $branch->district_id,
            'created_by' => $admin->id,
        ]);

        OfferingPayment::query()->create([
            'church_id' => $branch->id,
            'user_id' => $admin->id,
            'amount' => 85000,
            'currency' => 'TZS',
            'offering_date' => now()->toDateString(),
            'payer_name' => 'Neema Peter',
            'description' => 'Mission support',
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        OfferingPayment::query()->create([
            'church_id' => $branch->id,
            'user_id' => $admin->id,
            'amount' => 45000,
            'currency' => 'TZS',
            'offering_date' => now()->toDateString(),
            'payer_name' => 'Amina Paulo',
            'description' => 'Youth giving',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('branches.show', $branch));

        $response->assertOk();
        $response->assertSeeText($branch->name);
        $response->assertSeeText(__('Branch Details'));
        $response->assertSeeText('TZS 250,000.00');
        $response->assertSeeText('TZS 100,000.00');
        $response->assertSeeText('TZS 150,000.00');
        $response->assertSeeText('Mission support');
        $response->assertSeeText('Youth Revival');
        $response->assertSeeText('Sound repair');
        $response->assertSeeText(__('Export Records XLSX'));
        $response->assertSeeText(__('Export Records CSV'));
        $response->assertSeeText(__('Payment Requests'));
        $response->assertSeeText(__('Pending Payments'));
        $response->assertSeeText(__('Completed Payments'));
        $response->assertSeeText('TZS 85,000.00');
    }

    public function test_super_admin_can_open_branch_print_page(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $branch = Branch::query()->where('type', 'headquarters')->firstOrFail();

        $response = $this->actingAs($admin)->get(route('branches.print', $branch));

        $response->assertOk();
        $response->assertSeeText(__('Branch Profile Report'));
        $response->assertSeeText($branch->name);
    }

    public function test_super_admin_can_download_branch_profile_pdf(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $branch = Branch::query()->where('type', 'headquarters')->firstOrFail();

        $response = $this->actingAs($admin)->get(route('branches.pdf', $branch));

        $response->assertOk();
        $response->assertHeader('content-disposition');
    }

    public function test_super_admin_can_export_single_branch_records_as_csv(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $branch = Branch::query()->where('type', 'headquarters')->firstOrFail();

        Offering::query()->create([
            'church_id' => $branch->id,
            'amount' => 50000,
            'date' => now()->toDateString(),
            'recorded_by' => $admin->id,
        ]);

        Expense::query()->create([
            'church_id' => $branch->id,
            'amount' => 12000,
            'date' => now()->toDateString(),
            'description' => 'Fuel support',
            'recorded_by' => $admin->id,
        ]);

        Event::query()->create([
            'title' => 'Choir Practice',
            'description' => 'Evening rehearsal',
            'event_date' => now()->addDays(2),
            'church_id' => $branch->id,
            'region_id' => $branch->region_id,
            'district_id' => $branch->district_id,
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('branches.records.export', [
            'branch' => $branch,
            'format' => 'csv',
        ]));

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('branch-records-' . $branch->id . '.csv', (string) $response->headers->get('content-disposition'));
    }

    public function test_super_admin_can_filter_single_branch_records_export_by_date_range(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $branch = Branch::query()->where('type', 'headquarters')->firstOrFail();

        Offering::query()->create([
            'church_id' => $branch->id,
            'amount' => 99000,
            'date' => now()->subDays(40)->toDateString(),
            'recorded_by' => $admin->id,
        ]);

        Offering::query()->create([
            'church_id' => $branch->id,
            'amount' => 51000,
            'date' => now()->toDateString(),
            'recorded_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('branches.records.export', [
            'branch' => $branch,
            'format' => 'csv',
            'date_from' => now()->toDateString(),
            'date_to' => now()->toDateString(),
        ]));

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString(now()->toDateString() . '-to-' . now()->toDateString(), (string) $response->headers->get('content-disposition'));

        $file = $response->baseResponse->getFile();
        $content = file_get_contents($file->getPathname());

        $this->assertIsString($content);
        $this->assertStringContainsString('51000', $content);
        $this->assertStringContainsString('Rows exported', $content);
        $this->assertStringContainsString('Offerings total', $content);
        $this->assertStringNotContainsString('99000', $content);
    }

    public function test_super_admin_can_use_last_30_days_preset_for_branch_records_export(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $branch = Branch::query()->where('type', 'headquarters')->firstOrFail();

        Offering::query()->create([
            'church_id' => $branch->id,
            'amount' => 88000,
            'date' => now()->subDays(45)->toDateString(),
            'recorded_by' => $admin->id,
        ]);

        Offering::query()->create([
            'church_id' => $branch->id,
            'amount' => 44000,
            'date' => now()->subDays(10)->toDateString(),
            'recorded_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('branches.records.export', [
            'branch' => $branch,
            'format' => 'csv',
            'preset' => 'last_30_days',
        ]));

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('branch-records-' . $branch->id, (string) $response->headers->get('content-disposition'));

        $file = $response->baseResponse->getFile();
        $content = file_get_contents($file->getPathname());

        $this->assertIsString($content);
        $this->assertStringContainsString('44000', $content);
        $this->assertStringNotContainsString('88000', $content);
    }
}
