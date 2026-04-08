@extends('layouts.app')
@section('content')
<div class="card-rgc max-w-3xl">
    <div class="space-y-2">
        <p class="section-kicker">{{ __('Branch finance') }}</p>
        <h1 class="text-xl font-semibold">{{ __('Record Expense') }}</h1>
        <p class="text-sm text-black/65">{{ __('Use this form when the branch has already spent the money and you need to record the purpose, amount, and date for finance reporting.') }}</p>
    </div>
    <form class="mt-5 grid gap-4" method="POST" action="{{ route('expenses.store') }}">
        @csrf
        <div>
            <label class="field-label" for="expense_date">{{ __('Expense date') }}</label>
            <input class="input-rgc" id="expense_date" type="date" name="expense_date" value="{{ old('expense_date', now()->toDateString()) }}" required>
        </div>
        <div>
            <label class="field-label" for="category">{{ __('Category') }}</label>
            <input class="input-rgc" id="category" name="category" value="{{ old('category') }}" placeholder="{{ __('Category') }}" required>
            <p class="form-hint mt-2">{{ __('Examples: Transport, Fuel, Cleaning, Welfare, Electricity.') }}</p>
        </div>
        <div>
            <label class="field-label" for="amount">{{ __('Amount') }}</label>
            <input class="input-rgc" id="amount" type="number" step="0.01" name="amount" value="{{ old('amount') }}" placeholder="{{ __('Amount') }}" required>
        </div>
        <div>
            <label class="field-label" for="description">{{ __('Details') }}</label>
            <textarea class="textarea-rgc min-h-32" id="description" name="description" placeholder="{{ __('Why was this money spent? Add the short note staff should remember later.') }}">{{ old('description') }}</textarea>
        </div>
        <div class="announcement-callout">
            <p class="font-semibold text-black">{{ __('What will be saved?') }}</p>
            <p class="mt-2 text-sm text-black/70">{{ __('The expense will be stored with the category and your details together so finance reports stay readable later.') }}</p>
        </div>
        <div class="form-actions">
            <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Record expense') }}</button>
        </div>
    </form>
</div>
@endsection
