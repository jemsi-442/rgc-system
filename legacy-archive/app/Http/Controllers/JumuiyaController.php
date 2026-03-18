<?php

namespace App\Http\Controllers;

use App\Models\Jumuiya;
use App\Models\Member;
use Illuminate\Http\Request;

class JumuiyaController extends Controller
{
    /**
     * Display a listing of the resource - Redirect to Settings
     */
    public function index()
    {
        return redirect()->route('settings.index', ['tab' => 'jumuiya']);
    }

    /**
     * Show the form for creating a new resource - Return JSON for modal
     */
    public function create()
    {
        $members = Member::where('is_active', true)
            ->orderBy('first_name')
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->full_name,
                    'phone' => $member->phone,
                ];
            });

        return response()->json([
            'members' => $members
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:jumuiyas,name',
            'description' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'leader_id' => 'nullable|exists:members,id',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Tafadhali ingiza jina la jumuiya',
            'name.unique' => 'Jumuiya hii tayari ipo',
            'name.max' => 'Jina la jumuiya ni refu mno',
            'description.max' => 'Maelezo ni marefu mno',
            'leader_id.exists' => 'Kiongozi aliyechaguliwa hapatikani',
        ]);

        $validated['is_active'] = $request->has('is_active') || $request->input('is_active') ? true : false;

        // Get leader phone if leader is selected
        if (!empty($validated['leader_id'])) {
            $leader = Member::find($validated['leader_id']);
            if ($leader) {
                $validated['leader_phone'] = $leader->phone;
            }
        }

        $jumuiya = Jumuiya::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jumuiya imeongezwa kikamilifu',
                'jumuiya' => $jumuiya->load('leader')
            ]);
        }

        return redirect()->route('settings.index', ['tab' => 'jumuiya'])
            ->with('success', 'Jumuiya imeongezwa kikamilifu');
    }

    /**
     * Display the specified resource - Return JSON for modal
     */
    public function show(string $id)
    {
        $jumuiya = Jumuiya::with('leader')
            ->withCount('members')
            ->findOrFail($id);

        $members = $jumuiya->members()
            ->orderBy('first_name')
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->full_name,
                    'member_number' => $member->member_number,
                    'phone' => $member->phone,
                    'is_active' => $member->is_active,
                ];
            });

        return response()->json([
            'jumuiya' => [
                'id' => $jumuiya->id,
                'name' => $jumuiya->name,
                'description' => $jumuiya->description,
                'location' => $jumuiya->location,
                'leader' => $jumuiya->leader ? [
                    'id' => $jumuiya->leader->id,
                    'name' => $jumuiya->leader->full_name,
                    'phone' => $jumuiya->leader->phone,
                ] : null,
                'leader_phone' => $jumuiya->leader_phone,
                'is_active' => $jumuiya->is_active,
                'members_count' => $jumuiya->members_count,
                'created_at' => $jumuiya->created_at->format('d/m/Y'),
            ],
            'members' => $members
        ]);
    }

    /**
     * Show the form for editing - Return JSON for modal
     */
    public function edit(string $id)
    {
        $jumuiya = Jumuiya::with('leader')->findOrFail($id);
        $members = Member::where('is_active', true)
            ->orderBy('first_name')
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->full_name,
                    'phone' => $member->phone,
                ];
            });

        return response()->json([
            'jumuiya' => [
                'id' => $jumuiya->id,
                'name' => $jumuiya->name,
                'description' => $jumuiya->description,
                'location' => $jumuiya->location,
                'leader_id' => $jumuiya->leader_id,
                'is_active' => $jumuiya->is_active,
            ],
            'members' => $members
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $jumuiya = Jumuiya::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:jumuiyas,name,' . $id,
            'description' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'leader_id' => 'nullable|exists:members,id',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Tafadhali ingiza jina la jumuiya',
            'name.unique' => 'Jumuiya hii tayari ipo',
            'name.max' => 'Jina la jumuiya ni refu mno',
            'description.max' => 'Maelezo ni marefu mno',
            'leader_id.exists' => 'Kiongozi aliyechaguliwa hapatikani',
        ]);

        $validated['is_active'] = $request->has('is_active') || $request->input('is_active') ? true : false;

        // Get leader phone if leader is selected
        if (!empty($validated['leader_id'])) {
            $leader = Member::find($validated['leader_id']);
            if ($leader) {
                $validated['leader_phone'] = $leader->phone;
            }
        } else {
            $validated['leader_phone'] = null;
        }

        $jumuiya->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jumuiya imebadilishwa kikamilifu',
                'jumuiya' => $jumuiya->load('leader')
            ]);
        }

        return redirect()->route('settings.index', ['tab' => 'jumuiya'])
            ->with('success', 'Jumuiya imebadilishwa kikamilifu');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $jumuiya = Jumuiya::findOrFail($id);

        // Check if jumuiya has members
        if ($jumuiya->members()->count() > 0) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumuiya hii haiwezi kufutwa kwa sababu ina wanachama'
                ], 422);
            }
            return redirect()->route('settings.index', ['tab' => 'jumuiya'])
                ->with('error', 'Jumuiya hii haiwezi kufutwa kwa sababu ina wanachama');
        }

        $jumuiya->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jumuiya imefutwa kikamilifu'
            ]);
        }

        return redirect()->route('settings.index', ['tab' => 'jumuiya'])
            ->with('success', 'Jumuiya imefutwa kikamilifu');
    }

    /**
     * Assign members to jumuiya
     */
    public function assignMembers(Request $request, string $id)
    {
        $jumuiya = Jumuiya::findOrFail($id);

        $validated = $request->validate([
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:members,id',
        ]);

        Member::whereIn('id', $validated['member_ids'])
            ->update(['jumuiya_id' => $jumuiya->id]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Wanachama wameongezwa kwenye jumuiya kikamilifu'
            ]);
        }

        return redirect()->route('settings.index', ['tab' => 'jumuiya'])
            ->with('success', 'Wanachama wameongezwa kwenye jumuiya kikamilifu');
    }

    /**
     * Remove member from jumuiya
     */
    public function removeMember(Request $request, string $id, string $memberId)
    {
        $jumuiya = Jumuiya::findOrFail($id);
        $member = Member::findOrFail($memberId);

        if ($member->jumuiya_id == $jumuiya->id) {
            $member->update(['jumuiya_id' => null]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Mwanachama ameondolewa kwenye jumuiya'
            ]);
        }

        return redirect()->route('settings.index', ['tab' => 'jumuiya'])
            ->with('success', 'Mwanachama ameondolewa kwenye jumuiya');
    }

    /**
     * Get jumuiyas for API (used in registration form)
     */
    public function getJumuiyasApi()
    {
        $jumuiyas = Jumuiya::with('leader')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($jumuiya) {
                return [
                    'id' => $jumuiya->id,
                    'name' => $jumuiya->name,
                    'display_name' => $jumuiya->display_name,
                    'leader_name' => $jumuiya->leader ? $jumuiya->leader->full_name : null,
                    'location' => $jumuiya->location,
                ];
            });

        return response()->json($jumuiyas);
    }
}
