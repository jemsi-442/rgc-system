<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\District;
use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RgcSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $region = Region::query()->where('name', 'Dar es Salaam')->firstOrFail();
        $district = District::query()->where('region_id', $region->id)->where('name', 'Temeke')->firstOrFail();

        $hq = Branch::query()->updateOrCreate(
            ['name' => 'Toangoma'],
            [
                'region_id' => $region->id,
                'district_id' => $district->id,
                'type' => 'headquarters',
                'slug' => Str::slug('Toangoma'),
                'status' => 'active',
            ]
        );

        $user = User::query()->updateOrCreate(
            ['email' => 'superadmin@rgc.or.tz'],
            [
                'name' => 'RGC Super Admin',
                'password' => Hash::make('ChangeMe123!'),
                'role' => 'super_admin',
                'status' => 'active',
                'region_id' => $region->id,
                'district_id' => $district->id,
                'branch_id' => $hq->id,
                'church_id' => $hq->id,
                'email_verified_at' => now(),
            ]
        );

        $user->syncRoles(['super_admin']);
    }
}
