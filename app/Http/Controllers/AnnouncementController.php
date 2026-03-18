<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnnouncementRequest;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $announcements = Announcement::query()
            ->with('creator')
            ->where('church_id', $user->effectiveBranchId())
            ->latest()
            ->paginate(20);

        return view('panel.announcements.index', compact('announcements'));
    }

    public function create()
    {
        $this->authorize('create', Announcement::class);

        return view('panel.announcements.create');
    }

    public function store(StoreAnnouncementRequest $request)
    {
        $user = $request->user();

        Announcement::query()->create([
            'title' => $request->string('title')->toString(),
            'body' => $request->string('body')->toString(),
            'region_id' => $user->region_id,
            'district_id' => $user->district_id,
            'church_id' => $user->effectiveBranchId(),
            'created_by' => $user->id,
        ]);

        return redirect()->route('announcements.index')->with('status', 'Announcement posted.');
    }

    public function edit(Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        return view('panel.announcements.edit', compact('announcement'));
    }

    public function update(StoreAnnouncementRequest $request, Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        $announcement->update([
            'title' => $request->string('title')->toString(),
            'body' => $request->string('body')->toString(),
        ]);

        return redirect()->route('announcements.index')->with('status', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorize('delete', $announcement);

        $announcement->delete();

        return back()->with('status', 'Announcement deleted.');
    }
}
