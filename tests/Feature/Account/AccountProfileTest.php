<?php

namespace Tests\Feature\Account;

use App\Models\Branch;
use App\Models\District;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_update_their_own_contact_details(): void
    {
        $this->seed(DatabaseSeeder::class);

        $region = Region::query()->firstOrFail();
        $district = District::query()->where('region_id', $region->id)->firstOrFail();
        $branch = Branch::query()->where('district_id', $district->id)->firstOrFail();

        $user = User::factory()->create([
            'name' => 'Member Contact',
            'email' => 'member.contact@rgc.test',
            'password' => 'Password123!',
            'phone' => '255712345678',
            'region_id' => $region->id,
            'district_id' => $district->id,
            'branch_id' => $branch->id,
            'church_id' => $branch->id,
            'status' => 'active',
        ]);
        $user->assignRole('member');

        $this->actingAs($user)
            ->put(route('account.profile.update'), [
                'name' => 'Updated Member Name',
                'email' => 'updated.member@rgc.test',
                'phone' => '0712345678',
            ])
            ->assertRedirect(route('account.profile.edit'));

        $user->refresh();

        $this->assertSame('Updated Member Name', $user->name);
        $this->assertSame('updated.member@rgc.test', $user->email);
        $this->assertSame('255712345678', $user->phone);
    }
}
