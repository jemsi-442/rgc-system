<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $logs = ActivityLog::with('user:id,name,email')
            ->when($request->user_id, fn ($q, $userId) => $q->where('user_id', $userId))
            ->when($request->action, fn ($q, $action) => $q->where('action', $action))
            ->latest()
            ->paginate(50);

        return response()->json([
            'status' => 'success',
            'data' => $logs,
        ]);
    }
}
