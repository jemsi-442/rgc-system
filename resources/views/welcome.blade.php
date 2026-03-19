@extends('layouts.app')

@section('title', __('RGC - Redeemed Gospel Church Inc. Tanzania'))

@section('content')
<section class="hero-grid items-stretch">
    <div class="hero-panel">
        <div class="hero-copy">
            <div class="hero-brand-lockup">
                <img src="{{ asset('images/rgc_logo.png') }}" alt="{{ __('RGC Logo') }}" class="hero-brand-logo">
                <div class="hero-brand-text">
                    <span class="hero-brand-name">{{ __('Redeemed Gospel Church') }}</span>
                    <span class="hero-brand-subtitle">{{ __('Inc. Tanzania') }}</span>
                </div>
            </div>

            <span class="section-kicker">{{ __('National Church Governance') }}</span>
            <h1 class="hero-title mt-5">{{ __('Redeemed Gospel Church') }} <em>{{ __('Inc. Tanzania') }}</em></h1>
            <p class="mt-5 text-base leading-7 md:text-lg">
                {{ __('A unified platform for national oversight, regional coordination, district supervision, branch administration, and member engagement across Tanzania Mainland and Zanzibar.') }}
            </p>

            <div class="hero-actions mt-8">
                <a class="btn-rgc" href="{{ route('login') }}">{{ __('Enter Dashboard') }}</a>
                <a class="btn-rgc-alt" href="{{ route('register') }}">{{ __('Join Your Branch') }}</a>
            </div>

            <div class="hero-metrics">
                <div class="metric-tile">
                    <strong>31</strong>
                    <span>{{ __('Canonical Regions') }}</span>
                </div>
                <div class="metric-tile">
                    <strong>228</strong>
                    <span>{{ __('District Records') }}</span>
                </div>
                <div class="metric-tile">
                    <strong>{{ __('Scoped') }}</strong>
                    <span>{{ __('Governance Access') }}</span>
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
                <p class="mt-4 max-w-md text-sm text-white/80">{{ __('Upload homepage slides from the Super Admin dashboard to highlight church programs, leadership communication, and national campaigns.') }}</p>
            </div>
        @endif
    </div>
</section>

<section class="mt-8 info-grid cols-3">
    <article class="card-rgc-strong">
        <span class="section-kicker">{{ __('About Church') }}</span>
        <h3 class="mt-5 font-[family-name:var(--font-display)] text-3xl leading-none">{{ __('A governance platform built for accountability.') }}</h3>
        <p class="mt-4 text-sm leading-7 text-black/70">
            {{ __('RGC coordinates ministry administration with clear authority lines, protected branch data, and dependable reporting for leaders serving at national, regional, district, and branch level.') }}
        </p>
    </article>

    <article class="card-rgc">
        <span class="section-kicker">{{ __('Leadership') }}</span>
        <h3 class="mt-5 text-xl font-semibold">{{ __('Structured national oversight') }}</h3>
        <ul class="mt-4 space-y-3 text-sm text-black/72">
            <li>{{ __('Super Admin for national governance and branch establishment') }}</li>
            <li>{{ __('Regional and district admins with strict area scope') }}</li>
            <li>{{ __('Branch admins and officers for local operations') }}</li>
            <li>{{ __('Members connected through announcements and branch chat') }}</li>
        </ul>
    </article>

    <article class="card-rgc">
        <span class="section-kicker">{{ __('What Members See') }}</span>
        <h3 class="mt-5 text-xl font-semibold">{{ __('Simple, branch-focused participation') }}</h3>
        <p class="mt-4 text-sm leading-7 text-black/72">
            {{ __('Registration follows the Tanzania hierarchy: region, district, and branch. Members then access branch-scoped announcements, conversation, and profile information without cross-branch leakage.') }}
        </p>
    </article>
</section>

<section class="mt-8 tablet-stack two">
    <article class="card-rgc-strong">
        <span class="section-kicker">{{ __('Governance Map') }}</span>
        <h3 class="mt-5 font-[family-name:var(--font-display)] text-3xl leading-none">{{ __('Role hierarchy with no privilege escalation.') }}</h3>
        <div class="mt-6 grid gap-3 text-sm">
            <div class="branch-item"><strong>{{ __('Super Admin') }}</strong><br><span class="text-black/65">{{ __('National setup, branch creation, statistics, and top-level user governance.') }}</span></div>
            <div class="branch-item"><strong>{{ __('Regional Admin') }}</strong><br><span class="text-black/65">{{ __('District and branch visibility within a single region.') }}</span></div>
            <div class="branch-item"><strong>{{ __('District Admin') }}</strong><br><span class="text-black/65">{{ __('Branch-level coordination and district operations.') }}</span></div>
            <div class="branch-item"><strong>{{ __('Branch Admin and Officers') }}</strong><br><span class="text-black/65">{{ __('Announcements, offerings, expenses, events, and local member management.') }}</span></div>
        </div>
    </article>

    <article class="card-rgc">
        <span class="section-kicker">{{ __('Branch Locator') }}</span>
        <h3 class="mt-5 text-2xl font-semibold">{{ __('Current branch records') }}</h3>
        <div class="branch-list mt-5">
            @forelse($branches as $branch)
                <div class="branch-item">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-base font-semibold">{{ $branch->name }}</p>
                            <p class="mt-1 text-sm text-black/65">{{ $branch->district->name }}, {{ $branch->region->name }}</p>
                        </div>
                        <span class="rounded-full bg-rgc-yellow px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-black">{{ __(Illuminate\Support\Str::headline($branch->branch_type ?: $branch->type)) }}</span>
                    </div>
                </div>
            @empty
                <div class="branch-item text-sm text-black/65">{{ __('No branches are visible yet. The headquarters branch will appear here once seeded or created.') }}</div>
            @endforelse
        </div>
        <div class="mt-5">{{ $branches->links() }}</div>
    </article>
</section>
@endsection
