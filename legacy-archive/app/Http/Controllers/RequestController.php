<?php

namespace App\Http\Controllers;

use App\Models\Request as RequestModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RequestController extends Controller
{
    /**
     * Display a listing of requests with status filters
     */
    public function index(Request $request)
    {
        $query = RequestModel::with(['requester', 'approver']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('requested_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('requested_date', '<=', $request->end_date);
        }

        // Get requests ordered by date descending
        $requests = $query->orderBy('requested_date', 'desc')->paginate(7);

        // Get statistics
        $stats = [
            'total' => RequestModel::count(),
            'pending' => RequestModel::pending()->count(),
            'approved' => RequestModel::approved()->count(),
            'rejected' => RequestModel::rejected()->count(),
        ];

        // Get count by status (for compatibility if used elsewhere)
        $statusCounts = [
            'Inasubiri' => $stats['pending'],
            'Imeidhinishwa' => $stats['approved'],
            'Imekataliwa' => $stats['rejected'],
        ];

        // Get unique departments
        $departments = RequestModel::select('department')
            ->distinct()
            ->whereNotNull('department')
            ->orderBy('department')
            ->pluck('department');

        return view('panel.requests.index', compact(
            'requests',
            'stats',
            'statusCounts',
            'departments'
        ));
    }

    /**
     * Show the form for creating a new request
     */
    public function create()
    {
        // Common departments
        $departments = [
            'Uongozi',
            'Uimbaji',
            'Usafi',
            'Afya',
            'Teknolojia',
            'Mapokezi',
            'Vijana',
            'Wanawake',
            'Watoto'
        ];

        // Generate next request number for preview
        $year = date('Y');
        $lastRequest = RequestModel::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastRequest ? intval(substr($lastRequest->request_number, -4)) + 1 : 1;
        $nextRequestNumber = 'REQ' . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        return view('panel.requests.create', compact('departments', 'nextRequestNumber'));
    }

    /**
     * Store a newly created request in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:100',
            'description' => 'required|string|max:2000',
            'amount_requested' => 'required|numeric|min:0|max:999999999999.99',
            'requested_date' => 'required|date',
        ], [
            'title.required' => 'Tafadhali ingiza kichwa cha ombi',
            'title.max' => 'Kichwa ni kirefu mno',
            'department.required' => 'Tafadhali chagua idara',
            'department.max' => 'Jina la idara ni refu mno',
            'description.required' => 'Tafadhali ingiza maelezo ya ombi',
            'description.max' => 'Maelezo ni marefu mno',
            'amount_requested.required' => 'Tafadhali ingiza kiasi kinachohitajika',
            'amount_requested.numeric' => 'Kiasi lazima kiwe nambari',
            'amount_requested.min' => 'Kiasi lazima kiwe chanya',
            'amount_requested.max' => 'Kiasi ni kubwa mno',
            'requested_date.required' => 'Tafadhali chagua tarehe ya ombi',
            'requested_date.date' => 'Tarehe si sahihi',
        ]);

        // Generate request number
        $year = date('Y');
        $lastRequest = RequestModel::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastRequest ? intval(substr($lastRequest->request_number, -4)) + 1 : 1;
        $validated['request_number'] = 'REQ' . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        $validated['status'] = 'Inasubiri';
        $validated['requested_by'] = Auth::id();

        RequestModel::create($validated);

        return redirect()->route('requests.index')
            ->with('success', 'Ombi limewasilishwa kikamilifu');
    }

    /**
     * Display the specified request
     */
    public function show($id)
    {
        $request = RequestModel::with(['requester', 'approver'])->findOrFail($id);

        return view('panel.requests.show', compact('request'));
    }

    /**
     * Show the form for editing the specified request
     */
    public function edit($id)
    {
        $requestModel = RequestModel::findOrFail($id);

        // Only allow editing if status is pending
        if ($requestModel->status !== 'Inasubiri') {
            return redirect()->route('requests.show', $id)
                ->with('error', 'Ombi ambalo tayari limeidhinishwa au kukataliwa haliwezi kubadilishwa');
        }

        $departments = [
            'Uongozi',
            'Uimbaji',
            'Usafi',
            'Afya',
            'Teknolojia',
            'Mapokezi',
            'Vijana',
            'Wanawake',
            'Watoto'
        ];

        return view('panel.requests.edit', [
            'request' => $requestModel,
            'departments' => $departments
        ]);
    }

    /**
     * Update the specified request in storage
     */
    public function update(Request $request, $id)
    {
        $requestModel = RequestModel::findOrFail($id);

        // Only allow updating if status is pending
        if ($requestModel->status !== 'Inasubiri') {
            return redirect()->route('requests.show', $id)
                ->with('error', 'Ombi ambalo tayari limeidhinishwa au kukataliwa haliwezi kubadilishwa');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:100',
            'description' => 'required|string|max:2000',
            'amount_requested' => 'required|numeric|min:0|max:999999999999.99',
            'requested_date' => 'required|date',
        ], [
            'title.required' => 'Tafadhali ingiza kichwa cha ombi',
            'title.max' => 'Kichwa ni kirefu mno',
            'department.required' => 'Tafadhali chagua idara',
            'department.max' => 'Jina la idara ni refu mno',
            'description.required' => 'Tafadhali ingiza maelezo ya ombi',
            'description.max' => 'Maelezo ni marefu mno',
            'amount_requested.required' => 'Tafadhali ingiza kiasi kinachohitajika',
            'amount_requested.numeric' => 'Kiasi lazima kiwe nambari',
            'amount_requested.min' => 'Kiasi lazima kiwe chanya',
            'amount_requested.max' => 'Kiasi ni kubwa mno',
            'requested_date.required' => 'Tafadhali chagua tarehe ya ombi',
            'requested_date.date' => 'Tarehe si sahihi',
        ]);

        $requestModel->update($validated);

        return redirect()->route('requests.index')
            ->with('success', 'Ombi limebadilishwa kikamilifu');
    }

    /**
     * Remove the specified request from storage (soft delete)
     */
    public function destroy($id)
    {
        $request = RequestModel::findOrFail($id);
        $request->delete();

        return redirect()->route('requests.index')
            ->with('success', 'Ombi limefutwa kikamilifu');
    }

    /**
     * Approve a request
     */
    public function approve(Request $request, $id)
    {
        $requestModel = RequestModel::findOrFail($id);

        // Only allow approval if status is pending
        if ($requestModel->status !== 'Inasubiri') {
            return redirect()->route('requests.show', $id)
                ->with('error', 'Ombi hili tayari limeshughulikiwa');
        }

        $validated = $request->validate([
            'amount_approved' => 'required|numeric|min:0|max:999999999999.99',
            'approval_notes' => 'nullable|string|max:1000',
        ], [
            'amount_approved.required' => 'Tafadhali ingiza kiasi kilichoidhinishwa',
            'amount_approved.numeric' => 'Kiasi lazima kiwe nambari',
            'amount_approved.min' => 'Kiasi lazima kiwe chanya',
            'amount_approved.max' => 'Kiasi ni kubwa mno',
            'approval_notes.max' => 'Maelezo ni marefu mno',
        ]);

        $requestModel->update([
            'status' => 'Imeidhinishwa',
            'amount_approved' => $validated['amount_approved'],
            'approval_notes' => $validated['approval_notes'] ?? null,
            'approved_by' => Auth::id(),
            'approved_date' => Carbon::now(),
        ]);

        return redirect()->route('requests.show', $id)
            ->with('success', 'Ombi limeidhinishwa kikamilifu');
    }

    /**
     * Reject a request
     */
    public function reject(Request $request, $id)
    {
        $requestModel = RequestModel::findOrFail($id);

        // Only allow rejection if status is pending
        if ($requestModel->status !== 'Inasubiri') {
            return redirect()->route('requests.show', $id)
                ->with('error', 'Ombi hili tayari limeshughulikiwa');
        }

        $validated = $request->validate([
            'approval_notes' => 'required|string|max:1000',
        ], [
            'approval_notes.required' => 'Tafadhali ingiza sababu za kukataa ombi',
            'approval_notes.max' => 'Maelezo ni marefu mno',
        ]);

        $requestModel->update([
            'status' => 'Imekataliwa',
            'approval_notes' => $validated['approval_notes'],
            'approved_by' => Auth::id(),
            'approved_date' => Carbon::now(),
        ]);

        return redirect()->route('requests.show', $id)
            ->with('success', 'Ombi limekataliwa');
    }
}
