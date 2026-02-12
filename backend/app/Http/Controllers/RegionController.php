<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    // GET /api/regions
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => Region::orderBy('name')->get()
        ]);
    }

    // POST /api/regions
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:regions,name',
            'code' => 'nullable|string|unique:regions,code',
        ]);

        $region = Region::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $region
        ], 201);
    }

    // GET /api/regions/{id}
    public function show($id)
    {
        $region = Region::find($id);

        if (!$region) {
            return response()->json([
                'status' => 'error',
                'message' => 'Region not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $region
        ]);
    }

    // PUT /api/regions/{id}
    public function update(Request $request, $id)
    {
        $region = Region::find($id);

        if (!$region) {
            return response()->json([
                'status' => 'error',
                'message' => 'Region not found'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|unique:regions,name,' . $id,
            'code' => 'nullable|string|unique:regions,code,' . $id,
        ]);

        $region->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $region
        ]);
    }

    // DELETE /api/regions/{id}
    public function destroy($id)
    {
        $region = Region::find($id);

        if (!$region) {
            return response()->json([
                'status' => 'error',
                'message' => 'Region not found'
            ], 404);
        }

        $region->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Region deleted successfully'
        ]);
    }
}
