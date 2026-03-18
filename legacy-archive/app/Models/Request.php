<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'request_number',
        'title',
        'description',
        'department',
        'amount_requested',
        'amount_approved',
        'status',
        'approval_notes',
        'requested_date',
        'approved_date',
        'requested_by',
        'approved_by',
    ];

    protected $casts = [
        'amount_requested' => 'decimal:2',
        'amount_approved' => 'decimal:2',
        'requested_date' => 'date',
        'approved_date' => 'date',
    ];

    // Relationships
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'Inasubiri');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'Imeidhinishwa');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'Imekataliwa');
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }
}
