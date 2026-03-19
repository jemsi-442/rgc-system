<?php

namespace Tests\Feature\Payments;

use App\Mail\BranchPaymentCompletedMail;
use App\Mail\OfferingPaymentReceiptMail;
use App\Models\Branch;
use App\Models\District;
use App\Models\Offering;
use App\Models\OfferingPayment;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class SnippeOfferingPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.snippe.api_key' => 'test-snippe-key',
            'services.snippe.webhook_secret' => 'test-snippe-secret',
            'services.snippe.base_url' => 'https://api.snippe.sh',
        ]);
    }

    public function test_branch_admin_can_create_a_snippe_payment_session_for_offerings(): void
    {
        Http::fake([
            'https://api.snippe.sh/api/v1/sessions' => Http::response([
                'data' => [
                    'reference' => 'SNP-REMOTE-123',
                    'status' => 'pending',
                    'checkout_url' => 'https://checkout.snippe.sh/sessions/SNP-REMOTE-123',
                    'expires_at' => '2026-03-20T10:00:00Z',
                ],
            ], 201),
        ]);

        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.snippe@rgc.test');

        $this->actingAs($branchAdmin)
            ->post(route('offerings.payments.store'), [
                'offering_date' => '2026-03-19',
                'amount' => '25000',
                'payer_name' => 'Neema Joseph',
                'payer_phone' => '255712345678',
                'payer_email' => 'neema@example.com',
                'description' => 'Thanksgiving offering',
            ])
            ->assertRedirect(route('offerings.index'));

        $payment = OfferingPayment::query()->latest('id')->firstOrFail();

        $this->assertSame($branch->id, $payment->church_id);
        $this->assertSame('pending', $payment->status);
        $this->assertSame('pending', $payment->provider_status);
        $this->assertSame('https://checkout.snippe.sh/sessions/SNP-REMOTE-123', $payment->checkout_url);
        $this->assertSame('Neema Joseph', $payment->payer_name);
        $this->assertSame('Thanksgiving offering', $payment->description);

        Http::assertSent(function ($request) use ($payment) {
            return $request->url() === 'https://api.snippe.sh/api/v1/sessions'
                && $request->hasHeader('Authorization', 'Bearer test-snippe-key')
                && data_get($request->data(), 'reference') === $payment->provider_reference
                && data_get($request->data(), 'amount') === 25000.0
                && data_get($request->data(), 'customer_name') === 'Neema Joseph';
        });
    }

    public function test_completed_snippe_webhook_marks_payment_paid_and_creates_offering(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.snippe.webhook@rgc.test');

        $payment = OfferingPayment::query()->create([
            'church_id' => $branch->id,
            'user_id' => $branchAdmin->id,
            'amount' => 56000,
            'currency' => 'TZS',
            'offering_date' => '2026-03-19',
            'payer_name' => 'Agnes Paul',
            'description' => 'Sunday thanksgiving',
            'status' => 'pending',
        ]);

        $payload = [
            'type' => 'payment.completed',
            'data' => [
                'reference' => $payment->provider_reference,
                'status' => 'completed',
                'amount' => 56000,
            ],
        ];

        $rawPayload = json_encode($payload, JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $rawPayload, 'test-snippe-secret');

        $response = $this->call(
            'POST',
            route('payments.snippe.webhook'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
            ],
            $rawPayload,
        );

        $response->assertOk()->assertJson(['received' => true]);

        $payment->refresh();

        $this->assertSame('completed', $payment->status);
        $this->assertNotNull($payment->offering_id);
        $this->assertNotNull($payment->paid_at);

        $this->assertDatabaseHas('offerings', [
            'id' => $payment->offering_id,
            'church_id' => $branch->id,
            'amount' => 56000,
            'description' => 'Sunday thanksgiving',
            'recorded_by' => 'Snippe Checkout',
        ]);
    }

    public function test_completed_snippe_webhook_sends_receipt_email_once(): void
    {
        Mail::fake();
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.snippe.mail@rgc.test');

        $payment = OfferingPayment::query()->create([
            'church_id' => $branch->id,
            'user_id' => $branchAdmin->id,
            'amount' => 78000,
            'currency' => 'TZS',
            'offering_date' => '2026-03-19',
            'payer_name' => 'Mail Receiver',
            'payer_email' => 'receiver@example.com',
            'description' => 'Mail receipt test',
            'status' => 'pending',
        ]);

        $payload = [
            'type' => 'payment.completed',
            'data' => [
                'reference' => $payment->provider_reference,
                'status' => 'completed',
                'amount' => 78000,
            ],
        ];

        $rawPayload = json_encode($payload, JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $rawPayload, 'test-snippe-secret');

        $this->call(
            'POST',
            route('payments.snippe.webhook'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
            ],
            $rawPayload,
        )->assertOk();

        $payment->refresh();

        Mail::assertSent(OfferingPaymentReceiptMail::class, function (OfferingPaymentReceiptMail $mail) use ($payment) {
            return $mail->payment->is($payment);
        });

        $this->assertNotNull($payment->receipt_emailed_at);

        $this->call(
            'POST',
            route('payments.snippe.webhook'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
            ],
            $rawPayload,
        )->assertOk();

        Mail::assertSent(OfferingPaymentReceiptMail::class, 1);
    }

    public function test_completed_snippe_webhook_sends_branch_admin_alert_email_once(): void
    {
        Mail::fake();
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.alerts@rgc.test');
        $this->makeUser('pastor', $region, $district, $branch, 'pastor.alerts@rgc.test');

        $payment = OfferingPayment::query()->create([
            'church_id' => $branch->id,
            'user_id' => $branchAdmin->id,
            'amount' => 91000,
            'currency' => 'TZS',
            'offering_date' => '2026-03-19',
            'payer_name' => 'Branch Alert Giver',
            'payer_email' => 'giver.alerts@example.com',
            'description' => 'Branch alert test',
            'status' => 'pending',
        ]);

        $payload = [
            'type' => 'payment.completed',
            'data' => [
                'reference' => $payment->provider_reference,
                'status' => 'completed',
                'amount' => 91000,
            ],
        ];

        $rawPayload = json_encode($payload, JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $rawPayload, 'test-snippe-secret');

        $this->call(
            'POST',
            route('payments.snippe.webhook'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
            ],
            $rawPayload,
        )->assertOk();

        $payment->refresh();

        Mail::assertSent(BranchPaymentCompletedMail::class, function (BranchPaymentCompletedMail $mail) use ($payment) {
            return $mail->payment->is($payment);
        });

        $this->assertNotNull($payment->admin_notified_at);

        $this->call(
            'POST',
            route('payments.snippe.webhook'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
            ],
            $rawPayload,
        )->assertOk();

        Mail::assertSent(BranchPaymentCompletedMail::class, 1);
    }

    public function test_snippe_webhook_rejects_invalid_signature(): void
    {
        $payload = [
            'type' => 'payment.completed',
            'data' => [
                'reference' => 'SNP-UNKNOWN',
                'status' => 'completed',
            ],
        ];

        $rawPayload = json_encode($payload, JSON_THROW_ON_ERROR);

        $response = $this->call(
            'POST',
            route('payments.snippe.webhook'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => 'bad-signature',
            ],
            $rawPayload,
        );

        $response->assertStatus(401)->assertJson(['message' => 'Invalid signature.']);
        $this->assertSame(0, Offering::query()->count());
    }

    public function test_member_can_open_giving_workspace_and_create_payment_link(): void
    {
        Http::fake([
            'https://api.snippe.sh/api/v1/sessions' => Http::response([
                'data' => [
                    'reference' => 'SNP-MEMBER-123',
                    'status' => 'pending',
                    'checkout_url' => 'https://checkout.snippe.sh/sessions/SNP-MEMBER-123',
                    'expires_at' => '2026-03-20T10:00:00Z',
                ],
            ], 201),
        ]);

        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $member = $this->makeUser('member', $region, $district, $branch, 'member.giving@rgc.test');

        $this->actingAs($member)
            ->get(route('giving.index'))
            ->assertOk()
            ->assertSeeText(__('Give to your branch securely'))
            ->assertSeeText($branch->name);

        $this->actingAs($member)
            ->post(route('giving.store'), [
                'payment_type' => 'sadaka',
                'offering_date' => '2026-03-19',
                'amount' => '15000',
                'payer_name' => 'Member Giving',
                'payer_phone' => '255712222222',
                'payer_email' => 'member.giving@rgc.test',
                'description' => 'Evening sadaka',
            ])
            ->assertRedirect(route('giving.index'));

        $payment = OfferingPayment::query()->latest('id')->firstOrFail();

        $this->assertSame($member->id, $payment->user_id);
        $this->assertSame($branch->id, $payment->church_id);
        $this->assertSame('pending', $payment->status);
        $this->assertSame('sadaka', data_get($payment->metadata, 'payment_type'));
        $this->assertSame('Evening sadaka', $payment->description);
    }

    public function test_offerings_payment_list_shows_review_metadata(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.review.meta@rgc.test');

        OfferingPayment::query()->create([
            'church_id' => $branch->id,
            'user_id' => $branchAdmin->id,
            'amount' => 27000,
            'currency' => 'TZS',
            'offering_date' => now()->toDateString(),
            'payer_name' => 'Reviewed Payer',
            'description' => 'Reviewed payment metadata',
            'status' => 'completed',
            'paid_at' => now(),
            'reviewed_at' => now(),
            'reviewed_by' => $branchAdmin->id,
        ]);

        $this->actingAs($branchAdmin)
            ->get(route('offerings.index'))
            ->assertOk()
            ->assertSee('Reviewed')
            ->assertSee('Reviewed by')
            ->assertSee($branchAdmin->name)
            ->assertSee('Reviewed payment metadata');
    }
    public function test_completed_payment_status_page_shows_receipt_download_and_pdf_is_accessible(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.snippe.receipt@rgc.test');

        $offering = Offering::query()->create([
            'church_id' => $branch->id,
            'amount' => 43000,
            'date' => now()->toDateString(),
            'recorded_by' => 'Snippe Checkout',
            'description' => 'Special thanksgiving',
        ]);

        $payment = OfferingPayment::query()->create([
            'church_id' => $branch->id,
            'user_id' => $branchAdmin->id,
            'offering_id' => $offering->id,
            'amount' => 43000,
            'currency' => 'TZS',
            'offering_date' => now()->toDateString(),
            'payer_name' => 'Dorcas John',
            'payer_email' => 'dorcas@example.com',
            'payer_phone' => '255712000001',
            'description' => 'Special thanksgiving',
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        $this->get(route('offerings.payments.public.show', $payment->public_reference))
            ->assertOk()
            ->assertSeeText(__('Download receipt PDF'))
            ->assertSeeText(__('Copy reference'))
            ->assertSeeText(__('Share status page'))
            ->assertSeeText('Dorcas John')
            ->assertSeeText(__('Special thanksgiving'));

        $this->get(route('offerings.payments.public.receipt', $payment->public_reference))
            ->assertOk()
            ->assertHeader('content-disposition');
    }

    private function darHeadquartersContext(): array
    {
        $region = Region::query()->where('name', 'Dar es Salaam')->firstOrFail();
        $district = District::query()->where('region_id', $region->id)->where('name', 'Temeke')->firstOrFail();
        $branch = Branch::query()->where('name', 'Toangoma')->firstOrFail();

        return [$region, $district, $branch];
    }

    private function makeUser(string $role, Region $region, District $district, Branch $branch, string $email): User
    {
        $user = User::query()->create([
            'name' => Str::headline(str_replace(['@rgc.test', '.'], ['', ' '], $email)),
            'email' => $email,
            'password' => 'ChangeMe123!',
            'role' => $role,
            'status' => 'active',
            'region_id' => $region->id,
            'district_id' => $district->id,
            'branch_id' => $branch->id,
            'church_id' => $branch->id,
            'email_verified_at' => now(),
        ]);

        $user->syncRoles([$role]);

        return $user;
    }
}
