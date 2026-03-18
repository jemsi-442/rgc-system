<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TanzaniaRegionDistrictSeeder::class,
            RgcRolePermissionSeeder::class,
            RgcSuperAdminSeeder::class,
        ]);

        $this->command->info('RGC platform seeded successfully.');
        $this->command->info('Super Admin: superadmin@rgc.or.tz / ChangeMe123!');
    }
}
