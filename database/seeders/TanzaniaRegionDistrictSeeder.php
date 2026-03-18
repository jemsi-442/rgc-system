<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Region;
use Illuminate\Database\Seeder;

class TanzaniaRegionDistrictSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Arusha' => ['code' => 'ARU', 'districts' => ['Arusha City', 'Arusha DC', 'Karatu', 'Longido', 'Meru', 'Monduli', 'Ngorongoro']],
            'Dar es Salaam' => ['code' => 'DSM', 'districts' => ['Ilala', 'Kinondoni', 'Kigamboni', 'Temeke', 'Ubungo']],
            'Dodoma' => ['code' => 'DOD', 'districts' => ['Bahi', 'Chamwino', 'Chemba', 'Dodoma City', 'Kondoa', 'Kongwa', 'Mpwapwa']],
            'Geita' => ['code' => 'GEI', 'districts' => ['Bukombe', 'Chato', 'Geita', 'Mbogwe', 'Nyang’hwale']],
            'Iringa' => ['code' => 'IRI', 'districts' => ['Iringa DC', 'Iringa MC', 'Kilolo', 'Mafinga TC', 'Mufindi']],
            'Kagera' => ['code' => 'KAG', 'districts' => ['Biharamulo', 'Bukoba DC', 'Bukoba MC', 'Karagwe', 'Kyerwa', 'Missenyi', 'Muleba', 'Ngara']],
            'Katavi' => ['code' => 'KAT', 'districts' => ['Mlele', 'Mpanda DC', 'Mpanda TC', 'Tanganyika']],
            'Kigoma' => ['code' => 'KIG', 'districts' => ['Buhigwe', 'Kakonko', 'Kasulu DC', 'Kasulu TC', 'Kibondo', 'Kigoma DC', 'Kigoma Ujiji', 'Uvinza']],
            'Kilimanjaro' => ['code' => 'KIL', 'districts' => ['Hai', 'Moshi DC', 'Moshi MC', 'Mwanga', 'Rombo', 'Same', 'Siha']],
            'Lindi' => ['code' => 'LIN', 'districts' => ['Kilwa', 'Lindi DC', 'Lindi MC', 'Liwale', 'Nachingwea', 'Ruangwa']],
            'Manyara' => ['code' => 'MNY', 'districts' => ['Babati DC', 'Babati TC', 'Hanang', 'Kiteto', 'Mbulu', 'Simanjiro']],
            'Mara' => ['code' => 'MAR', 'districts' => ['Bunda', 'Butiama', 'Musoma DC', 'Musoma MC', 'Rorya', 'Serengeti', 'Tarime']],
            'Mbeya' => ['code' => 'MBE', 'districts' => ['Busokelo', 'Chunya', 'Kyela', 'Mbarali', 'Mbeya City', 'Mbeya DC', 'Rungwe']],
            'Morogoro' => ['code' => 'MOR', 'districts' => ['Gairo', 'Ifakara TC', 'Kilombero', 'Kilosa', 'Morogoro DC', 'Morogoro MC', 'Mvomero', 'Ulanga']],
            'Mtwara' => ['code' => 'MTW', 'districts' => ['Masasi DC', 'Masasi TC', 'Mtwara DC', 'Mtwara MC', 'Nanyamba TC', 'Nanyumbu', 'Newala', 'Tandahimba']],
            'Mwanza' => ['code' => 'MWZ', 'districts' => ['Ilemela', 'Kwimba', 'Magu', 'Misungwi', 'Nyamagana', 'Sengerema', 'Ukerewe']],
            'Njombe' => ['code' => 'NJO', 'districts' => ['Ludewa', 'Makambako TC', 'Makete', 'Njombe DC', 'Njombe TC', 'Wanging’ombe']],
            'Pwani' => ['code' => 'PWA', 'districts' => ['Bagamoyo', 'Kibaha DC', 'Kibaha TC', 'Kisarawe', 'Mafia', 'Mkuranga', 'Rufiji']],
            'Rukwa' => ['code' => 'RUK', 'districts' => ['Kalambo', 'Nkasi', 'Sumbawanga DC', 'Sumbawanga MC']],
            'Ruvuma' => ['code' => 'RUV', 'districts' => ['Mbinga DC', 'Mbinga TC', 'Namtumbo', 'Nyasa', 'Songea DC', 'Songea MC', 'Tunduru']],
            'Shinyanga' => ['code' => 'SHY', 'districts' => ['Kahama MC', 'Kahama TC', 'Kishapu', 'Shinyanga DC', 'Shinyanga MC']],
            'Simiyu' => ['code' => 'SIM', 'districts' => ['Bariadi', 'Busega', 'Itilima', 'Maswa', 'Meatu']],
            'Singida' => ['code' => 'SIN', 'districts' => ['Ikungi', 'Iramba', 'Manyoni', 'Mkalama', 'Singida DC', 'Singida MC']],
            'Songwe' => ['code' => 'SON', 'districts' => ['Ileje', 'Mbozi', 'Momba', 'Songwe', 'Tunduma TC']],
            'Tabora' => ['code' => 'TAB', 'districts' => ['Igunga', 'Kaliua', 'Nzega DC', 'Nzega TC', 'Sikonge', 'Tabora MC', 'Urambo', 'Uyui']],
            'Tanga' => ['code' => 'TAN', 'districts' => ['Handeni DC', 'Handeni TC', 'Kilindi', 'Korogwe DC', 'Korogwe TC', 'Lushoto', 'Muheza', 'Mkinga', 'Pangani', 'Tanga CC']],
            'Kaskazini Unguja' => ['code' => 'KUN', 'districts' => ['Kaskazini A', 'Kaskazini B']],
            'Kusini Unguja' => ['code' => 'KSU', 'districts' => ['Kati', 'Kusini']],
            'Mjini Magharibi' => ['code' => 'MMG', 'districts' => ['Magharibi A', 'Magharibi B', 'Mjini']],
            'Kaskazini Pemba' => ['code' => 'KPE', 'districts' => ['Micheweni', 'Wete']],
            'Kusini Pemba' => ['code' => 'KSP', 'districts' => ['Chake Chake', 'Mkoani']],
        ];

        foreach ($data as $regionName => $payload) {
            $region = Region::query()->updateOrCreate(
                ['name' => $regionName],
                [
                    'code' => $payload['code'],
                    'is_zanzibar' => str_contains($regionName, 'Unguja') || str_contains($regionName, 'Pemba') || $regionName === 'Mjini Magharibi',
                ]
            );

            foreach ($payload['districts'] as $districtName) {
                District::query()->updateOrCreate(
                    ['region_id' => $region->id, 'name' => $districtName],
                    [
                        'region_id' => $region->id,
                        'name' => $districtName,
                    ]
                );
            }
        }
    }
}
