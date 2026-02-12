<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'church_id',
        'amount',
        'description',
        'date',
        'recorded_by',
        'receipt_path',
    ];

    public function church()
    {
        return $this->belongsTo(Church::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
