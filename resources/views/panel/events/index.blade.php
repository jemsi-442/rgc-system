@extends('layouts.app')
@section('content')
<div class="card-rgc">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-xl font-semibold">{{ __('Events') }}</h1>
        <a class="btn-rgc w-full sm:w-auto" href="{{ route('events.create') }}">{{ __('New') }}</a>
    </div>
    <div class="table-wrap mt-3">
        <table class="responsive-table w-full text-sm">
            <thead>
                <tr>
                    <th class="text-left">{{ __('Title') }}</th>
                    <th class="text-left">{{ __('Date') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                    <tr class="border-t">
                        <td>{{ $event->title }}</td>
                        <td>{{ $event->event_date }}</td>
                        <td class="text-right"><a href="{{ route('events.edit', $event) }}">{{ __('Edit') }}</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $events->links() }}</div>
</div>
@endsection
