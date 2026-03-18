@extends('layouts.app')
@section('content')
<div class="card-rgc"><div class="flex items-center justify-between"><h1 class="text-xl font-semibold">Homepage Sliders</h1><a class="btn-rgc" href="{{ route('sliders.create') }}">Add Slide</a></div>
<ul class="mt-3 space-y-2">@foreach($sliders as $slider)<li class="rounded border p-3 flex items-center justify-between"><span>{{ $slider->title }}</span><form method="POST" action="{{ route('sliders.destroy',$slider) }}">@csrf @method('DELETE')<button class="text-red-700">Delete</button></form></li>@endforeach</ul>
<div class="mt-3">{{ $sliders->links() }}</div></div>
@endsection
