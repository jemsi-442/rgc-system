@extends('layouts.app')
@section('content')
<div class="card-rgc"><div class="flex items-center justify-between"><h1 class="text-xl font-semibold">Branches</h1><a class="btn-rgc" href="{{ route('branches.create') }}">Create Branch</a></div>
<table class="mt-3 w-full text-sm"><thead><tr><th>Name</th><th>Type</th><th>District</th><th>Region</th></tr></thead><tbody>
@foreach($branches as $branch)
<tr class="border-t"><td>{{ $branch->name }}</td><td>{{ $branch->branch_type }}</td><td>{{ $branch->district->name }}</td><td>{{ $branch->region->name }}</td></tr>
@endforeach
</tbody></table><div class="mt-3">{{ $branches->links() }}</div></div>
@endsection
