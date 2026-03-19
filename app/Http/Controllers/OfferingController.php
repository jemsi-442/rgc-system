<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfferingRequest;
use App\Models\Offering;
use App\Models\OfferingPayment;

class OfferingController extends Controller
{
    public function index()
    {
        $offerings = Offering::query()
            ->where('church_id', auth()->user()->effectiveBranchId())
            ->orderByDesc('date')
            ->paginate(20);

        $payments = OfferingPayment::query()
            ->with(['reviewedBy'])
            ->where('church_id', auth()->user()->effectiveBranchId())
            ->latest()
            ->paginate(10, ['*'], 'payments_page');

        return view('panel.offerings.index', compact('offerings', 'payments'));
    }

    public function create()
    {
        return view('panel.offerings.create');
    }

    public function store(StoreOfferingRequest $request)
    {
        Offering::query()->create([
            'church_id' => $request->user()->effectiveBranchId(),
            'recorded_by' => $request->user()->name,
            'date' => $request->input('offering_date'),
            'amount' => $request->input('amount'),
            'description' => $request->input('description'),
        ]);

        return redirect()->route('offerings.index')->with('status', __('Offering recorded.'));
    }

    public function edit(Offering $offering)
    {
        $this->authorize('update', $offering);

        return view('panel.offerings.edit', compact('offering'));
    }

    public function update(StoreOfferingRequest $request, Offering $offering)
    {
        $this->authorize('update', $offering);
        $offering->update([
            'date' => $request->input('offering_date'),
            'amount' => $request->input('amount'),
            'description' => $request->input('description'),
        ]);

        return redirect()->route('offerings.index')->with('status', __('Offering updated.'));
    }

    public function destroy(Offering $offering)
    {
        $this->authorize('delete', $offering);
        $offering->delete();

        return back()->with('status', __('Offering deleted.'));
    }
}
