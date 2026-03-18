<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IncomeController extends Controller
{
    /**
     * Display a listing of incomes with filters
     */
    public function index(Request $request)
    {
        $query = Income::with(['category', 'member', 'creator']);

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('collection_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('collection_date', '<=', $request->end_date);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('income_category_id', $request->category_id);
        }

        // Filter by year
        if ($request->filled('year')) {
            $query->whereYear('collection_date', $request->year);
        }

        // Filter by month
        if ($request->filled('month')) {
            $query->whereMonth('collection_date', $request->month);
        }

        // Get incomes ordered by date descending
        $incomes = $query->orderBy('collection_date', 'desc')->paginate(7);

        // Calculate totals per category and grand total
        $categoryTotals = Income::query()
            ->when($request->filled('start_date'), function($q) use ($request) {
                $q->where('collection_date', '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function($q) use ($request) {
                $q->where('collection_date', '<=', $request->end_date);
            })
            ->when($request->filled('year'), function($q) use ($request) {
                $q->whereYear('collection_date', $request->year);
            })
            ->when($request->filled('month'), function($q) use ($request) {
                $q->whereMonth('collection_date', $request->month);
            })
            ->select('income_category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('income_category_id')
            ->with('category')
            ->get();

        $grandTotal = $categoryTotals->sum('total');

        // Get categories for filter dropdown
        $categories = IncomeCategory::active()->ordered()->get();

        // Get available years
        $years = Income::selectRaw('YEAR(collection_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Return only table partial for AJAX requests
        if ($request->ajax()) {
            return view('panel.income._table', compact('incomes'));
        }

        return view('panel.income.index', compact(
            'incomes',
            'categories',
            'categoryTotals',
            'grandTotal',
            'years'
        ));
    }

    /**
     * Show the form for creating a new income
     */
    public function create()
    {
        $categories = IncomeCategory::active()->ordered()->get();
        $members = Member::active()->orderBy('first_name')->get();

        return view('panel.income.create', compact('categories', 'members'));
    }

    /**
     * Store a newly created income in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'income_category_id' => 'required|exists:income_categories,id',
            'collection_date' => 'required|date',
            'amount' => 'required|numeric|min:0|max:999999999999.99',
            'member_id' => 'nullable|exists:members,id',
            'receipt_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ], [
            'income_category_id.required' => 'Tafadhali chagua kategoria ya mapato',
            'income_category_id.exists' => 'Kategoria iliyochaguliwa haipo',
            'collection_date.required' => 'Tafadhali ingiza tarehe ya kukusanya',
            'collection_date.date' => 'Tarehe si sahihi',
            'amount.required' => 'Tafadhali ingiza kiasi',
            'amount.numeric' => 'Kiasi lazima kiwe nambari',
            'amount.min' => 'Kiasi lazima kiwe chanya',
            'amount.max' => 'Kiasi ni kubwa mno',
            'member_id.exists' => 'Muumini aliyechaguliwa hayupo',
            'receipt_number.max' => 'Nambari ya risiti ni ndefu mno',
            'notes.max' => 'Maelezo ni marefu mno',
        ]);

        $validated['created_by'] = Auth::id();

        Income::create($validated);

        return redirect()->route('income.index')
            ->with('success', 'Mapato yamerekodiwa kikamilifu');
    }

    /**
     * Display the specified income
     */
    public function show($id)
    {
        $income = Income::with(['category', 'member', 'creator', 'updater'])->findOrFail($id);

        return view('panel.income.show', compact('income'));
    }

    /**
     * Show the form for editing the specified income
     */
    public function edit($id)
    {
        $income = Income::findOrFail($id);
        $categories = IncomeCategory::active()->ordered()->get();
        $members = Member::active()->orderBy('first_name')->get();

        return view('panel.income.edit', compact('income', 'categories', 'members'));
    }

    /**
     * Update the specified income in storage
     */
    public function update(Request $request, $id)
    {
        $income = Income::findOrFail($id);

        $validated = $request->validate([
            'income_category_id' => 'required|exists:income_categories,id',
            'collection_date' => 'required|date',
            'amount' => 'required|numeric|min:0|max:999999999999.99',
            'member_id' => 'nullable|exists:members,id',
            'receipt_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ], [
            'income_category_id.required' => 'Tafadhali chagua kategoria ya mapato',
            'income_category_id.exists' => 'Kategoria iliyochaguliwa haipo',
            'collection_date.required' => 'Tafadhali ingiza tarehe ya kukusanya',
            'collection_date.date' => 'Tarehe si sahihi',
            'amount.required' => 'Tafadhali ingiza kiasi',
            'amount.numeric' => 'Kiasi lazima kiwe nambari',
            'amount.min' => 'Kiasi lazima kiwe chanya',
            'amount.max' => 'Kiasi ni kubwa mno',
            'member_id.exists' => 'Muumini aliyechaguliwa hayupo',
            'receipt_number.max' => 'Nambari ya risiti ni ndefu mno',
            'notes.max' => 'Maelezo ni marefu mno',
        ]);

        $validated['updated_by'] = Auth::id();

        $income->update($validated);

        return redirect()->route('income.index')
            ->with('success', 'Mapato yamebadilishwa kikamilifu');
    }

    /**
     * Remove the specified income from storage (soft delete)
     */
    public function destroy($id)
    {
        $income = Income::findOrFail($id);
        $income->delete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Mapato yamefutwa kikamilifu',
            ]);
        }

        return redirect()->route('income.index')
            ->with('success', 'Mapato yamefutwa kikamilifu');
    }

    /**
     * Show bulk entry form
     */
    public function bulkEntry()
    {
        $categories = IncomeCategory::active()->ordered()->get();
        $members = Member::active()->orderBy('first_name')->get();

        return view('panel.income.bulk-entry', compact('categories', 'members'));
    }

    /**
     * Store bulk income entries
     */
    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'entries' => 'required|array|min:1',
            'entries.*.income_category_id' => 'required|exists:income_categories,id',
            'entries.*.collection_date' => 'required|date',
            'entries.*.amount' => 'required|numeric|min:0|max:999999999999.99',
            'entries.*.member_id' => 'nullable|exists:members,id',
            'entries.*.receipt_number' => 'nullable|string|max:50',
            'entries.*.notes' => 'nullable|string|max:1000',
        ], [
            'entries.required' => 'Tafadhali ongeza angalau ingizo moja',
            'entries.*.income_category_id.required' => 'Kategoria inahitajika kwa kila ingizo',
            'entries.*.collection_date.required' => 'Tarehe inahitajika kwa kila ingizo',
            'entries.*.amount.required' => 'Kiasi kinahitajika kwa kila ingizo',
            'entries.*.amount.numeric' => 'Kiasi lazima kiwe nambari',
            'entries.*.amount.min' => 'Kiasi lazima kiwe chanya',
        ]);

        DB::beginTransaction();
        try {
            $count = 0;
            foreach ($validated['entries'] as $entry) {
                $entry['created_by'] = Auth::id();
                Income::create($entry);
                $count++;
            }
            DB::commit();

            return redirect()->route('income.index')
                ->with('success', "Mapato {$count} yamerekodiwa kikamilifu");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Hitilafu imetokea wakati wa kuhifadhi mapato')
                ->withInput();
        }
    }

    /**
     * Display income categories
     */
    public function categories()
    {
        $categories = IncomeCategory::orderBy('name')->get();
        return view('panel.income.categories', compact('categories'));
    }

    /**
     * Store a new income category
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:income_categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        IncomeCategory::create($validated);

        return redirect()->route('income.categories')
            ->with('success', 'Kategoria imeongezwa kikamilifu!');
    }

    /**
     * Get financial summary for API
     */
    public function getFinancialSummary(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Income::query();
        
        if ($startDate) {
            $query->where('collection_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('collection_date', '<=', $endDate);
        }

        $totalIncome = $query->sum('amount');
        $count = $query->count();

        return response()->json([
            'total' => $totalIncome,
            'count' => $count,
            'formatted_total' => number_format($totalIncome, 2)
        ]);
    }
}
