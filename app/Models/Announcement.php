<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'body',
        'region_id',
        'district_id',
        'church_id',
        'created_by',
        'is_global',
        'is_pinned',
        'pinned_at',
        'expires_at',
        'archived_at',
        'image_path',
        'image_name',
        'image_mime_type',
    ];

    protected $casts = [
        'is_global' => 'boolean',
        'is_pinned' => 'boolean',
        'pinned_at' => 'datetime',
        'expires_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public function getBranchIdAttribute(): ?int
    {
        return $this->church_id;
    }

    public function setBranchIdAttribute(int $value): void
    {
        $this->attributes['church_id'] = $value;
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function targetBranches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'announcement_branch_targets', 'announcement_id', 'church_id');
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasSystemRole('super_admin')) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($user): void {
            $builder->where('is_global', true);

            if ($user->effectiveBranchId()) {
                $builder->orWhereHas('targetBranches', function (Builder $targetBuilder) use ($user): void {
                    $targetBuilder->where('churches.id', $user->effectiveBranchId());
                });
            }

            if ($user->hasSystemRole('regional_admin') && $user->region_id) {
                $builder->orWhere('region_id', $user->region_id);
                return;
            }

            if ($user->hasSystemRole('district_admin') && $user->district_id) {
                $builder->orWhere(function (Builder $regionBuilder) use ($user): void {
                    $regionBuilder
                        ->where('region_id', $user->region_id)
                        ->whereNull('district_id')
                        ->whereNull('church_id');
                });

                $builder->orWhere('district_id', $user->district_id);
                return;
            }

            if ($user->region_id) {
                $builder->orWhere(function (Builder $regionBuilder) use ($user): void {
                    $regionBuilder
                        ->where('region_id', $user->region_id)
                        ->whereNull('district_id')
                        ->whereNull('church_id');
                });
            }

            if ($user->district_id) {
                $builder->orWhere(function (Builder $districtBuilder) use ($user): void {
                    $districtBuilder
                        ->where('district_id', $user->district_id)
                        ->whereNull('church_id');
                });
            }

            if ($user->effectiveBranchId()) {
                $builder->orWhere('church_id', $user->effectiveBranchId());
            }
        });
    }

    public function scopeOrderedForDisplay(Builder $query): Builder
    {
        return $query
            ->orderByRaw('CASE WHEN expires_at IS NOT NULL AND expires_at < ? THEN 1 ELSE 0 END ASC', [now()])
            ->orderByDesc('is_pinned')
            ->orderByDesc('pinned_at')
            ->orderByDesc('created_at');
    }

    public function scopeActiveListing(Builder $query): Builder
    {
        return $query->whereNull('archived_at');
    }

    public function scopeArchivedOnly(Builder $query): Builder
    {
        return $query->whereNotNull('archived_at')->orderByDesc('archived_at')->orderByDesc('created_at');
    }

    public function scopeDashboardVisible(Builder $query): Builder
    {
        return $query
            ->whereNull('archived_at')
            ->where(function (Builder $builder): void {
                $builder->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            });
    }

    public function hasImage(): bool
    {
        return filled($this->image_path);
    }

    public function hasPin(): bool
    {
        return $this->is_pinned;
    }

    public function hasExpiry(): bool
    {
        return $this->expires_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at?->isPast() ?? false;
    }

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    public function hasExplicitBranchTargets(): bool
    {
        if ($this->relationLoaded('targetBranches')) {
            return $this->targetBranches->isNotEmpty();
        }

        return $this->targetBranches()->exists();
    }

    public function targetBranchCount(): int
    {
        if ($this->relationLoaded('targetBranches')) {
            return $this->targetBranches->count();
        }

        return $this->targetBranches()->count();
    }

    public function targetBranchNames(int $limit = 3): array
    {
        $branches = $this->relationLoaded('targetBranches')
            ? $this->targetBranches->take($limit)
            : $this->targetBranches()->limit($limit)->get(['churches.id', 'name']);

        return $branches
            ->pluck('name')
            ->filter()
            ->values()
            ->all();
    }

    public function audienceVariant(): string
    {
        if ($this->is_global) {
            return 'global';
        }

        if ($this->hasExplicitBranchTargets()) {
            return 'selected';
        }

        if ($this->church_id) {
            return 'branch';
        }

        if ($this->district_id) {
            return 'district';
        }

        if ($this->region_id) {
            return 'region';
        }

        return 'default';
    }

    public function audienceLabel(): string
    {
        if ($this->is_global) {
            return __('National');
        }

        if ($this->hasExplicitBranchTargets()) {
            return $this->targetBranchCount() === 1 ? __('Branch') : __('Selected Branches');
        }

        if ($this->church_id) {
            return __('Branch');
        }

        if ($this->district_id) {
            return __('District');
        }

        if ($this->region_id) {
            return __('Region');
        }

        return __('Announcement');
    }

    public function deliverySummary(): string
    {
        if ($this->is_global) {
            return __('All system users and branches');
        }

        if ($this->hasExplicitBranchTargets()) {
            $count = $this->targetBranchCount();
            $names = $this->targetBranchNames(3);

            if ($count === 1 && isset($names[0])) {
                return __('Selected branch: :branch', ['branch' => $names[0]]);
            }

            return __('Selected branches: :count', ['count' => $count]);
        }

        if ($this->church_id) {
            return __('Branch: :branch', ['branch' => $this->branch?->name ?? __('Branch')]);
        }

        if ($this->district_id) {
            return __('District: :district', ['district' => $this->district?->name ?? __('District')]);
        }

        if ($this->region_id) {
            return __('Region: :region', ['region' => $this->region?->name ?? __('Region')]);
        }

        return __('Announcement');
    }
}
