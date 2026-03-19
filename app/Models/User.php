<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'api_token',
        'role',
        'status',
        'locale',
        'region_id',
        'district_id',
        'branch_id',
        'church_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'church_id');
    }

    public function sentBranchMessages(): HasMany
    {
        return $this->hasMany(BranchMessage::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }

    public function offeringPayments(): HasMany
    {
        return $this->hasMany(OfferingPayment::class);
    }

    public function normalizedRoleName(?string $role = null): ?string
    {
        $value = $role;

        if ($value === null) {
            $value = $this->getRoleNames()->first() ?: $this->role;
        }

        if (! $value) {
            return null;
        }

        return Str::of($value)
            ->lower()
            ->replace('-', '_')
            ->replace(' ', '_')
            ->value();
    }

    public function hasSystemRole(string $role): bool
    {
        $normalized = $this->normalizedRoleName($role);

        if (! $normalized) {
            return false;
        }

        $spatieRoles = $this->getRoleNames()->map(fn ($item) => $this->normalizedRoleName($item));

        return $spatieRoles->contains($normalized) || $this->normalizedRoleName($this->role) === $normalized;
    }

    public function hasAnySystemRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasSystemRole($role)) {
                return true;
            }
        }

        return false;
    }

    public function effectiveBranchId(): ?int
    {
        return $this->church_id ?: $this->branch_id;
    }

    public function canManageRegion(int $regionId): bool
    {
        return $this->hasSystemRole('super_admin') || (int) $this->region_id === $regionId;
    }

    public function canManageDistrict(int $districtId): bool
    {
        if ($this->hasSystemRole('super_admin')) {
            return true;
        }

        if ($this->hasSystemRole('regional_admin')) {
            return (int) optional($this->district)->region_id === (int) $this->region_id;
        }

        return (int) $this->district_id === $districtId;
    }

    public function canManageBranch(int $branchId): bool
    {
        if ($this->hasSystemRole('super_admin')) {
            return true;
        }

        if ($this->hasSystemRole('regional_admin')) {
            return Branch::query()->where('id', $branchId)->where('region_id', $this->region_id)->exists();
        }

        if ($this->hasSystemRole('district_admin')) {
            return Branch::query()->where('id', $branchId)->where('district_id', $this->district_id)->exists();
        }

        return (int) $this->effectiveBranchId() === $branchId;
    }
}
