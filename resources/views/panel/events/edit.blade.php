@extends('layouts.app')
@section('content')
<div class="card-rgc max-w-2xl"><h1 class="text-xl font-semibold">Edit Event</h1>
<form class="mt-3 grid gap-3" method="POST" action="{{ route('events.update',$event) }}">@csrf @method('PUT')
<input class="rounded border px-3 py-2" name="title" value="{{ $event->title }}" required>
<textarea class="rounded border px-3 py-2" name="description">{{ $event->description }}</textarea>
<input class="rounded border px-3 py-2" type="datetime-local" name="event_date" value="{{ \Illuminate\Support\Carbon::parse($event->event_date)->format('Y-m-d\\TH:i') }}" required>
<button class="btn-rgc" type="submit">Update</button>
</form></div>
@endsection
