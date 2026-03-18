<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeCategory extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'target_amount',
        'minimum_expected',
        'maximum_expected',
        'income_level',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'target_amount' => 'decimal:2',
        'minimum_expected' => 'decimal:2',
        'maximum_expected' => 'decimal:2',
    ];

    // Relationships
    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    // Scope for active categories
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for ordered categories
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('code');
    }
}
