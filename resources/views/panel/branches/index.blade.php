@extends('layouts.app')

@section('content')
<div class="card-rgc">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold">{{ __('Branches') }}</h1>
            <p class="mt-1 text-sm text-black/65">{{ __('Manage branches, update records, remove inactive locations, or move into the creation page to use CSV and Excel import with hierarchy validation.') }}</p>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap branch-export-actions">
            <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('branches.template', 'xlsx') }}">{{ __('Blank Template') }}</a>
            <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('branches.template.sample', 'xlsx') }}">{{ __('Sample Template') }}</a>
            <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('branches.export', 'xlsx') }}">{{ __('Export XLSX') }}</a>
            <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('branches.export', 'csv') }}">{{ __('Export CSV') }}</a>
            <a class="btn-rgc w-full sm:w-auto" href="{{ route('branches.create') }}">{{ __('Create or Import Branches') }}</a>
        </div>
    </div>

    <form class="branch-filter-shell mt-5" method="GET" action="{{ route('branches.index') }}">
        <div class="branch-filter-header">
            <div>
                <span class="section-kicker">{{ __('Filter and Export') }}</span>
                <h2>{{ __('Focus on a region or district') }}</h2>
                <p>{{ __('Use the same filter for the table and export buttons so you can download only the branches you need.') }}</p>
            </div>
            <div class="branch-filter-actions">
                <button class="btn-rgc-alt w-full sm:w-auto" type="submit">{{ __('Apply filter') }}</button>
                <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('branches.index') }}">{{ __('Reset') }}</a>
            </div>
        </div>

        <div class="form-grid-responsive mt-4">
            <div>
                <label class="field-label" for="region_id">{{ __('Region') }}</label>
                <select class="select-rgc" id="region_id" name="region_id" data-region-select>
                    <option value="">{{ __('All regions') }}</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}" @selected(($filters['region_id'] ?? null) == $region->id)>{{ $region->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="field-label" for="district_id">{{ __('District') }}</label>
                <select
                    class="select-rgc"
                    id="district_id"
                    name="district_id"
                    data-district-select
                    data-empty-option-label="{{ __('All districts') }}"
                    data-selected-value="{{ $filters['district_id'] ?? '' }}"
                >
                    <option value="">{{ __('All districts') }}</option>
                    @if($selectedDistrict)
                        <option value="{{ $selectedDistrict->id }}" selected>{{ $selectedDistrict->name }}</option>
                    @endif
                </select>
            </div>
        </div>

        <div class="branch-filter-summary mt-4">
            <span>{{ __('Current region: :region', ['region' => $selectedRegion?->name ?? __('All regions')]) }}</span>
            <span>{{ __('Current district: :district', ['district' => $selectedDistrict?->name ?? __('All districts')]) }}</span>
        </div>

        <div class="branch-filter-export mt-4">
            <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('branches.export', ['format' => 'xlsx', 'region_id' => $filters['region_id'], 'district_id' => $filters['district_id']]) }}">{{ __('Export filtered XLSX') }}</a>
            <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('branches.export', ['format' => 'csv', 'region_id' => $filters['region_id'], 'district_id' => $filters['district_id']]) }}">{{ __('Export filtered CSV') }}</a>
        </div>
    </form>

    <div class="table-wrap mt-3">
        <table class="responsive-table w-full text-sm branch-index-table">
            <thead>
                <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('District') }}</th>
                    <th>{{ __('Region') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Contacts') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($branches as $branch)
                    <tr class="border-t">
                        <td>
                            <div class="font-semibold text-black">{{ $branch->name }}</div>
                            @if($branch->address)
                                <div class="text-xs text-black/60">{{ $branch->address }}</div>
                            @endif
                        </td>
                        <td>{{ __(Illuminate\Support\Str::headline($branch->branch_type)) }}</td>
                        <td>{{ $branch->district->name }}</td>
                        <td>{{ $branch->region->name }}</td>
                        <td>{{ __(Illuminate\Support\Str::headline($branch->status)) }}</td>
                        <td>
                            <div class="text-xs text-black/65">
                                <div>{{ $branch->phone ?: __('No phone') }}</div>
                                <div>{{ $branch->email ?: __('No email') }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="branch-row-actions">
                                <a class="btn-rgc-alt btn-rgc-xs" href="{{ route('branches.show', $branch) }}">{{ __('View') }}</a>
                                @can('update', $branch)
                                    <a class="btn-rgc-alt btn-rgc-xs" href="{{ route('branches.edit', $branch) }}">{{ __('Edit') }}</a>
                                @endcan

                                @can('delete', $branch)
                                    <form method="POST" action="{{ route('branches.destroy', $branch) }}" onsubmit="return confirm('{{ __('Delete this branch record?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-rgc-danger btn-rgc-xs" type="submit">{{ __('Delete') }}</button>
                                    </form>
                                @elseif($branch->is_headquarters)
                                    <span class="branch-locked-pill">{{ __('Headquarters locked') }}</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $branches->links() }}</div>
</div>
@endsection
