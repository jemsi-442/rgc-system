<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pledge extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'member_id',
        'pledge_type',
        'amount',
        'amount_paid',
        'pledge_date',
        'due_date',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'pledge_date' => 'date',
        'due_date' => 'date',
    ];

    // Relationships
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function payments()
    {
        return $this->hasMany(PledgePayment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors
    public function getRemainingAmountAttribute()
    {
        return $this->amount - $this->amount_paid;
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->amount == 0) return 0;
        return ($this->amount_paid / $this->amount) * 100;
    }

    public function getIsCompletedAttribute()
    {
        return $this->amount_paid >= $this->amount;
    }

    // Methods
    public function updateStatus()
    {
        if ($this->amount_paid >= $this->amount) {
            $this->status = 'Completed';
        } elseif ($this->amount_paid > 0) {
            $this->status = 'Partial';
        } else {
            $this->status = 'Pending';
        }
        $this->save();
    }
}
