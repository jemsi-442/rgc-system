@extends('layouts.app')
@section('content')
<div class="card-rgc"><div class="flex items-center justify-between"><h1 class="text-xl font-semibold">Offerings</h1><a class="btn-rgc" href="{{ route('offerings.create') }}">Add</a></div>
<table class="mt-3 w-full text-sm"><thead><tr><th>Date</th><th>Amount</th><th>Description</th></tr></thead><tbody>
@foreach($offerings as $offering)
<tr class="border-t"><td>{{ $offering->offering_date }}</td><td>{{ number_format($offering->amount,2) }}</td><td>{{ $offering->description }}</td></tr>
@endforeach
</tbody></table><div class="mt-3">{{ $offerings->links() }}</div></div>
@endsection
