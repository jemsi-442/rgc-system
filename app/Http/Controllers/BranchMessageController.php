<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBranchMessageRequest;
use App\Models\BranchMessage;

class BranchMessageController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $branchId = $user->effectiveBranchId();

        $messages = BranchMessage::query()
            ->with('user')
            ->where('church_id', $branchId)
            ->latest()
            ->paginate(30);

        return view('panel.messages.index', compact('messages'));
    }

    public function store(StoreBranchMessageRequest $request)
    {
        $user = $request->user();

        BranchMessage::query()->create([
            'church_id' => $user->effectiveBranchId(),
            'user_id' => $user->id,
            'message' => $request->string('message')->toString(),
        ]);

        return back()->with('status', 'Message sent.');
    }

    public function feed()
    {
        $user = auth()->user();

        $messages = BranchMessage::query()
            ->with('user:id,name')
            ->where('church_id', $user->effectiveBranchId())
            ->latest()
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        return response()->json($messages);
    }
}
