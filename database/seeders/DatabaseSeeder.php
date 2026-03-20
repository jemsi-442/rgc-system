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
        $this->command->info('Super admin bootstrap account seeded. Configure or rotate the credentials through environment setup before production use.');
    }
}
