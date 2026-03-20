<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\District;
use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class RgcSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $region = Region::query()->where('name', 'Dar es Salaam')->firstOrFail();
        $email = (string) env('RGC_SUPER_ADMIN_EMAIL', 'superadmin@rgc.or.tz');
        $password = (string) env('RGC_SUPER_ADMIN_PASSWORD', '');

        if ($password === '') {
            if (app()->environment(['local', 'testing'])) {
                $password = 'ChangeMe123!';
            } else {
                throw new RuntimeException('Set RGC_SUPER_ADMIN_PASSWORD before seeding this environment.');
            }
        }

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
            ['email' => $email],
            [
                'name' => 'RGC Super Admin',
                'password' => Hash::make($password),
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
