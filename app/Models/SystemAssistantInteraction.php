<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemAssistantInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'locale',
        'question',
        'normalized_question',
        'matched_topic_id',
        'matched_slug',
        'source',
        'confidence',
        'answer',
        'role_snapshot',
        'ip_address',
        'user_agent',
        'helpful',
        'feedback_note',
        'feedback_submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'confidence' => 'integer',
            'role_snapshot' => 'array',
            'helpful' => 'boolean',
            'feedback_note' => 'string',
            'feedback_submitted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(SystemAssistantTopic::class, 'matched_topic_id');
    }

    public function feedbackLabel(): string
    {
        if ($this->helpful === null) {
            return __('No feedback');
        }

        return $this->helpful ? __('Helpful') : __('Not helpful');
    }
}
