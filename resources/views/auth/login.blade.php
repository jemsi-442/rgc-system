@extends('layouts.app')

@section('title', __('Login') . ' - RGC')

@section('content')
<div class="auth-grid" data-csrf-refresh-on-restore>
    <aside class="auth-aside">
        <div class="auth-brand-lockup">
            <img src="{{ asset('images/rgc_logo.png') }}" alt="{{ __('RGC Logo') }}" class="auth-brand-logo">
            <div class="auth-brand-text">
                <span class="auth-brand-name">{{ __('Redeemed Gospel Church') }}</span>
                <span class="auth-brand-subtitle">{{ __('Inc. Tanzania Platform') }}</span>
            </div>
        </div>

        <span class="section-kicker !border-white/10 !bg-white/10 !text-rgc-yellow">{{ __('Sign In') }}</span>
        <h2 class="mt-5">{{ __('Enter your church workspace.') }}</h2>
        <p class="mt-5 max-w-xl text-sm leading-7 text-white/80">
            {{ __('Sign in with your registered account to continue with church updates, communication, and giving services.') }}
        </p>

        <ul class="auth-list mt-8 text-sm text-white/82">
            <li><strong class="block text-white">{{ __('Use your account') }}</strong> {{ __('Enter with the email and password that were registered for you.') }}</li>
            <li><strong class="block text-white">{{ __('Continue your journey') }}</strong> {{ __('Open updates, giving, and branch conversation from one place.') }}</li>
            <li><strong class="block text-white">{{ __('Church location ready') }}</strong> {{ __('Church regions, districts, and branches are already prepared for RGC Tanzania.') }}</li>
        </ul>
    </aside>

    <section class="form-shell">
        <div class="form-panel">
            <span class="section-kicker">{{ __('Account Access') }}</span>
            <h1 class="mt-5 font-[family-name:var(--font-display)] text-4xl leading-none">{{ __('Sign in to your RGC workspace') }}</h1>
            <p class="mt-4 form-hint">{{ __('Use your registered email and password to enter the platform.') }}</p>



            <form class="mt-8 space-y-5" method="POST" action="{{ route('login.attempt') }}">
                @csrf

                <div>
                    <label class="field-label" for="email">{{ __('Email address') }}</label>
                    <input class="input-rgc" id="email" type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('name@rgc.or.tz') }}" required>
                </div>

                <div>
                    <label class="field-label" for="password">{{ __('Password') }}</label>
                    <input class="input-rgc" id="password" type="password" name="password" placeholder="{{ __('Enter your password') }}" required>
                </div>

                <label class="flex items-center gap-3 text-sm text-black/70">
                    <input class="h-4 w-4 rounded border-black/20 text-rgc-red focus:ring-rgc-red" type="checkbox" name="remember">
                    {{ __('Keep me signed in on this device') }}
                </label>

                <div class="form-actions pt-2">
                    <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Login') }}</button>
                    <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('register') }}">{{ __('Create member account') }}</a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
