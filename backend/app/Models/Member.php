<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'church_id',
        'first_name',
        'last_name',
        'dob',
        'phone',
        'email',
        'gender',
        'marital_status',
        'address',
        'notes',
    ];

    public function church()
    {
        return $this->belongsTo(Church::class);
    }
}
