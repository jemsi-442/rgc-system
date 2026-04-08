<?php

namespace App\Services\Snippe;

use App\Models\OfferingPayment;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class SnippeClient
{
    public function createPayment(OfferingPayment $payment, array $options = []): array
    {
        $response = $this->request()
            ->withHeaders([
                'Idempotency-Key' => (string) Str::uuid(),
            ])
            ->post('/v1/payments', $this->paymentPayload($payment, $options))
            ->throw()
            ->json();

        return is_array($response) ? $response : [];
    }

    public function createSession(OfferingPayment $payment, array $options = []): array
    {
        $response = $this->request()
            ->withHeaders([
                'Idempotency-Key' => (string) Str::uuid(),
            ])
            ->post('/api/v1/sessions', $this->sessionPayload($payment, $options))
            ->throw()
            ->json();

        return is_array($response) ? $response : [];
    }

    public function fetchPayment(string $reference): array
    {
        $response = $this->request()
            ->get('/v1/payments/'.$reference)
            ->throw()
            ->json();

        return is_array($response) ? $response : [];
    }

    public function fetchSession(string $reference): array
    {
        $response = $this->request()
            ->get('/api/v1/sessions/'.$reference)
            ->throw()
            ->json();

        return is_array($response) ? $response : [];
    }

    public function verifyWebhookSignature(string $payload, ?string $signature): bool
    {
        $secret = (string) config('services.snippe.webhook_secret');

        if ($payload === '' || $signature === null || $signature === '' || $secret === '') {
            return false;
        }

        $provided = trim($signature);
        $provided = Str::startsWith($provided, 'sha256=') ? Str::after($provided, 'sha256=') : $provided;
        $expected = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expected, $provided);
    }

    public function extractCheckoutUrl(array $response): ?string
    {
        return Arr::get($response, 'data.checkout_url')
            ?? Arr::get($response, 'data.payment_url')
            ?? Arr::get($response, 'data.url')
            ?? Arr::get($response, 'data.links.checkout')
            ?? Arr::get($response, 'data._links.redirect.href');
    }

    public function extractExternalReference(array $response): ?string
    {
        return Arr::get($response, 'data.external_reference')
            ?? Arr::get($response, 'data.externalReference');
    }

    public function extractChannelProvider(array $response): ?string
    {
        return Arr::get($response, 'data.channel.provider')
            ?? Arr::get($response, 'data.provider');
    }

    public function extractChannelType(array $response): ?string
    {
        return Arr::get($response, 'data.channel.type')
            ?? Arr::get($response, 'data.channel');
    }

    public function extractSettlementGross(array $response): ?float
    {
        return $this->extractMoneyValue($response, 'data.settlement.gross.value');
    }

    public function extractSettlementFees(array $response): ?float
    {
        return $this->extractMoneyValue($response, 'data.settlement.fees.value');
    }

    public function extractSettlementNet(array $response): ?float
    {
        return $this->extractMoneyValue($response, 'data.settlement.net.value');
    }

    protected function extractMoneyValue(array $response, string $path): ?float
    {
        $value = Arr::get($response, $path);

        return is_numeric($value) ? (float) $value : null;
    }

    protected function paymentPayload(OfferingPayment $payment, array $options): array
    {
        [$firstName, $lastName] = $this->splitCustomerName($payment->payer_name);

        return array_filter([
            'payment_type' => 'mobile',
            'reference' => $payment->provider_reference,
            'details' => [
                'amount' => (int) round((float) $payment->amount),
                'currency' => $payment->currency,
            ],
            'phone_number' => $payment->payer_phone,
            'customer' => array_filter([
                'firstname' => $firstName,
                'lastname' => $lastName,
                'email' => $payment->payer_email ?: 'payments@rgc.local',
            ], static fn ($value) => $value !== null && $value !== ''),
            'webhook_url' => $options['webhook_url'] ?? null,
            'metadata' => array_filter([
                'payment_id' => $payment->id,
                'public_reference' => $payment->public_reference,
                'branch_id' => $payment->church_id,
                'branch_name' => $payment->branch?->name,
                'initiated_by' => $payment->user?->email,
                'payment_type' => $payment->paymentType(),
                'requested_network' => data_get($payment->metadata, 'requested_network'),
                'payment_flow' => data_get($payment->metadata, 'payment_flow', 'mobile_prompt'),
                'description' => $payment->description ?: __('Offering payment for :branch', ['branch' => $payment->branch?->name ?? 'RGC']),
            ]),
        ], static fn ($value) => $value !== null && $value !== '');
    }

    protected function splitCustomerName(?string $name): array
    {
        $parts = collect(preg_split('/\s+/', trim((string) $name)) ?: [])
            ->filter()
            ->values();

        if ($parts->isEmpty()) {
            return ['Customer', 'Payment'];
        }

        if ($parts->count() === 1) {
            return [$parts->first(), 'Payment'];
        }

        return [$parts->first(), $parts->slice(1)->implode(' ')];
    }

    protected function sessionPayload(OfferingPayment $payment, array $options): array
    {
        return array_filter([
            'reference' => $payment->provider_reference,
            'amount' => (float) $payment->amount,
            'currency' => $payment->currency,
            'description' => $payment->description ?: __('Offering payment for :branch', ['branch' => $payment->branch?->name ?? 'RGC']),
            'customer_name' => $payment->payer_name,
            'customer_phone' => $payment->payer_phone,
            'customer_email' => $payment->payer_email,
            'return_url' => $options['return_url'] ?? null,
            'cancel_url' => $options['cancel_url'] ?? ($options['return_url'] ?? null),
            'webhook_url' => $options['webhook_url'] ?? null,
            'metadata' => array_filter([
                'payment_id' => $payment->id,
                'public_reference' => $payment->public_reference,
                'branch_id' => $payment->church_id,
                'branch_name' => $payment->branch?->name,
                'initiated_by' => $payment->user?->email,
                'payment_type' => $payment->paymentType(),
            ]),
        ], static fn ($value) => $value !== null && $value !== '');
    }

    protected function request(): PendingRequest
    {
        $apiKey = (string) config('services.snippe.api_key');

        if ($apiKey === '') {
            throw new RuntimeException('Snippe API key is not configured.');
        }

        return Http::acceptJson()
            ->asJson()
            ->baseUrl((string) config('services.snippe.base_url', 'https://api.snippe.sh'))
            ->withToken($apiKey)
            ->timeout((int) config('services.snippe.timeout', 15));
    }

    public function extractStatus(array $response): ?string
    {
        return Arr::get($response, 'data.status')
            ?? Arr::get($response, 'status')
            ?? Arr::get($response, 'data.payment_status');
    }

    public function extractExpiry(array $response): ?string
    {
        return Arr::get($response, 'data.expires_at')
            ?? Arr::get($response, 'data.expiresAt')
            ?? Arr::get($response, 'data.expiry_date');
    }
}
