@extends('layouts.app')

@section('title', __('RGC - Redeemed Gospel Church Inc. Tanzania'))

@section('content')
<div class="home-stack">
<section class="hero-grid items-stretch home-hero">
    <div class="hero-panel">
        <div class="hero-copy">
            <div class="hero-brand-lockup">
                <img src="{{ asset('images/rgc_logo.png') }}" alt="{{ __('RGC Logo') }}" class="hero-brand-logo">
                <div class="hero-brand-text">
                    <span class="hero-brand-name">{{ __('Redeemed Gospel Church') }}</span>
                    <span class="hero-brand-subtitle">{{ __('Inc. Tanzania') }}</span>
                </div>
            </div>

            <span class="section-kicker">{{ __('Official Church Platform') }}</span>
            <h1 class="hero-title mt-5">{{ __('Redeemed Gospel Church') }} <em>{{ __('Inc. Tanzania') }}</em></h1>
            <p class="mt-5 text-base leading-7 md:text-lg">
                {{ __('An official digital platform for church updates, member access, giving, and shared information across Tanzania Mainland and Zanzibar.') }}
            </p>

            <div class="hero-actions mt-8">
                <a class="btn-rgc" href="{{ route('login') }}">{{ __('Enter Dashboard') }}</a>
                <a class="btn-rgc-alt" href="{{ route('register') }}">{{ __('Join Your Branch') }}</a>
            </div>

            <div class="hero-metrics">
                <div class="metric-tile">
                    <strong>31</strong>
                    <span>{{ __('Regions Served') }}</span>
                </div>
                <div class="metric-tile">
                    <strong>228</strong>
                    <span>{{ __('District Coverage') }}</span>
                </div>
                <div class="metric-tile">
                    <strong>{{ __('One') }}</strong>
                    <span>{{ __('Church Platform') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="slider-shell" data-hero-slider>
        @if($sliders->isNotEmpty())
            <div class="slider-track min-h-[22rem]" data-slider-track>
                @foreach($sliders as $slide)
                    <article class="slide-item {{ $loop->first ? 'is-active' : '' }}" data-slide>
                        <div class="slide-media">
                            <img src="{{ route('slides.show', $slide) }}" alt="{{ $slide->title }}" loading="{{ $loop->first ? 'eager' : 'lazy' }}" decoding="async">
                            <div class="slide-overlay"></div>
                        </div>
                        <div class="slide-copy">
                            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-rgc-yellow">{{ __('RGC Feature') }}</p>
                            <h2 class="mt-3">{{ $slide->title }}</h2>
                            <p class="mt-3 max-w-md text-sm text-white/80">{{ $slide->subtitle ?: __('National church communication, visibility, and branch connectivity from one central platform.') }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
            @if($sliders->count() > 1)
                <div class="slide-dots">
                    @foreach($sliders as $slide)
                        <button class="slide-dot {{ $loop->first ? 'is-active' : '' }}" type="button" data-slide-dot aria-label="{{ __('Show slide :number', ['number' => $loop->iteration]) }}"></button>
                    @endforeach
                </div>
            @endif
        @else
            <div class="flex min-h-[22rem] flex-col justify-end p-6 text-rgc-white">
                <span class="section-kicker !border-white/10 !bg-white/10 !text-rgc-yellow">{{ __('Homepage Slider') }}</span>
                <h2 class="mt-5 font-[family-name:var(--font-display)] text-4xl leading-none">{{ __('National visibility for every branch.') }}</h2>
                <p class="mt-4 max-w-md text-sm text-white/80">{{ __('Homepage slides can be used to highlight church programs, ministry updates, and national campaigns.') }}</p>
            </div>
        @endif
    </div>
</section>

<section class="info-grid cols-3 home-band home-band--light">
    <article class="card-rgc-strong">
        <span class="section-kicker">{{ __('About Church') }}</span>
        <h3 class="mt-5 font-[family-name:var(--font-display)] text-3xl leading-none">{{ __('A digital church platform built for connection.') }}</h3>
        <p class="mt-4 text-sm leading-7 text-black/70">
            {{ __('RGC brings together church communication, member access, giving, and shared records in one clear and friendly experience.') }}
        </p>
    </article>

    <article class="card-rgc">
        <span class="section-kicker">{{ __('Church Life') }}</span>
        <h3 class="mt-5 text-xl font-semibold">{{ __('Simple access to key church services') }}</h3>
        <ul class="mt-4 space-y-3 text-sm text-black/72">
            <li>{{ __('Church updates and ministry communication in one place') }}</li>
            <li>{{ __('Guided registration using your church location') }}</li>
            <li>{{ __('Giving and member participation tools') }}</li>
            <li>{{ __('Announcements and branch conversations for daily connection') }}</li>
        </ul>
    </article>

    <article class="card-rgc">
        <span class="section-kicker">{{ __('What Members See') }}</span>
        <h3 class="mt-5 text-xl font-semibold">{{ __('Simple, branch-focused participation') }}</h3>
        <p class="mt-4 text-sm leading-7 text-black/72">
            {{ __('Registration follows the Tanzania church location flow: region, district, and branch. After joining, members can follow updates, conversation, and giving for their church location.') }}
        </p>
    </article>
</section>

<section class="tablet-stack two home-band home-band--warm">
    <article class="card-rgc-strong">
        <span class="section-kicker">{{ __('Church Platform') }}</span>
        <h3 class="mt-5 font-[family-name:var(--font-display)] text-3xl leading-none">{{ __('Built to support church service across Tanzania.') }}</h3>
        <p class="mt-5 max-w-2xl text-sm leading-7 text-black/70">
            {{ __('The platform supports church communication, records, and member participation while keeping internal system settings inside the application.') }}
        </p>
    </article>

    <article class="card-rgc">
        <span class="section-kicker">{{ __('Member Registration') }}</span>
        <h3 class="mt-5 text-2xl font-semibold">{{ __('Branch selection stays inside signup flow') }}</h3>
        <p class="mt-4 text-sm leading-7 text-black/72">
            {{ __('Church locations are prepared in the system, then shown step by step after a member chooses the correct region and district during account creation.') }}
        </p>
        <div class="branch-list mt-5">
            <div class="branch-item">
                <strong class="block text-base">{{ __('Registered church locations') }}</strong>
                <p class="mt-2 text-sm text-black/65">{{ __('The platform currently has :count active church locations available during registration.', ['count' => number_format($branchCount)]) }}</p>
            </div>
            <div class="branch-item">
                <strong class="block text-base">{{ __('Guided registration steps') }}</strong>
                <p class="mt-2 text-sm text-black/65">{{ __('During signup, district choices reveal the branch list below the form only after the correct location is selected.') }}</p>
            </div>
        </div>
        <div class="mt-6">
            <a class="btn-rgc-alt" href="{{ route('register') }}">{{ __('Open guided registration') }}</a>
        </div>
    </article>
</section>
</div>
@endsection
