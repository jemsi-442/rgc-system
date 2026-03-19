<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\District;
use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RgcRoleDashboardSeeder extends Seeder
{
    private const DEFAULT_PASSWORD = 'ChangeMe123!';

    public function run(): void
    {
        $darRegion = Region::query()->where('name', 'Dar es Salaam')->firstOrFail();
        $temekeDistrict = District::query()->where('region_id', $darRegion->id)->where('name', 'Temeke')->firstOrFail();
        $ilalaDistrict = District::query()->where('region_id', $darRegion->id)->where('name', 'Ilala')->firstOrFail();
        $headquarters = Branch::query()->where('name', 'Toangoma')->firstOrFail();

        $ilalaBranch = Branch::query()->updateOrCreate(
            ['slug' => 'ilala-city-centre'],
            [
                'name' => 'Ilala City Centre',
                'region_id' => $darRegion->id,
                'district_id' => $ilalaDistrict->id,
                'type' => 'regional',
                'slug' => 'ilala-city-centre',
                'status' => 'active',
                'address' => 'Ilala, Dar es Salaam',
                'phone' => '255700100100',
                'email' => 'ilala.branch@rgc.or.tz',
            ]
        );

        $temekeBranch = Branch::query()->updateOrCreate(
            ['slug' => 'temeke-city-centre'],
            [
                'name' => 'Temeke City Centre',
                'region_id' => $darRegion->id,
                'district_id' => $temekeDistrict->id,
                'type' => 'district',
                'slug' => 'temeke-city-centre',
                'status' => 'active',
                'address' => 'Temeke, Dar es Salaam',
                'phone' => '255700200200',
                'email' => 'temeke.branch@rgc.or.tz',
            ]
        );

        $this->seedUser(
            name: 'RGC Regional Admin',
            email: 'regionaladmin@rgc.or.tz',
            role: 'regional_admin',
            region: $darRegion,
            district: $ilalaDistrict,
            branch: $ilalaBranch,
        );

        $this->seedUser(
            name: 'RGC District Admin',
            email: 'districtadmin@rgc.or.tz',
            role: 'district_admin',
            region: $darRegion,
            district: $temekeDistrict,
            branch: $temekeBranch,
        );

        $this->seedUser(
            name: 'RGC Branch Admin',
            email: 'branchadmin@rgc.or.tz',
            role: 'branch_admin',
            region: $darRegion,
            district: $temekeDistrict,
            branch: $headquarters,
        );
    }

    private function seedUser(string $name, string $email, string $role, Region $region, District $district, Branch $branch): void
    {
        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make(self::DEFAULT_PASSWORD),
                'role' => $role,
                'status' => 'active',
                'region_id' => $region->id,
                'district_id' => $district->id,
                'branch_id' => $branch->id,
                'church_id' => $branch->id,
                'locale' => 'en',
                'email_verified_at' => now(),
            ]
        );

        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        if (! $user->locale) {
            $user->forceFill(['locale' => 'en'])->save();
        }

        if (! $user->remember_token) {
            $user->forceFill(['remember_token' => Str::random(20)])->save();
        }

        $user->syncRoles([$role]);
    }
}
