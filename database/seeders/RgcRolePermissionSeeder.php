<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RgcRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage-regions',
            'manage-districts',
            'manage-branches',
            'manage-users',
            'manage-announcements',
            'manage-events',
            'manage-offerings',
            'manage-expenses',
            'manage-slider',
            'view-announcements',
            'branch-chat',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $map = [
            'super_admin' => $permissions,
            'regional_admin' => ['manage-districts', 'manage-branches', 'manage-users', 'manage-announcements', 'manage-events', 'view-announcements', 'branch-chat'],
            'district_admin' => ['manage-branches', 'manage-users', 'manage-announcements', 'manage-events', 'view-announcements', 'branch-chat'],
            'branch_admin' => ['manage-users', 'manage-announcements', 'manage-events', 'manage-offerings', 'manage-expenses', 'view-announcements', 'branch-chat'],
            'pastor' => ['view-announcements', 'branch-chat'],
            'bishop' => ['view-announcements', 'branch-chat'],
            'accountant' => ['manage-offerings', 'manage-expenses', 'view-announcements', 'branch-chat'],
            'member' => ['view-announcements', 'branch-chat'],
        ];

        foreach ($map as $roleName => $grants) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->syncPermissions($grants);
        }
    }
}
