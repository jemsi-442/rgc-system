<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Nauli -Bible Study',
                'description' => 'Nauli za Bible Study',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Posho -Mwinjilisti',
                'description' => 'Posho ya Mwinjilisti',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Posho Mhudumu',
                'description' => 'Posho ya Mhudumu',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Posho -Wahubiri',
                'description' => 'Posho ya Wahubiri',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Posho Mlinzi',
                'description' => 'Posho ya Mlinzi',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Posho Mbalimbali',
                'description' => 'Posho mbalimbali',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Posho -Mwl K/kuu',
                'description' => 'Posho ya Mwalimu Kanisa Kuu',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Posho-Mwl K/Vijana',
                'description' => 'Posho ya Mwalimu Kanisa Vijana',
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'Nauli-Bank',
                'description' => 'Nauli za kwenda benki',
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'Nauli Ubebaji Vyombo',
                'description' => 'Nauli za ubebaji wa vyombo',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'Nauli mbalimbali',
                'description' => 'Nauli mbalimbali',
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'name' => 'Maji kunywa/usafi/Chakula',
                'description' => 'Maji ya kunywa, usafi na chakula',
                'is_active' => true,
                'sort_order' => 12,
            ],
            [
                'name' => 'Matumizi -Mtaa',
                'description' => 'Matumizi ya mtaa',
                'is_active' => true,
                'sort_order' => 13,
            ],
            [
                'name' => '8% ya Jimbo',
                'description' => '8% ya mapato yanayotolewa kwa Jimbo',
                'is_active' => true,
                'sort_order' => 14,
            ],
            [
                'name' => 'Umeme',
                'description' => 'Gharama za umeme',
                'is_active' => true,
                'sort_order' => 15,
            ],
            [
                'name' => 'Pango la ofisi',
                'description' => 'Pango la ofisi',
                'is_active' => true,
                'sort_order' => 16,
            ],
            [
                'name' => 'Stationery',
                'description' => 'Vifaa vya ofisi',
                'is_active' => true,
                'sort_order' => 17,
            ],
            [
                'name' => 'Matumizi mengineyo',
                'description' => 'Matumizi mengine mbalimbali',
                'is_active' => true,
                'sort_order' => 18,
            ],
            [
                'name' => 'Huduma za Kichungaji',
                'description' => 'Huduma za kichungaji',
                'is_active' => true,
                'sort_order' => 19,
            ],
            [
                'name' => 'Vikao/Semina/kongamano',
                'description' => 'Vikao, semina na kongamano',
                'is_active' => true,
                'sort_order' => 20,
            ],
            [
                'name' => 'Marejesho-mkopo',
                'description' => 'Marejesho ya mikopo',
                'is_active' => true,
                'sort_order' => 21,
            ],
            [
                'name' => 'Mkesha',
                'description' => 'Gharama za mkesha',
                'is_active' => true,
                'sort_order' => 22,
            ],
            [
                'name' => 'Ujenzi',
                'description' => 'Gharama za ujenzi',
                'is_active' => true,
                'sort_order' => 23,
            ],
            [
                'name' => 'Ujenzi -Ofisi ya Jimbo',
                'description' => 'Ujenzi wa ofisi ya Jimbo',
                'is_active' => true,
                'sort_order' => 24,
            ],
            [
                'name' => 'Ununuzi wa viti',
                'description' => 'Ununuzi wa viti',
                'is_active' => true,
                'sort_order' => 25,
            ],
            [
                'name' => 'mikaeli na watoto',
                'description' => 'Mikaeli na watoto',
                'is_active' => true,
                'sort_order' => 26,
            ],
            [
                'name' => 'Tamasha vijana',
                'description' => 'Tamasha la vijana',
                'is_active' => true,
                'sort_order' => 27,
            ],
            [
                'name' => 'Gharama ya majoho',
                'description' => 'Gharama za majoho',
                'is_active' => true,
                'sort_order' => 28,
            ],
            [
                'name' => 'Ujenzi madhabahu',
                'description' => 'Ujenzi wa madhabahu',
                'is_active' => true,
                'sort_order' => 29,
            ],
            [
                'name' => 'Bango',
                'description' => 'Gharama za bango',
                'is_active' => true,
                'sort_order' => 30,
            ],
            [
                'name' => 'Ununuzi vyombo vya muziki',
                'description' => 'Ununuzi wa vyombo vya muziki',
                'is_active' => true,
                'sort_order' => 31,
            ],
            [
                'name' => 'Watoto-safari',
                'description' => 'Safari za watoto',
                'is_active' => true,
                'sort_order' => 32,
            ],
            [
                'name' => 'Kwaya kuu',
                'description' => 'Gharama za kwaya kuu',
                'is_active' => true,
                'sort_order' => 33,
            ],
            [
                'name' => 'Wahitaji',
                'description' => 'Msaada kwa wahitaji',
                'is_active' => true,
                'sort_order' => 34,
            ],
            [
                'name' => 'Posho store keeper',
                'description' => 'Posho ya store keeper',
                'is_active' => true,
                'sort_order' => 35,
            ],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        $this->command->info('Expense categories seeded successfully! Total: ' . \count($categories));
    }
}
