<?php

namespace Tests\Feature\Branches;

use App\Models\Branch;
use App\Models\District;
use App\Models\Region;
use App\Models\User;
use App\Exports\BranchExport;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class BranchImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_download_the_blank_branch_import_template(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $response = $this->actingAs($admin)->get(route('branches.template', 'csv'));

        $response->assertOk();
        $response->assertHeader('content-disposition');
    }

    public function test_super_admin_can_download_the_filled_branch_import_sample_template(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $response = $this->actingAs($admin)->get(route('branches.template.sample', 'xlsx'));

        $response->assertOk();
        $response->assertHeader('content-disposition');
    }

    public function test_super_admin_can_export_existing_branches(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $response = $this->actingAs($admin)->get(route('branches.export', 'xlsx'));

        $response->assertOk();
        $response->assertHeader('content-disposition');
    }

    public function test_super_admin_can_export_branches_filtered_by_region(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $dar = Region::query()->where('name', 'Dar es Salaam')->firstOrFail();
        $mwanza = Region::query()->where('name', 'Mwanza')->firstOrFail();
        $mwanzaDistrict = District::query()->where('region_id', $mwanza->id)->firstOrFail();

        Branch::query()->create([
            'name' => 'Filtered Export Branch',
            'slug' => 'filtered-export-branch',
            'type' => 'local',
            'region_id' => $mwanza->id,
            'district_id' => $mwanzaDistrict->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('branches.export', [
            'format' => 'csv',
            'region_id' => $dar->id,
        ]));

        $response->assertOk();
        $response->assertHeader('content-disposition');

        $filteredCollection = (new BranchExport($dar->id))->collection();

        $this->assertTrue($filteredCollection->contains(fn ($branch) => $branch->name === 'Toangoma'));
        $this->assertFalse($filteredCollection->contains(fn ($branch) => $branch->name === 'Filtered Export Branch'));
    }

    public function test_valid_branch_import_shows_preview_before_saving_anything(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $region = Region::query()->where('name', 'Dar es Salaam')->firstOrFail();
        $districts = District::query()->where('region_id', $region->id)->orderBy('name')->take(2)->get();

        $csv = implode("\n", [
            'region,district,branch_name,branch_type,address,phone,email,status',
            sprintf('%s,%s,%s,%s,%s,%s,%s,%s', $region->name, $districts[0]->name, 'Import Branch One', 'local', 'Temeke Area', '+255700000010', 'one@rgc.or.tz', 'active'),
            sprintf('%s,%s,%s,%s,%s,%s,%s,%s', $region->name, $districts[1]->name, 'Import Branch Two', 'district', 'City Centre', '+255700000011', 'two@rgc.or.tz', 'inactive'),
        ]);

        $file = UploadedFile::fake()->createWithContent('branches.csv', $csv);

        $response = $this->actingAs($admin)
            ->from(route('branches.create'))
            ->post(route('branches.import'), [
                'branch_file' => $file,
            ]);

        $response
            ->assertRedirect(route('branches.create'))
            ->assertSessionHas('branch_import_token')
            ->assertSessionHas('branch_import_preview');

        $this->assertDatabaseMissing((new Branch())->getTable(), [
            'name' => 'Import Branch One',
        ]);
    }

    public function test_super_admin_can_confirm_a_valid_branch_import_preview(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $region = Region::query()->where('name', 'Dar es Salaam')->firstOrFail();
        $districts = District::query()->where('region_id', $region->id)->orderBy('name')->take(2)->get();

        $csv = implode("\n", [
            'region,district,branch_name,branch_type,address,phone,email,status',
            sprintf('%s,%s,%s,%s,%s,%s,%s,%s', $region->name, $districts[0]->name, 'Import Branch One', 'local', 'Temeke Area', '+255700000010', 'one@rgc.or.tz', 'active'),
            sprintf('%s,%s,%s,%s,%s,%s,%s,%s', $region->name, $districts[1]->name, 'Import Branch Two', 'district', 'City Centre', '+255700000011', 'two@rgc.or.tz', 'inactive'),
        ]);

        $file = UploadedFile::fake()->createWithContent('branches.csv', $csv);

        $this->actingAs($admin)->post(route('branches.import'), [
            'branch_file' => $file,
        ]);

        $token = session('branch_import_token');

        $response = $this->actingAs($admin)->post(route('branches.import.confirm'), [
            'import_token' => $token,
        ]);

        $response->assertRedirect(route('branches.index'));

        $this->assertDatabaseHas((new Branch())->getTable(), [
            'name' => 'Import Branch One',
            'type' => 'local',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas((new Branch())->getTable(), [
            'name' => 'Import Branch Two',
            'type' => 'district',
            'status' => 'inactive',
        ]);
    }

    public function test_branch_import_rejects_rows_with_invalid_hierarchy(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $region = Region::query()->where('name', 'Dar es Salaam')->firstOrFail();
        $wrongDistrict = District::query()->where('region_id', '!=', $region->id)->firstOrFail();

        $csv = implode("\n", [
            'region,district,branch_name,branch_type,address,phone,email,status',
            sprintf('%s,%s,%s,%s,%s,%s,%s,%s', $region->name, $wrongDistrict->name, 'Broken Import Branch', 'local', 'Mismatch Area', '+255700000099', 'broken@rgc.or.tz', 'active'),
        ]);

        $file = UploadedFile::fake()->createWithContent('invalid-branches.csv', $csv);

        $response = $this->actingAs($admin)
            ->from(route('branches.create'))
            ->post(route('branches.import'), [
                'branch_file' => $file,
            ]);

        $response
            ->assertRedirect(route('branches.create'))
            ->assertSessionHasErrors(['branch_import']);

        $this->assertDatabaseMissing((new Branch())->getTable(), [
            'name' => 'Broken Import Branch',
        ]);
    }

    public function test_super_admin_can_see_branch_crud_actions_on_index_page(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $headquarters = Branch::query()->where('type', 'headquarters')->firstOrFail();
        $branch = Branch::query()->create([
            'name' => 'CRUD Branch Test',
            'slug' => 'crud-branch-test',
            'type' => 'local',
            'region_id' => $headquarters->region_id,
            'district_id' => $headquarters->district_id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('branches.index'));

        $response->assertOk();
        $response->assertSee(route('branches.show', $branch), false);
        $response->assertSee(route('branches.edit', $branch), false);
        $response->assertSeeText(__('Delete'));
    }
}
