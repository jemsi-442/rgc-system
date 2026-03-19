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
            RgcRoleDashboardSeeder::class,
        ]);

        $this->command->info('RGC platform seeded successfully.');
        $this->command->info('Super Admin: superadmin@rgc.or.tz / ChangeMe123!');
        $this->command->info('Regional Admin: regionaladmin@rgc.or.tz / ChangeMe123!');
        $this->command->info('District Admin: districtadmin@rgc.or.tz / ChangeMe123!');
        $this->command->info('Branch Admin: branchadmin@rgc.or.tz / ChangeMe123!');
    }
}
