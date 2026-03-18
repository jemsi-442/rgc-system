@extends('layouts.app')
@section('content')
<div class="card-rgc max-w-xl"><h1 class="text-xl font-semibold">Add Slider Image</h1>
<form class="mt-3 grid gap-3" method="POST" action="{{ route('sliders.store') }}" enctype="multipart/form-data">@csrf
<input class="rounded border px-3 py-2" name="title" placeholder="Title" required>
<input class="rounded border px-3 py-2" name="subtitle" placeholder="Subtitle">
<input class="rounded border px-3 py-2" type="file" name="image" accept="image/*" required>
<button class="btn-rgc" type="submit">Upload</button>
</form></div>
@endsection
