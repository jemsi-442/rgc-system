<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SystemAssistantTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'locale',
        'title',
        'answer',
        'keywords',
        'suggestions',
        'roles',
        'is_active',
        'is_system',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'keywords' => 'array',
            'suggestions' => 'array',
            'roles' => 'array',
            'is_active' => 'boolean',
            'is_system' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(SystemAssistantInteraction::class, 'matched_topic_id');
    }

    public function roleLabels(): array
    {
        return collect($this->roles ?? [])
            ->map(fn (string $role): string => Str::headline(str_replace('_', ' ', $role)))
            ->all();
    }
}
