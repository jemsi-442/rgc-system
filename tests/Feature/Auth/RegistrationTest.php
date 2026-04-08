<?php

namespace Tests\Feature\Auth;

use App\Models\Branch;
use App\Models\District;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_register_with_a_valid_region_district_branch_hierarchy(): void
    {
        $this->seed(DatabaseSeeder::class);

        $region = Region::query()->where('name', 'Dar es Salaam')->firstOrFail();
        $district = District::query()->where('region_id', $region->id)->where('name', 'Temeke')->firstOrFail();
        $branch = Branch::query()->where('district_id', $district->id)->where('name', 'Toangoma')->firstOrFail();

        $response = $this->post(route('register.store'), [
            'name' => 'QA Member',
            'email' => 'qa.member@example.test',
            'phone' => '0712345678',
            'password' => 'ChangeMe123!',
            'password_confirmation' => 'ChangeMe123!',
            'region_id' => $region->id,
            'district_id' => $district->id,
            'branch_id' => $branch->id,
        ]);

        $response->assertRedirect(route('dashboard'));

        $user = User::query()->where('email', 'qa.member@example.test')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->hasSystemRole('member'));
        $this->assertSame($region->id, $user->region_id);
        $this->assertSame($district->id, $user->district_id);
        $this->assertSame($branch->id, $user->branch_id);
        $this->assertSame($branch->id, $user->church_id);
        $this->assertSame('255712345678', $user->phone);
    }

    public function test_registration_rejects_a_district_that_does_not_belong_to_the_selected_region(): void
    {
        $this->seed(DatabaseSeeder::class);

        $region = Region::query()->where('name', 'Dar es Salaam')->firstOrFail();
        $invalidDistrict = District::query()->where('region_id', '!=', $region->id)->firstOrFail();
        $branch = Branch::query()->where('name', 'Toangoma')->firstOrFail();

        $response = $this->from(route('register'))->post(route('register.store'), [
            'name' => 'QA Invalid Member',
            'email' => 'qa.invalid.member@example.test',
            'phone' => '0712345678',
            'password' => 'ChangeMe123!',
            'password_confirmation' => 'ChangeMe123!',
            'region_id' => $region->id,
            'district_id' => $invalidDistrict->id,
            'branch_id' => $branch->id,
        ]);

        $response
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['district_id']);

        $this->assertGuest();
        $this->assertDatabaseMissing('users', [
            'email' => 'qa.invalid.member@example.test',
        ]);
    }

    public function test_registration_rejects_an_inactive_branch(): void
    {
        $this->seed(DatabaseSeeder::class);

        $region = Region::query()->where('name', 'Dar es Salaam')->firstOrFail();
        $district = District::query()->where('region_id', $region->id)->where('name', 'Temeke')->firstOrFail();
        $inactiveBranch = Branch::query()->create([
            'name' => 'Dormant Branch',
            'type' => 'local',
            'slug' => 'dormant-branch',
            'region_id' => $region->id,
            'district_id' => $district->id,
            'status' => 'inactive',
        ]);

        $response = $this->from(route('register'))->post(route('register.store'), [
            'name' => 'Inactive Branch Member',
            'email' => 'inactive.branch.member@example.test',
            'phone' => '0712345678',
            'password' => 'ChangeMe123!',
            'password_confirmation' => 'ChangeMe123!',
            'region_id' => $region->id,
            'district_id' => $district->id,
            'branch_id' => $inactiveBranch->id,
        ]);

        $response
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['branch_id']);

        $this->assertGuest();
        $this->assertDatabaseMissing('users', [
            'email' => 'inactive.branch.member@example.test',
        ]);
    }

    public function test_registration_requires_a_valid_tanzania_phone_number(): void
    {
        $this->seed(DatabaseSeeder::class);

        $region = Region::query()->where('name', 'Dar es Salaam')->firstOrFail();
        $district = District::query()->where('region_id', $region->id)->where('name', 'Temeke')->firstOrFail();
        $branch = Branch::query()->where('district_id', $district->id)->where('name', 'Toangoma')->firstOrFail();

        $response = $this->from(route('register'))->post(route('register.store'), [
            'name' => 'Phone Invalid Member',
            'email' => 'phone.invalid.member@example.test',
            'phone' => '12345',
            'password' => 'ChangeMe123!',
            'password_confirmation' => 'ChangeMe123!',
            'region_id' => $region->id,
            'district_id' => $district->id,
            'branch_id' => $branch->id,
        ]);

        $response
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['phone']);

        $this->assertGuest();
        $this->assertDatabaseMissing('users', [
            'email' => 'phone.invalid.member@example.test',
        ]);
    }
}
