@extends('layouts.app')
@section('content')
<div class="card-rgc">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="section-kicker">{{ __('Ledger') }}</p>
            <h1 class="text-xl font-semibold">{{ __('Expenses') }}</h1>
            <p class="mt-2 text-sm text-black/65">{{ __('Branch spending records are listed here so finance staff can review what was spent, when, and why.') }}</p>
        </div>
        <a class="btn-rgc w-full sm:w-auto" href="{{ route('expenses.create') }}">{{ __('Record expense') }}</a>
    </div>
    <div class="table-wrap mt-3">
        <table class="responsive-table w-full text-sm">
            <thead>
                <tr>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Category') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Details') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr class="border-t">
                        <td>{{ optional($expense->expense_date)->format('Y-m-d') ?? $expense->expense_date }}</td>
                        <td>{{ $expense->category }}</td>
                        <td>TZS {{ number_format((float) $expense->amount, 2) }}</td>
                        <td>{{ $expense->description_body ?: __('No extra details') }}</td>
                    </tr>
                @empty
                    <tr class="border-t">
                        <td colspan="4" class="py-6 text-center text-black/60">{{ __('No expenses recorded yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $expenses->links() }}</div>
</div>
@endsection
