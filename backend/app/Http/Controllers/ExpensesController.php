<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{
    public function index(Request $request)
    {
        $q = Expense::with(['church', 'recorder'])
            ->when($request->church_id, fn($q) =>
                $q->where('church_id', $request->church_id))
            ->when($request->month, fn($q) =>
                $q->whereMonth('date', $request->month))
            ->when($request->year, fn($q) =>
                $q->whereYear('date', $request->year));

        return response()->json($q->orderBy('date', 'desc')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'church_id' => 'required|exists:churches,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'date' => 'required|date',
            'recorded_by' => 'required|exists:users,id',
        ]);

        $expense = Expense::create($data);

        return response()->json($expense, 201);
    }

    public function destroy($id)
    {
        Expense::findOrFail($id)->delete();
        return response()->json(['message' => 'Expense deleted']);
    }
}
