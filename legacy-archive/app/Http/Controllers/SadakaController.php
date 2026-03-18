<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Income;
use App\Models\Pledge;
use App\Models\PledgePayment;
use App\Models\IncomeCategory;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;

class SadakaController extends Controller
{
    /**
     * Display sadaka page
     */
public function index(Request $request)
{
    $tab = $request->get('tab', 'sadaka');

    $perPage = 10;
    $year = $request->get('year', date('Y'));
    $month = $request->get('month', '');

    // Get sadaka data for initial page load
    $sadakaQuery = Income::with(['category', 'member'])
        ->orderBy('collection_date', 'desc');

    if ($year) {
        $sadakaQuery->whereYear('collection_date', $year);
    }

    if ($month) {
        $sadakaQuery->whereMonth('collection_date', $month);
    }

    $sadaka = $sadakaQuery->paginate($perPage);

    // Calculate total sadaka
    $totalSadaka = $sadakaQuery->sum('amount');

    // Get ahadi data for initial page load
    $ahadiQuery = Pledge::with(['member', 'payments'])
        ->orderBy('pledge_date', 'desc');

    if ($year) {
        $ahadiQuery->whereYear('pledge_date', $year);
    }

    $ahadi = $ahadiQuery->paginate($perPage);

    // Calculate totals for initial page load
    $totalAhadi = Pledge::sum('amount');
    $totalMalipo = Pledge::sum('amount_paid');

    // Get categories for dropdowns
    $categories = IncomeCategory::active()->ordered()->get();

    // Get members for dropdowns
    $members = Member::where('is_active', true)
        ->orderBy('first_name', 'asc')
        ->get();

    return view('panel.sadaka.sadaka', [
        'current_tab' => $tab,
        'sadaka' => $sadaka,
        'ahadi' => $ahadi,
        'totalSadaka' => $totalSadaka,
        'totalAhadi' => $totalAhadi,
        'totalMalipo' => $totalMalipo,
        'categories' => $categories,
        'members' => $members,
        'year' => $year,
        'selectedMonth' => $month,
    ]);
}

    /**
     * Show create form for sadaka
     */
    public function create()
    {
        $categories = IncomeCategory::active()->ordered()->get();
        $members = Member::where('is_active', true)
            ->orderBy('first_name', 'asc')
            ->get();

        return view('panel.sadaka.sadaka-create', [
            'categories' => $categories,
            'members' => $members,
        ]);
    }

