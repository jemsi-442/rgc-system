<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
}
