<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChurchRequest;
use App\Http\Requests\UpdateChurchRequest;
use App\Models\Church;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChurchController extends Controller
{
    public function __construct(private readonly ActivityLogService $activityLogService)
    {
        $this->authorizeResource(Church::class, 'church');
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $churches = Church::with(['district.region', 'pastor'])
            ->when(
                $user?->hasAnyRoleOrLegacy(['regional_admin']),
                fn ($query) => $query->where('region_id', $user->region_id)
            )
            ->when(
                $user?->hasAnyRoleOrLegacy(['district_admin']),
                fn ($query) => $query->where('district_id', $user->district_id)
            )
            ->when(
                $user?->hasAnyRoleOrLegacy(['branch_admin']),
                fn ($query) => $query->where('id', $user->branch_id)
            )
            ->when($request->district_id, fn ($query, $districtId) => $query->where('district_id', $districtId))
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $churches,
        ]);
    }

    public function publicIndex(Request $request): JsonResponse
    {
        $churches = Church::query()
            ->select(['id', 'name', 'district_id'])
            ->where('status', 'active')
            ->when($request->district_id, fn ($query, $districtId) => $query->where('district_id', $districtId))
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $churches,
        ]);
    }

    public function store(StoreChurchRequest $request): JsonResponse
    {
        $payload = $request->safe()->except(['assigned_branch_admin_id']);

        $church = DB::transaction(function () use ($request, $payload) {
            $church = Church::create($payload);
            $this->assignBranchAdmin($request, $church);

            return $church;
        });

        $this->activityLogService->log($request, 'church.created', Church::class, $church->id, $church->toArray());

        return response()->json([
            'status' => 'success',
            'data' => $church->load(['district.region', 'pastor']),
        ], 201);
    }

    public function show(Church $church): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $church->load(['district.region', 'pastor']),
        ]);
    }

    public function update(UpdateChurchRequest $request, Church $church): JsonResponse
    {
        $payload = $request->safe()->except(['assigned_branch_admin_id']);

        DB::transaction(function () use ($request, $church, $payload) {
            $church->update($payload);
            $this->assignBranchAdmin($request, $church);
        });

        $this->activityLogService->log($request, 'church.updated', Church::class, $church->id, $church->toArray());

        return response()->json([
            'status' => 'success',
            'data' => $church->load(['district.region', 'pastor']),
        ]);
    }

    public function destroy(Request $request, Church $church): JsonResponse
    {
        $church->delete();
        $this->activityLogService->log($request, 'church.deleted', Church::class, $church->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Church deleted successfully',
        ]);
    }

    private function assignBranchAdmin(Request $request, Church $church): void
    {
        $user = $request->user();
        $candidateId = $request->integer('assigned_branch_admin_id');

        if (!$candidateId || !$user?->hasAnyRoleOrLegacy(['super_admin'])) {
            return;
        }

        $candidate = User::find($candidateId);
        if (!$candidate) {
            return;
        }

        $candidate->update([
            'role' => 'branch_admin',
            'region_id' => $church->region_id,
            'district_id' => $church->district_id,
            'branch_id' => $church->id,
            'church_id' => $church->id,
        ]);

        if (method_exists($candidate, 'syncRoles')) {
            $candidate->syncRoles(['branch_admin']);
        }
    }
}
