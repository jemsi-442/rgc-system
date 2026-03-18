<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PastoralService extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'service_number',
        'member_id',
        'service_type',
        'preferred_date',
        'description',
        'status',
        'admin_notes',
        'approved_by',
        'approved_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
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

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Imekamilika');
    }

    public function scopeByServiceType($query, $type)
    {
        return $query->where('service_type', $type);
    }

    // Helper Methods
    public static function generateServiceNumber()
    {
        $year = date('Y');
        $lastService = self::whereYear('created_at', $year)
                          ->orderBy('id', 'desc')
                          ->first();

        $nextNumber = $lastService ? intval(substr($lastService->service_number, -3)) + 1 : 1;
        return 'PS' . $year . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    // Boot method to auto-generate service number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->service_number)) {
                $service->service_number = self::generateServiceNumber();
            }
        });
    }
}
