<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'super_admin',
            'regional_admin',
            'district_admin',
            'branch_admin',
            'bishop',
            'pastor',
            'assistant_pastor',
            'accountant',
            'evangelist',
            'choir_leader',
            'youth_leader',
            'member',
            'admin',
            'user',
        ] as $name) {
            Role::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }
    }
}
