<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(["church", "recorder"]);

        if ($request->church_id) {
            $query->where('church_id', $request->church_id);
        }

        if ($request->date) {
            $query->where('date', $request->date);
        }

        return response()->json($query->orderBy("date", "desc")->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'church_id' => 'required|exists:churches,id',
            'date' => 'required|date',
            'men' => 'nullable|integer|min:0',
            'women' => 'nullable|integer|min:0',
            'youth' => 'nullable|integer|min:0',
            'children' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['total'] =
            ($validated['men'] ?? 0) +
            ($validated['women'] ?? 0) +
            ($validated['youth'] ?? 0) +
            ($validated['children'] ?? 0);

        $validated['recorded_by'] = auth()->id();

        $att = Attendance::create($validated);

        return response()->json($att, 201);
    }

    // 🔥 BULK CREATE
    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'records' => 'required|array|min:1',
            'records.*.church_id' => 'required|exists:churches,id',
            'records.*.date' => 'required|date',
            'records.*.men' => 'nullable|integer|min:0',
            'records.*.women' => 'nullable|integer|min:0',
            'records.*.youth' => 'nullable|integer|min:0',
            'records.*.children' => 'nullable|integer|min:0',
            'records.*.notes' => 'nullable|string',
        ]);

        $userId = auth()->id();
        $data = [];

        foreach ($validated['records'] as $rec) {
            $total =
                ($rec['men'] ?? 0) +
                ($rec['women'] ?? 0) +
                ($rec['youth'] ?? 0) +
                ($rec['children'] ?? 0);

            $data[] = [
                'church_id' => $rec['church_id'],
                'date' => $rec['date'],
                'men' => $rec['men'] ?? 0,
                'women' => $rec['women'] ?? 0,
                'youth' => $rec['youth'] ?? 0,
                'children' => $rec['children'] ?? 0,
                'total' => $total,
                'notes' => $rec['notes'] ?? null,
                'recorded_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::transaction(function () use ($data) {
            Attendance::insert($data);
        });

        return response()->json([
            'status' => 'success',
            'message' => count($data) . ' attendance records saved',
            'count' => count($data)
        ], 201);
    }

    public function show($id)
    {
        $att = Attendance::with("church", "recorder")->find($id);

        if (!$att) {
            return response()->json(['message' => 'Attendance not found'], 404);
        }

        return response()->json($att);
    }

    public function update(Request $request, $id)
    {
        $att = Attendance::find($id);

        if (!$att) {
            return response()->json(['message' => 'Attendance not found'], 404);
        }

        $validated = $request->validate([
            'church_id' => 'required|exists:churches,id',
            'date' => 'required|date',
            'men' => 'nullable|integer|min:0',
            'women' => 'nullable|integer|min:0',
            'youth' => 'nullable|integer|min:0',
            'children' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['total'] =
            ($validated['men'] ?? 0) +
            ($validated['women'] ?? 0) +
            ($validated['youth'] ?? 0) +
            ($validated['children'] ?? 0);

        $att->update($validated);

        return response()->json($att);
    }

    public function destroy($id)
    {
        $att = Attendance::find($id);

        if (!$att) {
            return response()->json(['message' => 'Attendance not found'], 404);
        }

        $att->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
