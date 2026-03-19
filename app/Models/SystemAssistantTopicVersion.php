<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemAssistantTopicVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'slug',
        'locale',
        'region_id',
        'title',
        'answer',
        'keywords',
        'suggestions',
        'roles',
        'is_active',
        'is_system',
        'sort_order',
        'action',
        'created_by',
        'restored_from_version_id',
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
            'restored_from_version_id' => 'integer',
        ];
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(SystemAssistantTopic::class, 'topic_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function restoredFrom(): BelongsTo
    {
        return $this->belongsTo(self::class, 'restored_from_version_id');
    }

    public function scopeLabel(): string
    {
        if ($this->region) {
            return __('Region: :name', ['name' => $this->region->name]);
        }

        return __('Global');
    }
}
