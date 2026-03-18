@extends('layouts.app')
@section('content')
<div class="card-rgc max-w-2xl"><h1 class="text-xl font-semibold">Create Event</h1>
<form class="mt-3 grid gap-3" method="POST" action="{{ route('events.store') }}">@csrf
<input class="rounded border px-3 py-2" name="title" placeholder="Title" required>
<textarea class="rounded border px-3 py-2" name="description" placeholder="Description"></textarea>
<input class="rounded border px-3 py-2" type="datetime-local" name="event_date" required>
<button class="btn-rgc" type="submit">Save</button>
</form></div>
@endsection