    /**
     * Get sadaka data via AJAX
     */
    public function getSadakaData(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');
        $month = $request->get('month', '');
        $year = $request->get('year', date('Y'));

        // Query incomes with category - REMOVED 'createdBy'
        $query = Income::with(['category', 'member'])
            ->orderBy('collection_date', 'desc');

        // Apply filters
        if ($search) {
            $query->whereHas('category', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }

        if ($month) {
            $query->whereMonth('collection_date', $month);
        }

        if ($year) {
            $query->whereYear('collection_date', $year);
        }

        $incomes = $query->paginate($perPage);

        // Calculate totals
        $total = $query->sum('amount');

        return response()->json([
            'success' => true,
            'data' => $incomes->map(function($income) {
                return [
                    'id' => $income->id,
                    'tarehe' => $income->collection_date->format('Y-m-d'),
                    'tarehe_formatted' => $income->collection_date->format('d/m/Y'),
                    'aina_sadaka' => $income->category ? $income->category->name : 'N/A',
                    'mwanachama' => $income->member ? $income->member->full_name : 'Jumla',
                    'kiasi' => $income->amount,
                    'kiasi_formatted' => number_format($income->amount, 0),
                    'maelezo' => $income->notes,
                    'mwezi' => $income->collection_date->format('M'),
                    'created_by' => 'System' // REMOVED creator reference
                ];
            }),
            'pagination' => [
                'current_page' => $incomes->currentPage(),
                'last_page' => $incomes->lastPage(),
                'per_page' => $incomes->perPage(),
                'total' => $incomes->total(),
                'from' => $incomes->firstItem(),
                'to' => $incomes->lastItem(),
            ],
            'summary' => [
                'total_amount' => $total,
                'total_amount_formatted' => number_format($total, 0),
                'total_records' => $incomes->total()
            ]
        ]);
    }

    /**
     * Get ahadi data via AJAX
     */
    public function getAhadiData(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        $pledgeType = $request->get('pledge_type', '');

        // Query pledges with member - REMOVED 'createdBy'
        $query = Pledge::with(['member', 'payments'])
            ->orderBy('pledge_date', 'desc');

        // Apply filters
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('member', function($memberQuery) use ($search) {
                    $memberQuery->where('first_name', 'LIKE', "%{$search}%")
                               ->orWhere('last_name', 'LIKE', "%{$search}%")
                               ->orWhere('phone', 'LIKE', "%{$search}%");
                })->orWhere('pledge_type', 'LIKE', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($pledgeType) {
            $query->where('pledge_type', 'LIKE', "%{$pledgeType}%");
        }

        $pledges = $query->paginate($perPage);

        // Calculate totals
        $totalPledged = Pledge::sum('amount');
        $totalPaid = Pledge::sum('amount_paid');
        $totalRemaining = $totalPledged - $totalPaid;

        return response()->json([
            'success' => true,
            'data' => $pledges->map(function($pledge) {
                return [
                    'id' => $pledge->id,
                    'jina' => $pledge->member ? $pledge->member->full_name : 'N/A',
                    'namba_simu' => $pledge->member ? $pledge->member->phone : 'N/A',
                    'namba_mwanachama' => $pledge->member ? $pledge->member->member_number : 'N/A',
                    'kiasi_ahadi' => $pledge->amount,
                    'kiasi_ahadi_formatted' => number_format($pledge->amount, 0),
                    'kiasi_lililolipwa' => $pledge->amount_paid,
                    'kiasi_lililolipwa_formatted' => number_format($pledge->amount_paid, 0),
                    'kiasi_kilichobaki' => $pledge->remaining_amount,
                    'kiasi_kilichobaki_formatted' => number_format($pledge->remaining_amount, 0),
                    'aina_ahadi' => $pledge->pledge_type,
                    'tarehe_ahadi' => $pledge->pledge_date->format('d/m/Y'),
                    'tarehe_mwisho' => $pledge->due_date ? $pledge->due_date->format('d/m/Y') : 'N/A',
                    'hali' => $pledge->status,
                    'maendeleo' => round($pledge->progress_percentage, 1),
                    'idadi_malipo' => $pledge->payments->count(),
                    'mwezi' => $pledge->pledge_date->format('M')
                ];
            }),
            'pagination' => [
                'current_page' => $pledges->currentPage(),
                'last_page' => $pledges->lastPage(),
                'per_page' => $pledges->perPage(),
                'total' => $pledges->total(),
                'from' => $pledges->firstItem(),
                'to' => $pledges->lastItem(),
            ],
            'summary' => [
                'total_pledged' => $totalPledged,
                'total_pledged_formatted' => number_format($totalPledged, 0),
                'total_paid' => $totalPaid,
                'total_paid_formatted' => number_format($totalPaid, 0),
                'total_remaining' => $totalRemaining,
                'total_remaining_formatted' => number_format($totalRemaining, 0),
                'total_records' => $pledges->total(),
                'pending_count' => Pledge::where('status', 'Pending')->count(),
                'partial_count' => Pledge::where('status', 'Partial')->count(),
                'completed_count' => Pledge::where('status', 'Completed')->count(),
            ]
        ]);
    }

    /**
     * Get malipo data via AJAX
     */
    public function getMalipoData(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');
        $pledgeId = $request->get('pledge_id', '');

        // Query payments with pledge and member - REMOVED 'recordedBy'
        $query = PledgePayment::with(['pledge', 'member'])
            ->orderBy('payment_date', 'desc');

        // Apply filters
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('member', function($memberQuery) use ($search) {
                    $memberQuery->where('first_name', 'LIKE', "%{$search}%")
                               ->orWhere('last_name', 'LIKE', "%{$search}%");
                })->orWhere('receipt_number', 'LIKE', "%{$search}%");
            });
        }

