@extends('layouts.app')
@section('content')
<div class="card-rgc">
    <div class="flex items-center justify-between"><h1 class="text-xl font-semibold">Events</h1><a class="btn-rgc" href="{{ route('events.create') }}">New</a></div>
    <table class="mt-3 w-full text-sm"><thead><tr><th class="text-left">Title</th><th class="text-left">Date</th><th></th></tr></thead><tbody>
        @foreach($events as $event)
            <tr class="border-t"><td>{{ $event->title }}</td><td>{{ $event->event_date }}</td><td class="text-right"><a href="{{ route('events.edit',$event) }}">Edit</a></td></tr>
        @endforeach
    </tbody></table>
    <div class="mt-3">{{ $events->links() }}</div>
</div>
@endsection
