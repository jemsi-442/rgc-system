<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::query()
            ->where('church_id', auth()->user()->effectiveBranchId())
            ->orderByDesc('date')
            ->paginate(20);

        return view('panel.expenses.index', compact('expenses'));
    }

    public function create()
    {
        return view('panel.expenses.create');
    }

    public function store(StoreExpenseRequest $request)
    {
        $description = trim($request->input('category') . ($request->filled('description') ? ': ' . $request->input('description') : ''));

        Expense::query()->create([
            'church_id' => $request->user()->effectiveBranchId(),
            'recorded_by' => $request->user()->id,
            'date' => $request->input('expense_date'),
            'amount' => $request->input('amount'),
            'description' => $description,
        ]);

        return redirect()->route('expenses.index')->with('status', 'Expense recorded.');
    }

    public function edit(Expense $expense)
    {
        $this->authorize('update', $expense);

        return view('panel.expenses.edit', compact('expense'));
    }

    public function update(StoreExpenseRequest $request, Expense $expense)
    {
        $this->authorize('update', $expense);

        $description = trim($request->input('category') . ($request->filled('description') ? ': ' . $request->input('description') : ''));

        $expense->update([
            'date' => $request->input('expense_date'),
            'amount' => $request->input('amount'),
            'description' => $description,
        ]);

        return redirect()->route('expenses.index')->with('status', 'Expense updated.');
    }

    public function destroy(Expense $expense)
    {
        $this->authorize('delete', $expense);
        $expense->delete();

        return back()->with('status', 'Expense deleted.');
    }
}
