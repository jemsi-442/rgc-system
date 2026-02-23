<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegionRequest;
use App\Http\Requests\UpdateRegionRequest;
use App\Models\Region;
use App\Services\ActivityLogService;
use App\Services\RegionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function __construct(
        private readonly RegionService $regionService,
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $regions = $this->regionService->allWithDistricts($request->boolean('refresh'));

        return response()->json([
            'status' => 'success',
            'data' => $regions,
        ]);
    }

    public function store(StoreRegionRequest $request): JsonResponse
    {
        $region = Region::create($request->validated());
        $this->regionService->clearCache();
        $this->activityLogService->log($request, 'region.created', Region::class, $region->id, $region->toArray());

        return response()->json([
            'status' => 'success',
            'data' => $region,
        ], 201);
    }

    public function show(Region $region): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $region->load('districts:id,region_id,name'),
        ]);
    }

    public function update(UpdateRegionRequest $request, Region $region): JsonResponse
    {
        $region->update($request->validated());
        $this->regionService->clearCache();
        $this->activityLogService->log($request, 'region.updated', Region::class, $region->id, $region->toArray());

        return response()->json([
            'status' => 'success',
            'data' => $region,
        ]);
    }

    public function destroy(Request $request, Region $region): JsonResponse
    {
        $region->delete();
        $this->regionService->clearCache();
        $this->activityLogService->log($request, 'region.deleted', Region::class, $region->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Region deleted successfully',
        ]);
    }
}
