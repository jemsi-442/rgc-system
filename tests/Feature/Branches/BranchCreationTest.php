<?php

namespace Tests\Feature\Branches;

use App\Models\Branch;
use App\Models\District;
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
}
