<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class UserController extends Controller
{
    private const ROLE_RANK = [
        'member' => 10,
        'accountant' => 20,
        'pastor' => 30,
        'bishop' => 40,
        'branch_admin' => 50,
        'district_admin' => 60,
        'regional_admin' => 70,
        'super_admin' => 80,
    ];

    public function index(): JsonResponse
    {
        $actor = auth()->user();
        $this->authorize('viewAny', User::class);

        $query = User::query()->with(['region:id,name', 'district:id,name', 'branch:id,name,type']);

        if ($actor->hasSystemRole('regional_admin')) {
            $query->where('region_id', $actor->region_id);
        } elseif ($actor->hasSystemRole('district_admin')) {
            $query->where('district_id', $actor->district_id);
        } elseif ($actor->hasSystemRole('branch_admin')) {
            $branchId = $actor->effectiveBranchId();
            $query->where(function ($inner) use ($branchId) {
                $inner->where('branch_id', $branchId)->orWhere('church_id', $branchId);
            });
        }

        return response()->json($query->latest('id')->paginate(20));
    }

    public function me(): JsonResponse
    {
        $user = auth()->user()->load(['region:id,name', 'district:id,name', 'branch:id,name,type']);

        return response()->json($user);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $actor = $request->user();
        $this->authorize('create', User::class);

        $role = $this->normalizeRole($request->string('role')->toString());

        if (! $this->canAssignRole($actor, $role)) {
            return response()->json(['message' => __('Unauthorized role assignment.')], 403);
        }

        if (! $this->withinActorScope($actor, $request->integer('region_id'), $request->integer('district_id'), $request->integer('branch_id'))) {
            return response()->json(['message' => __('Cannot create user outside your governance scope.')], 403);
        }

        $branchId = $request->integer('branch_id');

        $user = User::query()->create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'phone' => $request->input('phone'),
            'password' => $request->string('password')->toString(),
            'role' => $role,
            'status' => 'active',
            'region_id' => $request->integer('region_id'),
            'district_id' => $request->integer('district_id'),
            'branch_id' => $branchId,
            'church_id' => $branchId,
        ]);

        $user->syncRoles([$role]);

        return response()->json($user->load(['region:id,name', 'district:id,name', 'branch:id,name,type']), 201);
    }

    public function show(User $user): JsonResponse
    {
        $this->authorize('view', $user);

        return response()->json($user->load(['region:id,name', 'district:id,name', 'branch:id,name,type']));
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $actor = $request->user();
        $this->authorize('update', $user);

        $targetRole = $request->filled('role')
            ? $this->normalizeRole($request->string('role')->toString())
            : $this->normalizeRole($user->normalizedRoleName() ?? 'member');

        if (! $targetRole || ! $this->canAssignRole($actor, $targetRole)) {
            return response()->json(['message' => __('Unauthorized role assignment.')], 403);
        }

        if (! $this->withinActorScope($actor, $request->integer('region_id'), $request->integer('district_id'), $request->integer('branch_id'))) {
            return response()->json(['message' => __('Cannot update user outside your governance scope.')], 403);
        }

        $branchId = $request->integer('branch_id');

        $payload = [
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'phone' => $request->input('phone'),
            'role' => $targetRole,
            'region_id' => $request->integer('region_id'),
            'district_id' => $request->integer('district_id'),
            'branch_id' => $branchId,
            'church_id' => $branchId,
        ];

        if ($request->filled('password')) {
            $payload['password'] = $request->string('password')->toString();
        }

        $user->update($payload);

        if ($request->filled('role')) {
            $user->syncRoles([$targetRole]);
        }

        return response()->json($user->load(['region:id,name', 'district:id,name', 'branch:id,name,type']));
    }

    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        $user->delete();

        return response()->json(['message' => __('User deleted.')]);
    }

    private function normalizeRole(string $role): string
    {
        return Str::of($role)->lower()->replace('-', '_')->replace(' ', '_')->value();
    }

    private function canAssignRole(User $actor, string $targetRole): bool
    {
        if (! isset(self::ROLE_RANK[$targetRole])) {
            return false;
        }

        if ($actor->hasSystemRole('super_admin')) {
            return true;
        }

        $actorRole = $actor->normalizedRoleName();

        if (! $actorRole || ! isset(self::ROLE_RANK[$actorRole])) {
            return false;
        }

        return self::ROLE_RANK[$targetRole] < self::ROLE_RANK[$actorRole];
    }

    private function withinActorScope(User $actor, int $regionId, int $districtId, int $branchId): bool
    {
        if ($actor->hasSystemRole('super_admin')) {
            return true;
        }

        if ($actor->hasSystemRole('regional_admin')) {
            return (int) $actor->region_id === $regionId;
        }

        if ($actor->hasSystemRole('district_admin')) {
            return (int) $actor->district_id === $districtId;
        }

        if ($actor->hasSystemRole('branch_admin')) {
            return (int) $actor->effectiveBranchId() === $branchId;
        }

        return false;
    }
}
