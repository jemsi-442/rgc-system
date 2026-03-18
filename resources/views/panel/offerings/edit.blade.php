@extends('layouts.app')
@section('content')
<div class="card-rgc max-w-xl"><h1 class="text-xl font-semibold">Edit Offering</h1>
<form class="mt-3 grid gap-3" method="POST" action="{{ route('offerings.update',$offering) }}">@csrf @method('PUT')
<input class="rounded border px-3 py-2" type="date" name="offering_date" value="{{ $offering->offering_date }}" required>
<input class="rounded border px-3 py-2" type="number" step="0.01" name="amount" value="{{ $offering->amount }}" required>
<textarea class="rounded border px-3 py-2" name="description">{{ $offering->description }}</textarea>
<button class="btn-rgc" type="submit">Update</button></form></div>
@endsection
