<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Income extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'income_category_id',
        'collection_date',
        'amount',
        'notes',
        'receipt_number',
        'member_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'collection_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(IncomeCategory::class, 'income_category_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
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
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('collection_date', $date);
    }

    public function scopeByMonth($query, $year, $month)
    {
        return $query->whereYear('collection_date', $year)
                     ->whereMonth('collection_date', $month);
    }

    public function scopeByYear($query, $year)
    {
        return $query->whereYear('collection_date', $year);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('income_category_id', $categoryId);
    }
}
