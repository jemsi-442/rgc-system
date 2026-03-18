@extends('layouts.app')
@section('content')
<div class="card-rgc max-w-2xl"><h1 class="text-xl font-semibold">Edit Branch</h1>
<form class="mt-3 grid gap-3" method="POST" action="{{ route('branches.update',$branch) }}">@csrf @method('PUT')
<select class="rounded border px-3 py-2" name="region_id" data-region-select required><option value="">Select region</option>@foreach($regions as $r)<option value="{{ $r->id }}" @selected($branch->region_id==$r->id)>{{ $r->name }}</option>@endforeach</select>
<select class="rounded border px-3 py-2" name="district_id" data-district-select required><option value="{{ $branch->district_id }}">{{ $branch->district->name }}</option></select>
<input class="rounded border px-3 py-2" name="name" value="{{ $branch->name }}" required>
<select class="rounded border px-3 py-2" name="branch_type" required>
<option value="headquarters" @selected($branch->branch_type==='headquarters')>Headquarters</option><option value="regional" @selected($branch->branch_type==='regional')>Regional</option><option value="district" @selected($branch->branch_type==='district')>District</option><option value="local" @selected($branch->branch_type==='local')>Local</option>
</select>
<button class="btn-rgc" type="submit">Update Branch</button>
</form></div>
@endsection
