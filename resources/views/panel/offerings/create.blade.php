@extends('layouts.app')
@section('content')
<div class="card-rgc max-w-xl"><h1 class="text-xl font-semibold">Record Offering</h1>
<form class="mt-3 grid gap-3" method="POST" action="{{ route('offerings.store') }}">@csrf
<input class="rounded border px-3 py-2" type="date" name="offering_date" required>
<input class="rounded border px-3 py-2" type="number" step="0.01" name="amount" placeholder="Amount" required>
<textarea class="rounded border px-3 py-2" name="description" placeholder="Description"></textarea>
<button class="btn-rgc" type="submit">Save</button></form></div>
@endsection
