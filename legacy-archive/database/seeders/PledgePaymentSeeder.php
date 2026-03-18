<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PledgePayment;
use App\Models\Pledge;
use App\Models\User;
use Carbon\Carbon;

class PledgePaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user for recorded_by
        $year = date('Y');
        $adminUser = User::where('email', 'RGC-' . $year . '-0001@rgc.org')->first(); // Mchungaji
        $mhasibuUser = User::where('email', 'RGC-' . $year . '-0002@rgc.org')->first(); // Mhasibu
        $recordedBy = $adminUser ? $adminUser->id : 1;
        $recordedByMhasibu = $mhasibuUser ? $mhasibuUser->id : $recordedBy;

        // Get all pledges
        $pledges = Pledge::with('member')->get();

        $payments = [];

        foreach ($pledges as $pledge) {
            // Only create payments for pledges that have amount_paid > 0
            if ($pledge->amount_paid > 0) {
                // Determine number of payments based on amount
                $remainingAmount = $pledge->amount_paid;
                $paymentCount = 1;

                if ($remainingAmount >= 500000) {
                    $paymentCount = 3; // Large amounts paid in 3 installments
                } elseif ($remainingAmount >= 200000) {
                    $paymentCount = 2; // Medium amounts paid in 2 installments
                } else {
                    $paymentCount = 1; // Small amounts paid in one payment
                }

                // Create individual payments
                $paymentDate = Carbon::parse($pledge->pledge_date)->addDays(7);

                for ($i = 0; $i < $paymentCount; $i++) {
                    // Calculate payment amount (divide evenly, last payment gets remainder)
                    if ($i == $paymentCount - 1) {
                        // Last payment gets the remaining amount
                        $amount = $remainingAmount;
                    } else {
                        // Divide evenly
                        $amount = floor($pledge->amount_paid / $paymentCount);
                        $remainingAmount -= $amount;
                    }

                    // Generate unique receipt number
                    $receiptNumber = 'RCP-' . $paymentDate->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

                    // Randomly assign payment method
                    $paymentMethods = ['Taslimu', 'M-Pesa', 'Benki', 'Airtel Money', 'Hundi'];
                    $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

                    // Generate reference number for electronic payments
                    $referenceNumber = null;
                    if (in_array($paymentMethod, ['M-Pesa', 'Airtel Money', 'Benki'])) {
                        $referenceNumber = strtoupper(substr($paymentMethod, 0, 1)) . date('Ymd') . rand(100000, 999999);
                    }

                    // Alternate between admin and mhasibu for recorded_by
                    $recorder = ($i % 2 == 0) ? $recordedBy : $recordedByMhasibu;

                    $payments[] = [
                        'pledge_id' => $pledge->id,
                        'member_id' => $pledge->member_id,
                        'amount' => $amount,
                        'payment_date' => $paymentDate->format('Y-m-d'),
                        'payment_method' => $paymentMethod,
                        'reference_number' => $referenceNumber,
                        'receipt_number' => $receiptNumber,
                        'notes' => 'Malipo ya ahadi - ' . $pledge->pledge_type . ' (' . ($i + 1) . '/' . $paymentCount . ')',
                        'recorded_by' => $recorder,
                        'created_at' => $paymentDate,
                        'updated_at' => $paymentDate,
                    ];

                    // Increment payment date by 7-14 days for next payment
                    $paymentDate = $paymentDate->addDays(rand(7, 14));
                }
            }
        }

        // Create all payments without triggering the boot events
        // (since pledges already have the correct amount_paid values from seeder)
        foreach ($payments as $payment) {
            PledgePayment::withoutEvents(function () use ($payment) {
                PledgePayment::create($payment);
            });
        }

        $this->command->info('✓ ' . count($payments) . ' malipo ya ahadi (pledge payments) yameongezwa successfully!');

        // Show statistics by payment method
        $methodStats = [];
        foreach ($payments as $payment) {
            $method = $payment['payment_method'];
            if (!isset($methodStats[$method])) {
                $methodStats[$method] = 0;
            }
            $methodStats[$method]++;
        }

        $this->command->info('  Njia za malipo:');
        foreach ($methodStats as $method => $count) {
            $this->command->info("    - $method: $count malipo");
        }
    }
}
