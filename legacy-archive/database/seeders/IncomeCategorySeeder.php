<?php

namespace Database\Seeders;

use App\Models\IncomeCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IncomeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'code' => 'M0001',
                'name' => 'SADAKA YA MWAKA MPYA',
                'description' => 'Sadaka ya mwaka mpya',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'M0002',
                'name' => 'SHUKRANI YA WIKI (kawaida) na colect',
                'description' => 'Shukrani ya wiki ya kawaida na collection',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'M0003',
                'name' => 'SADAKA YA AHADI',
                'description' => 'Sadaka ya ahadi',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'code' => 'M0004',
                'name' => 'SHULE YA JUMAPILI',
                'description' => 'Sadaka ya shule ya jumapili',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'code' => 'M0005',
                'name' => 'SADAKA YA MAVUNO',
                'description' => 'Sadaka ya mavuno',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'code' => 'M0006',
                'name' => 'SADAKA YA PASAKA/kupaa',
                'description' => 'Sadaka ya Pasaka na kupaa',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'code' => 'M0007',
                'name' => 'SADAKA YA SHUKRANI AINA ZOTE',
                'description' => 'Sadaka ya shukrani aina zote',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'code' => 'M0008',
                'name' => 'SADAKA YA UBATIZO',
                'description' => 'Sadaka ya ubatizo',
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'code' => 'M0009',
                'name' => 'SHUKRANI YA KIPAIMARA',
                'description' => 'Shukrani ya kipaimara',
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'code' => 'M0010',
                'name' => 'SHUKRANI YA NDOA',
                'description' => 'Shukrani ya ndoa',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'code' => 'M0011',
                'name' => 'SADAKA FUNGU LA KUMI',
                'description' => 'Sadaka fungu la kumi (Zaka)',
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'code' => 'M0012',
                'name' => 'SHUKRANI YA CCB',
                'description' => 'Shukrani ya CCB (Chama cha Biblia)',
                'is_active' => true,
                'sort_order' => 12,
            ],
            [
                'code' => 'M0013',
                'name' => 'SADAKA YA JENGO',
                'description' => 'Sadaka ya ujenzi wa jengo',
                'is_active' => true,
                'sort_order' => 13,
            ],
            [
                'code' => 'M0014',
                'name' => 'MICHANGO MBALIMBALI',
                'description' => 'Michango mbalimbali',
                'is_active' => true,
                'sort_order' => 14,
            ],
            [
                'code' => 'M0015',
                'name' => 'SADAKA YA NYUMBA KWA NYUMBA',
                'description' => 'Sadaka ya nyumba kwa nyumba',
                'is_active' => true,
                'sort_order' => 15,
            ],
            [
                'code' => 'M0016',
                'name' => 'SADAKA YA MORNING & EVENING GLORY',
                'description' => 'Sadaka ya Morning & Evening Glory',
                'is_active' => true,
                'sort_order' => 16,
            ],
            [
                'code' => 'M0017',
                'name' => 'HARAMBEE na mnada',
                'description' => 'Harambee na mnada',
                'is_active' => true,
                'sort_order' => 17,
            ],
            [
                'code' => 'M0018',
                'name' => 'SADAKA YA MFUKO WA ELIMU',
                'description' => 'Sadaka ya mfuko wa elimu',
                'is_active' => true,
                'sort_order' => 18,
            ],
            [
                'code' => 'M0019',
                'name' => 'SADAKA MAALUM- MAKAO MAKUU, VIJANA, KAZI ZA MISIONI,AKINA MAMA, VYAMA VYA INJILI, MSAMARIA MWEMA',
                'description' => 'Sadaka maalum kwa Makao Makuu, Vijana, Kazi za Misioni, Akina Mama, Vyama vya Injili, Msamaria Mwema',
                'is_active' => true,
                'sort_order' => 19,
            ],
            [
                'code' => 'M0020',
                'name' => 'SADAKA ZA KAZI ZA UMOJA _JIMBO NA DAYOSISI',
                'description' => 'Sadaka za kazi za umoja wa Jimbo na Dayosisi',
                'is_active' => true,
                'sort_order' => 20,
            ],
            [
                'code' => 'M0021',
                'name' => 'SADAKA YA KRISMAS',
                'description' => 'Sadaka ya Krismas',
                'is_active' => true,
                'sort_order' => 21,
            ],
        ];

        foreach ($categories as $category) {
            IncomeCategory::updateOrCreate(
                ['code' => $category['code']],
                $category
            );
        }

        $this->command->info('Income categories seeded successfully! Total: ' . \count($categories));
    }
}
