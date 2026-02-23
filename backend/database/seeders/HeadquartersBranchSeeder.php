<?php

namespace Database\Seeders;

use App\Models\Church;
use App\Models\District;
use App\Models\Region;
use Illuminate\Database\Seeder;

class HeadquartersBranchSeeder extends Seeder
{
    public function run(): void
    {
        $region = Region::firstOrCreate(['name' => 'Dar es Salaam'], ['code' => null]);

        $district = District::firstOrCreate([
            'region_id' => $region->id,
            'name' => 'Temeke',
        ]);

        Church::updateOrCreate(
            ['name' => 'Toangoma'],
            [
                'region_id' => $region->id,
                'district_id' => $district->id,
                'type' => 'headquarters',
                'address' => 'Toangoma, Temeke, Dar es Salaam',
                'status' => 'active',
            ]
        );
    }
}
