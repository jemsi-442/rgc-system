@extends('layouts.app')
@section('content')
<div class="card-rgc max-w-xl">
    <h1 class="text-xl font-semibold">{{ __('Edit Expense') }}</h1>
    <form class="mt-4 grid gap-4" method="POST" action="{{ route('expenses.update', $expense) }}">
        @csrf
        @method('PUT')
        <div>
            <label class="field-label" for="expense_date">{{ __('Expense date') }}</label>
            <input class="input-rgc" id="expense_date" type="date" name="expense_date" value="{{ old('expense_date', $expense->expense_date) }}" required>
        </div>
        <div>
            <label class="field-label" for="category">{{ __('Category') }}</label>
            <input class="input-rgc" id="category" name="category" value="{{ old('category', $expense->category) }}" required>
        </div>
        <div>
            <label class="field-label" for="amount">{{ __('Amount') }}</label>
            <input class="input-rgc" id="amount" type="number" step="0.01" name="amount" value="{{ old('amount', $expense->amount) }}" required>
        </div>
        <div>
            <label class="field-label" for="description">{{ __('Description') }}</label>
            <textarea class="textarea-rgc min-h-32" id="description" name="description">{{ old('description', $expense->description) }}</textarea>
        </div>
        <div class="form-actions">
            <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Update') }}</button>
        </div>
    </form>
</div>
@endsection
