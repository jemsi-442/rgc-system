@extends('layouts.app')

@section('title', __('Register') . ' - RGC')

@section('content')
<div class="auth-grid" data-csrf-refresh-on-restore data-csrf-refresh-endpoint="{{ route('csrf.refresh') }}">
    <aside class="auth-aside">
        <span class="section-kicker !border-white/10 !bg-white/10 !text-rgc-yellow">{{ __('Member Onboarding') }}</span>
        <h2 class="mt-5">{{ __('Join through your church location.') }}</h2>
        <p class="mt-5 max-w-xl text-sm leading-7 text-white/80">
            {{ __('Registration follows your church location: region, district, and branch. This helps every member join the right place from the start.') }}
        </p>

        <ul class="auth-list mt-8 text-sm text-white/82">
            <li><strong class="block text-white">{{ __('Step 1') }}</strong> {{ __('Choose your region from the church location list.') }}</li>
            <li><strong class="block text-white">{{ __('Step 2') }}</strong> {{ __('Select the district that belongs to that region.') }}</li>
            <li><strong class="block text-white">{{ __('Step 3') }}</strong> {{ __('Pick your branch from the available church locations.') }}</li>
        </ul>
    </aside>

    <section class="form-shell">
        <div class="form-panel">
            <span class="section-kicker">{{ __('Registration') }}</span>
            <h1 class="mt-5 font-[family-name:var(--font-display)] text-4xl leading-none">{{ __('Create your church account') }}</h1>
            <p class="mt-4 form-hint">{{ __('Each new account is connected to the right church location before access begins.') }}</p>

            <form class="mt-8 grid gap-5 md:grid-cols-2" method="POST" action="{{ route('register.store') }}" data-auth-form>
                @csrf

                <div class="md:col-span-2">
                    <label class="field-label" for="name">{{ __('Full name') }}</label>
                    <input class="input-rgc" id="name" name="name" value="{{ old('name') }}" placeholder="{{ __('Enter your full name') }}" required>
                </div>

                <div class="md:col-span-2">
                    <label class="field-label" for="email">{{ __('Email address') }}</label>
                    <input class="input-rgc" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="{{ __('name@rgc.or.tz') }}" required>
                </div>

                <div class="md:col-span-2">
                    <label class="field-label" for="phone">{{ __('Phone number') }}</label>
                    <input class="input-rgc" id="phone" name="phone" type="tel" value="{{ old('phone') }}" placeholder="{{ __('Start with 06, 07, or 255') }}" inputmode="tel" autocomplete="tel" required>
                    <p class="mt-2 form-hint">{{ __('Use a reachable Tanzania number. Start with 06, 07, or 255.') }}</p>
                </div>

                <div>
                    <label class="field-label" for="region_id">{{ __('Region') }}</label>
                    <select class="select-rgc" id="region_id" name="region_id" data-region-select required>
                        <option value="">{{ __('Select region') }}</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" @selected(old('region_id') == $region->id)>{{ $region->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div
                    data-district-field
                    @class(['hidden' => ! old('region_id')])
                >
                    <label class="field-label" for="district_id">{{ __('District') }}</label>
                    <select
                        class="select-rgc"
                        id="district_id"
                        name="district_id"
                        data-district-select
                        data-empty-option-label="{{ __('Select district') }}"
                        data-selected-value="{{ old('district_id') }}"
                        @disabled(! old('region_id'))
                        required
                    >
                        <option value="">{{ __('Select district') }}</option>
                    </select>
                    <p class="mt-2 form-hint">{{ __('District choices appear after selecting a region.') }}</p>
                </div>

                <div
                    class="md:col-span-2"
                    data-branch-field
                    @class(['hidden' => ! old('district_id')])
                >
                    <label class="field-label" for="branch_id">{{ __('Branch') }}</label>
                    <select
                        class="select-rgc"
                        id="branch_id"
                        name="branch_id"
                        data-branch-select
                        data-empty-option-label="{{ __('Select branch') }}"
                        data-selected-value="{{ old('branch_id') }}"
                        @disabled(! old('district_id'))
                        required
                    >
                        <option value="">{{ __('Select branch') }}</option>
                    </select>
                    <p class="mt-2 form-hint">{{ __('Branch choices appear after selecting a district.') }}</p>
                </div>

                <div>
                    <label class="field-label" for="password">{{ __('Password') }}</label>
                    <input class="input-rgc" id="password" name="password" type="password" placeholder="{{ __('Create password') }}" required>
                </div>

                <div>
                    <label class="field-label" for="password_confirmation">{{ __('Confirm password') }}</label>
                    <input class="input-rgc" id="password_confirmation" name="password_confirmation" type="password" placeholder="{{ __('Repeat password') }}" required>
                </div>

                <div class="md:col-span-2 form-actions pt-2">
                    <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Create account') }}</button>
                    <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('login') }}">{{ __('Already registered') }}</a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
