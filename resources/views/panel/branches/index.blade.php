@extends('layouts.app')
@section('content')
<div class="card-rgc">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-xl font-semibold">{{ __('Branches') }}</h1>
        <a class="btn-rgc w-full sm:w-auto" href="{{ route('branches.create') }}">{{ __('Create Branch') }}</a>
    </div>
    <div class="table-wrap mt-3">
        <table class="responsive-table w-full text-sm">
            <thead>
                <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('District') }}</th>
                    <th>{{ __('Region') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($branches as $branch)
                    <tr class="border-t">
                        <td>{{ $branch->name }}</td>
                        <td>{{ __(Illuminate\Support\Str::headline($branch->branch_type)) }}</td>
                        <td>{{ $branch->district->name }}</td>
                        <td>{{ $branch->region->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $branches->links() }}</div>
</div>
@endsection
