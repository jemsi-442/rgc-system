<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pastor extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'title',
        'church_id',
    ];

    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class, 'church_id');
    }
}
