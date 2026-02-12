<?php

namespace App\Http\Controllers;

use App\Models\Offering;
use Illuminate\Http\Request;

class OfferingsController extends Controller
{
    public function index()
    {
        return Offering::with('church')->orderBy('date', 'DESC')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'church_id' => 'required|exists:churches,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'recorded_by' => 'required|string'
        ]);

        $offering = Offering::create($validated);

        return response()->json($offering, 201);
    }

    public function show($id)
    {
        $offering = Offering::with('church')->find($id);

        if (!$offering) {
            return response()->json(['message' => 'Offering not found'], 404);
        }

        return $offering;
    }

    public function update(Request $request, $id)
    {
        $offering = Offering::find($id);

        if (!$offering) {
            return response()->json(['message' => 'Offering not found'], 404);
        }

        $validated = $request->validate([
            'church_id' => 'required|exists:churches,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'recorded_by' => 'required|string'
        ]);

        $offering->update($validated);

        return $offering;
    }

    public function destroy($id)
    {
        $offering = Offering::find($id);

        if (!$offering) {
            return response()->json(['message' => 'Offering not found'], 404);
        }

        $offering->delete();

        return response()->json(['message' => 'Offering deleted']);
    }
}
