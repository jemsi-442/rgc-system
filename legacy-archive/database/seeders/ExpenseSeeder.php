<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Get admin user dynamically
        $year = date('Y');
        $adminUser = User::where('email', "RGC-{$year}-0001@rgc.org")->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $expenses = [];

        // Generate expense data for the last 6 months
        for ($monthsAgo = 5; $monthsAgo >= 0; $monthsAgo--) {
            $date = Carbon::now()->subMonths($monthsAgo);
            $year = $date->year;
            $month = $date->month;

            // Regular monthly expenses

            // 1. Posho - Mwinjilisti
            $expenses[] = [
                'expense_category_id' => 2,
                'year' => $year,
                'month' => $month,
                'amount' => 150000,
                'notes' => 'Posho ya Mwinjilisti - ' . $date->translatedFormat('F Y'),
                'receipt_number' => 'EXP' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '001',
                'payee' => 'Mwinjilisti Petro Mwakasege',
                'created_by' => $createdBy,
                'created_at' => $date,
                'updated_at' => $date,
            ];

            // 2. Posho - Mhudumu
            $expenses[] = [
                'expense_category_id' => 3,
                'year' => $year,
                'month' => $month,
                'amount' => 120000,
                'notes' => 'Posho ya Mhudumu - ' . $date->translatedFormat('F Y'),
                'receipt_number' => 'EXP' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '002',
                'payee' => 'Mhudumu Josephat Kisamo',
                'created_by' => $createdBy,
                'created_at' => $date,
                'updated_at' => $date,
            ];

            // 3. Posho - Wahubiri
            $expenses[] = [
                'expense_category_id' => 4,
                'year' => $year,
                'month' => $month,
                'amount' => 100000,
                'notes' => 'Posho ya Wahubiri - ' . $date->translatedFormat('F Y'),
                'receipt_number' => 'EXP' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '003',
                'payee' => 'Wahubiri Wasaidizi',
                'created_by' => $createdBy,
                'created_at' => $date,
                'updated_at' => $date,
            ];

            // 4. Posho - Mlinzi
            $expenses[] = [
                'expense_category_id' => 5,
                'year' => $year,
                'month' => $month,
                'amount' => 80000,
                'notes' => 'Posho ya Mlinzi - ' . $date->translatedFormat('F Y'),
                'receipt_number' => 'EXP' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '004',
                'payee' => 'Mlinzi Bakari Mwinyipembe',
                'created_by' => $createdBy,
                'created_at' => $date,
                'updated_at' => $date,
            ];

            // 5. Posho - Mwalimu Kanisa Kuu
            $expenses[] = [
                'expense_category_id' => 7,
                'year' => $year,
                'month' => $month,
                'amount' => 90000,
                'notes' => 'Posho ya Mwalimu Kanisa Kuu - ' . $date->translatedFormat('F Y'),
                'receipt_number' => 'EXP' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '005',
                'payee' => 'Mwalimu Sarah Mwakawago',
                'created_by' => $createdBy,
                'created_at' => $date,
                'updated_at' => $date,
            ];

            // 6. Posho - Mwalimu Kanisa Vijana
            $expenses[] = [
                'expense_category_id' => 8,
                'year' => $year,
                'month' => $month,
                'amount' => 70000,
                'notes' => 'Posho ya Mwalimu Kanisa Vijana - ' . $date->translatedFormat('F Y'),
                'receipt_number' => 'EXP' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '006',
                'payee' => 'Mwalimu Emmanuel Lyimo',
                'created_by' => $createdBy,
                'created_at' => $date,
                'updated_at' => $date,
            ];

            // 7. Posho - Store Keeper
            $expenses[] = [
                'expense_category_id' => 35,
                'year' => $year,
                'month' => $month,
                'amount' => 60000,
                'notes' => 'Posho ya Store Keeper - ' . $date->translatedFormat('F Y'),
                'receipt_number' => 'EXP' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '007',
                'payee' => 'Store Keeper Grace Mushi',
                'created_by' => $createdBy,
                'created_at' => $date,
                'updated_at' => $date,
            ];

            // 8. Umeme
            $expenses[] = [
                'expense_category_id' => 15,
                'year' => $year,
                'month' => $month,
                'amount' => rand(180000, 250000),
                'notes' => 'Malipo ya Umeme - ' . $date->translatedFormat('F Y'),
                'receipt_number' => 'EXP' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '008',
                'payee' => 'TANESCO',
                'created_by' => $createdBy,
                'created_at' => $date,
                'updated_at' => $date,
            ];

            // 9. Maji kunywa/usafi/Chakula
            $expenses[] = [
                'expense_category_id' => 12,
                'year' => $year,
                'month' => $month,
                'amount' => rand(120000, 180000),
                'notes' => 'Maji, usafi na chakula - ' . $date->translatedFormat('F Y'),
                'receipt_number' => 'EXP' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '009',
                'payee' => 'Maduka Mbalimbali',
                'created_by' => $createdBy,
                'created_at' => $date,
                'updated_at' => $date,
            ];

            // 10. 8% ya Jimbo (calculated from monthly income average)
            $jimboAmount = rand(80000, 150000);
            $expenses[] = [
                'expense_category_id' => 14,
                'year' => $year,
                'month' => $month,
                'amount' => $jimboAmount,
                'notes' => '8% ya mapato yanayotolewa kwa Jimbo - ' . $date->translatedFormat('F Y'),
                'receipt_number' => 'EXP' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '010',
                'payee' => 'Jimbo la Magharibi',
                'created_by' => $createdBy,
                'created_at' => $date,
                'updated_at' => $date,
            ];

            // 11. Stationery (not every month)
            if ($month % 2 == 0) {
                $expenses[] = [
                    'expense_category_id' => 17,
                    'year' => $year,
                    'month' => $month,
                    'amount' => rand(50000, 120000),
                    'notes' => 'Ununuzi wa vifaa vya ofisi - ' . $date->translatedFormat('F Y'),
                    'receipt_number' => 'EXP' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '011',
                    'payee' => 'Stationers Ltd',
                    'created_by' => $createdBy,
                    'created_at' => $date,
                    'updated_at' => $date,
                ];
            }

            // 12. Nauli - Bible Study (weekly)
            $nauliAmount = 4 * 10000; // 4 weeks
            $expenses[] = [
                'expense_category_id' => 1,
                'year' => $year,
                'month' => $month,
                'amount' => $nauliAmount,
                'notes' => 'Nauli za Bible Study - ' . $date->translatedFormat('F Y'),
                'receipt_number' => 'EXP' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '012',
                'payee' => 'Nauli Mbalimbali',
                'created_by' => $createdBy,
                'created_at' => $date,
                'updated_at' => $date,
            ];

            // 13. Huduma za Kichungaji (quarterly)
            if ($month % 3 == 0) {
                $expenses[] = [
                    'expense_category_id' => 19,
                    'year' => $year,
                    'month' => $month,
                    'amount' => rand(200000, 400000),
                    'notes' => 'Huduma za Kichungaji - Roho, Safari, n.k - ' . $date->translatedFormat('F Y'),
                    'receipt_number' => 'EXP' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '013',
                    'payee' => 'Mchungaji Msaidizi',
                    'created_by' => $createdBy,
                    'created_at' => $date,
                    'updated_at' => $date,
                ];
            }

            // 14. Wahitaji (occasional)
            if (rand(1, 3) == 1) {
                $expenses[] = [
                    'expense_category_id' => 34,
                    'year' => $year,
                    'month' => $month,
                    'amount' => rand(50000, 150000),
                    'notes' => 'Msaada kwa wahitaji - ' . $date->translatedFormat('F Y'),
                    'receipt_number' => 'EXP' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '014',
                    'payee' => 'Wanachama Wahitaji',
                    'created_by' => $createdBy,
                    'created_at' => $date,
                    'updated_at' => $date,
                ];
            }
        }

        // Special/Project expenses

        // Ujenzi (construction - occasional large expenses)
        $constructionDate = Carbon::now()->subMonths(3);
        $expenses[] = [
            'expense_category_id' => 23,
            'year' => $constructionDate->year,
            'month' => $constructionDate->month,
            'amount' => 2500000,
            'notes' => 'Ukarabati wa paa la kanisa',
            'receipt_number' => 'EXP' . $constructionDate->format('Ym') . 'CONST001',
            'payee' => 'Kampuni ya Ujenzi ABC',
            'created_by' => $createdBy,
            'created_at' => $constructionDate,
            'updated_at' => $constructionDate,
        ];

        // Vikao/Semina
        $seminaDate = Carbon::now()->subMonths(2);
        $expenses[] = [
            'expense_category_id' => 20,
            'year' => $seminaDate->year,
            'month' => $seminaDate->month,
            'amount' => 450000,
            'notes' => 'Semina ya Viongozi wa Kanisa',
            'receipt_number' => 'EXP' . $seminaDate->format('Ym') . 'SEM001',
            'payee' => 'Hotel XYZ',
            'created_by' => $createdBy,
            'created_at' => $seminaDate,
            'updated_at' => $seminaDate,
        ];

        // Kwaya Kuu
        $kwayaDate = Carbon::now()->subMonths(1);
        $expenses[] = [
            'expense_category_id' => 33,
            'year' => $kwayaDate->year,
            'month' => $kwayaDate->month,
            'amount' => 350000,
            'notes' => 'Kanzu mpya za kwaya kuu',
            'receipt_number' => 'EXP' . $kwayaDate->format('Ym') . 'KWA001',
            'payee' => 'Tailors Group',
            'created_by' => $createdBy,
            'created_at' => $kwayaDate,
            'updated_at' => $kwayaDate,
        ];

        // Tamasha Vijana
        $tamashaDate = Carbon::now()->subMonths(4);
        $expenses[] = [
            'expense_category_id' => 27,
            'year' => $tamashaDate->year,
            'month' => $tamashaDate->month,
            'amount' => 600000,
            'notes' => 'Tamasha la Vijana - Chakula, Nauli, Shughuli',
            'receipt_number' => 'EXP' . $tamashaDate->format('Ym') . 'VIJ001',
            'payee' => 'Kamati ya Vijana',
            'created_by' => $createdBy,
            'created_at' => $tamashaDate,
            'updated_at' => $tamashaDate,
        ];

        // Insert all expenses
        foreach ($expenses as $expense) {
            try {
                Expense::create($expense);
            } catch (\Exception $e) {
                // Skip duplicates (unique constraint on category, year, month)
                continue;
            }
        }

        $this->command->info('✓ ' . count($expenses) . ' rekodi za matumizi zimeongezwa successfully!');
    }
}
