<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PledgePayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pledge_id',
        'member_id',
        'amount',
        'payment_date',
        'payment_method',
        'reference_number',
        'receipt_number',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    // Relationships
    public function pledge()
    {
        return $this->belongsTo(Pledge::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // Boot method to auto-generate receipt number and update pledge
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            // Generate unique receipt number
            if (empty($payment->receipt_number)) {
                $payment->receipt_number = 'RCP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            }
        });

        static::created(function ($payment) {
            // Update pledge amount_paid and status
            $pledge = $payment->pledge;
            $pledge->amount_paid += $payment->amount;
            $pledge->save();
            $pledge->updateStatus();
        });

        static::deleted(function ($payment) {
            // Decrease pledge amount_paid when payment is deleted
            $pledge = $payment->pledge;
            $pledge->amount_paid -= $payment->amount;
            if ($pledge->amount_paid < 0) $pledge->amount_paid = 0;
            $pledge->save();
            $pledge->updateStatus();
        });
    }
}
