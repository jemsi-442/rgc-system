<?php

namespace App\Http\Controllers;

use App\Models\Church;
use Illuminate\Http\Request;

class ChurchController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => Church::with('district')->orderBy('name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name' => 'required|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $church = Church::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $church
        ], 201);
    }

    public function show($id)
    {
        $church = Church::with('district')->find($id);

        if (!$church) {
            return response()->json([
                'status' => 'error',
                'message' => 'Church not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $church
        ]);
    }

    public function update(Request $request, $id)
    {
        $church = Church::find($id);

        if (!$church) {
            return response()->json([
                'status' => 'error',
                'message' => 'Church not found'
            ], 404);
        }

        $validated = $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name' => 'required|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $church->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $church
        ]);
    }

    public function destroy($id)
    {
        $church = Church::find($id);

        if (!$church) {
            return response()->json([
                'status' => 'error',
                'message' => 'Church not found'
            ], 404);
        }

        $church->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Church deleted successfully'
        ]);
    }
}
