<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogService
{
    public function log(Request $request, string $action, string $entityType, ?int $entityId = null, array $meta = []): void
    {
        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'meta' => $meta,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
