<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $table = 'expenses';

    protected $fillable = [
        'church_id',
        'recorded_by',
        'date',
        'amount',
        'description',
        'receipt_path',
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

    public function getExpenseDateAttribute()
    {
        return $this->date;
    }

    public function setExpenseDateAttribute($value): void
    {
        $this->attributes['date'] = $value;
    }

    public function getCategoryAttribute(): string
    {
        [$category] = $this->splitDescriptionParts();

        return $category;
    }

    public function getDescriptionBodyAttribute(): ?string
    {
        [, $details] = $this->splitDescriptionParts();

        return $details;
    }

    private function splitDescriptionParts(): array
    {
        $description = trim((string) $this->attributes['description'] ?? '');

        if ($description === '') {
            return ['General', null];
        }

        $parts = explode(': ', $description, 2);
        $category = trim($parts[0]) !== '' ? trim($parts[0]) : 'General';
        $details = isset($parts[1]) && trim($parts[1]) !== '' ? trim($parts[1]) : null;

        return [$category, $details];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'church_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
