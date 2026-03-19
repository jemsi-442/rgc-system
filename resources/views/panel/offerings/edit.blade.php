@extends('layouts.app')
@section('content')
<div class="card-rgc max-w-xl">
    <h1 class="text-xl font-semibold">{{ __('Edit Offering') }}</h1>
    <form class="mt-4 grid gap-4" method="POST" action="{{ route('offerings.update', $offering) }}">
        @csrf
        @method('PUT')
        <div>
            <label class="field-label" for="offering_date">{{ __('Offering date') }}</label>
            <input class="input-rgc" id="offering_date" type="date" name="offering_date" value="{{ old('offering_date', $offering->offering_date?->format('Y-m-d')) }}" required>
        </div>
        <div>
            <label class="field-label" for="amount">{{ __('Amount') }}</label>
            <input class="input-rgc" id="amount" type="number" step="0.01" name="amount" value="{{ old('amount', $offering->amount) }}" required>
        </div>
        <div>
            <label class="field-label" for="description">{{ __('Description') }}</label>
            <textarea class="textarea-rgc min-h-32" id="description" name="description">{{ old('description', $offering->description) }}</textarea>
        </div>
        <div class="form-actions">
            <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Update') }}</button>
        </div>
    </form>
</div>
@endsection
