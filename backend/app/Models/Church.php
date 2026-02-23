<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Church extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'region_id',
        'district_id',
        'name',
        'type',
        'slug',
        'address',
        'phone',
        'email',
        'pastor_id',
        'status',
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function pastor(): BelongsTo
    {
        return $this->belongsTo(Pastor::class, 'pastor_id');
    }

    public function pastors(): HasMany
    {
        return $this->hasMany(Pastor::class, 'church_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class, 'church_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'church_id');
    }

    public function branchMessages(): HasMany
    {
        return $this->hasMany(BranchMessage::class, 'church_id');
    }
}
