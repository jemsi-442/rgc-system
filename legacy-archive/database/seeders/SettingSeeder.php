<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Church Information
            [
                'key' => 'church_name',
                'value' => 'KANISA LA KIINJILI LA KILUTHERI TANZANIA',
                'type' => 'text',
                'group' => 'church_info',
            ],
            [
                'key' => 'diocese',
                'value' => 'DAYOSISI YA MASHARIKI NA PWANI',
                'type' => 'text',
                'group' => 'church_info',
            ],
            [
                'key' => 'district',
                'value' => 'JIMBO LA MAGHARIBI',
                'type' => 'text',
                'group' => 'church_info',
            ],
            [
                'key' => 'parish',
                'value' => 'USHARIKA WA MAKABE',
                'type' => 'text',
                'group' => 'church_info',
            ],
            [
                'key' => 'mtaa',
                'value' => 'MTAA WA RGC',
                'type' => 'text',
                'group' => 'church_info',
            ],
            [
                'key' => 'church_full_name',
                'value' => 'KANISA LA KIINJILI LA KILUTHERI TANZANIA - MTAA WA RGC',
                'type' => 'text',
                'group' => 'church_info',
            ],

            // Contact Information
            [
                'key' => 'address',
                'value' => 'Dar es Salaam, Tanzania',
                'type' => 'text',
                'group' => 'contact',
            ],
            [
                'key' => 'phone',
                'value' => '+255 712 345 678',
                'type' => 'text',
                'group' => 'contact',
            ],
            [
                'key' => 'church_phone',
                'value' => '+255 712 345 678',
                'type' => 'text',
                'group' => 'contact',
            ],
            [
                'key' => 'email',
                'value' => 'info@rgc.org',
                'type' => 'email',
                'group' => 'contact',
            ],
            [
                'key' => 'church_email',
                'value' => 'info@rgc.org',
                'type' => 'email',
                'group' => 'contact',
            ],
            [
                'key' => 'website',
                'value' => 'www.agapekanisa.or.tz',
                'type' => 'url',
                'group' => 'contact',
            ],

            // Financial Settings
            [
                'key' => 'diocese_percentage',
                'value' => '8',
                'type' => 'number',
                'group' => 'financial',
            ],
            [
                'key' => 'currency',
                'value' => 'TZS',
                'type' => 'text',
                'group' => 'financial',
            ],
            [
                'key' => 'currency_symbol',
                'value' => 'Tsh',
                'type' => 'text',
                'group' => 'financial',
            ],

            // System Settings
            [
                'key' => 'timezone',
                'value' => 'Africa/Dar_es_Salaam',
                'type' => 'text',
                'group' => 'system',
            ],
            [
                'key' => 'date_format',
                'value' => 'd/m/Y',
                'type' => 'text',
                'group' => 'system',
            ],
            [
                'key' => 'time_format',
                'value' => 'H:i',
                'type' => 'text',
                'group' => 'system',
            ],
            [
                'key' => 'language',
                'value' => 'sw',
                'type' => 'text',
                'group' => 'system',
            ],

            // Notification Settings
            [
                'key' => 'email_notifications',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notifications',
            ],
            [
                'key' => 'sms_notifications',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'notifications',
            ],

            // Report Settings
            [
                'key' => 'financial_year_start',
                'value' => '01-01',
                'type' => 'text',
                'group' => 'reports',
            ],
            [
                'key' => 'report_logo',
                'value' => '',
                'type' => 'image',
                'group' => 'reports',
            ],

            // Service Times
            [
                'key' => 'sunday_service_time',
                'value' => '09:00',
                'type' => 'time',
                'group' => 'services',
            ],
            [
                'key' => 'midweek_service_time',
                'value' => '18:00',
                'type' => 'time',
                'group' => 'services',
            ],
            [
                'key' => 'bible_study_day',
                'value' => 'Jumatano',
                'type' => 'text',
                'group' => 'services',
            ],
            [
                'key' => 'bible_study_time',
                'value' => '18:00',
                'type' => 'time',
                'group' => 'services',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Settings seeded successfully! Total: ' . \count($settings));
    }
}
