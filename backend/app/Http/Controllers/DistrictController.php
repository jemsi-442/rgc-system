<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDistrictRequest;
use App\Http\Requests\UpdateDistrictRequest;
use App\Models\District;
use App\Services\ActivityLogService;
use App\Services\RegionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    public function __construct(
        private readonly RegionService $regionService,
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $districts = District::with('region:id,name')
            ->when($request->region_id, fn ($query, $regionId) => $query->where('region_id', $regionId))
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $districts,
        ]);
    }

    public function store(StoreDistrictRequest $request): JsonResponse
    {
        $district = District::create($request->validated());
        $this->regionService->clearCache();
        $this->activityLogService->log($request, 'district.created', District::class, $district->id, $district->toArray());

        return response()->json([
            'status' => 'success',
            'data' => $district,
        ], 201);
    }

    public function show(District $district): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $district->load('region:id,name'),
        ]);
    }

    public function update(UpdateDistrictRequest $request, District $district): JsonResponse
    {
        $district->update($request->validated());
        $this->regionService->clearCache();
        $this->activityLogService->log($request, 'district.updated', District::class, $district->id, $district->toArray());

        return response()->json([
            'status' => 'success',
            'data' => $district,
        ]);
    }

    public function destroy(Request $request, District $district): JsonResponse
    {
        $district->delete();
        $this->regionService->clearCache();
        $this->activityLogService->log($request, 'district.deleted', District::class, $district->id);

        return response()->json([
            'status' => 'success',
            'message' => 'District deleted',
        ]);
    }
}
