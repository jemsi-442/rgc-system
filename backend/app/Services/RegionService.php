<?php

namespace App\Services;

use App\Models\Region;
use Illuminate\Support\Facades\Cache;

class RegionService
{
    private const CACHE_KEY = 'regions_with_districts_v1';

    public function allWithDistricts(bool $refresh = false)
    {
        if ($refresh) {
            Cache::forget(self::CACHE_KEY);
        }

        return Cache::remember(self::CACHE_KEY, now()->addHours(12), function () {
            return Region::with(['districts' => function ($query) {
                $query->select('id', 'region_id', 'name')->orderBy('name');
            }])
                ->select('id', 'name', 'code')
                ->orderBy('name')
                ->get();
        });
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
