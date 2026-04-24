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
    public const SYSTEM_ROLE_RANK = [
        'member' => 10,
        'accountant' => 20,
        'pastor' => 30,
        'bishop' => 40,
        'branch_admin' => 50,
        'district_admin' => 60,
        'regional_admin' => 70,
        'super_admin' => 80,
    ];

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
        'api_token_expires_at',
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
            'api_token_expires_at' => 'datetime',
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

    public function roleDisplayName(?string $role = null): string
    {
        $normalized = $this->normalizedRoleName($role);

        if (! $normalized) {
            return 'Member';
        }

        return Str::of($normalized)
            ->replace('_', ' ')
            ->title()
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

    public function isActive(): bool
    {
        return ($this->status ?? 'active') === 'active';
    }

    public function invalidatedAuthAttributes(): array
    {
        return [
            'api_token' => null,
            'api_token_expires_at' => null,
            'remember_token' => Str::random(60),
        ];
    }

    public function issueApiToken(string $plainToken): array
    {
        $expiresAt = now()->addMinutes((int) config('auth.api_token_expire_minutes', 1440));

        return [
            'api_token' => hash('sha256', $plainToken),
            'api_token_expires_at' => $expiresAt,
        ];
    }

    public function apiTokenIsExpired(): bool
    {
        return $this->api_token_expires_at?->isPast() ?? true;
    }

    public function roleRank(?string $role = null): int
    {
        $normalized = $this->normalizedRoleName($role);

        return self::SYSTEM_ROLE_RANK[$normalized] ?? 0;
    }

    public function canAssignSystemRole(string $role): bool
    {
        $targetRank = $this->roleRank($role);

        if ($targetRank === 0) {
            return false;
        }

        if ($this->hasSystemRole('super_admin')) {
            return true;
        }

        return $targetRank < $this->roleRank();
    }

    public function outranks(User $target): bool
    {
        if ($this->hasSystemRole('super_admin')) {
            return $this->id !== $target->id;
        }

        return $this->roleRank() > $target->roleRank();
    }

    public function effectiveBranchId(): ?int
    {
        return $this->church_id ?: $this->branch_id;
    }

    public function canManageRegion(int $regionId): bool
    {
        if ($this->hasSystemRole('super_admin')) {
            return true;
        }

        return $this->hasSystemRole('regional_admin') && (int) $this->region_id === $regionId;
    }

    public function canManageDistrict(int $districtId): bool
    {
        if ($this->hasSystemRole('super_admin')) {
            return true;
        }

        if ($this->hasSystemRole('regional_admin')) {
            return District::query()
                ->whereKey($districtId)
                ->where('region_id', $this->region_id)
                ->exists();
        }

        return $this->hasSystemRole('district_admin') && (int) $this->district_id === $districtId;
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

        if ($this->hasAnySystemRole(['branch_admin', 'pastor', 'bishop', 'accountant', 'member'])) {
            return (int) $this->effectiveBranchId() === $branchId;
        }

        return false;
    }
}
