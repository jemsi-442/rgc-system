<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private const GOVERNANCE_ROLES = [
        'super_admin',
        'regional_admin',
        'district_admin',
        'branch_admin',
        'bishop',
        'pastor',
        'assistant_pastor',
        'accountant',
        'evangelist',
        'choir_leader',
        'youth_leader',
        'member',
        'admin',
        'user',
    ];

    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => User::with(['region:id,name', 'district:id,name', 'branch:id,name'])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:30',
            'role' => ['nullable', 'string', Rule::in(self::GOVERNANCE_ROLES)],
            'region_id' => 'nullable|exists:regions,id',
            'district_id' => 'nullable|exists:districts,id',
            'branch_id' => 'nullable|exists:churches,id',
            'church_id' => 'nullable|exists:churches,id',
            'status' => 'nullable|in:active,inactive',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'] ?? 'member',
            'region_id' => $validated['region_id'] ?? null,
            'district_id' => $validated['district_id'] ?? null,
            'branch_id' => $validated['branch_id'] ?? ($validated['church_id'] ?? null),
            'church_id' => $validated['church_id'] ?? ($validated['branch_id'] ?? null),
            'status' => $validated['status'] ?? 'active',
        ]);

        if (method_exists($user, 'syncRoles')) {
            $user->syncRoles([$user->role]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user->load('roles'),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user->load('roles'),
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:30',
            'role' => ['nullable', 'string', Rule::in(self::GOVERNANCE_ROLES)],
            'region_id' => 'nullable|exists:regions,id',
            'district_id' => 'nullable|exists:districts,id',
            'branch_id' => 'nullable|exists:churches,id',
            'church_id' => 'nullable|exists:churches,id',
            'status' => 'nullable|in:active,inactive',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if (isset($validated['branch_id']) && !isset($validated['church_id'])) {
            $validated['church_id'] = $validated['branch_id'];
        }

        if (isset($validated['church_id']) && !isset($validated['branch_id'])) {
            $validated['branch_id'] = $validated['church_id'];
        }

        $user->update($validated);

        if (isset($validated['role']) && method_exists($user, 'syncRoles')) {
            $user->syncRoles([$validated['role']]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user->load('roles'),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully',
        ]);
    }
}
