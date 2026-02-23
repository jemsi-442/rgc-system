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
            'Arusha' => ['Arusha City', 'Arumeru', 'Karatu', 'Longido', 'Monduli', 'Ngorongoro'],
            'Dar es Salaam' => ['Ilala', 'Kinondoni', 'Temeke', 'Ubungo', 'Kigamboni'],
            'Dodoma' => ['Dodoma City', 'Bahi', 'Chamwino', 'Chemba', 'Kondoa', 'Mpwapwa'],
            'Geita' => ['Geita', 'Bukombe', 'Chato', 'Mbogwe', 'Nyanghwale'],
            'Iringa' => ['Iringa Urban', 'Iringa Rural', 'Kilolo', 'Mafinga', 'Mufindi'],
            'Kagera' => ['Bukoba Urban', 'Bukoba Rural', 'Karagwe', 'Kyerwa', 'Missenyi', 'Muleba', 'Ngara'],
            'Katavi' => ['Mpanda Urban', 'Mpanda Rural', 'Mlele', 'Tanganyika'],
            'Kigoma' => ['Kigoma Urban', 'Kigoma Rural', 'Kasulu', 'Kibondo', 'Buhigwe', 'Uvinza'],
            'Kilimanjaro' => ['Moshi Urban', 'Moshi Rural', 'Hai', 'Rombo', 'Same', 'Siha'],
            'Lindi' => ['Lindi Urban', 'Kilwa', 'Liwale', 'Nachingwea', 'Ruangwa'],
            'Manyara' => ['Babati Urban', 'Babati Rural', 'Hanang', 'Kiteto', 'Mbulu', 'Simanjiro'],
            'Mara' => ['Musoma Urban', 'Musoma Rural', 'Bunda', 'Butiama', 'Rorya', 'Serengeti', 'Tarime'],
            'Mbeya' => ['Mbeya City', 'Mbeya Rural', 'Busokelo', 'Chunya', 'Kyela', 'Mbarali', 'Rungwe'],
            'Morogoro' => ['Morogoro Urban', 'Morogoro Rural', 'Gairo', 'Ifakara', 'Kilombero', 'Kilosa', 'Mvomero', 'Ulanga'],
            'Mtwara' => ['Mtwara Urban', 'Mtwara Rural', 'Masasi', 'Nanyumbu', 'Newala', 'Tandahimba'],
            'Mwanza' => ['Nyamagana', 'Ilemela', 'Kwimba', 'Magu', 'Misungwi', 'Sengerema', 'Ukerewe'],
            'Njombe' => ['Njombe Urban', 'Njombe Rural', 'Ludewa', 'Makambako', 'Wanging’ombe'],
            'Pwani' => ['Bagamoyo', 'Kibaha', 'Kibiti', 'Kisarawe', 'Mafia', 'Mkuranga', 'Rufiji'],
            'Rukwa' => ['Sumbawanga Urban', 'Sumbawanga Rural', 'Kalambo', 'Nkasi'],
            'Ruvuma' => ['Songea Urban', 'Songea Rural', 'Mbinga', 'Namtumbo', 'Nyasa', 'Tunduru'],
            'Shinyanga' => ['Shinyanga Urban', 'Shinyanga Rural', 'Kahama', 'Kishapu', 'Msalala', 'Ushetu'],
            'Simiyu' => ['Bariadi', 'Busega', 'Itilima', 'Maswa', 'Meatu'],
            'Singida' => ['Singida Urban', 'Singida Rural', 'Ikungi', 'Iramba', 'Manyoni', 'Mkalama'],
            'Songwe' => ['Ileje', 'Mbozi', 'Momba', 'Songwe'],
            'Tabora' => ['Tabora Urban', 'Tabora Rural', 'Igunga', 'Kaliua', 'Nzega', 'Sikonge', 'Urambo'],
            'Tanga' => ['Tanga City', 'Handeni', 'Kilindi', 'Korogwe', 'Lushoto', 'Mkinga', 'Muheza', 'Pangani'],
            'Zanzibar North' => ['Kaskazini A', 'Kaskazini B'],
            'Zanzibar South and Central' => ['Kusini', 'Kati'],
            'Zanzibar Urban West' => ['Mjini', 'Magharibi A', 'Magharibi B'],
            'Pemba North' => ['Wete', 'Micheweni'],
            'Pemba South' => ['Chake Chake', 'Mkoani'],
        ];

        foreach ($data as $regionName => $districts) {
            $region = Region::firstOrCreate(['name' => $regionName], ['code' => null]);

            foreach ($districts as $districtName) {
                District::firstOrCreate([
                    'region_id' => $region->id,
                    'name' => $districtName,
                ]);
            }
        }
    }
}
