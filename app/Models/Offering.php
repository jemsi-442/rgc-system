<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offering extends Model
{
    use SoftDeletes;

    protected $table = 'offerings';

    protected $fillable = [
        'church_id',
        'amount',
        'description',
        'date',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function getBranchIdAttribute(): ?int
    {
        return $this->church_id;
    }

    public function setBranchIdAttribute(int $value): void
    {
        $this->attributes['church_id'] = $value;
    }

    public function getOfferingDateAttribute()
    {
        return $this->date;
    }

    public function setOfferingDateAttribute($value): void
    {
        $this->attributes['date'] = $value;
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'church_id');
    }
}
