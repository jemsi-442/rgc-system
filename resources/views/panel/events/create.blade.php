@extends('layouts.app')
@section('content')
<div class="card-rgc max-w-2xl">
    <h1 class="text-xl font-semibold">{{ __('Create Event') }}</h1>
    <form class="mt-4 grid gap-4" method="POST" action="{{ route('events.store') }}">
        @csrf
        <div>
            <label class="field-label" for="title">{{ __('Title') }}</label>
            <input class="input-rgc" id="title" name="title" value="{{ old('title') }}" placeholder="{{ __('Title') }}" required>
        </div>
        <div>
            <label class="field-label" for="description">{{ __('Description') }}</label>
            <textarea class="textarea-rgc min-h-32" id="description" name="description" placeholder="{{ __('Description') }}">{{ old('description') }}</textarea>
        </div>
        <div>
            <label class="field-label" for="event_date">{{ __('Event date and time') }}</label>
            <input class="input-rgc" id="event_date" type="datetime-local" name="event_date" value="{{ old('event_date') }}" required>
        </div>
        <div class="form-actions">
            <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Save') }}</button>
        </div>
    </form>
</div>
@endsection
