<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pledge;
use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;

class PledgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user for created_by (Mchungaji - first user)
        $year = date('Y');
        $adminUser = User::where('email', "RGC-{$year}-0001@rgc.org")->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        // Helper function to get member by number
        $getMember = function($number) use ($year) {
            return Member::where('member_number', 'RGC-' . $year . '-' . sprintf('%04d', $number))->first();
        };

        $pledges = [];

        // Member 3: John Mwangi (RGC-2025-0003) - Has multiple pledges, some completed
        $member3 = $getMember(3);
        if ($member3) {
            $pledges[] = [
                'member_id' => $member3->id,
                'pledge_type' => 'Kiwanja 2025',
                'amount' => 500000,
                'amount_paid' => 500000, // Fully paid
                'pledge_date' => Carbon::create(2025, 1, 5),
                'due_date' => Carbon::create(2025, 12, 31),
                'status' => 'Completed',
                'notes' => 'Ahadi ya mchango wa kiwanja 2025 - Imelipwa kikamilifu',
                'created_by' => $createdBy,
                'created_at' => Carbon::create(2025, 1, 5),
                'updated_at' => Carbon::now(),
            ];

            $pledges[] = [
                'member_id' => $member3->id,
                'pledge_type' => 'Mavuno 2025',
                'amount' => 300000,
                'amount_paid' => 150000, // Partially paid
                'pledge_date' => Carbon::create(2025, 2, 10),
                'due_date' => Carbon::create(2025, 6, 30),
                'status' => 'Partial',
                'notes' => 'Ahadi ya sherehe ya mavuno - Amelipa nusu',
                'created_by' => $createdBy,
                'created_at' => Carbon::create(2025, 2, 10),
                'updated_at' => Carbon::now(),
            ];
        }

        // Member 4: Sarah Moshi (RGC-2025-0004) - Active pledges with partial payments
        $member4 = $getMember(4);
        if ($member4) {
            $pledges[] = [
                'member_id' => $member4->id,
                'pledge_type' => 'Usiku wa RGC 2025',
                'amount' => 750000,
                'amount_paid' => 250000, // Partially paid
                'pledge_date' => Carbon::create(2025, 1, 15),
                'due_date' => Carbon::create(2025, 11, 30),
                'status' => 'Partial',
                'notes' => 'Ahadi ya sherehe ya usiku wa RGC',
                'created_by' => $createdBy,
                'created_at' => Carbon::create(2025, 1, 15),
                'updated_at' => Carbon::now(),
            ];

            $pledges[] = [
                'member_id' => $member4->id,
                'pledge_type' => 'Ujenzi Kanisa 2025',
                'amount' => 1000000,
                'amount_paid' => 400000, // Partially paid
                'pledge_date' => Carbon::create(2025, 2, 1),
                'due_date' => Carbon::create(2025, 12, 31),
                'status' => 'Partial',
                'notes' => 'Ahadi ya ujenzi wa kanisa',
                'created_by' => $createdBy,
                'created_at' => Carbon::create(2025, 2, 1),
                'updated_at' => Carbon::now(),
            ];
        }

        // Member 5: Emmanuel Komba (RGC-2025-0005) - Student, smaller pledges
        $member5 = $getMember(5);
        if ($member5) {
            $pledges[] = [
                'member_id' => $member5->id,
                'pledge_type' => 'Mavuno 2025',
                'amount' => 100000,
                'amount_paid' => 50000, // Partially paid
                'pledge_date' => Carbon::create(2025, 2, 15),
                'due_date' => Carbon::create(2025, 6, 30),
                'status' => 'Partial',
                'notes' => 'Ahadi ya sherehe ya mavuno - Mwanafunzi',
                'created_by' => $createdBy,
                'created_at' => Carbon::create(2025, 2, 15),
                'updated_at' => Carbon::now(),
            ];
        }

        // Member 6: Agnes Massawe (RGC-2025-0006) - Widow, some completed pledges
        $member6 = $getMember(6);
        if ($member6) {
            $pledges[] = [
                'member_id' => $member6->id,
                'pledge_type' => 'Kiwanja 2025',
                'amount' => 600000,
                'amount_paid' => 600000, // Fully paid
                'pledge_date' => Carbon::create(2025, 1, 8),
                'due_date' => Carbon::create(2025, 12, 31),
                'status' => 'Completed',
                'notes' => 'Ahadi ya kiwanja - Imelipwa kikamilifu',
                'created_by' => $createdBy,
                'created_at' => Carbon::create(2025, 1, 8),
                'updated_at' => Carbon::now(),
            ];

            $pledges[] = [
                'member_id' => $member6->id,
                'pledge_type' => 'Miradi Maalum 2025',
                'amount' => 200000,
                'amount_paid' => 0, // Not yet paid
                'pledge_date' => Carbon::create(2025, 3, 1),
                'due_date' => Carbon::create(2025, 9, 30),
                'status' => 'Pending',
                'notes' => 'Ahadi ya miradi maalum ya kanisa',
                'created_by' => $createdBy,
                'created_at' => Carbon::create(2025, 3, 1),
                'updated_at' => Carbon::now(),
            ];
        }

        // Member 7: Joshua Mwakyembe (RGC-2025-0007) - Engineer, large pledges
        $member7 = $getMember(7);
        if ($member7) {
            $pledges[] = [
                'member_id' => $member7->id,
                'pledge_type' => 'Ujenzi Kanisa 2025',
                'amount' => 2000000,
                'amount_paid' => 1200000, // Partially paid
                'pledge_date' => Carbon::create(2025, 1, 10),
                'due_date' => Carbon::create(2025, 12, 31),
                'status' => 'Partial',
                'notes' => 'Ahadi ya ujenzi wa kanisa - Mshauri wa miradi',
                'created_by' => $createdBy,
                'created_at' => Carbon::create(2025, 1, 10),
                'updated_at' => Carbon::now(),
            ];

            $pledges[] = [
                'member_id' => $member7->id,
                'pledge_type' => 'Usiku wa RGC 2025',
                'amount' => 500000,
                'amount_paid' => 500000, // Fully paid
                'pledge_date' => Carbon::create(2025, 1, 20),
                'due_date' => Carbon::create(2025, 11, 30),
                'status' => 'Completed',
                'notes' => 'Ahadi ya usiku wa RGC - Imelipwa kikamilifu',
                'created_by' => $createdBy,
                'created_at' => Carbon::create(2025, 1, 20),
                'updated_at' => Carbon::now(),
            ];
        }

        // Member 8: Grace Ndunguru (RGC-2025-0008) - Accountant, moderate pledges
        $member8 = $getMember(8);
        if ($member8) {
            $pledges[] = [
                'member_id' => $member8->id,
                'pledge_type' => 'Mavuno 2025',
                'amount' => 250000,
                'amount_paid' => 100000, // Partially paid
                'pledge_date' => Carbon::create(2025, 2, 5),
                'due_date' => Carbon::create(2025, 6, 30),
                'status' => 'Partial',
                'notes' => 'Ahadi ya sherehe ya mavuno',
                'created_by' => $createdBy,
                'created_at' => Carbon::create(2025, 2, 5),
                'updated_at' => Carbon::now(),
            ];

            $pledges[] = [
                'member_id' => $member8->id,
                'pledge_type' => 'Kiwanja 2025',
                'amount' => 400000,
                'amount_paid' => 200000, // Partially paid
                'pledge_date' => Carbon::create(2025, 1, 12),
                'due_date' => Carbon::create(2025, 12, 31),
                'status' => 'Partial',
                'notes' => 'Ahadi ya kiwanja 2025',
                'created_by' => $createdBy,
                'created_at' => Carbon::create(2025, 1, 12),
                'updated_at' => Carbon::now(),
            ];
        }

        // Member 9: Peter Msuya (RGC-2025-0009) - Student, smaller pledges
        $member9 = $getMember(9);
        if ($member9) {
            $pledges[] = [
                'member_id' => $member9->id,
                'pledge_type' => 'Mavuno 2025',
                'amount' => 50000,
                'amount_paid' => 50000, // Fully paid
                'pledge_date' => Carbon::create(2025, 2, 20),
                'due_date' => Carbon::create(2025, 6, 30),
                'status' => 'Completed',
                'notes' => 'Ahadi ya mavuno - Mwanafunzi, amelipa kikamilifu',
                'created_by' => $createdBy,
                'created_at' => Carbon::create(2025, 2, 20),
                'updated_at' => Carbon::now(),
            ];
        }

        // Member 10: Joyce Kilave (RGC-2025-0010) - Doctor, large pledges
        $member10 = $getMember(10);
        if ($member10) {
            $pledges[] = [
                'member_id' => $member10->id,
                'pledge_type' => 'Ujenzi Kanisa 2025',
                'amount' => 1500000,
                'amount_paid' => 750000, // Partially paid
                'pledge_date' => Carbon::create(2025, 1, 18),
                'due_date' => Carbon::create(2025, 12, 31),
                'status' => 'Partial',
                'notes' => 'Ahadi ya ujenzi wa kanisa',
                'created_by' => $createdBy,
                'created_at' => Carbon::create(2025, 1, 18),
                'updated_at' => Carbon::now(),
            ];

            $pledges[] = [
                'member_id' => $member10->id,
                'pledge_type' => 'Usiku wa RGC 2025',
                'amount' => 600000,
                'amount_paid' => 300000, // Partially paid
                'pledge_date' => Carbon::create(2025, 1, 25),
                'due_date' => Carbon::create(2025, 11, 30),
                'status' => 'Partial',
                'notes' => 'Ahadi ya usiku wa RGC',
                'created_by' => $createdBy,
                'created_at' => Carbon::create(2025, 1, 25),
                'updated_at' => Carbon::now(),
            ];
        }

        // Member 11: Michael Lyatuu (RGC-2025-0011) - Journalist, moderate pledges
        $member11 = $getMember(11);
        if ($member11) {
            $pledges[] = [
                'member_id' => $member11->id,
                'pledge_type' => 'Miradi Maalum 2025',
                'amount' => 300000,
                'amount_paid' => 0, // Not yet paid
                'pledge_date' => Carbon::create(2025, 2, 28),
                'due_date' => Carbon::create(2025, 9, 30),
                'status' => 'Pending',
                'notes' => 'Ahadi ya miradi ya media na mawasiliano',
                'created_by' => $createdBy,
                'created_at' => Carbon::create(2025, 2, 28),
                'updated_at' => Carbon::now(),
            ];

            $pledges[] = [
                'member_id' => $member11->id,
                'pledge_type' => 'Mavuno 2025',
                'amount' => 150000,
                'amount_paid' => 75000, // Partially paid
                'pledge_date' => Carbon::create(2025, 2, 12),
                'due_date' => Carbon::create(2025, 6, 30),
                'status' => 'Partial',
                'notes' => 'Ahadi ya sherehe ya mavuno',
                'created_by' => $createdBy,
                'created_at' => Carbon::create(2025, 2, 12),
                'updated_at' => Carbon::now(),
            ];
        }

        // Member 12: Ruth Swai (RGC-2025-0012) - Bank Manager, large pledges
        $member12 = $getMember(12);
        if ($member12) {
            $pledges[] = [
                'member_id' => $member12->id,
                'pledge_type' => 'Kiwanja 2025',
                'amount' => 800000,
                'amount_paid' => 800000, // Fully paid
                'pledge_date' => Carbon::create(2025, 1, 6),
                'due_date' => Carbon::create(2025, 12, 31),
                'status' => 'Completed',
                'notes' => 'Ahadi ya kiwanja - Imelipwa kikamilifu',
                'created_by' => $createdBy,
                'created_at' => Carbon::create(2025, 1, 6),
                'updated_at' => Carbon::now(),
            ];

            $pledges[] = [
                'member_id' => $member12->id,
                'pledge_type' => 'Ujenzi Kanisa 2025',
                'amount' => 1800000,
                'amount_paid' => 900000, // Partially paid
                'pledge_date' => Carbon::create(2025, 1, 22),
                'due_date' => Carbon::create(2025, 12, 31),
                'status' => 'Partial',
                'notes' => 'Ahadi ya ujenzi wa kanisa - Mshauri wa fedha',
                'created_by' => $createdBy,
                'created_at' => Carbon::create(2025, 1, 22),
                'updated_at' => Carbon::now(),
            ];

            $pledges[] = [
                'member_id' => $member12->id,
                'pledge_type' => 'Usiku wa RGC 2025',
                'amount' => 400000,
                'amount_paid' => 200000, // Partially paid
                'pledge_date' => Carbon::create(2025, 2, 3),
                'due_date' => Carbon::create(2025, 11, 30),
                'status' => 'Partial',
                'notes' => 'Ahadi ya usiku wa RGC',
                'created_by' => $createdBy,
                'created_at' => Carbon::create(2025, 2, 3),
                'updated_at' => Carbon::now(),
            ];
        }

        // Create all pledges
        foreach ($pledges as $pledge) {
            Pledge::create($pledge);
        }

        $this->command->info('✓ ' . count($pledges) . ' ahadi (pledges) zimeongezwa successfully!');
        $this->command->info('  - Pending: ' . collect($pledges)->where('status', 'Pending')->count());
        $this->command->info('  - Partial: ' . collect($pledges)->where('status', 'Partial')->count());
        $this->command->info('  - Completed: ' . collect($pledges)->where('status', 'Completed')->count());
    }
}
