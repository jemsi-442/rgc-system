@extends('layouts.app')
@section('content')
<div class="card-rgc">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-xl font-semibold">{{ __('Homepage Sliders') }}</h1>
        <a class="btn-rgc w-full sm:w-auto" href="{{ route('sliders.create') }}">{{ __('Add Slide') }}</a>
    </div>
    <ul class="mt-3 space-y-3">
        @foreach($sliders as $slider)
            <li class="slider-admin-item rounded border p-3">
                <img class="slider-admin-preview" src="{{ route('slides.show', $slider) }}" alt="{{ $slider->title }}">
                <div class="min-w-0 flex-1">
                    <p class="font-semibold">{{ $slider->title }}</p>
                    @if($slider->subtitle)
                        <p class="mt-1 text-sm text-black/65">{{ $slider->subtitle }}</p>
                    @endif
                </div>
                <form method="POST" action="{{ route('sliders.destroy', $slider) }}">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-700" type="submit">{{ __('Delete') }}</button>
                </form>
            </li>
        @endforeach
    </ul>
    <div class="mt-3">{{ $sliders->links() }}</div>
</div>
@endsection
