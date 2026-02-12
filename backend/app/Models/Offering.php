<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offering extends Model
{
    use HasFactory;

    protected $fillable = [
        'church_id',
        'amount',
        'date',
        'recorded_by'
    ];

    public function church()
    {
        return $this->belongsTo(Church::class, 'church_id');
    }
}

