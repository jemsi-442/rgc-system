<?php

namespace App\Http\Controllers;

use App\Models\BranchMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchMessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $churchId = $request->integer('church_id');
        if ($user->hasAnyRoleOrLegacy(['member', 'user'])) {
            $churchId = $user->church_id;
        }

        if (!$churchId) {
            return response()->json([
                'status' => 'error',
                'message' => 'church_id is required',
            ], 422);
        }

        $messages = BranchMessage::with(['user:id,name,role'])
            ->where('church_id', $churchId)
            ->latest('id')
            ->limit(100)
            ->get()
            ->reverse()
            ->values();

        return response()->json([
            'status' => 'success',
            'data' => $messages,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'church_id' => 'nullable|exists:churches,id',
            'message' => 'required|string|max:2000',
        ]);

        $churchId = $validated['church_id'] ?? null;
        if ($user->hasAnyRoleOrLegacy(['member', 'user'])) {
            $churchId = $user->church_id;
        }

        if (!$churchId) {
            return response()->json([
                'status' => 'error',
                'message' => 'church_id is required',
            ], 422);
        }

        $message = BranchMessage::create([
            'church_id' => $churchId,
            'user_id' => $user->id,
            'message' => trim($validated['message']),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $message->load('user:id,name,role'),
        ], 201);
    }

    public function destroy(Request $request, BranchMessage $branchMessage): JsonResponse
    {
        $user = $request->user();

        if (!$user->hasAnyRoleOrLegacy(['super_admin', 'regional_admin', 'district_admin', 'branch_admin', 'admin']) && $branchMessage->user_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden',
            ], 403);
        }

        $branchMessage->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Message deleted',
        ]);
    }
}
