<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchMessage extends Model
{
    protected $fillable = [
        'church_id',
        'user_id',
        'message',
    ];

    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class, 'church_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
