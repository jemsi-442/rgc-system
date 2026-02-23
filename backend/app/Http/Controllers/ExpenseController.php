<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Expense::with(['church', 'recorder'])
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
            'description' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $validated['recorded_by'] = $request->user()->id;

        $expense = Expense::create($validated);

        return response()->json($expense, 201);
    }

    public function show(string $id): JsonResponse
    {
        $expense = Expense::with(['church', 'recorder'])->find($id);

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        return response()->json($expense);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        $validated = $request->validate([
            'church_id' => 'required|exists:churches,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $validated['recorded_by'] = $request->user()->id;

        $expense->update($validated);

        return response()->json($expense);
    }

    public function destroy(string $id): JsonResponse
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        $expense->delete();

        return response()->json(['message' => 'Expense deleted']);
    }

    public function summary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'church_id' => 'nullable|exists:churches,id',
        ]);

        $query = Expense::query()
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
