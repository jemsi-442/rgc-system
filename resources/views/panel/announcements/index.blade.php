@extends('layouts.app')
@section('title', 'Announcements - RGC')
@section('content')
<div class="card-rgc">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Announcements</h1>
        @can('create', App\Models\Announcement::class)
            <a class="btn-rgc" href="{{ route('announcements.create') }}">New</a>
        @endcan
    </div>
    <ul class="mt-3 space-y-3">
        @foreach($announcements as $a)
            <li class="rounded border p-3">
                <p class="font-semibold">{{ $a->title }}</p>
                <p class="text-sm">{{ $a->body }}</p>
            </li>
        @endforeach
    </ul>
    <div class="mt-3">{{ $announcements->links() }}</div>
</div>
@endsection
