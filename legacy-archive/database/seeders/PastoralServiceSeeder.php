<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PastoralService;
use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;

class PastoralServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $year = date('Y');

        // Get admin user (Mchungaji)
        $adminUser = User::where('email', "RGC-{$year}-0001@rgc.org")->first();
        $approvedBy = $adminUser ? $adminUser->id : 1;
        $createdBy = $approvedBy;

        // Helper function to get member by number
        $getMember = function($number) use ($year) {
            return Member::where('member_number', 'RGC-' . $year . '-' . sprintf('%04d', $number))->first();
        };

        $services = [];
        $serviceNumber = 1;

        // Member 3 - John Mwangi: Ubatizo wa mtoto
        $member3 = $getMember(3);
        if ($member3) {
            $services[] = [
                'service_number' => 'PS' . $year . sprintf('%03d', $serviceNumber++),
                'member_id' => $member3->id,
                'service_type' => 'Ubatizo',
                'preferred_date' => Carbon::now()->addDays(30)->format('Y-m-d'),
                'description' => 'Ubatizo wa mtoto wangu mdogo. Jina: David Mwangi Jr. Tarehe ya kuzaliwa: ' . Carbon::now()->subMonths(3)->format('d/m/Y'),
                'status' => 'Imeidhinishwa',
                'admin_notes' => 'Imeidhinishwa. Ubatizo utafanyika Jumapili tarehe iliyopendelewa. Maandalizi yameanza.',
                'approved_by' => $approvedBy,
                'approved_at' => Carbon::now()->subDays(5),
                'created_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(5),
            ];
        }

        // Member 4 - Sarah Moshi: Ushauri wa Kichungaji
        $member4 = $getMember(4);
        if ($member4) {
            $services[] = [
                'service_number' => 'PS' . $year . sprintf('%03d', $serviceNumber++),
                'member_id' => $member4->id,
                'service_type' => 'Ushauri wa Kichungaji',
                'preferred_date' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'description' => 'Ninahitaji ushauri kuhusu changamoto za kifamilia na malezi ya watoto katika mazingira ya sasa.',
                'status' => 'Imeidhinishwa',
                'admin_notes' => 'Nimepanga kikao cha ushauri saa 10 asubuhi. Watafika ofisini.',
                'approved_by' => $approvedBy,
                'approved_at' => Carbon::now()->subDays(1),
                'created_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(1),
            ];
        }

        // Member 5 - Emmanuel Komba: Uthibitisho
        $member5 = $getMember(5);
        if ($member5) {
            $services[] = [
                'service_number' => 'PS' . $year . sprintf('%03d', $serviceNumber++),
                'member_id' => $member5->id,
                'service_type' => 'Uthibitisho',
                'preferred_date' => Carbon::now()->addMonths(2)->format('Y-m-d'),
                'description' => 'Nimemaliza masomo ya Kipaimara na niko tayari kwa uthibitisho. Nimehudhuria darasa la Kipaimara kwa miezi 6.',
                'status' => 'Inasubiri',
                'admin_notes' => null,
                'approved_by' => null,
                'approved_at' => null,
                'created_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ];
        }

        // Member 6 - Agnes Massawe: Mazishi (completed)
        $member6 = $getMember(6);
        if ($member6) {
            $services[] = [
                'service_number' => 'PS' . $year . sprintf('%03d', $serviceNumber++),
                'member_id' => $member6->id,
                'service_type' => 'Mazishi',
                'preferred_date' => Carbon::now()->subMonths(2)->format('Y-m-d'),
                'description' => 'Mazishi ya mume wangu marehemu Bw. Robert Massawe. Alikuwa mwanachama wa kanisa kwa miaka 25.',
                'status' => 'Imekamilika',
                'admin_notes' => 'Mazishi yalifanyika kwa amani. Kanisa lilitoa msaada wa chakula na nauli kwa wageni. Tutaendelea kuwaombea familia.',
                'approved_by' => $approvedBy,
                'approved_at' => Carbon::now()->subMonths(2)->subDays(2),
                'created_by' => $createdBy,
                'created_at' => Carbon::now()->subMonths(2)->subDays(5),
                'updated_at' => Carbon::now()->subMonths(2),
            ];
        }

        // Member 7 - Joshua Mwakyembe: Wakfu wa nyumba
        $member7 = $getMember(7);
        if ($member7) {
            $services[] = [
                'service_number' => 'PS' . $year . sprintf('%03d', $serviceNumber++),
                'member_id' => $member7->id,
                'service_type' => 'Wakfu',
                'preferred_date' => Carbon::now()->addDays(14)->format('Y-m-d'),
                'description' => 'Nimemaliza kujenga nyumba mpya na ninaomba mchungaji aje kuiweka wakfu. Anwani: Plot 45, Mbezi Beach.',
                'status' => 'Imeidhinishwa',
                'admin_notes' => 'Wakfu umepangwa saa 4 asubuhi. Timu ya kwaya itashiriki.',
                'approved_by' => $approvedBy,
                'approved_at' => Carbon::now()->subDays(3),
                'created_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(3),
            ];
        }

        // Member 8 - Grace Ndunguru: Ndoa
        $member8 = $getMember(8);
        if ($member8) {
            $services[] = [
                'service_number' => 'PS' . $year . sprintf('%03d', $serviceNumber++),
                'member_id' => $member8->id,
                'service_type' => 'Ndoa',
                'preferred_date' => Carbon::now()->addMonths(3)->format('Y-m-d'),
                'description' => 'Tunaomba ndoa ya kikristo. Mchumba wangu: Daniel Mwakisu. Tumemaliza masomo ya kabla ya ndoa.',
                'status' => 'Inasubiri',
                'admin_notes' => 'Ombi limepokelewa. Tunahitaji kukutana na wazazi wa pande zote mbili kwanza.',
                'approved_by' => null,
                'approved_at' => null,
                'created_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ];
        }

        // Member 9 - Peter Msuya: Ubatizo (student)
        $member9 = $getMember(9);
        if ($member9) {
            $services[] = [
                'service_number' => 'PS' . $year . sprintf('%03d', $serviceNumber++),
                'member_id' => $member9->id,
                'service_type' => 'Ubatizo',
                'preferred_date' => Carbon::now()->subMonths(1)->format('Y-m-d'),
                'description' => 'Niliomba kubatizwa upya kwa sababu ubatizo wangu wa kwanza ulifanyika kwa madhehebu mengine.',
                'status' => 'Imekamilika',
                'admin_notes' => 'Ubatizo ulifanyika tarehe iliyopangwa. Amesajiliwa rasmi kama mwanachama kamili wa RGC.',
                'approved_by' => $approvedBy,
                'approved_at' => Carbon::now()->subMonths(1)->subDays(3),
                'created_by' => $createdBy,
                'created_at' => Carbon::now()->subMonths(1)->subDays(10),
                'updated_at' => Carbon::now()->subMonths(1),
            ];
        }

        // Member 10 - Joyce Kilave: Ushauri wa Kichungaji
        $member10 = $getMember(10);
        if ($member10) {
            $services[] = [
                'service_number' => 'PS' . $year . sprintf('%03d', $serviceNumber++),
                'member_id' => $member10->id,
                'service_type' => 'Ushauri wa Kichungaji',
                'preferred_date' => Carbon::now()->subWeeks(2)->format('Y-m-d'),
                'description' => 'Nilihitaji ushauri kuhusu maamuzi ya kazi - kupewa uhamisho wa kwenda mkoa mwingine.',
                'status' => 'Imekamilika',
                'admin_notes' => 'Kikao kilifanyika. Tuliomba pamoja na kutoa ushauri. Wameamua kubaki na familia.',
                'approved_by' => $approvedBy,
                'approved_at' => Carbon::now()->subWeeks(2)->subDays(1),
                'created_by' => $createdBy,
                'created_at' => Carbon::now()->subWeeks(3),
                'updated_at' => Carbon::now()->subWeeks(2),
            ];
        }

        // Member 11 - Michael Lyatuu: Nyingine (Baraka ya biashara)
        $member11 = $getMember(11);
        if ($member11) {
            $services[] = [
                'service_number' => 'PS' . $year . sprintf('%03d', $serviceNumber++),
                'member_id' => $member11->id,
                'service_type' => 'Nyingine',
                'preferred_date' => Carbon::now()->addDays(7)->format('Y-m-d'),
                'description' => 'Ninaomba baraka za kufungua ofisi mpya ya habari. Naomba mchungaji aje kuomba na kuweka wakfu mahali.',
                'status' => 'Imeidhinishwa',
                'admin_notes' => 'Baraka ya biashara imepangwa. Tutashiriki na timu ya viongozi.',
                'approved_by' => $approvedBy,
                'approved_at' => Carbon::now()->subDays(2),
                'created_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(2),
            ];
        }

        // Member 12 - Ruth Swai: Wakfu wa gari
        $member12 = $getMember(12);
        if ($member12) {
            $services[] = [
                'service_number' => 'PS' . $year . sprintf('%03d', $serviceNumber++),
                'member_id' => $member12->id,
                'service_type' => 'Wakfu',
                'preferred_date' => Carbon::now()->subMonths(1)->format('Y-m-d'),
                'description' => 'Nimenunua gari jipya na ninaomba liwekwe wakfu. Toyota Land Cruiser.',
                'status' => 'Imekamilika',
                'admin_notes' => 'Wakfu ulifanyika baada ya ibada ya Jumapili. Waumini walifurahi sana kushiriki.',
                'approved_by' => $approvedBy,
                'approved_at' => Carbon::now()->subMonths(1)->subDays(2),
                'created_by' => $createdBy,
                'created_at' => Carbon::now()->subMonths(1)->subDays(7),
                'updated_at' => Carbon::now()->subMonths(1),
            ];

            // Additional service request from same member - Ndoa ya mtoto
            $services[] = [
                'service_number' => 'PS' . $year . sprintf('%03d', $serviceNumber++),
                'member_id' => $member12->id,
                'service_type' => 'Ndoa',
                'preferred_date' => Carbon::now()->addMonths(4)->format('Y-m-d'),
                'description' => 'Ndoa ya mtoto wangu Faith Swai na mchumba wake James Mwita. Wote ni wanachama wa kanisa.',
                'status' => 'Inasubiri',
                'admin_notes' => null,
                'approved_by' => null,
                'approved_at' => null,
                'created_by' => $createdBy,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ];
        }

        // Create all pastoral services
        foreach ($services as $service) {
            PastoralService::create($service);
        }

        $this->command->info('✓ ' . count($services) . ' huduma za kichungaji (pastoral services) zimeongezwa successfully!');

        // Show statistics
        $statusCounts = collect($services)->groupBy('status')->map->count();
        $this->command->info('  Takwimu:');
        foreach ($statusCounts as $status => $count) {
            $this->command->info("    - {$status}: {$count}");
        }
    }
}
