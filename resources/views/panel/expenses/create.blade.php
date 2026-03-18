@extends('layouts.app')
@section('content')
<div class="card-rgc max-w-xl">
    <h1 class="text-xl font-semibold">{{ __('Record Expense') }}</h1>
    <form class="mt-4 grid gap-4" method="POST" action="{{ route('expenses.store') }}">
        @csrf
        <div>
            <label class="field-label" for="expense_date">{{ __('Expense date') }}</label>
            <input class="input-rgc" id="expense_date" type="date" name="expense_date" value="{{ old('expense_date') }}" required>
        </div>
        <div>
            <label class="field-label" for="category">{{ __('Category') }}</label>
            <input class="input-rgc" id="category" name="category" value="{{ old('category') }}" placeholder="{{ __('Category') }}" required>
        </div>
        <div>
            <label class="field-label" for="amount">{{ __('Amount') }}</label>
            <input class="input-rgc" id="amount" type="number" step="0.01" name="amount" value="{{ old('amount') }}" placeholder="{{ __('Amount') }}" required>
        </div>
        <div>
            <label class="field-label" for="description">{{ __('Description') }}</label>
            <textarea class="textarea-rgc min-h-32" id="description" name="description" placeholder="{{ __('Description') }}">{{ old('description') }}</textarea>
        </div>
        <div class="form-actions">
            <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Save') }}</button>
        </div>
    </form>
</div>
@endsection
