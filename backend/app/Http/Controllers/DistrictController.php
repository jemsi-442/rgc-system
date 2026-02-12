<?php

namespace App\Http\Controllers;

use App\Models\District;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    // GET /api/districts
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => District::with('region')->orderBy('name')->get()
        ]);
    }

    // POST /api/districts
    public function store(Request $request)
    {
        $validated = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'name' => 'required|string',
            'code' => 'nullable|string'
        ]);

        $district = District::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $district
        ], 201);
    }

    // GET /api/districts/{id}
    public function show($id)
    {
        $district = District::with('region')->find($id);

        if (!$district) {
            return response()->json([
                'status' => 'error',
                'message' => 'District not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $district
        ]);
    }

    // PUT /api/districts/{id}
    public function update(Request $request, $id)
    {
        $district = District::find($id);

        if (!$district) {
            return response()->json([
                'status' => 'error',
                'message' => 'District not found'
            ], 404);
        }

        $validated = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'name' => 'required|string',
            'code' => 'nullable|string'
        ]);

        $district->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $district
        ]);
    }

    // DELETE /api/districts/{id}
    public function destroy($id)
    {
        $district = District::find($id);

        if (!$district) {
            return response()->json([
                'status' => 'error',
                'message' => 'District not found'
            ], 404);
        }

        $district->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'District deleted'
        ]);
    }
}
