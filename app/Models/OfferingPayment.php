<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class OfferingPayment extends Model
{
    protected $fillable = [
        'church_id',
        'user_id',
        'offering_id',
        'public_reference',
        'provider',
        'provider_reference',
        'status',
        'provider_status',
        'amount',
        'currency',
        'offering_date',
        'payer_name',
        'payer_phone',
        'payer_email',
        'description',
        'checkout_url',
        'expires_at',
        'paid_at',
        'failed_at',
        'receipt_emailed_at',
        'admin_notified_at',
        'reviewed_at',
        'reviewed_by',
        'metadata',
        'provider_payload',
        'last_webhook_payload',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'offering_date' => 'date',
            'expires_at' => 'datetime',
            'paid_at' => 'datetime',
            'failed_at' => 'datetime',
            'receipt_emailed_at' => 'datetime',
            'admin_notified_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'reviewed_by' => 'integer',
            'metadata' => 'array',
            'provider_payload' => 'array',
            'last_webhook_payload' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $payment): void {
            if (! $payment->public_reference) {
                $payment->public_reference = 'RGC-PAY-'.Str::upper(Str::random(12));
            }

            if (! $payment->provider_reference) {
                $payment->provider_reference = 'SNP-'.Str::upper(Str::random(20));
            }
        });
    }

    public function getBranchIdAttribute(): ?int
    {
        return $this->church_id;
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'church_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function offering(): BelongsTo
    {
        return $this->belongsTo(Offering::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'completed' => __('Completed'),
            'failed' => __('Failed'),
            'expired' => __('Expired'),
            default => __('Pending'),
        };
    }

    public function paymentType(): string
    {
        return (string) data_get($this->metadata, 'payment_type', 'offering');
    }

    public function paymentTypeLabel(): string
    {
        return match ($this->paymentType()) {
            'sadaka' => __('Sadaka'),
            'thanksgiving' => __('Thanksgiving'),
            'special_contribution' => __('Special Contribution'),
            'project_support' => __('Project Support'),
            default => __('Offering'),
        };
    }

    public function paymentFlow(): string
    {
        return (string) data_get($this->metadata, 'payment_flow', 'mobile_prompt');
    }

    public function usesHostedCheckout(): bool
    {
        return $this->paymentFlow() === 'checkout_session';
    }

    public function requestedNetwork(): ?string
    {
        $value = (string) data_get($this->metadata, 'requested_network', '');

        return $value !== '' ? $value : null;
    }

    public function requestedNetworkLabel(): string
    {
        return match ($this->requestedNetwork()) {
            'mpesa' => __('M-Pesa'),
            'airtel_money' => __('Airtel Money'),
            'mixx_by_yas' => __('Mixx by Yas'),
            'halopesa' => __('HaloPesa'),
            default => __('Auto detect'),
        };
    }

    public function providerChannelLabel(): string
    {
        $provider = strtolower((string) data_get($this->metadata, 'provider_channel', ''));

        return match ($provider) {
            'mpesa', 'm-pesa' => __('M-Pesa'),
            'airtel', 'airtel_money' => __('Airtel Money'),
            'tigo', 'mixx', 'mixx_by_yas', 'yas' => __('Mixx by Yas'),
            'halotel', 'halopesa' => __('HaloPesa'),
            default => __('Waiting for provider confirmation'),
        };
    }

    public function externalReference(): ?string
    {
        $value = (string) data_get($this->metadata, 'external_reference', '');

        return $value !== '' ? $value : null;
    }

    public function publicStatusUrl(): string
    {
        return route('offerings.payments.public.show', $this->public_reference);
    }

    public function temporaryPublicReceiptUrl(int $minutes = 30): string
    {
        return URL::temporarySignedRoute(
            'offerings.payments.public.receipt',
            now()->addMinutes(max(1, $minutes)),
            ['publicReference' => $this->public_reference],
        );
    }

    public function maskedPayerName(): string
    {
        $name = trim((string) $this->payer_name);

        if ($name === '') {
            return __('Walk-in giver');
        }

        return collect(preg_split('/\s+/', $name) ?: [])
            ->filter()
            ->map(function (string $part): string {
                $length = Str::length($part);

                if ($length <= 1) {
                    return $part;
                }

                return Str::substr($part, 0, 1) . str_repeat('*', max(1, $length - 1));
            })
            ->implode(' ');
    }

    public function maskedPayerEmail(): string
    {
        $email = trim((string) $this->payer_email);

        if ($email === '' || ! str_contains($email, '@')) {
            return __('Not provided');
        }

        [$local, $domain] = explode('@', $email, 2);
        $localLength = Str::length($local);

        return ($localLength <= 1
            ? str_repeat('*', max(1, $localLength))
            : Str::substr($local, 0, 1) . str_repeat('*', max(1, $localLength - 1)))
            . '@' . $domain;
    }

    public function maskedPayerPhone(): string
    {
        $phone = preg_replace('/\s+/', '', (string) $this->payer_phone) ?? '';

        if ($phone === '') {
            return __('Not provided');
        }

        $length = Str::length($phone);

        if ($length <= 4) {
            return str_repeat('*', $length);
        }

        return str_repeat('*', max(1, $length - 4)) . Str::substr($phone, -4);
    }
}
