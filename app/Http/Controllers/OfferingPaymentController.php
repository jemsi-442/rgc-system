<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfferingPaymentRequest;
use App\Mail\BranchPaymentCompletedMail;
use App\Mail\OfferingPaymentReceiptMail;
use App\Models\Branch;
use App\Models\Offering;
use App\Models\OfferingPayment;
use App\Models\User;
use App\Services\Snippe\SnippeClient;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class OfferingPaymentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $branch = Branch::query()
            ->with(['region', 'district'])
            ->findOrFail($user->effectiveBranchId());

        $payments = OfferingPayment::query()
            ->where('user_id', $user->id)
            ->with('branch')
            ->latest()
            ->paginate(10);

        $stats = [
            'payments' => OfferingPayment::query()->where('user_id', $user->id)->count(),
            'pending' => OfferingPayment::query()->where('user_id', $user->id)->where('status', 'pending')->count(),
            'completed' => OfferingPayment::query()->where('user_id', $user->id)->where('status', 'completed')->count(),
            'completed_total' => (float) OfferingPayment::query()->where('user_id', $user->id)->where('status', 'completed')->sum('amount'),
        ];

        return view('panel.giving.index', [
            'branch' => $branch,
            'payments' => $payments,
            'stats' => $stats,
            'paymentTypes' => $this->paymentTypeOptions(),
        ]);
    }

    public function store(StoreOfferingPaymentRequest $request, SnippeClient $snippe): RedirectResponse
    {
        return $this->createPaymentRequest($request, $snippe, 'dashboard', 'offerings.index');
    }

    public function memberStore(StoreOfferingPaymentRequest $request, SnippeClient $snippe): RedirectResponse
    {
        return $this->createPaymentRequest($request, $snippe, 'member_giving', 'giving.index');
    }

    public function sync(Request $request, OfferingPayment $payment, SnippeClient $snippe): RedirectResponse
    {
        $this->authorize('sync', $payment);

        try {
            $response = $payment->usesHostedCheckout()
                ? $snippe->fetchSession($payment->provider_reference)
                : $snippe->fetchPayment($payment->provider_reference);
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors(['snippe' => __('Unable to refresh payment status right now.')]);
        }

        $status = $snippe->extractStatus($response) ?: 'pending';
        $normalizedStatus = $this->normalizeStatus($status);

        $payment->update([
            'provider_status' => $status,
            'status' => $normalizedStatus,
            'provider_payload' => $response,
            'metadata' => $this->mergeProviderMetadata($payment, $response),
            'expires_at' => $snippe->extractExpiry($response) ? Carbon::parse($snippe->extractExpiry($response)) : $payment->expires_at,
            'paid_at' => $normalizedStatus === 'completed' ? ($payment->paid_at ?: now()) : $payment->paid_at,
            'failed_at' => $normalizedStatus === 'failed' ? now() : $payment->failed_at,
        ]);

        if ($payment->fresh()->status === 'completed') {
            $completedPayment = $payment->fresh();
            $this->ensureOfferingExists($completedPayment);
            $completedPayment = $completedPayment->fresh(['branch.region', 'branch.district', 'user', 'offering']);
            $this->sendCompletionReceiptIfNeeded($completedPayment);
            $this->sendAdminPaymentNotificationIfNeeded($completedPayment);
        }

        return back()->with('status', __('Payment status refreshed.'));
    }

    public function publicShow(string $publicReference): Response
    {
        $payment = OfferingPayment::query()
            ->with(['branch', 'offering'])
            ->where('public_reference', $publicReference)
            ->firstOrFail();

        return response()
            ->view('panel.offerings.payment-status', compact('payment'))
            ->header('Cache-Control', 'private, no-store, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('X-Robots-Tag', 'noindex, nofollow, noarchive');
    }

    public function publicReceipt(string $publicReference): Response
    {
        $payment = OfferingPayment::query()
            ->with(['branch.region', 'branch.district', 'offering'])
            ->where('public_reference', $publicReference)
            ->where('status', 'completed')
            ->firstOrFail();

        $pdf = Pdf::loadView('panel.offerings.receipt-pdf', [
            'payment' => $payment,
            'logoDataUri' => $this->pdfLogoDataUri(),
        ])->setPaper('a4');

        $response = $pdf->download('offering-receipt-' . $payment->public_reference . '.pdf');
        $response->headers->set('Cache-Control', 'private, no-store, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');

        return $response;
    }

    public function review(Request $request, OfferingPayment $payment): RedirectResponse
    {
        $this->authorize('review', $payment);

        if (! $payment->reviewed_at) {
            $payment->forceFill([
                'reviewed_at' => now(),
                'reviewed_by' => $request->user()->id,
            ])->save();
        }

        return back()->with('status', __('Payment alert marked as reviewed.'));
    }
    public function reviewAll(Request $request): RedirectResponse
    {
        $user = $request->user();
        $this->authorize('reviewAny', OfferingPayment::class);

        $updated = $this->reviewableAlertQuery($user)
            ->update([
                'reviewed_at' => now(),
                'reviewed_by' => $user->id,
            ]);

        return back()->with('status', $updated > 0
            ? __('All visible payment alerts were marked as reviewed.')
            : __('No payment alerts were waiting for review.'));
    }
    public function webhook(Request $request, SnippeClient $snippe)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Webhook-Signature');

        if (! $snippe->verifyWebhookSignature($payload, $signature)) {
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        $event = $request->json()->all();
        $reference = data_get($event, 'data.reference');

        if (! $reference) {
            return response()->json(['message' => 'Missing payment reference.'], 422);
        }

        $payment = OfferingPayment::query()->where('provider_reference', $reference)->first();

        if (! $payment) {
            Log::warning('Snippe webhook reference not found.', ['reference' => $reference]);

            return response()->json(['message' => 'Payment reference not found.'], 404);
        }

        $eventType = (string) data_get($event, 'type', '');
        $providerStatus = (string) data_get($event, 'data.status', '');
        $normalizedStatus = $this->normalizeStatus($providerStatus ?: $eventType);

        $payment->update([
            'provider_status' => $providerStatus ?: $eventType,
            'status' => $normalizedStatus,
            'last_webhook_payload' => $event,
            'provider_payload' => $payment->provider_payload ?: $event,
            'metadata' => $this->mergeProviderMetadata($payment, $event),
            'paid_at' => $normalizedStatus === 'completed' ? ($payment->paid_at ?: now()) : $payment->paid_at,
            'failed_at' => $normalizedStatus === 'failed' ? now() : $payment->failed_at,
        ]);

        if ($normalizedStatus === 'completed') {
            $completedPayment = $payment->fresh();
            $this->ensureOfferingExists($completedPayment);
            $completedPayment = $completedPayment->fresh(['branch.region', 'branch.district', 'user', 'offering']);
            $this->sendCompletionReceiptIfNeeded($completedPayment);
            $this->sendAdminPaymentNotificationIfNeeded($completedPayment);
        }

        return response()->json(['received' => true]);
    }

    protected function reviewableAlertQuery(User $user)
    {
        $query = OfferingPayment::query()->whereNull('reviewed_at');

        if ($user->hasSystemRole('super_admin')) {
            return $query;
        }

        if ($user->hasSystemRole('regional_admin')) {
            $branchIds = Branch::query()->where('region_id', $user->region_id)->pluck('id');

            return $query->whereIn('church_id', $branchIds);
        }

        if ($user->hasSystemRole('district_admin')) {
            $branchIds = Branch::query()->where('district_id', $user->district_id)->pluck('id');

            return $query->whereIn('church_id', $branchIds);
        }

        return $query->where('church_id', $user->effectiveBranchId());
    }
    protected function createPaymentRequest(StoreOfferingPaymentRequest $request, SnippeClient $snippe, string $initiatedFrom, string $redirectRoute): RedirectResponse
    {
        $user = $request->user();
        $branchId = $user->effectiveBranchId();

        abort_unless($branchId !== null, 403);

        $paymentType = $this->normalizePaymentType($request->input('payment_type'));
        $paymentFlow = $this->paymentFlow();

        $payment = DB::transaction(function () use ($request, $user, $branchId, $initiatedFrom, $paymentType, $paymentFlow): OfferingPayment {
            return OfferingPayment::query()->create([
                'church_id' => $branchId,
                'user_id' => $user->id,
                'amount' => $request->input('amount'),
                'currency' => 'TZS',
                'offering_date' => $request->input('offering_date') ?: now()->toDateString(),
                'payer_name' => $request->input('payer_name'),
                'payer_phone' => $request->input('payer_phone'),
                'payer_email' => $request->input('payer_email'),
                'description' => $this->resolveDescription($request, $paymentType),
                'metadata' => [
                    'initiated_from' => $initiatedFrom,
                    'payment_type' => $paymentType,
                    'payment_flow' => $paymentFlow,
                    'requested_network' => $request->input('mobile_network'),
                ],
            ]);
        });

        try {
            $response = $paymentFlow === 'checkout_session'
                ? $snippe->createSession($payment, [
                    'return_url' => route('offerings.payments.public.show', $payment->public_reference),
                    'cancel_url' => route('offerings.payments.public.show', $payment->public_reference),
                    'webhook_url' => $this->secureWebhookUrl(),
                ])
                : $snippe->createPayment($payment, [
                    'channel' => 'mobile',
                    'webhook_url' => $this->secureWebhookUrl(),
                ]);
        } catch (\Throwable $e) {
            $payment->update([
                'status' => 'failed',
                'provider_status' => 'payment_request_error',
                'provider_payload' => [
                    'message' => $e->getMessage(),
                ],
                'failed_at' => now(),
            ]);

            report($e);

            return back()
                ->withInput()
                ->withErrors(['snippe' => __('Unable to start the payment request right now. Please try again.')]);
        }

        $providerStatus = $snippe->extractStatus($response) ?: 'pending';
        $normalizedStatus = $this->normalizeStatus($providerStatus);

        $payment->update([
            'provider_reference' => data_get($response, 'data.reference', $payment->provider_reference),
            'checkout_url' => $snippe->extractCheckoutUrl($response),
            'provider_status' => $providerStatus,
            'status' => $normalizedStatus,
            'provider_payload' => $response,
            'metadata' => $this->mergeProviderMetadata($payment, $response),
            'expires_at' => $snippe->extractExpiry($response) ? Carbon::parse($snippe->extractExpiry($response)) : null,
            'paid_at' => $normalizedStatus === 'completed' ? now() : null,
            'failed_at' => $normalizedStatus === 'failed' ? now() : null,
        ]);

        $redirect = redirect()
            ->route($redirectRoute)
            ->with('payment_reference', $payment->public_reference);

        if ($paymentFlow === 'checkout_session' && $payment->checkout_url) {
            return $redirect
                ->with('status', __('Payment checkout link created successfully.'))
                ->with('payment_link', $payment->checkout_url);
        }

        return $redirect
            ->with('status', __('Payment prompt sent to :phone. Ask the payer to approve it on their phone.', [
                'phone' => $payment->maskedPayerPhone(),
            ]))
            ->with('payment_prompt_phone', $payment->maskedPayerPhone());
    }

    protected function paymentTypeOptions(): array
    {
        return [
            'offering' => __('Offering'),
            'sadaka' => __('Sadaka'),
            'thanksgiving' => __('Thanksgiving'),
            'special_contribution' => __('Special Contribution'),
            'project_support' => __('Project Support'),
        ];
    }

    protected function normalizePaymentType(?string $value): string
    {
        $type = $value ?: 'offering';

        if (! array_key_exists($type, $this->paymentTypeOptions())) {
            return 'offering';
        }

        return $type;
    }

    protected function resolveDescription(StoreOfferingPaymentRequest $request, string $paymentType): string
    {
        $description = trim((string) $request->input('description', ''));

        if ($description !== '') {
            return $description;
        }

        return $this->paymentTypeOptions()[$paymentType] ?? __('Offering payment');
    }

    protected function paymentFlow(): string
    {
        return (string) config('services.snippe.payment_flow', 'mobile_prompt') === 'checkout_session'
            ? 'checkout_session'
            : 'mobile_prompt';
    }

    protected function secureWebhookUrl(): ?string
    {
        $url = route('payments.snippe.webhook');

        return str_starts_with($url, 'https://') ? $url : null;
    }

    protected function mergeProviderMetadata(OfferingPayment $payment, array $payload): array
    {
        $metadata = array_merge($payment->metadata ?? [], [
            'payment_type' => $payment->paymentType(),
            'payment_flow' => $payment->paymentFlow(),
            'requested_network' => $payment->requestedNetwork(),
            'provider_channel' => $this->stringOrNull(data_get($payload, 'data.channel.provider')),
            'provider_channel_type' => $this->stringOrNull(data_get($payload, 'data.channel.type')),
            'external_reference' => $this->stringOrNull(data_get($payload, 'data.external_reference')),
            'settlement_gross' => $this->numericOrNull(data_get($payload, 'data.settlement.gross.value')),
            'settlement_fees' => $this->numericOrNull(data_get($payload, 'data.settlement.fees.value')),
            'settlement_net' => $this->numericOrNull(data_get($payload, 'data.settlement.net.value')),
        ]);

        return array_filter($metadata, static fn ($value) => $value !== null && $value !== '');
    }

    protected function stringOrNull(mixed $value): ?string
    {
        $string = is_string($value) ? trim($value) : '';

        return $string !== '' ? $string : null;
    }

    protected function numericOrNull(mixed $value): float|int|null
    {
        return is_numeric($value) ? $value + 0 : null;
    }

    protected function sendAdminPaymentNotificationIfNeeded(OfferingPayment $payment): void
    {
        if (! $payment->isCompleted() || $payment->admin_notified_at) {
            return;
        }

        $recipients = User::query()
            ->where('church_id', $payment->church_id)
            ->where('status', 'active')
            ->whereNotNull('email')
            ->get()
            ->filter(fn (User $user) => $user->hasAnySystemRole(['branch_admin', 'pastor', 'bishop', 'accountant']))
            ->pluck('email')
            ->filter()
            ->unique()
            ->values();

        if ($recipients->isEmpty()) {
            return;
        }

        Mail::to($recipients->all())->send(new BranchPaymentCompletedMail(
            $payment->fresh(['branch.region', 'branch.district', 'user', 'offering'])
        ));

        $payment->forceFill([
            'admin_notified_at' => now(),
        ])->save();
    }

    protected function sendCompletionReceiptIfNeeded(OfferingPayment $payment): void
    {
        if (! $payment->isCompleted() || $payment->receipt_emailed_at) {
            return;
        }

        $recipient = $payment->payer_email ?: $payment->user?->email;

        if (! $recipient) {
            return;
        }

        Mail::to($recipient)->send(new OfferingPaymentReceiptMail(
            $payment,
            $this->pdfLogoDataUri(),
        ));

        $payment->forceFill([
            'receipt_emailed_at' => now(),
        ])->save();
    }

    protected function ensureOfferingExists(OfferingPayment $payment): void
    {
        if ($payment->offering_id) {
            return;
        }

        DB::transaction(function () use ($payment): void {
            $lockedPayment = OfferingPayment::query()->lockForUpdate()->findOrFail($payment->id);

            if ($lockedPayment->offering_id) {
                return;
            }

            $offering = Offering::query()->create([
                'church_id' => $lockedPayment->church_id,
                'recorded_by' => __('Snippe Checkout'),
                'date' => $lockedPayment->offering_date ?: now()->toDateString(),
                'amount' => $lockedPayment->amount,
                'description' => $lockedPayment->description ?: __('Snippe offering payment'),
            ]);

            $lockedPayment->update([
                'offering_id' => $offering->id,
            ]);
        });
    }

    protected function normalizeStatus(string $status): string
    {
        $normalized = strtolower($status);

        return match (true) {
            str_contains($normalized, 'complete') || str_contains($normalized, 'success') || str_contains($normalized, 'paid') => 'completed',
            str_contains($normalized, 'fail') || str_contains($normalized, 'cancel') || str_contains($normalized, 'expired') => 'failed',
            default => 'pending',
        };
    }

    protected function pdfLogoDataUri(): ?string
    {
        $path = public_path('images/rgc_logo.png');

        if (! is_file($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/png';
        $contents = file_get_contents($path);

        if ($contents === false) {
            return null;
        }

        return 'data:' . $mime . ';base64,' . base64_encode($contents);
    }
}
