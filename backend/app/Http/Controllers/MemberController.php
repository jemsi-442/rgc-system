<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    // GET /members?church_id=?&search=?
    public function index(Request $request)
    {
        $query = Member::with('church');

        if ($request->church_id) {
            $query->where('church_id', $request->church_id);
        }

        if ($request->search) {
            $q = $request->search;
            $query->where(function ($x) use ($q) {
                $x->where('first_name', 'LIKE', "%$q%")
                  ->orWhere('last_name', 'LIKE', "%$q%")
                  ->orWhere('phone', 'LIKE', "%$q%")
                  ->orWhere('email', 'LIKE', "%$q%");
            });
        }

        return response()->json($query->orderBy('first_name')->get());
    }

    // POST /members
    public function store(Request $request)
    {
        $validated = $request->validate([
            'church_id' => 'nullable|exists:churches,id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'dob' => 'nullable|date',
            'phone' => 'nullable|string',
            'email' => 'nullable|email|unique:members,email',
            'gender' => 'nullable|string',
            'marital_status' => 'nullable|string',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $member = Member::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $member
        ], 201);
    }

    // GET /members/{id}
    public function show($id)
    {
        $member = Member::with('church')->find($id);

        if (!$member) {
            return response()->json(['message' => 'Member not found'], 404);
        }

        return response()->json($member);
    }

    // PUT /members/{id}
    public function update(Request $request, $id)
    {
        $member = Member::find($id);

        if (!$member) {
            return response()->json(['message' => 'Member not found'], 404);
        }

        $validated = $request->validate([
            'church_id' => 'nullable|exists:churches,id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'dob' => 'nullable|date',
            'phone' => 'nullable|string',
            'email' => "nullable|email|unique:members,email,$id",
            'gender' => 'nullable|string',
            'marital_status' => 'nullable|string',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $member->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $member
        ]);
    }

    // DELETE /members/{id}
    public function destroy($id)
    {
        $member = Member::find($id);

        if (!$member) {
            return response()->json(['message' => 'Member not found'], 404);
        }

        $member->delete();

        return response()->json(['message' => 'Member deleted']);
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'members' => 'required|array|min:1',
            'members.*.church_id' => 'nullable|exists:churches,id',
            'members.*.first_name' => 'required|string',
            'members.*.last_name' => 'required|string',
            'members.*.dob' => 'nullable|date',
            'members.*.phone' => 'nullable|string',
            'members.*.email' => 'nullable|email',
            'members.*.gender' => 'nullable|string',
            'members.*.marital_status' => 'nullable|string',
            'members.*.address' => 'nullable|string',
            'members.*.notes' => 'nullable|string',
        ]);

        $rows = [];
        $now = now();

        foreach ($validated['members'] as $member) {
            $rows[] = [
                'church_id' => $member['church_id'] ?? null,
                'first_name' => $member['first_name'],
                'last_name' => $member['last_name'],
                'dob' => $member['dob'] ?? null,
                'phone' => $member['phone'] ?? null,
                'email' => $member['email'] ?? null,
                'gender' => $member['gender'] ?? null,
                'marital_status' => $member['marital_status'] ?? null,
                'address' => $member['address'] ?? null,
                'notes' => $member['notes'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::transaction(function () use ($rows) {
            Member::insert($rows);
        });

        return response()->json([
            'status' => 'success',
            'count' => count($rows),
            'message' => 'Members imported successfully',
        ], 201);
    }
}
