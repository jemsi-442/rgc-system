@extends('layouts.app')
@section('content')
<div class="card-rgc max-w-2xl">
    <h1 class="text-xl font-semibold">New Announcement</h1>
    <form class="mt-3 grid gap-3" method="POST" action="{{ route('announcements.store') }}">@csrf
        <input class="rounded border px-3 py-2" name="title" placeholder="Title" required>
        <textarea class="rounded border px-3 py-2" name="body" rows="5" placeholder="Body" required></textarea>
        <button class="btn-rgc" type="submit">Publish</button>
    </form>
</div>
@endsection
