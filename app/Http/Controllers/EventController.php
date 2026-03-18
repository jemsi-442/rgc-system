<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $events = Event::query()
            ->where('church_id', $user->effectiveBranchId())
            ->orderByDesc('event_date')
            ->paginate(20);

        return view('panel.events.index', compact('events'));
    }

    public function create()
    {
        return view('panel.events.create');
    }

    public function store(StoreEventRequest $request)
    {
        $user = $request->user();

        Event::query()->create([
            'title' => $request->string('title')->toString(),
            'description' => $request->input('description'),
            'event_date' => $request->input('event_date'),
            'region_id' => $user->region_id,
            'district_id' => $user->district_id,
            'church_id' => $user->effectiveBranchId(),
            'created_by' => $user->id,
        ]);

        return redirect()->route('events.index')->with('status', __('Event created.'));
    }

    public function edit(Event $event)
    {
        $this->authorize('update', $event);

        return view('panel.events.edit', compact('event'));
    }

    public function update(StoreEventRequest $request, Event $event)
    {
        $this->authorize('update', $event);
        $event->update($request->validated());

        return redirect()->route('events.index')->with('status', __('Event updated.'));
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        $event->delete();

        return back()->with('status', __('Event deleted.'));
    }
}
