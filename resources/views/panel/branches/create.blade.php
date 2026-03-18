@extends('layouts.app')
@section('content')
<div class="form-shell max-w-3xl">
    <div class="form-panel">
        <div class="form-page-header">
            <div>
                <span class="section-kicker">{{ __('Branch Setup') }}</span>
                <h1 class="mt-4 text-2xl font-semibold">{{ __('Create Branch') }}</h1>
                <p class="mt-2 text-sm text-black/65">{{ __('The location hierarchy and branch type stay easy to review on mobile before you submit.') }}</p>
            </div>
            <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('branches.index') }}">{{ __('Back to branches') }}</a>
        </div>

        <form class="mt-6 form-stack" method="POST" action="{{ route('branches.store') }}">
            @csrf
            <section class="form-section">
                <div class="form-section-heading">
                    <h2>{{ __('Branch location') }}</h2>
                    <p>{{ __('Choose the correct region and district first so the branch is stored under the right governance scope.') }}</p>
                </div>

                <div class="form-grid-responsive">
                    <div>
                        <label class="field-label" for="region_id">{{ __('Region') }}</label>
                        <select class="select-rgc" id="region_id" name="region_id" data-region-select required>
                            <option value="">{{ __('Select region') }}</option>
                            @foreach($regions as $r)
                                <option value="{{ $r->id }}" @selected(old('region_id') == $r->id)>{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="field-label" for="district_id">{{ __('District') }}</label>
                        <select class="select-rgc" id="district_id" name="district_id" data-district-select data-empty-option-label="{{ __('Select district') }}" required>
                            <option value="">{{ __('Select district') }}</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="form-section">
                <div class="form-section-heading">
                    <h2>{{ __('Branch identity') }}</h2>
                    <p>{{ __('Name the branch clearly and choose the branch type before saving.') }}</p>
                </div>

                <div class="form-grid-responsive">
                    <div class="md:col-span-2">
                        <label class="field-label" for="name">{{ __('Branch name') }}</label>
                        <input class="input-rgc" id="name" name="name" value="{{ old('name') }}" placeholder="{{ __('Branch name') }}" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="field-label" for="branch_type">{{ __('Branch type') }}</label>
                        <select class="select-rgc" id="branch_type" name="branch_type" required>
                            <option value="headquarters">{{ __('Headquarters') }}</option>
                            <option value="regional">{{ __('Regional') }}</option>
                            <option value="district">{{ __('District') }}</option>
                            <option value="local">{{ __('Local') }}</option>
                        </select>
                    </div>
                </div>
            </section>

            <div class="form-actions">
                <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Save Branch') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
