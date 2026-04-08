<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\District;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LocationController extends Controller
{
    public function regions()
    {
        $regions = Cache::remember('regions:list', now()->addDay(), fn () => Region::query()->orderBy('name')->get());

        return response()->json($regions);
    }

    public function districts(Request $request)
    {
        $regionId = $request->integer('region_id');

        $districts = Cache::remember(
            'districts:region:' . $regionId,
            now()->addDay(),
            fn () => District::query()->where('region_id', $regionId)->orderBy('name')->get()
        );

        return response()->json($districts);
    }

    public function branches(Request $request)
    {
        $districtId = $request->integer('district_id');

        $branches = Branch::query()
            ->where('district_id', $districtId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return response()->json($branches);
    }
}
