<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Church extends Model
{
    protected $fillable = [
        'district_id','name','slug','address','phone','email','pastor_id','status'
    ];

    public function pastors(): HasMany
    {
        return $this->hasMany(Pastor::class, 'branch_id');
    }

    public function district()
    {
        return $this->belongsTo(Region::class, 'district_id'); // if you used District model, change accordingly
    }
}
