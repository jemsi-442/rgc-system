@extends('layouts.app')
@section('content')
<div class="card-rgc">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-xl font-semibold">{{ __('Expenses') }}</h1>
        <a class="btn-rgc w-full sm:w-auto" href="{{ route('expenses.create') }}">{{ __('Add') }}</a>
    </div>
    <div class="table-wrap mt-3">
        <table class="responsive-table w-full text-sm">
            <thead>
                <tr>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Category') }}</th>
                    <th>{{ __('Amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $expense)
                    <tr class="border-t">
                        <td>{{ $expense->expense_date }}</td>
                        <td>{{ $expense->category }}</td>
                        <td>{{ number_format($expense->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $expenses->links() }}</div>
</div>
@endsection
