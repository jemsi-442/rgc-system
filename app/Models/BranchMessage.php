<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BranchMessage extends Model
{
    protected $fillable = [
        'church_id',
        'user_id',
        'parent_id',
        'message',
        'attachment_path',
        'attachment_name',
        'attachment_mime_type',
        'attachment_size',
        'attachments',
    ];

    protected $casts = [
        'attachment_size' => 'integer',
        'attachments' => 'array',
    ];

    public function getBranchIdAttribute(): ?int
    {
        return $this->church_id;
    }

    public function setBranchIdAttribute(int $value): void
    {
        $this->attributes['church_id'] = $value;
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'church_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function attachmentItems(): array
    {
        $items = collect($this->attachments ?? [])
            ->map(fn ($item) => $this->normalizeAttachmentItem($item))
            ->filter(fn ($item) => filled($item['path'] ?? null))
            ->values();

        if ($items->isNotEmpty()) {
            return $items->all();
        }

        if (! filled($this->attachment_path)) {
            return [];
        }

        return [[
            'path' => $this->attachment_path,
            'name' => $this->attachment_name,
            'mime_type' => $this->attachment_mime_type,
            'size' => $this->attachment_size,
        ]];
    }

    public function primaryAttachment(): ?array
    {
        return $this->attachmentItems()[0] ?? null;
    }

    public function hasAttachment(): bool
    {
        return $this->primaryAttachment() !== null;
    }

    public function isImageAttachment(?array $attachment = null): bool
    {
        $item = $attachment ?? $this->primaryAttachment();

        return filled($item['mime_type'] ?? null)
            && Str::startsWith((string) ($item['mime_type'] ?? ''), 'image/');
    }

    public function attachmentTypeLabel(?array $attachment = null): string
    {
        $item = $attachment ?? $this->primaryAttachment();

        if (! $item) {
            return 'FILE';
        }

        $extension = strtoupper((string) pathinfo((string) ($item['name'] ?? $item['path'] ?? ''), PATHINFO_EXTENSION));

        if ($extension !== '') {
            return Str::limit($extension, 5, '');
        }

        return $this->isImageAttachment($item) ? 'IMG' : 'FILE';
    }

    public function attachmentSizeLabel(?array $attachment = null): ?string
    {
        $item = $attachment ?? $this->primaryAttachment();
        $bytes = (int) ($item['size'] ?? 0);

        if ($bytes <= 0) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = min((int) floor(log(max($bytes, 1), 1024)), count($units) - 1);
        $value = $bytes / (1024 ** $power);

        return number_format($value, $power === 0 ? 0 : 1) . ' ' . $units[$power];
    }

    public function previewText(int $limit = 120): string
    {
        $message = trim((string) $this->message);

        if ($message !== '') {
            return Str::limit(preg_replace('/\s+/', ' ', $message) ?? $message, $limit);
        }

        $attachments = $this->attachmentItems();
        $count = count($attachments);

        if ($count === 0) {
            return '';
        }

        if ($count === 1) {
            return $attachments[0]['name'] ?? __('Attachment');
        }

        return __(':count attachments', ['count' => $count]);
    }

    private function normalizeAttachmentItem(mixed $item): array
    {
        return [
            'path' => Arr::get($item, 'path'),
            'name' => Arr::get($item, 'name'),
            'mime_type' => Arr::get($item, 'mime_type'),
            'size' => Arr::get($item, 'size'),
        ];
    }
}
