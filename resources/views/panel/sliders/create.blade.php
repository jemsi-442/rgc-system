@extends('layouts.app')
@section('content')
<div class="card-rgc max-w-xl">
    <h1 class="text-xl font-semibold">{{ __('Add Slider Image') }}</h1>
    <p class="mt-2 text-sm text-black/65">{{ __('Use a wide, high-quality image so the homepage slider stays sharp on mobile and desktop.') }}</p>
    <form class="mt-4 grid gap-4" method="POST" action="{{ route('sliders.store') }}" enctype="multipart/form-data">
        @csrf
        <div>
            <label class="field-label" for="title">{{ __('Title') }}</label>
            <input class="input-rgc" id="title" name="title" value="{{ old('title') }}" placeholder="{{ __('Title') }}" required>
        </div>
        <div>
            <label class="field-label" for="subtitle">{{ __('Subtitle') }}</label>
            <input class="input-rgc" id="subtitle" name="subtitle" value="{{ old('subtitle') }}" placeholder="{{ __('Subtitle') }}">
        </div>
        <div>
            <label class="field-label" for="image">{{ __('Slider image') }}</label>
            <input class="input-rgc" id="image" type="file" name="image" accept="image/*" required>
        </div>
        <div class="form-actions">
            <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Upload') }}</button>
        </div>
    </form>
</div>
@endsection
