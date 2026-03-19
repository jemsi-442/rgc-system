@extends('layouts.app')

@section('content')
<div class="form-shell max-w-4xl">
    <div class="form-panel">
        <div class="form-page-header">
            <div>
                <span class="section-kicker">{{ __('Branch Setup') }}</span>
                <h1 class="mt-4 text-2xl font-semibold">{{ __('Edit Branch') }}</h1>
                <p class="mt-2 text-sm text-black/65">{{ __('Review location, identity, and contact details carefully before updating this branch record.') }}</p>
            </div>
            <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('branches.index') }}">{{ __('Back to branches') }}</a>
        </div>

        <form class="mt-6 form-stack" method="POST" action="{{ route('branches.update', $branch) }}">
            @csrf
            @method('PUT')

            <section class="form-section">
                <div class="form-section-heading">
                    <h2>{{ __('Branch location') }}</h2>
                    <p>{{ __('Confirm the region and district carefully so the branch remains under the correct authority chain.') }}</p>
                </div>

                <div class="form-grid-responsive">
                    <div>
                        <label class="field-label" for="region_id">{{ __('Region') }}</label>
                        <select class="select-rgc" id="region_id" name="region_id" data-region-select required>
                            <option value="">{{ __('Select region') }}</option>
                            @foreach($regions as $r)
                                <option value="{{ $r->id }}" @selected(old('region_id', $branch->region_id) == $r->id)>{{ $r->name }}</option>
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
                            data-empty-option-label="{{ __('Select district') }}"
                            data-selected-value="{{ old('district_id', $branch->district_id) }}"
                            required
                        >
                            <option value="{{ old('district_id', $branch->district_id) }}">{{ $branch->district->name }}</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="form-section">
                <div class="form-section-heading">
                    <h2>{{ __('Branch identity') }}</h2>
                    <p>{{ __('Update the visible branch name, type, and operating status with a clear governance trail.') }}</p>
                </div>

                <div class="form-grid-responsive">
                    <div class="md:col-span-2">
                        <label class="field-label" for="name">{{ __('Branch name') }}</label>
                        <input class="input-rgc" id="name" name="name" value="{{ old('name', $branch->name) }}" required>
                    </div>
                    <div>
                        <label class="field-label" for="branch_type">{{ __('Branch type') }}</label>
                        <select class="select-rgc" id="branch_type" name="branch_type" required>
                            @foreach($branchTypes as $value => $label)
                                <option value="{{ $value }}" @selected(old('branch_type', $branch->branch_type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="field-label" for="status">{{ __('Status') }}</label>
                        <select class="select-rgc" id="status" name="status" required>
                            @foreach($branchStatuses as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $branch->status) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </section>

            <section class="form-section">
                <div class="form-section-heading">
                    <h2>{{ __('Branch contact details') }}</h2>
                    <p>{{ __('Keep address, phone, and email current so reporting and future communications stay clean.') }}</p>
                </div>

                <div class="form-grid-responsive">
                    <div class="md:col-span-2">
                        <label class="field-label" for="address">{{ __('Address') }}</label>
                        <input class="input-rgc" id="address" name="address" value="{{ old('address', $branch->address) }}" placeholder="{{ __('Street, ward, district, city') }}">
                    </div>
                    <div>
                        <label class="field-label" for="phone">{{ __('Phone') }}</label>
                        <input class="input-rgc" id="phone" name="phone" value="{{ old('phone', $branch->phone) }}" placeholder="{{ __('+255700000000') }}">
                    </div>
                    <div>
                        <label class="field-label" for="email">{{ __('Email') }}</label>
                        <input class="input-rgc" id="email" type="email" name="email" value="{{ old('email', $branch->email) }}" placeholder="{{ __('branch@rgc.or.tz') }}">
                    </div>
                </div>
            </section>

            <div class="form-actions">
                <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Update Branch') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
