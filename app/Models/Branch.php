<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;

    protected $table = 'churches';

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

    protected $appends = [
        'branch_type',
        'is_headquarters',
    ];

    public function getBranchTypeAttribute(): ?string
    {
        return $this->attributes['type'] ?? null;
    }

    public function setBranchTypeAttribute(string $value): void
    {
        $this->attributes['type'] = $value;
    }

    public function getIsHeadquartersAttribute(): bool
    {
        return ($this->attributes['type'] ?? null) === 'headquarters';
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'church_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(BranchMessage::class, 'church_id');
    }
}
