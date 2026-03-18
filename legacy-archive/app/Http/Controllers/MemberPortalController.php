<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use App\Models\Income;
use App\Models\Pledge;
use App\Models\PledgePayment;
use Barryvdh\DomPDF\Facade\Pdf;

class MemberPortalController extends Controller
{
    /**
     * Show member dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Get the member associated with this user
        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            return redirect()->route('dashboard')
                ->with('error', 'Taarifa za muumini hazijapatikana. Tafadhali wasiliana na msimamizi.');
        }

        // Get member's contributions (excluding weekly thanksgiving - M0002)
        $contributions = Income::where('member_id', $member->id)
            ->whereHas('category', function($query) {
                $query->where('name', 'NOT LIKE', '%M0002%')
                      ->orWhere('name', 'NOT LIKE', '%Shukrani ya Wiki%');
            })
            ->orderBy('collection_date', 'desc')
            ->paginate(10);

        // Get member's pledges with payment progress
        $pledges = Pledge::where('member_id', $member->id)
            ->with('payments')
            ->orderBy('pledge_date', 'desc')
            ->get();

        // Get member's recent payments
        $recentPayments = PledgePayment::where('member_id', $member->id)
            ->with('pledge')
            ->orderBy('payment_date', 'desc')
            ->take(5)
            ->get();

        // Calculate statistics
        $totalPledged = $pledges->sum('amount');
        $totalPaid = $pledges->sum('amount_paid');
        $totalRemaining = $totalPledged - $totalPaid;
        $completedPledges = $pledges->where('status', 'Completed')->count();
        $pendingPledges = $pledges->whereIn('status', ['Pending', 'Partial'])->count();

        return view('member-portal.dashboard', compact(
            'member',
            'contributions',
            'pledges',
            'recentPayments',
            'totalPledged',
            'totalPaid',
            'totalRemaining',
            'completedPledges',
            'pendingPledges'
        ));
    }

    /**
     * Show member's contribution history
     */
    public function contributions()
    {
        $user = Auth::user();
        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            return redirect()->route('dashboard')
                ->with('error', 'Taarifa za muumini hazijapatikana.');
        }

        // Get all contributions except weekly thanksgiving
        $contributions = Income::where('member_id', $member->id)
            ->whereHas('category', function($query) {
                $query->where('name', 'NOT LIKE', '%M0002%')
                      ->orWhere('name', 'NOT LIKE', '%Shukrani ya Wiki%');
            })
            ->with('category')
            ->orderBy('collection_date', 'desc')
            ->paginate(15);

        $totalContributions = Income::where('member_id', $member->id)
            ->whereHas('category', function($query) {
                $query->where('name', 'NOT LIKE', '%M0002%')
                      ->orWhere('name', 'NOT LIKE', '%Shukrani ya Wiki%');
            })
            ->sum('amount');

        return view('member-portal.contributions', compact('member', 'contributions', 'totalContributions'));
    }

    /**
     * Show member's pledges
     */
    public function pledges()
    {
        $user = Auth::user();
        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            return redirect()->route('dashboard')
                ->with('error', 'Taarifa za muumini hazijapatikana.');
        }

        $pledges = Pledge::where('member_id', $member->id)
            ->with('payments')
            ->orderBy('pledge_date', 'desc')
            ->get();

        return view('member-portal.pledges', compact('member', 'pledges'));
    }

    /**
     * Show member's payment receipts
     */
    public function receipts()
    {
        $user = Auth::user();
        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            return redirect()->route('dashboard')
                ->with('error', 'Taarifa za muumini hazijapatikana.');
        }

        $payments = PledgePayment::where('member_id', $member->id)
            ->with('pledge')
            ->orderBy('payment_date', 'desc')
            ->paginate(15);

        return view('member-portal.receipts', compact('member', 'payments'));
    }

    /**
     * Download receipt PDF for a specific payment
     */
    public function downloadReceipt($paymentId)
    {
        $user = Auth::user();
        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            return redirect()->route('dashboard')
                ->with('error', 'Taarifa za muumini hazijapatikana.');
        }

        $payment = PledgePayment::where('id', $paymentId)
            ->where('member_id', $member->id)
            ->with(['pledge', 'member'])
            ->first();

        if (!$payment) {
            return redirect()->back()
                ->with('error', 'Risiti haijapatikana.');
        }

        // Generate PDF in landscape orientation
        $pdf = Pdf::loadView('member-portal.receipt-pdf', compact('payment', 'member'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('risiti-' . $payment->receipt_number . '.pdf');
    }

    /**
     * View receipt in browser
     */
    public function viewReceipt($paymentId)
    {
        $user = Auth::user();
        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            return redirect()->route('dashboard')
                ->with('error', 'Taarifa za muumini hazijapatikana.');
        }

        $payment = PledgePayment::where('id', $paymentId)
            ->where('member_id', $member->id)
            ->with(['pledge', 'member'])
            ->first();

        if (!$payment) {
            return redirect()->back()
                ->with('error', 'Risiti haijapatikana.');
        }

        return view('member-portal.receipt-view', compact('payment', 'member'));
    }

    /**
     * Show member profile edit form
     */
    public function editProfile()
    {
        $user = Auth::user();
        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            return redirect()->route('dashboard')
                ->with('error', 'Taarifa za muumini hazijapatikana.');
        }

        return view('member-portal.profile-edit', compact('member'));
    }

    /**
     * Update member profile
     * Members can only update their personal information, not department or role
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            return redirect()->route('dashboard')
                ->with('error', 'Taarifa za muumini hazijapatikana.');
        }

        // Validate only allowed fields (no department, role, or member_number)
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Mme,Mke',
            'marital_status' => 'required|string|max:50',
            'occupation' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'house_number' => 'nullable|string|max:50',
            'block_number' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'spouse_name' => 'nullable|string|max:255',
            'spouse_phone' => 'nullable|string|max:20',
            'neighbor_name' => 'nullable|string|max:255',
            'neighbor_phone' => 'nullable|string|max:20',
            'id_number' => 'nullable|string|max:50',
        ], [
            'first_name.required' => 'Jina la kwanza linahitajika',
            'last_name.required' => 'Jina la mwisho linahitajika',
            'phone.required' => 'Namba ya simu inahitajika',
            'date_of_birth.required' => 'Tarehe ya kuzaliwa inahitajika',
            'gender.required' => 'Jinsia inahitajika',
            'marital_status.required' => 'Hali ya ndoa inahitajika',
        ]);

        // Update member with only allowed fields
        $member->update($validated);

        return redirect()->route('member.profile.edit')
            ->with('success', 'Taarifa zako zimesasishwa kikamilifu!');
    }
}
