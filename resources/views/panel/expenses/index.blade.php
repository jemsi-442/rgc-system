@extends('layouts.app')
@section('content')
<div class="card-rgc"><div class="flex items-center justify-between"><h1 class="text-xl font-semibold">Expenses</h1><a class="btn-rgc" href="{{ route('expenses.create') }}">Add</a></div>
<table class="mt-3 w-full text-sm"><thead><tr><th>Date</th><th>Category</th><th>Amount</th></tr></thead><tbody>
@foreach($expenses as $expense)
<tr class="border-t"><td>{{ $expense->expense_date }}</td><td>{{ $expense->category }}</td><td>{{ number_format($expense->amount,2) }}</td></tr>
@endforeach
</tbody></table><div class="mt-3">{{ $expenses->links() }}</div></div>
@endsection
