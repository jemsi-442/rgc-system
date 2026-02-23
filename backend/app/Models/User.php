<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'status',
        'role',
        'region_id',
        'district_id',
        'branch_id',
        'church_id',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class, 'church_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Church::class, 'branch_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function branchMessages(): HasMany
    {
        return $this->hasMany(BranchMessage::class, 'user_id');
    }

    public function hasAnyRoleOrLegacy(array $roles): bool
    {
        if ($this->hasAnyRole($roles)) {
            return true;
        }

        return in_array($this->role, $roles, true);
    }
}
