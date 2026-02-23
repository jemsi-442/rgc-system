<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function __construct(private readonly ActivityLogService $activityLogService)
    {
    }

    public function sync(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'roles' => 'required|array|min:1',
            'roles.*' => 'required|string|exists:roles,name',
            'legacy_role' => 'nullable|in:super_admin,regional_admin,district_admin,branch_admin,bishop,pastor,assistant_pastor,accountant,evangelist,choir_leader,youth_leader,member,admin,user',
        ]);

        $user->syncRoles($validated['roles']);

        if (!empty($validated['legacy_role'])) {
            $user->update(['role' => $validated['legacy_role']]);
        }

        $this->activityLogService->log($request, 'user.roles.synced', User::class, $user->id, $validated);

        return response()->json([
            'status' => 'success',
            'data' => $user->load('roles'),
        ]);
    }
}
