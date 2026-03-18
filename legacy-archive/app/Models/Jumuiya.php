<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Jumuiya extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'location',
        'leader_id',
        'leader_phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($jumuiya) {
            if (empty($jumuiya->slug)) {
                $jumuiya->slug = Str::slug($jumuiya->name);
            }
        });

        static::updating(function ($jumuiya) {
            if ($jumuiya->isDirty('name')) {
                $jumuiya->slug = Str::slug($jumuiya->name);
            }
        });
    }

    // Relationships
    public function leader()
    {
        return $this->belongsTo(Member::class, 'leader_id');
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    // Accessor for display name with leader
    public function getDisplayNameAttribute()
    {
        if ($this->leader) {
            return $this->leader->full_name . ' - ' . $this->name;
        }
        return $this->name;
    }

    // Scope for active jumuiyas
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get member count
    public function getMemberCountAttribute()
    {
        return $this->members()->count();
    }
}
