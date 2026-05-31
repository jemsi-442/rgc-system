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

            <span class="section-kicker">{{ __('Official Church Home') }}</span>
            <h1 class="hero-title mt-5">{{ __('Redeemed Gospel Church') }} <em>{{ __('Inc. Tanzania') }}</em></h1>
            <p class="mt-5 text-base leading-7 md:text-lg">
                {{ __('A place for the RGC family to receive church news, follow branch announcements, give faithfully, and stay connected across Tanzania Mainland and Zanzibar.') }}
            </p>

            <div class="hero-actions mt-8">
                <a class="btn-rgc" href="{{ route('login') }}">{{ __('Member Login') }}</a>
                <a class="btn-rgc-alt" href="{{ route('register') }}">{{ __('Join Your Branch') }}</a>
            </div>

            <div class="hero-metrics">
                <div class="metric-tile">
                    <strong>31</strong>
                    <span>{{ __('Regions Reached') }}</span>
                </div>
                <div class="metric-tile">
                    <strong>228</strong>
                    <span>{{ __('Districts Covered') }}</span>
                </div>
                <div class="metric-tile">
                    <strong>{{ __('One') }}</strong>
                    <span>{{ __('Church Family') }}</span>
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
                            <p class="mt-3 max-w-md text-sm text-white/80">{{ $slide->subtitle ?: __('Church news, ministry moments, and branch updates for the RGC family across Tanzania.') }}</p>
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
                <span class="section-kicker !border-white/10 !bg-white/10 !text-rgc-yellow">{{ __('Church Highlights') }}</span>
                <h2 class="mt-5 font-[family-name:var(--font-display)] text-4xl leading-none">{{ __('Sharing church life from every branch.') }}</h2>
                <p class="mt-4 max-w-md text-sm text-white/80">{{ __('Use this space to share church programs, ministry updates, and national church gatherings.') }}</p>
            </div>
        @endif
    </div>
</section>

<section class="info-grid cols-3 home-band home-band--light">
    <article class="card-rgc-strong">
        <span class="section-kicker">{{ __('About Church') }}</span>
        <h3 class="mt-5 font-[family-name:var(--font-display)] text-3xl leading-none">{{ __('A church family growing together in faith and service.') }}</h3>
        <p class="mt-4 text-sm leading-7 text-black/70">
            {{ __('RGC brings members, leaders, and branches together for worship updates, ministry care, giving, and everyday church fellowship.') }}
        </p>
    </article>

    <article class="card-rgc">
        <span class="section-kicker">{{ __('Church Life') }}</span>
        <h3 class="mt-5 text-xl font-semibold">{{ __('Stay close to your branch and church family') }}</h3>
        <ul class="mt-4 space-y-3 text-sm text-black/72">
            <li>{{ __('Receive church announcements and ministry updates') }}</li>
            <li>{{ __('Join through your region, district, and branch') }}</li>
            <li>{{ __('Give and take part in church life with ease') }}</li>
            <li>{{ __('Follow branch conversations and fellowship updates') }}</li>
        </ul>
    </article>

    <article class="card-rgc">
        <span class="section-kicker">{{ __('What Members See') }}</span>
        <h3 class="mt-5 text-xl font-semibold">{{ __('Begin from your home branch') }}</h3>
        <p class="mt-4 text-sm leading-7 text-black/72">
            {{ __('Members join through their region, district, and branch so every update, conversation, and act of giving stays connected to the right church family.') }}
        </p>
    </article>
</section>

<section class="tablet-stack two home-band home-band--warm">
    <article class="card-rgc-strong">
        <span class="section-kicker">{{ __('Serving Together') }}</span>
        <h3 class="mt-5 font-[family-name:var(--font-display)] text-3xl leading-none">{{ __('Supporting church work across Tanzania.') }}</h3>
        <p class="mt-5 max-w-2xl text-sm leading-7 text-black/70">
            {{ __('From local branches to national church gatherings, RGC helps leaders and members share updates, care for people, and serve with one heart.') }}
        </p>
    </article>

    <article class="card-rgc">
        <span class="section-kicker">{{ __('Member Registration') }}</span>
        <h3 class="mt-5 text-2xl font-semibold">{{ __('Choose the branch where you worship') }}</h3>
        <p class="mt-4 text-sm leading-7 text-black/72">
            {{ __('During registration, choose your region and district, then select your branch so your church updates and fellowship stay in the right place.') }}
        </p>
        <div class="branch-list mt-5">
            <div class="branch-item">
                <strong class="block text-base">{{ __('Church branches available') }}</strong>
                <p class="mt-2 text-sm text-black/65">{{ __('There are :count active church branches ready for members to join during registration.', ['count' => number_format($branchCount)]) }}</p>
            </div>
            <div class="branch-item">
                <strong class="block text-base">{{ __('Simple joining steps') }}</strong>
                <p class="mt-2 text-sm text-black/65">{{ __('Choose your location step by step, then connect with the branch where you worship and serve.') }}</p>
            </div>
        </div>
        <div class="mt-6">
            <a class="btn-rgc-alt" href="{{ route('register') }}">{{ __('Join your church branch') }}</a>
        </div>
    </article>
</section>
</div>
@endsection
