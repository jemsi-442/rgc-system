<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Request;
use App\Models\User;
use Carbon\Carbon;

class RequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users dynamically
        $year = date('Y');
        $adminUser = User::where('email', "RGC-{$year}-0001@rgc.org")->first(); // Mchungaji
        $mhasibuUser = User::where('email', "RGC-{$year}-0002@rgc.org")->first(); // Mhasibu

        $approvedBy = $adminUser ? $adminUser->id : 1;
        $requestedBy = $mhasibuUser ? $mhasibuUser->id : 2;

        $requests = [
            // Approved Requests
            [
                'request_number' => 'REQ2025001',
                'title' => 'Ununuzi wa Mikrofoni Mipya',
                'description' => 'Mikrofoni iliyopo imezeeka na inazalisha sauti isiyo safi. Tunahitaji ununuzi wa mikrofoni 4 mpya za ubora wa juu pamoja na vifaa vyake.',
                'department' => 'Huduma za Sauti',
                'amount_requested' => 800000,
                'amount_approved' => 750000,
                'status' => 'Imeidhinishwa',
                'approval_notes' => 'Ombi limeidhinishwa. Nunua mikrofoni za bei nafuu lakini za ubora.',
                'requested_date' => Carbon::now()->subMonths(3)->format('Y-m-d'),
                'approved_date' => Carbon::now()->subMonths(3)->addDays(5)->format('Y-m-d'),
                'requested_by' => $requestedBy,
                'approved_by' => $approvedBy,
                'created_at' => Carbon::now()->subMonths(3),
                'updated_at' => Carbon::now()->subMonths(3)->addDays(5),
            ],
            [
                'request_number' => 'REQ2025002',
                'title' => 'Ukarabati wa Paa la Kanisa',
                'description' => 'Paa la kanisa linadondosha maji wakati wa mvua. Kunahitajika ukarabati wa haraka ili kuzuia uharibifu zaidi wa jengo.',
                'department' => 'Ujenzi na Matengenezo',
                'amount_requested' => 3000000,
                'amount_approved' => 2500000,
                'status' => 'Imeidhinishwa',
                'approval_notes' => 'Ombi limeidhinishwa kwa TZS 2,500,000. Kamati ya ujenzi itasimamia kazi.',
                'requested_date' => Carbon::now()->subMonths(4)->format('Y-m-d'),
                'approved_date' => Carbon::now()->subMonths(4)->addDays(3)->format('Y-m-d'),
                'requested_by' => $requestedBy,
                'approved_by' => $approvedBy,
                'created_at' => Carbon::now()->subMonths(4),
                'updated_at' => Carbon::now()->subMonths(4)->addDays(3),
            ],
            [
                'request_number' => 'REQ2025003',
                'title' => 'Kanzu za Kwaya ya Vijana',
                'description' => 'Kwaya ya vijana ina wanachama 30 lakini kanzu ni 15 tu. Tunahitaji kanzu 15 zaidi.',
                'department' => 'Kwaya ya Vijana',
                'amount_requested' => 450000,
                'amount_approved' => 450000,
                'status' => 'Imeidhinishwa',
                'approval_notes' => 'Ombi limeidhinishwa kikamilifu. Hakikisheni ubora wa kanzu ni mzuri.',
                'requested_date' => Carbon::now()->subMonths(2)->format('Y-m-d'),
                'approved_date' => Carbon::now()->subMonths(2)->addDays(2)->format('Y-m-d'),
                'requested_by' => $requestedBy,
                'approved_by' => $approvedBy,
                'created_at' => Carbon::now()->subMonths(2),
                'updated_at' => Carbon::now()->subMonths(2)->addDays(2),
            ],
            [
                'request_number' => 'REQ2025004',
                'title' => 'Kompyuta kwa Ofisi ya Kanisa',
                'description' => 'Kompyuta ya ofisi imeharibika kabisa. Tunahitaji kompyuta mpya pamoja na printer.',
                'department' => 'Ofisi ya Kanisa',
                'amount_requested' => 1200000,
                'amount_approved' => 1000000,
                'status' => 'Imeidhinishwa',
                'approval_notes' => 'Nununue kompyuta ya bei ya wastani ambayo itafanya kazi. Kiasi cha TZS 1,000,000 kimeidhinishwa.',
                'requested_date' => Carbon::now()->subMonths(5)->format('Y-m-d'),
                'approved_date' => Carbon::now()->subMonths(5)->addDays(7)->format('Y-m-d'),
                'requested_by' => $requestedBy,
                'approved_by' => $approvedBy,
                'created_at' => Carbon::now()->subMonths(5),
                'updated_at' => Carbon::now()->subMonths(5)->addDays(7),
            ],

            // Pending Requests
            [
                'request_number' => 'REQ2025005',
                'title' => 'Msaada kwa Familia ya Ndugu Mwanachama Aliyefariki',
                'description' => 'Ndugu John Mtani amefariki. Familia yake inahitaji msaada wa fedha kwa matumizi ya mazishi na kusaidia watoto wake 4 walioachwa.',
                'department' => 'Huduma za Kijamii',
                'amount_requested' => 500000,
                'amount_approved' => null,
                'status' => 'Inasubiri',
                'approval_notes' => null,
                'requested_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'approved_date' => null,
                'requested_by' => $requestedBy,
                'approved_by' => null,
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'request_number' => 'REQ2025006',
                'title' => 'Projector kwa Shule ya Jumapili',
                'description' => 'Tunahitaji projector ili kuweza kuonyesha video za kielimu kwa watoto wakati wa masomo ya Jumapili. Itasaidia sana katika kufundisha.',
                'department' => 'Shule ya Jumapili',
                'amount_requested' => 600000,
                'amount_approved' => null,
                'status' => 'Inasubiri',
                'approval_notes' => null,
                'requested_date' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'approved_date' => null,
                'requested_by' => $requestedBy,
                'approved_by' => null,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'request_number' => 'REQ2025007',
                'title' => 'Fedha za Safari ya Tamasha la Vijana',
                'description' => 'Tunapanga tamasha la vijana tarehe 15 mwezi ujao. Tunahitaji fedha za nauli, chakula na shughuli mbalimbali kwa vijana 120.',
                'department' => 'Vijana',
                'amount_requested' => 800000,
                'amount_approved' => null,
                'status' => 'Inasubiri',
                'approval_notes' => null,
                'requested_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'approved_date' => null,
                'requested_by' => $requestedBy,
                'approved_by' => null,
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            [
                'request_number' => 'REQ2025008',
                'title' => 'Vifaa vya Ulinzi (Kamera za CCTV)',
                'description' => 'Kwa usalama wa kanisa, tunahitaji kusakinisha kamera za CCTV 6 katika maeneo muhimu ya kanisa.',
                'department' => 'Usalama',
                'amount_requested' => 1500000,
                'amount_approved' => null,
                'status' => 'Inasubiri',
                'approval_notes' => null,
                'requested_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'approved_date' => null,
                'requested_by' => $requestedBy,
                'approved_by' => null,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'request_number' => 'REQ2025009',
                'title' => 'Msaada wa Wanafunzi Maskini',
                'description' => 'Kuna wanafunzi 5 kutoka familia maskini wanaohitaji msaada wa ada za shule. Tunahitaji TZS 1,000,000 kuwasaidia.',
                'department' => 'Elimu',
                'amount_requested' => 1000000,
                'amount_approved' => null,
                'status' => 'Inasubiri',
                'approval_notes' => null,
                'requested_date' => Carbon::now()->subDays(7)->format('Y-m-d'),
                'approved_date' => null,
                'requested_by' => $requestedBy,
                'approved_by' => null,
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],

            // Rejected Requests
            [
                'request_number' => 'REQ2025010',
                'title' => 'Ununuzi wa Gari la Kanisa',
                'description' => 'Tunahitaji gari la kanisa kwa huduma mbalimbali za kanisa na kusafirisha wahudumu.',
                'department' => 'Usafiri',
                'amount_requested' => 15000000,
                'amount_approved' => null,
                'status' => 'Imekataliwa',
                'approval_notes' => 'Ombi limekataliwa kwa sasa kwa sababu fedha za kanisa hazitoshi. Tunaweza kulifikiria baadaye.',
                'requested_date' => Carbon::now()->subMonths(1)->format('Y-m-d'),
                'approved_date' => Carbon::now()->subMonths(1)->addDays(4)->format('Y-m-d'),
                'requested_by' => $requestedBy,
                'approved_by' => $approvedBy,
                'created_at' => Carbon::now()->subMonths(1),
                'updated_at' => Carbon::now()->subMonths(1)->addDays(4),
            ],
            [
                'request_number' => 'REQ2025011',
                'title' => 'Safari ya Nje ya Nchi - Kongamano',
                'description' => 'Kuomba ruhusa na fedha kwa viongozi 3 kwenda kongamano la kimataifa Kenya.',
                'department' => 'Viongozi',
                'amount_requested' => 3000000,
                'amount_approved' => null,
                'status' => 'Imekataliwa',
                'approval_notes' => 'Ombi limekataliwa. Kuna ziara nyingi za ndani zinazohitaji fedha. Safari ya nje inaweza kungojea.',
                'requested_date' => Carbon::now()->subMonths(2)->format('Y-m-d'),
                'approved_date' => Carbon::now()->subMonths(2)->addDays(10)->format('Y-m-d'),
                'requested_by' => $requestedBy,
                'approved_by' => $approvedBy,
                'created_at' => Carbon::now()->subMonths(2),
                'updated_at' => Carbon::now()->subMonths(2)->addDays(10),
            ],
        ];

        foreach ($requests as $request) {
            Request::create($request);
        }

        $this->command->info('✓ ' . count($requests) . ' maombi yameongezwa successfully!');
    }
}
