<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\User;
use App\Models\Jumuiya;
use Carbon\Carbon;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates members with RGC-YYYY-NNNN format linked to users
     */
    public function run(): void
    {
        $year = date('Y');

        // Get Jumuiyas for assignment
        $jumuiyaRGC = Jumuiya::where('slug', 'jumuiya-agape')->first();
        $jumuiyaImani = Jumuiya::where('slug', 'jumuiya-imani')->first();
        $jumuiyaTumaini = Jumuiya::where('slug', 'jumuiya-tumaini')->first();
        $jumuiyaNeema = Jumuiya::where('slug', 'jumuiya-neema')->first();
        $jumuiyaBaraka = Jumuiya::where('slug', 'jumuiya-baraka')->first();

        // Get users by their member number emails
        $getMemberUser = function($number) use ($year) {
            return User::where('email', 'RGC-' . $year . '-' . sprintf('%04d', $number) . '@rgc.org')->first();
        };

        $members = [
            // Pastor - RGC-2025-0001
            [
                'member_number' => 'RGC-' . $year . '-0001',
                'envelope_number' => 'ENV' . $year . '0001',
                'first_name' => 'Joseph',
                'middle_name' => 'Elias',
                'last_name' => 'Mwakasege',
                'date_of_birth' => '1970-03-15',
                'gender' => 'Mme',
                'phone' => '+255712000001',
                'email' => 'mchungaji.mwakasege@gmail.com',
                'user_id' => $getMemberUser(1)?->id,
                'jumuiya_id' => $jumuiyaRGC?->id,
                'occupation' => 'Mchungaji',
                'address' => 'Mbezi Beach, Dar es Salaam',
                'house_number' => 'HSE-001',
                'block_number' => 'BLK-A',
                'city' => 'Dar es Salaam',
                'region' => 'Dar es Salaam',
                'baptism_date' => '1975-06-10',
                'confirmation_date' => '1983-05-20',
                'membership_date' => '2010-01-01',
                'marital_status' => 'Ameoa/Ameolewa',
                'spouse_name' => 'Martha Mwakasege',
                'spouse_phone' => '+255712000002',
                'children_info' => json_encode([
                    ['name' => 'Daniel Mwakasege', 'age' => 28],
                    ['name' => 'Esther Mwakasege', 'age' => 25]
                ]),
                'neighbor_name' => 'James Kimaro',
                'neighbor_phone' => '+255712000003',
                'church_elder' => 'Mzee Benjamin Moshi',
                'pledge_number' => 'PLD' . $year . '0001',
                'special_group' => 'Viongozi',
                'ministry_groups' => json_encode(['Uongozi', 'Mahubiri']),
                'id_number' => '19700315-00001-00001-01',
                'is_active' => true,
                'notes' => 'Mchungaji Mkuu wa Mtaa wa RGC',
            ],
            // Accountant - RGC-2025-0002
            [
                'member_number' => 'RGC-' . $year . '-0002',
                'envelope_number' => 'ENV' . $year . '0002',
                'first_name' => 'Grace',
                'middle_name' => 'Neema',
                'last_name' => 'Kimaro',
                'date_of_birth' => '1985-07-22',
                'gender' => 'Mke',
                'phone' => '+255754000001',
                'email' => 'grace.kimaro@gmail.com',
                'user_id' => $getMemberUser(2)?->id,
                'jumuiya_id' => $jumuiyaImani?->id,
                'occupation' => 'Mhasibu',
                'address' => 'Mikocheni, Dar es Salaam',
                'house_number' => 'HSE-205',
                'block_number' => 'BLK-B',
                'city' => 'Dar es Salaam',
                'region' => 'Dar es Salaam',
                'baptism_date' => '1990-08-15',
                'confirmation_date' => '1998-07-10',
                'membership_date' => '2015-03-20',
                'marital_status' => 'Ameoa/Ameolewa',
                'spouse_name' => 'Daniel Kimaro',
                'spouse_phone' => '+255754000002',
                'children_info' => json_encode([
                    ['name' => 'Faith Kimaro', 'age' => 10],
                    ['name' => 'Hope Kimaro', 'age' => 7]
                ]),
                'neighbor_name' => 'Anna Mtui',
                'neighbor_phone' => '+255754000003',
                'church_elder' => 'Mama Joyce Mlimba',
                'pledge_number' => 'PLD' . $year . '0002',
                'special_group' => 'Akina Mama',
                'ministry_groups' => json_encode(['Fedha', 'Kwaya ya Wanawake']),
                'id_number' => '19850722-00002-00002-02',
                'is_active' => true,
                'notes' => 'Mhasibu wa Kanisa',
            ],
            // Member 3 - RGC-2025-0003
            [
                'member_number' => 'RGC-' . $year . '-0003',
                'envelope_number' => 'ENV' . $year . '0003',
                'first_name' => 'John',
                'middle_name' => 'Peter',
                'last_name' => 'Mwangi',
                'date_of_birth' => '1985-03-15',
                'gender' => 'Mme',
                'phone' => '+255712345678',
                'email' => 'john.mwangi@email.com',
                'user_id' => $getMemberUser(3)?->id,
                'jumuiya_id' => $jumuiyaRGC?->id,
                'occupation' => 'Mwalimu',
                'address' => 'Mbezi Beach, Dar es Salaam',
                'house_number' => 'HSE-101',
                'block_number' => 'BLK-A',
                'city' => 'Dar es Salaam',
                'region' => 'Dar es Salaam',
                'baptism_date' => '1990-06-10',
                'confirmation_date' => '1998-05-20',
                'membership_date' => '2020-01-15',
                'marital_status' => 'Ameoa/Ameolewa',
                'spouse_name' => 'Mary Mwangi',
                'spouse_phone' => '+255712345679',
                'children_info' => json_encode([
                    ['name' => 'Grace Mwangi', 'age' => 12],
                    ['name' => 'David Mwangi', 'age' => 8]
                ]),
                'neighbor_name' => 'James Kamau',
                'neighbor_phone' => '+255712345680',
                'church_elder' => 'Mzee Joseph Kimani',
                'pledge_number' => 'PLD' . $year . '0003',
                'special_group' => 'Wazazi',
                'ministry_groups' => json_encode(['Kwaya Kuu', 'Bibilia']),
                'id_number' => '19850315-12345-00003-67',
                'is_active' => true,
                'notes' => 'Mwanachama mwenye bidii katika huduma za kanisa',
            ],
            // Member 4 - RGC-2025-0004
            [
                'member_number' => 'RGC-' . $year . '-0004',
                'envelope_number' => 'ENV' . $year . '0004',
                'first_name' => 'Sarah',
                'middle_name' => 'Elizabeth',
                'last_name' => 'Moshi',
                'date_of_birth' => '1990-07-22',
                'gender' => 'Mke',
                'phone' => '+255754321098',
                'email' => 'sarah.moshi@email.com',
                'user_id' => $getMemberUser(4)?->id,
                'jumuiya_id' => $jumuiyaTumaini?->id,
                'occupation' => 'Muuguzi',
                'address' => 'Kinondoni, Dar es Salaam',
                'house_number' => 'HSE-205',
                'block_number' => 'BLK-B',
                'city' => 'Dar es Salaam',
                'region' => 'Dar es Salaam',
                'baptism_date' => '1995-08-15',
                'confirmation_date' => '2003-07-10',
                'membership_date' => '2018-03-20',
                'marital_status' => 'Ameoa/Ameolewa',
                'spouse_name' => 'Daniel Moshi',
                'spouse_phone' => '+255754321099',
                'children_info' => json_encode([
                    ['name' => 'Emmanuel Moshi', 'age' => 5],
                    ['name' => 'Esther Moshi', 'age' => 3]
                ]),
                'neighbor_name' => 'Anna Mtui',
                'neighbor_phone' => '+255754321100',
                'church_elder' => 'Mama Grace Njau',
                'pledge_number' => 'PLD' . $year . '0004',
                'special_group' => 'Akina Mama',
                'ministry_groups' => json_encode(['Kwaya ya Wanawake', 'Shule ya Jumapili']),
                'id_number' => '19900722-23456-00004-78',
                'is_active' => true,
                'notes' => 'Msaidizi wa kundi la uuguzi',
            ],
            // Member 5 - RGC-2025-0005
            [
                'member_number' => 'RGC-' . $year . '-0005',
                'envelope_number' => 'ENV' . $year . '0005',
                'first_name' => 'Emmanuel',
                'middle_name' => 'Joseph',
                'last_name' => 'Komba',
                'date_of_birth' => '2000-11-05',
                'gender' => 'Mme',
                'phone' => '+255765432109',
                'email' => 'emmanuel.komba@email.com',
                'user_id' => $getMemberUser(5)?->id,
                'jumuiya_id' => $jumuiyaNeema?->id,
                'occupation' => 'Mwanafunzi - Chuo Kikuu',
                'address' => 'Ubungo, Dar es Salaam',
                'house_number' => 'HSE-310',
                'block_number' => 'BLK-C',
                'city' => 'Dar es Salaam',
                'region' => 'Dar es Salaam',
                'baptism_date' => '2005-12-25',
                'confirmation_date' => '2013-06-15',
                'membership_date' => '2019-09-01',
                'marital_status' => 'Sijaoa/Sijaolewa',
                'children_info' => null,
                'neighbor_name' => 'Peter Nyerere',
                'neighbor_phone' => '+255765432110',
                'church_elder' => 'Mzee David Lyimo',
                'pledge_number' => 'PLD' . $year . '0005',
                'special_group' => 'Vijana',
                'ministry_groups' => json_encode(['Kwaya ya Vijana', 'ICT Ministry']),
                'id_number' => '20001105-34567-00005-89',
                'is_active' => true,
                'notes' => 'Kiongozi wa vijana, msaidizi wa ICT',
            ],
            // Member 6 - RGC-2025-0006
            [
                'member_number' => 'RGC-' . $year . '-0006',
                'envelope_number' => 'ENV' . $year . '0006',
                'first_name' => 'Agnes',
                'middle_name' => 'Maria',
                'last_name' => 'Massawe',
                'date_of_birth' => '1975-02-14',
                'gender' => 'Mke',
                'phone' => '+255713456789',
                'email' => 'agnes.massawe@email.com',
                'user_id' => $getMemberUser(6)?->id,
                'jumuiya_id' => $jumuiyaBaraka?->id,
                'occupation' => 'Mfanyabiashara',
                'address' => 'Tegeta, Dar es Salaam',
                'house_number' => 'HSE-105',
                'block_number' => 'BLK-A',
                'city' => 'Dar es Salaam',
                'region' => 'Dar es Salaam',
                'baptism_date' => '1980-04-20',
                'confirmation_date' => '1988-05-15',
                'membership_date' => '2015-07-10',
                'marital_status' => 'Mjane',
                'children_info' => json_encode([
                    ['name' => 'Michael Massawe', 'age' => 25],
                    ['name' => 'Ruth Massawe', 'age' => 22],
                    ['name' => 'Joseph Massawe', 'age' => 19]
                ]),
                'neighbor_name' => 'Elizabeth Mwakasege',
                'neighbor_phone' => '+255713456790',
                'church_elder' => 'Mama Joyce Mlimba',
                'pledge_number' => 'PLD' . $year . '0006',
                'special_group' => 'Wajane',
                'ministry_groups' => json_encode(['Kwaya Kuu', 'Huduma za Wahitaji']),
                'id_number' => '19750214-45678-00006-90',
                'is_active' => true,
                'notes' => 'Mchungaji msaidizi wa kundi la wajane',
            ],
            // Member 7 - RGC-2025-0007
            [
                'member_number' => 'RGC-' . $year . '-0007',
                'envelope_number' => 'ENV' . $year . '0007',
                'first_name' => 'Joshua',
                'middle_name' => 'Samuel',
                'last_name' => 'Mwakyembe',
                'date_of_birth' => '1968-09-30',
                'gender' => 'Mme',
                'phone' => '+255724567890',
                'email' => 'joshua.mwakyembe@email.com',
                'user_id' => $getMemberUser(7)?->id,
                'jumuiya_id' => $jumuiyaRGC?->id,
                'occupation' => 'Mhandisi',
                'address' => 'Mbezi Beach, Dar es Salaam',
                'house_number' => 'HSE-401',
                'block_number' => 'BLK-D',
                'city' => 'Dar es Salaam',
                'region' => 'Dar es Salaam',
                'baptism_date' => '1973-10-15',
                'confirmation_date' => '1981-08-20',
                'membership_date' => '2010-02-28',
                'marital_status' => 'Ameoa/Ameolewa',
                'spouse_name' => 'Rebecca Mwakyembe',
                'spouse_phone' => '+255724567891',
                'children_info' => json_encode([
                    ['name' => 'Samuel Mwakyembe', 'age' => 30],
                    ['name' => 'Deborah Mwakyembe', 'age' => 27],
                    ['name' => 'Jonathan Mwakyembe', 'age' => 24]
                ]),
                'neighbor_name' => 'Francis Mhina',
                'neighbor_phone' => '+255724567892',
                'church_elder' => 'Mzee Benjamin Moshi',
                'pledge_number' => 'PLD' . $year . '0007',
                'special_group' => 'Wazee',
                'ministry_groups' => json_encode(['Kwaya Kuu', 'Ushauri wa Ujenzi']),
                'id_number' => '19680930-56789-00007-01',
                'is_active' => true,
                'notes' => 'Mzee wa kanisa, mshauri wa miradi ya ujenzi',
            ],
            // Member 8 - RGC-2025-0008
            [
                'member_number' => 'RGC-' . $year . '-0008',
                'envelope_number' => 'ENV' . $year . '0008',
                'first_name' => 'Grace',
                'middle_name' => 'Neema',
                'last_name' => 'Ndunguru',
                'date_of_birth' => '1995-05-18',
                'gender' => 'Mke',
                'phone' => '+255755678901',
                'email' => 'grace.ndunguru@email.com',
                'user_id' => $getMemberUser(8)?->id,
                'jumuiya_id' => $jumuiyaImani?->id,
                'occupation' => 'Mkaguzi wa Hesabu',
                'address' => 'Mikocheni, Dar es Salaam',
                'house_number' => 'HSE-210',
                'block_number' => 'BLK-B',
                'city' => 'Dar es Salaam',
                'region' => 'Dar es Salaam',
                'baptism_date' => '2000-06-25',
                'confirmation_date' => '2008-09-14',
                'membership_date' => '2021-11-05',
                'marital_status' => 'Sijaoa/Sijaolewa',
                'children_info' => null,
                'neighbor_name' => 'Lucy Chambo',
                'neighbor_phone' => '+255755678902',
                'church_elder' => 'Mama Christina Ngowi',
                'pledge_number' => 'PLD' . $year . '0008',
                'special_group' => 'Vijana',
                'ministry_groups' => json_encode(['Kwaya ya Wanawake', 'Bibilia']),
                'id_number' => '19950518-67890-00008-12',
                'is_active' => true,
                'notes' => 'Msaidizi wa mhasibu wa kanisa',
            ],
            // Member 9 - RGC-2025-0009
            [
                'member_number' => 'RGC-' . $year . '-0009',
                'envelope_number' => 'ENV' . $year . '0009',
                'first_name' => 'Peter',
                'middle_name' => 'Daniel',
                'last_name' => 'Msuya',
                'date_of_birth' => '2005-12-10',
                'gender' => 'Mme',
                'phone' => '+255766789012',
                'email' => 'peter.msuya@email.com',
                'user_id' => $getMemberUser(9)?->id,
                'jumuiya_id' => $jumuiyaTumaini?->id,
                'occupation' => 'Mwanafunzi - Sekondari',
                'address' => 'Kinondoni, Dar es Salaam',
                'house_number' => 'HSE-115',
                'block_number' => 'BLK-A',
                'city' => 'Dar es Salaam',
                'region' => 'Dar es Salaam',
                'baptism_date' => '2010-08-15',
                'confirmation_date' => '2018-10-21',
                'membership_date' => '2022-01-16',
                'marital_status' => 'Sijaoa/Sijaolewa',
                'children_info' => null,
                'neighbor_name' => 'John Kileo',
                'neighbor_phone' => '+255766789013',
                'church_elder' => 'Mzee Paul Mgeni',
                'pledge_number' => 'PLD' . $year . '0009',
                'special_group' => 'Vijana',
                'ministry_groups' => json_encode(['Kwaya ya Vijana', 'Michezo']),
                'id_number' => '20051210-78901-00009-23',
                'is_active' => true,
                'notes' => 'Mwanamuziki hodari, anacheza gitaa',
            ],
            // Member 10 - RGC-2025-0010
            [
                'member_number' => 'RGC-' . $year . '-0010',
                'envelope_number' => 'ENV' . $year . '0010',
                'first_name' => 'Joyce',
                'middle_name' => 'Ester',
                'last_name' => 'Kilave',
                'date_of_birth' => '1982-04-25',
                'gender' => 'Mke',
                'phone' => '+255717890123',
                'email' => 'joyce.kilave@email.com',
                'user_id' => $getMemberUser(10)?->id,
                'jumuiya_id' => $jumuiyaNeema?->id,
                'occupation' => 'Daktari',
                'address' => 'Ubungo, Dar es Salaam',
                'house_number' => 'HSE-305',
                'block_number' => 'BLK-C',
                'city' => 'Dar es Salaam',
                'region' => 'Dar es Salaam',
                'baptism_date' => '1987-07-12',
                'confirmation_date' => '1995-06-08',
                'membership_date' => '2017-04-22',
                'marital_status' => 'Ameoa/Ameolewa',
                'spouse_name' => 'Robert Kilave',
                'spouse_phone' => '+255717890124',
                'children_info' => json_encode([
                    ['name' => 'Anna Kilave', 'age' => 10],
                    ['name' => 'Benjamin Kilave', 'age' => 7]
                ]),
                'neighbor_name' => 'Martha Msigwa',
                'neighbor_phone' => '+255717890125',
                'church_elder' => 'Mama Rose Mwakawago',
                'pledge_number' => 'PLD' . $year . '0010',
                'special_group' => 'Wazazi',
                'ministry_groups' => json_encode(['Huduma za Afya', 'Kwaya Kuu']),
                'id_number' => '19820425-89012-00010-34',
                'is_active' => true,
                'notes' => 'Msimamizi wa huduma za afya kanisani',
            ],
            // Member 11 - RGC-2025-0011
            [
                'member_number' => 'RGC-' . $year . '-0011',
                'envelope_number' => 'ENV' . $year . '0011',
                'first_name' => 'Michael',
                'middle_name' => 'George',
                'last_name' => 'Lyatuu',
                'date_of_birth' => '1998-08-07',
                'gender' => 'Mme',
                'phone' => '+255758901234',
                'email' => 'michael.lyatuu@email.com',
                'user_id' => $getMemberUser(11)?->id,
                'jumuiya_id' => $jumuiyaBaraka?->id,
                'occupation' => 'Mwandishi wa Habari',
                'address' => 'Tegeta, Dar es Salaam',
                'house_number' => 'HSE-220',
                'block_number' => 'BLK-B',
                'city' => 'Dar es Salaam',
                'region' => 'Dar es Salaam',
                'baptism_date' => '2003-09-28',
                'confirmation_date' => '2011-11-13',
                'membership_date' => '2020-06-14',
                'marital_status' => 'Sijaoa/Sijaolewa',
                'children_info' => null,
                'neighbor_name' => 'Juma Mgaya',
                'neighbor_phone' => '+255758901235',
                'church_elder' => 'Mzee Thomas Msemo',
                'pledge_number' => 'PLD' . $year . '0011',
                'special_group' => 'Vijana',
                'ministry_groups' => json_encode(['Media Ministry', 'Kwaya ya Vijana']),
                'id_number' => '19980807-90123-00011-45',
                'is_active' => true,
                'notes' => 'Msimamizi wa media na mawasiliano',
            ],
            // Member 12 - RGC-2025-0012
            [
                'member_number' => 'RGC-' . $year . '-0012',
                'envelope_number' => 'ENV' . $year . '0012',
                'first_name' => 'Ruth',
                'middle_name' => 'Anna',
                'last_name' => 'Swai',
                'date_of_birth' => '1972-01-20',
                'gender' => 'Mke',
                'phone' => '+255769012345',
                'email' => 'ruth.swai@email.com',
                'user_id' => $getMemberUser(12)?->id,
                'jumuiya_id' => $jumuiyaRGC?->id,
                'occupation' => 'Meneja - Benki',
                'address' => 'Mbezi Beach, Dar es Salaam',
                'house_number' => 'HSE-405',
                'block_number' => 'BLK-D',
                'city' => 'Dar es Salaam',
                'region' => 'Dar es Salaam',
                'baptism_date' => '1977-02-13',
                'confirmation_date' => '1985-03-10',
                'membership_date' => '2012-08-19',
                'marital_status' => 'Ameoa/Ameolewa',
                'spouse_name' => 'Jacob Swai',
                'spouse_phone' => '+255769012346',
                'children_info' => json_encode([
                    ['name' => 'Faith Swai', 'age' => 28],
                    ['name' => 'Hope Swai', 'age' => 25],
                    ['name' => 'Love Swai', 'age' => 22]
                ]),
                'neighbor_name' => 'Sarah Kisamo',
                'neighbor_phone' => '+255769012347',
                'church_elder' => 'Mama Deborah Tesha',
                'pledge_number' => 'PLD' . $year . '0012',
                'special_group' => 'Wazee',
                'ministry_groups' => json_encode(['Kwaya ya Wanawake', 'Ushauri wa Fedha']),
                'id_number' => '19720120-01234-00012-56',
                'is_active' => true,
                'notes' => 'Mshauri wa mambo ya fedha za kanisa',
            ],
        ];

        foreach ($members as $member) {
            Member::updateOrCreate(
                ['member_number' => $member['member_number']],
                $member
            );
        }

        // Update Jumuiya leaders with leader_id and leader_phone
        if ($jumuiyaRGC) {
            $leader = Member::where('member_number', 'RGC-' . $year . '-0003')->first();
            if ($leader) {
                $jumuiyaRGC->update([
                    'leader_id' => $leader->id,
                    'leader_phone' => $leader->phone,
                ]);
            }
        }

        if ($jumuiyaImani) {
            $leader = Member::where('member_number', 'RGC-' . $year . '-0008')->first();
            if ($leader) {
                $jumuiyaImani->update([
                    'leader_id' => $leader->id,
                    'leader_phone' => $leader->phone,
                ]);
            }
        }

        if ($jumuiyaTumaini) {
            $leader = Member::where('member_number', 'RGC-' . $year . '-0004')->first();
            if ($leader) {
                $jumuiyaTumaini->update([
                    'leader_id' => $leader->id,
                    'leader_phone' => $leader->phone,
                ]);
            }
        }

        if ($jumuiyaNeema) {
            $leader = Member::where('member_number', 'RGC-' . $year . '-0005')->first();
            if ($leader) {
                $jumuiyaNeema->update([
                    'leader_id' => $leader->id,
                    'leader_phone' => $leader->phone,
                ]);
            }
        }

        if ($jumuiyaBaraka) {
            $leader = Member::where('member_number', 'RGC-' . $year . '-0006')->first();
            if ($leader) {
                $jumuiyaBaraka->update([
                    'leader_id' => $leader->id,
                    'leader_phone' => $leader->phone,
                ]);
            }
        }

        $this->command->info('Wanachama wameongezwa successfully! Jumla: ' . \count($members));
    }
}
