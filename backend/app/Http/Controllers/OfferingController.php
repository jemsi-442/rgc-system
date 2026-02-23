<?php

namespace App\Http\Controllers;

use App\Models\Offering;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfferingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Offering::with('church')
            ->when($request->church_id, fn ($q) => $q->where('church_id', $request->church_id))
            ->when($request->month, fn ($q) => $q->whereMonth('date', $request->month))
            ->when($request->year, fn ($q) => $q->whereYear('date', $request->year));

        return response()->json($query->orderBy('date', 'desc')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'church_id' => 'required|exists:churches,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        $validated['recorded_by'] = (string) $request->user()->id;
        $offering = Offering::create($validated);

        return response()->json($offering, 201);
    }

    public function show(string $id): JsonResponse
    {
        $offering = Offering::with('church')->find($id);

        if (!$offering) {
            return response()->json(['message' => 'Offering not found'], 404);
        }

        return response()->json($offering);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $offering = Offering::find($id);

        if (!$offering) {
            return response()->json(['message' => 'Offering not found'], 404);
        }

        $validated = $request->validate([
            'church_id' => 'required|exists:churches,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        $validated['recorded_by'] = (string) $request->user()->id;

        $offering->update($validated);

        return response()->json($offering);
    }

    public function destroy(string $id): JsonResponse
    {
        $offering = Offering::find($id);

        if (!$offering) {
            return response()->json(['message' => 'Offering not found'], 404);
        }

        $offering->delete();

        return response()->json(['message' => 'Offering deleted']);
    }

    public function summary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'church_id' => 'nullable|exists:churches,id',
        ]);

        $query = Offering::query()
            ->when($validated['church_id'] ?? null, fn ($q, $churchId) => $q->where('church_id', $churchId))
            ->when($validated['from'] ?? null, fn ($q, $from) => $q->whereDate('date', '>=', $from))
            ->when($validated['to'] ?? null, fn ($q, $to) => $q->whereDate('date', '<=', $to));

        $total = (float) $query->sum('amount');

        $byChurch = (clone $query)
            ->select('church_id', DB::raw('SUM(amount) as total_amount'))
            ->groupBy('church_id')
            ->with('church:id,name')
            ->get();

        return response()->json([
            'total_amount' => $total,
            'by_church' => $byChurch,
        ]);
    }
}
