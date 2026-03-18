@extends('layouts.app')
@section('content')
<div class="card-rgc max-w-2xl"><h1 class="text-xl font-semibold">Create Branch</h1>
<form class="mt-3 grid gap-3" method="POST" action="{{ route('branches.store') }}">@csrf
<select class="rounded border px-3 py-2" name="region_id" data-region-select required><option value="">Select region</option>@foreach($regions as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach</select>
<select class="rounded border px-3 py-2" name="district_id" data-district-select required><option value="">Select district</option></select>
<input class="rounded border px-3 py-2" name="name" placeholder="Branch name" required>
<select class="rounded border px-3 py-2" name="branch_type" required>
<option value="headquarters">Headquarters</option><option value="regional">Regional</option><option value="district">District</option><option value="local">Local</option>
</select>
<button class="btn-rgc" type="submit">Save Branch</button>
</form></div>
@endsection
