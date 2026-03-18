<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users
        $users = User::all();

        if ($users->count() < 2) {
            $this->command->warn('Hakuna watumiaji wa kutosha kutengeneza ujumbe wa demo');
            return;
        }

        // Sample messages in Swahili
        $sampleMessages = [
            'Habari za leo?',
            'Asante kwa ujumbe wako',
            'Nitahudhuria mkutano wa leo',
            'Tafadhali nisaidie na taarifa za sadaka',
            'Mungu akubariki',
            'Nimepokea taarifa, asante',
            'Je, mkutano utafanyika saa ngapi?',
            'Nitakupigia simu baadaye',
            'Asante kwa maombi yako',
            'Nimefika kanisani',
            'Nitafika mapema kesho',
            'Nakubali kushiriki katika ibada',
            'Taarifa za sadaka za mwezi huu ni zipi?',
            'Nimekumbuka kuhusu mkutano wa kamati',
            'Je, tunaweza kukutana kesho?',
            'Asante kwa msaada wako wa leo',
            'Nimepata taarifa muhimu',
            'Nitahudhuria kikao cha viongozi',
            'Baraka za Mungu ziwe nawe',
            'Nitakuwa tayari saa 10 asubuhi',
        ];

        // Get first few users for demo
        $mchungaji = User::whereHas('role', function ($q) {
            $q->where('name', 'Mchungaji');
        })->first();

        $mhasibu = User::whereHas('role', function ($q) {
            $q->where('name', 'Mhasibu');
        })->first();

        $members = User::whereHas('role', function ($q) {
            $q->where('name', 'Mwanachama');
        })->take(5)->get();

        // Create conversations between mchungaji and members
        if ($mchungaji && $members->count() > 0) {
            foreach ($members as $index => $member) {
                // Create a conversation with 3-8 messages
                $messageCount = rand(3, 8);

                for ($i = 0; $i < $messageCount; $i++) {
                    $isSenderMchungaji = rand(0, 1) === 1;
                    $senderId = $isSenderMchungaji ? $mchungaji->id : $member->id;
                    $receiverId = $isSenderMchungaji ? $member->id : $mchungaji->id;

                    Message::create([
                        'sender_id' => $senderId,
                        'receiver_id' => $receiverId,
                        'content' => $sampleMessages[array_rand($sampleMessages)],
                        'is_read' => $i < $messageCount - rand(0, 2), // Last few might be unread
                        'read_at' => $i < $messageCount - rand(0, 2) ? now()->subMinutes(rand(1, 60)) : null,
                        'created_at' => now()->subDays(rand(0, 7))->subHours(rand(1, 12))->subMinutes($messageCount - $i),
                        'updated_at' => now()->subDays(rand(0, 7))->subHours(rand(1, 12)),
                    ]);
                }
            }
        }

        // Create conversations between mhasibu and members
        if ($mhasibu && $members->count() > 0) {
            foreach ($members->take(3) as $member) {
                $messageCount = rand(2, 5);

                for ($i = 0; $i < $messageCount; $i++) {
                    $isSenderMhasibu = rand(0, 1) === 1;
                    $senderId = $isSenderMhasibu ? $mhasibu->id : $member->id;
                    $receiverId = $isSenderMhasibu ? $member->id : $mhasibu->id;

                    Message::create([
                        'sender_id' => $senderId,
                        'receiver_id' => $receiverId,
                        'content' => $sampleMessages[array_rand($sampleMessages)],
                        'is_read' => $i < $messageCount - 1,
                        'read_at' => $i < $messageCount - 1 ? now()->subMinutes(rand(1, 120)) : null,
                        'created_at' => now()->subDays(rand(0, 5))->subHours(rand(1, 8))->subMinutes($messageCount - $i),
                        'updated_at' => now()->subDays(rand(0, 5))->subHours(rand(1, 8)),
                    ]);
                }
            }
        }

        // Create conversation between mchungaji and mhasibu
        if ($mchungaji && $mhasibu) {
            $leaderMessages = [
                'Tunahitaji kukutana kuhusu bajeti ya mwezi ujao',
                'Nimepitia ripoti ya fedha, inaonekana vizuri',
                'Mkutano wa kamati utafanyika Jumanne',
                'Tafadhali andaa taarifa ya sadaka za mwezi',
                'Nimekubali mapendekezo yako',
                'Tutazungumza zaidi wakati wa mkutano',
            ];

            for ($i = 0; $i < count($leaderMessages); $i++) {
                $isSenderMchungaji = $i % 2 === 0;
                $senderId = $isSenderMchungaji ? $mchungaji->id : $mhasibu->id;
                $receiverId = $isSenderMchungaji ? $mhasibu->id : $mchungaji->id;

                Message::create([
                    'sender_id' => $senderId,
                    'receiver_id' => $receiverId,
                    'content' => $leaderMessages[$i],
                    'is_read' => $i < count($leaderMessages) - 1,
                    'read_at' => $i < count($leaderMessages) - 1 ? now()->subMinutes(rand(5, 60)) : null,
                    'created_at' => now()->subDays(2)->addHours($i),
                    'updated_at' => now()->subDays(2)->addHours($i),
                ]);
            }
        }

        $this->command->info('Ujumbe wa demo umetengenezwa kikamilifu!');
    }
}
