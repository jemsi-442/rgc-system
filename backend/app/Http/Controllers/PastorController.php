<?php

namespace App\Http\Controllers;

use App\Models\Pastor;
use Illuminate\Http\Request;

class PastorController extends Controller
{
    public function index(Request $request)
    {
        $query = Pastor::with('church');

        if ($request->has('church_id')) {
            $query->where('church_id', $request->church_id);
        }

        return response()->json($query->orderBy('full_name')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'title' => 'nullable|string',
            'church_id' => 'nullable|exists:churches,id',
        ]);

        $pastor = Pastor::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $pastor
        ], 201);
    }

    public function show($id)
    {
        $pastor = Pastor::with('church')->find($id);

        if (!$pastor) {
            return response()->json(['error' => 'Pastor not found'], 404);
        }

        return response()->json($pastor);
    }

    public function update(Request $request, $id)
    {
        $pastor = Pastor::find($id);

        if (!$pastor) {
            return response()->json(['error' => 'Pastor not found'], 404);
        }

        $validated = $request->validate([
            'full_name' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'title' => 'nullable|string',
            'church_id' => 'nullable|exists:churches,id',
        ]);

        $pastor->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $pastor
        ]);
    }

    public function destroy($id)
    {
        $pastor = Pastor::find($id);

        if (!$pastor) {
            return response()->json(['error' => 'Pastor not found'], 404);
        }

        $pastor->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pastor deleted'
        ]);
    }
}