        if ($pledgeId) {
            $query->where('pledge_id', $pledgeId);
        }

        $payments = $query->paginate($perPage);

        // Calculate totals
        $totalPayments = PledgePayment::sum('amount');

        return response()->json([
            'success' => true,
            'data' => $payments->map(function($payment) {
                return [
                    'id' => $payment->id,
                    'jina' => $payment->member ? $payment->member->full_name : 'N/A',
                    'namba_risiti' => $payment->receipt_number,
                    'kiasi' => $payment->amount,
                    'kiasi_formatted' => number_format($payment->amount, 0),
                    'tarehe_malipo' => $payment->payment_date->format('d/m/Y'),
                    'njia_malipo' => $payment->payment_method ?? 'N/A',
                    'namba_kumbukumbu' => $payment->reference_number ?? 'N/A',
                    'aina_ahadi' => $payment->pledge ? $payment->pledge->pledge_type : 'N/A',
                    'maelezo' => $payment->notes,
                    'recorded_by' => 'System' // REMOVED recordedBy reference
                ];
            }),
            'pagination' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
                'from' => $payments->firstItem(),
                'to' => $payments->lastItem(),
            ],
            'summary' => [
                'total_amount' => $totalPayments,
                'total_amount_formatted' => number_format($totalPayments, 0),
                'total_records' => $payments->total()
            ]
        ]);
    }

    /**
     * Store a newly created sadaka
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'income_category_id' => 'required|exists:income_categories,id',
            'collection_date' => 'required|date',
            'amount' => 'required|numeric|min:0|max:999999999999.99',
            'member_id' => 'nullable|exists:members,id',
            'notes' => 'nullable|string|max:1000'
        ]);

        Income::create([
            'income_category_id' => $validated['income_category_id'],
            'collection_date' => $validated['collection_date'],
            'amount' => $validated['amount'],
            'member_id' => $validated['member_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Sadaka imehifadhiwa kikamilifu!'
            ]);
        }

        return redirect()->route('offerings.index')
            ->with('success', 'Sadaka imehifadhiwa kikamilifu!');
    }

    /**
     * Store a new ahadi
     */
    public function storeAhadi(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'pledge_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0|max:999999999999.99',
            'pledge_date' => 'required|date',
            'due_date' => 'nullable|date|after:pledge_date',
            'notes' => 'nullable|string|max:1000'
        ]);

        Pledge::create([
            'member_id' => $validated['member_id'],
            'pledge_type' => $validated['pledge_type'],
            'amount' => $validated['amount'],
            'pledge_date' => $validated['pledge_date'],
            'due_date' => $validated['due_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ahadi imehifadhiwa kikamilifu!'
            ]);
        }

        return redirect()->route('offerings.index', ['tab' => 'ahadi'])
            ->with('success', 'Ahadi imehifadhiwa kikamilifu!');
    }

    /**
     * Store a new malipo
     */
    public function storeMalipo(Request $request)
    {
        $validated = $request->validate([
            'pledge_id' => 'required|exists:pledges,id',
            'amount' => 'required|numeric|min:0|max:999999999999.99',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Get pledge to get member_id
        $pledge = Pledge::findOrFail($validated['pledge_id']);

        PledgePayment::create([
            'pledge_id' => $validated['pledge_id'],
            'member_id' => $pledge->member_id,
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'] ?? null,
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'recorded_by' => Auth::id(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Malipo yamehifadhiwa kikamilifu!'
            ]);
        }

        return redirect()->route('offerings.index', ['tab' => 'malipo'])
            ->with('success', 'Malipo yamehifadhiwa kikamilifu!');
    }

    /**
     * Get categories for dropdowns
     */
    public function getCategories()
    {
        $categories = IncomeCategory::active()->ordered()->get();

        return response()->json([
            'success' => true,
            'categories' => $categories->map(function($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'description' => $cat->description
                ];
            })
        ]);
    }

    /**
     * Store jimbo percentage (8% of thanksgiving offering)
     */
    public function storeJimbo(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'collection_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        // Find or create "Asilimia ya Jimbo" category
        $jimboCategory = IncomeCategory::firstOrCreate(
            ['name' => 'Asilimia ya Jimbo'],
            [
                'code' => 'JIMBO-8',
                'description' => 'Asilimia 8% ya Sadaka ya Shukrani inayopelekwa Jimboni',
                'is_active' => true,
                'sort_order' => 999
            ]
        );

        Income::create([
            'income_category_id' => $jimboCategory->id,
            'collection_date' => $validated['collection_date'],
            'amount' => $validated['amount'],
            'notes' => $validated['notes'] ?? 'Asilimia 8% ya Sadaka ya Shukrani',
            'created_by' => Auth::id(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Asilimia ya Jimbo imehifadhiwa!'
            ]);
        }

        return redirect()->route('offerings.index')
            ->with('success', 'Asilimia ya Jimbo imehifadhiwa!');
    }

    /**
     * Get members for dropdowns
     */
    public function getMembers(Request $request)
    {
        $search = $request->get('search', '');

        $query = Member::where('is_active', true)
            ->orderBy('first_name', 'asc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('member_number', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $members = $query->limit(20)->get();

        return response()->json([
            'success' => true,
            'members' => $members->map(function($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->full_name,
                    'member_number' => $member->member_number,
                    'phone' => $member->phone
                ];
            })
        ]);
    }

    /**
     * Get pledges for a specific member
     */
    public function getMemberPledges($memberId)
    {
        $pledges = Pledge::where('member_id', $memberId)
            ->whereIn('status', ['Pending', 'Partial'])
            ->get();

        return response()->json([
            'success' => true,
            'pledges' => $pledges->map(function($pledge) {
                return [
                    'id' => $pledge->id,
                    'pledge_type' => $pledge->pledge_type,
                    'amount' => $pledge->amount,
                    'amount_paid' => $pledge->amount_paid,
                    'remaining' => $pledge->remaining_amount,
                    'status' => $pledge->status
                ];
            })
        ]);
    }

    /**
     * Display the specified offering
     */
    public function show($id)
    {
        $income = Income::with(['category', 'member', 'creator'])->findOrFail($id);
        return view('panel.offerings.show', compact('income'));
    }

    /**
     * Show the form for editing the specified offering
     */
    public function edit($id)
    {
        $income = Income::findOrFail($id);
        $categories = IncomeCategory::active()->ordered()->get();
        $members = Member::where('is_active', true)
            ->orderBy('first_name', 'asc')
            ->get();

        return view('panel.offerings.edit', compact('income', 'categories', 'members'));
    }

    /**
     * Update the specified offering
     */
    public function update(Request $request, $id)
    {
        $income = Income::findOrFail($id);

        $validated = $request->validate([
            'income_category_id' => 'required|exists:income_categories,id',
            'collection_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'member_id' => 'nullable|exists:members,id',
            'receipt_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['updated_by'] = Auth::id();
        $income->update($validated);

        return redirect()->route('offerings.index')
            ->with('success', 'Sadaka imesasishwa kikamilifu!');
    }

    /**
     * Remove the specified offering
     */
    public function destroy($id)
    {
        $income = Income::findOrFail($id);
        $income->delete();

        return redirect()->route('offerings.index')
            ->with('success', 'Sadaka imefutwa kikamilifu!');
    }

    /**
     * Display offering types (categories)
     */
    public function types()
    {
        $categories = IncomeCategory::orderBy('name')->get();
        return view('panel.offerings.types', compact('categories'));
    }

    /**
     * Store a new offering type
     */
    public function storeType(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:income_categories,name',
            'code' => 'nullable|string|max:50|unique:income_categories,code',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        IncomeCategory::create($validated);

        return redirect()->route('offerings.types')
            ->with('success', 'Aina ya sadaka imeongezwa kikamilifu!');
    }
}
