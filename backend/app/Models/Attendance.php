<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = "attendance";

    protected $fillable = [
        'church_id',
        'date',
        'men',
        'women',
        'youth',
        'children',
        'total',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
        'men' => 'integer',
        'women' => 'integer',
        'youth' => 'integer',
        'children' => 'integer',
        'total' => 'integer',
    ];

    // Relationships
    public function church()
    {
        return $this->belongsTo(Church::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
