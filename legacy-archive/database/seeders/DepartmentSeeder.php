<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Uhasibu',
                'slug' => 'uhasibu',
                'description' => 'Idara ya Uhasibu - Inashughulikia mambo yote ya fedha na hesabu',
                'is_active' => true,
            ],
            [
                'name' => 'Muziki',
                'slug' => 'muziki',
                'description' => 'Idara ya Muziki - Kwaya na huduma za uimbaji',
                'is_active' => true,
            ],
            [
                'name' => 'Ujenzi',
                'slug' => 'ujenzi',
                'description' => 'Idara ya Ujenzi - Miradi ya ujenzi na ukarabati',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $departmentData) {
            Department::updateOrCreate(
                ['slug' => $departmentData['slug']],
                $departmentData
            );
        }

        $this->command->info('Departments seeded successfully!');
    }
}
