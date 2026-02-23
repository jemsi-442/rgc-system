<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'pallajemsingyo@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Jay442tx'),
                'role' => 'super_admin',
            ]
        );

        if (method_exists($user, 'assignRole')) {
            $user->syncRoles(['super_admin']);
        }
    }
}
