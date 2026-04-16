@extends('layouts.app')

@section('title', __('My Contact Details') . ' - RGC')

@section('content')
<div class="form-shell max-w-3xl">
    <div class="form-panel">
        <span class="section-kicker">{{ __('My Account') }}</span>
        <h1 class="mt-4 text-2xl font-semibold">{{ __('My contact details') }}</h1>
        <p class="mt-2 text-sm text-black/65">{{ __('Keep your name, email, and phone current so branch leaders can reach you when they need to follow up, share updates, or support you.') }}</p>

        <form class="mt-6 form-stack" method="POST" action="{{ route('account.profile.update') }}">
            @csrf
            @method('PUT')

            <section class="form-section">
                <div class="form-section-heading">
                    <h2>{{ __('Contact information') }}</h2>
                    <p>{{ __('This stays simple on mobile so you can quickly refresh your own contact details from any device.') }}</p>
                </div>

                <div class="form-grid-responsive">
                    <div class="md:col-span-2">
                        <label class="field-label" for="name">{{ __('Full name') }}</label>
                        <input class="input-rgc" id="name" type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                    </div>
                    <div>
                        <label class="field-label" for="email">{{ __('Email address') }}</label>
                        <input class="input-rgc" id="email" type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                    </div>
                    <div>
                        <label class="field-label" for="phone">{{ __('Phone number') }}</label>
                        <input class="input-rgc" id="phone" type="tel" name="phone" inputmode="tel" autocomplete="tel" value="{{ old('phone', auth()->user()->phone) }}" placeholder="{{ __('Start with 06, 07, or 255') }}" required>
                    </div>
                </div>
            </section>

            <div class="form-actions pt-2">
                <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Save contact details') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
