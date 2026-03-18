<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfferingRequest;
use App\Models\Offering;

class OfferingController extends Controller
{
    public function index()
    {
        $offerings = Offering::query()
            ->where('church_id', auth()->user()->effectiveBranchId())
            ->orderByDesc('date')
            ->paginate(20);

        return view('panel.offerings.index', compact('offerings'));
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
        ]);

        return redirect()->route('offerings.index')->with('status', 'Offering recorded.');
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
        ]);

        return redirect()->route('offerings.index')->with('status', 'Offering updated.');
    }

    public function destroy(Offering $offering)
    {
        $this->authorize('delete', $offering);
        $offering->delete();

        return back()->with('status', 'Offering deleted.');
    }
}
