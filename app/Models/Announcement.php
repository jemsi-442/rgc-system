<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasSystemRole('super_admin')) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($user): void {
            $builder->where('is_global', true);

            if ($user->hasSystemRole('regional_admin') && $user->region_id) {
                $builder->orWhere('region_id', $user->region_id);
                return;
            }

            if ($user->hasSystemRole('district_admin') && $user->district_id) {
                $builder->orWhere('district_id', $user->district_id);
                return;
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

    public function audienceLabel(): string
    {
        if ($this->is_global) {
            return __('National');
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
}
