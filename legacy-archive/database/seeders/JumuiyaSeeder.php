<?php

namespace Database\Seeders;

use App\Models\Jumuiya;
use Illuminate\Database\Seeder;

class JumuiyaSeeder extends Seeder
{
    public function run(): void
    {
        $jumuiyas = [
            [
                'name' => 'Jumuiya RGC',
                'slug' => 'jumuiya-agape',
                'description' => 'Jumuiya ya RGC - upendo wa Mungu',
                'location' => 'Eneo la Kati - Mbezi Beach',
                'is_active' => true,
            ],
            [
                'name' => 'Jumuiya Imani',
                'slug' => 'jumuiya-imani',
                'description' => 'Jumuiya ya Imani - nguvu ya kuamini',
                'location' => 'Eneo la Kaskazini - Mikocheni',
                'is_active' => true,
            ],
            [
                'name' => 'Jumuiya Tumaini',
                'slug' => 'jumuiya-tumaini',
                'description' => 'Jumuiya ya Tumaini - ujasiri katika Bwana',
                'location' => 'Eneo la Kusini - Kinondoni',
                'is_active' => true,
            ],
            [
                'name' => 'Jumuiya Neema',
                'slug' => 'jumuiya-neema',
                'description' => 'Jumuiya ya Neema - baraka za Mungu',
                'location' => 'Eneo la Mashariki - Ubungo',
                'is_active' => true,
            ],
            [
                'name' => 'Jumuiya Baraka',
                'slug' => 'jumuiya-baraka',
                'description' => 'Jumuiya ya Baraka - neema na rehema',
                'location' => 'Eneo la Magharibi - Tegeta',
                'is_active' => true,
            ],
        ];

        foreach ($jumuiyas as $jumuiya) {
            Jumuiya::firstOrCreate(
                ['slug' => $jumuiya['slug']],
                $jumuiya
            );
        }

        $this->command->info('Jumuiyas seeded successfully! Total: ' . count($jumuiyas));
    }
}
