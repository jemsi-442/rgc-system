@extends('layouts.app')
@section('content')
<div class="card-rgc max-w-2xl">
    <h1 class="text-xl font-semibold">Edit Announcement</h1>
    <form class="mt-3 grid gap-3" method="POST" action="{{ route('announcements.update',$announcement) }}">@csrf @method('PUT')
        <input class="rounded border px-3 py-2" name="title" value="{{ $announcement->title }}" required>
        <textarea class="rounded border px-3 py-2" name="body" rows="5" required>{{ $announcement->body }}</textarea>
        <button class="btn-rgc" type="submit">Save</button>
    </form>
</div>
@endsection
