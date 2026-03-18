<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\Member;
use App\Models\Pledge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class QuickEntryController extends Controller
{
    /**
     * Show quick entry login page
     */
    public function showLogin()
    {
        return view('quick-entry.login');
    }

    /**
     * Handle quick entry login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Tafadhali ingiza email',
            'email.email' => 'Email si sahihi',
            'password.required' => 'Tafadhali ingiza nywila',
        ]);

        // Verify user credentials
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Store quick entry session flag
            session(['quick_entry' => true]);

            return redirect()->route('quick-entry.scanner');
        }

        return back()->withErrors([
            'email' => 'Taarifa sio sahihi',
        ])->withInput($request->only('email'));
    }

    /**
     * Show QR scanner page
     */
    public function scanner()
    {
        // Check if user is authenticated and in quick entry mode
        if (!Auth::check() || !session('quick_entry')) {
            return redirect()->route('quick-entry.login');
        }

        return view('quick-entry.scanner');
    }

    /**
     * Get member details by member number
     */
    public function getMemberInfo($memberNumber)
    {
        if (!Auth::check() || !session('quick_entry')) {
            return response()->json([
                'success' => false,
                'message' => 'Hauruhusiwi kufikia huduma hii'
            ], 401);
        }

        $member = Member::where('member_number', $memberNumber)->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Muumini hajapatikana'
            ], 404);
        }

        // Get member's total contributions
        $totalContributions = Income::where('member_id', $member->id)->sum('amount');

        // Get member's pledges
        $pledges = Pledge::where('member_id', $member->id)->get();
        $totalPledged = $pledges->sum('amount');
        $totalPaid = $pledges->sum('amount_paid');
        $totalRemaining = $totalPledged - $totalPaid;
        $activePledges = $pledges->whereIn('status', ['Pending', 'Partial'])->count();
        $completedPledges = $pledges->where('status', 'Completed')->count();

        // Format pledges for response
        $pledgesData = $pledges->map(function($pledge) {
            return [
                'id' => $pledge->id,
                'type' => $pledge->pledge_type,
                'amount' => $pledge->amount,
                'amount_paid' => $pledge->amount_paid,
                'remaining' => $pledge->remaining_amount,
                'status' => $pledge->status,
                'progress' => round($pledge->progress_percentage, 1),
                'pledge_date' => $pledge->pledge_date->format('d/m/Y'),
            ];
        });

        return response()->json([
            'success' => true,
            'member' => [
                'id' => $member->id,
                'member_number' => $member->member_number,
                'full_name' => $member->first_name . ' ' . ($member->middle_name ? $member->middle_name . ' ' : '') . $member->last_name,
                'phone' => $member->phone,
                'email' => $member->email,
                'gender' => $member->gender,
                'age' => $member->age,
                'age_group' => $member->age_group,
                'marital_status' => $member->marital_status,
                'special_group' => $member->special_group,
                'is_active' => $member->is_active,
                'total_contributions' => number_format($totalContributions, 0),
            ],
            'pledges' => [
                'total_pledged' => $totalPledged,
                'total_paid' => $totalPaid,
                'total_remaining' => $totalRemaining,
                'active_count' => $activePledges,
                'completed_count' => $completedPledges,
                'has_debt' => $totalRemaining > 0,
                'list' => $pledgesData
            ]
        ]);
    }

    /**
     * Show contribution entry form for a member
     */
    public function showContributionForm($memberNumber)
    {
        if (!Auth::check() || !session('quick_entry')) {
            return redirect()->route('quick-entry.login');
        }

        $member = Member::where('member_number', $memberNumber)->firstOrFail();
        $categories = IncomeCategory::active()->ordered()->get();

        return view('quick-entry.contribution-form', compact('member', 'categories'));
    }

    /**
     * Store contribution
     */
    public function storeContribution(Request $request, $memberNumber)
    {
        if (!Auth::check() || !session('quick_entry')) {
            return response()->json([
                'success' => false,
                'message' => 'Hauruhusiwi kufikia huduma hii'
            ], 401);
        }

        $member = Member::where('member_number', $memberNumber)->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Muumini hajapatikana'
            ], 404);
        }

        $validated = $request->validate([
            'income_category_id' => 'required|exists:income_categories,id',
            'amount' => 'required|numeric|min:0|max:999999999999.99',
            'collection_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ], [
            'income_category_id.required' => 'Tafadhali chagua aina ya mchango',
            'income_category_id.exists' => 'Aina ya mchango haipo',
            'amount.required' => 'Tafadhali ingiza kiasi',
            'amount.numeric' => 'Kiasi lazima kiwe nambari',
            'amount.min' => 'Kiasi lazima kiwe chanya',
            'amount.max' => 'Kiasi ni kubwa mno',
            'collection_date.required' => 'Tafadhali ingiza tarehe',
            'collection_date.date' => 'Tarehe si sahihi',
            'notes.max' => 'Maelezo ni marefu mno',
        ]);

        // Create income record
        Income::create([
            'income_category_id' => $validated['income_category_id'],
            'amount' => $validated['amount'],
            'collection_date' => $validated['collection_date'],
            'member_id' => $member->id,
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mchango umerekodiwa kikamilifu'
        ]);
    }

    /**
     * Logout from quick entry
     */
    public function logout(Request $request)
    {
        session()->forget('quick_entry');
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('quick-entry.login')
            ->with('success', 'Umetoka kikamilifu');
    }
}
