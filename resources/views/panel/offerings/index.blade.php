@extends('layouts.app')
@section('content')
<div class="card-rgc">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-xl font-semibold">{{ __('Offerings') }}</h1>
        <a class="btn-rgc w-full sm:w-auto" href="{{ route('offerings.create') }}">{{ __('Add') }}</a>
    </div>
    <div class="table-wrap mt-3">
        <table class="responsive-table w-full text-sm">
            <thead>
                <tr>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Description') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($offerings as $offering)
                    <tr class="border-t">
                        <td>{{ $offering->offering_date }}</td>
                        <td>{{ number_format($offering->amount, 2) }}</td>
                        <td>{{ $offering->description }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $offerings->links() }}</div>
</div>
@endsection
