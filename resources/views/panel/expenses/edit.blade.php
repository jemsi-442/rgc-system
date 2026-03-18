@extends('layouts.app')
@section('content')
<div class="card-rgc max-w-xl"><h1 class="text-xl font-semibold">Edit Expense</h1>
<form class="mt-3 grid gap-3" method="POST" action="{{ route('expenses.update',$expense) }}">@csrf @method('PUT')
<input class="rounded border px-3 py-2" type="date" name="expense_date" value="{{ $expense->expense_date }}" required>
<input class="rounded border px-3 py-2" name="category" value="{{ $expense->category }}" required>
<input class="rounded border px-3 py-2" type="number" step="0.01" name="amount" value="{{ $expense->amount }}" required>
<textarea class="rounded border px-3 py-2" name="description">{{ $expense->description }}</textarea>
<button class="btn-rgc" type="submit">Update</button></form></div>
@endsection
