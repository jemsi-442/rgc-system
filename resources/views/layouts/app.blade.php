<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#5e0d0d">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="RGC Tanzania">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="icon" type="image/png" href="{{ asset('images/rgc_logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-180.png') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell min-h-screen">
@php
    $dashboardActive = request()->routeIs('dashboard');
    $announcementsActive = request()->routeIs('announcements.*');
    $messagesActive = request()->routeIs('messages.*');
    $givingActive = request()->routeIs('giving.*') || request()->routeIs('offerings.payments.public.*');
    $accountActive = request()->routeIs('account.profile.*');
    $passwordActive = request()->routeIs('account.password.*');
    $usersActive = request()->routeIs('admin.users.*');
    $branchesActive = request()->routeIs('branches.*');
    $slidesActive = request()->routeIs('sliders.*');
    $assistantTopicsActive = request()->routeIs('assistant.topics.*');
@endphp
<header class="site-header text-rgc-white" data-site-header>
    <div class="site-header-inner mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="brand-lockup">
            <img src="{{ asset('images/rgc_logo.png') }}" alt="{{ __('RGC Logo') }}" class="brand-mark">
            <span>
                <span class="brand-subtitle">{{ __('Redeemed Gospel Church') }}</span>
                <span class="brand-title block">{{ __('Inc. Tanzania') }}</span>
            </span>
        </a>

        <div class="header-actions">
            <div class="locale-row">
                <div class="locale-switcher" aria-label="{{ __('Language switcher') }}">
                    <span class="locale-switcher-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" focusable="false">
                            <path d="M12 3a9 9 0 1 0 0 18a9 9 0 0 0 0-18Zm6.93 8h-3.12a14.8 14.8 0 0 0-1.56-5.02A7.04 7.04 0 0 1 18.93 11ZM12 4.9c.84 1.01 1.8 3.03 2.18 6.1H9.82C10.2 7.93 11.16 5.91 12 4.9ZM9.75 5.98A14.8 14.8 0 0 0 8.19 11H5.07a7.04 7.04 0 0 1 4.68-5.02ZM4.9 13h3.29c.16 1.85.7 3.7 1.56 5.02A7.04 7.04 0 0 1 4.9 13Zm7.1 6.1c-.84-1.01-1.8-3.03-2.18-6.1h4.36c-.38 3.07-1.34 5.09-2.18 6.1Zm2.25-1.08c.86-1.32 1.4-3.17 1.56-5.02h3.29a7.04 7.04 0 0 1-4.85 5.02Z" fill="currentColor"/>
                        </svg>
                    </span>
                    <form method="POST" action="{{ route('locale.update') }}">
                        @csrf
                        <input type="hidden" name="locale" value="en">
                        <button class="locale-chip {{ app()->getLocale() === 'en' ? 'locale-chip--active' : '' }}" type="submit" aria-label="{{ __('Switch to English') }}">EN</button>
                    </form>
                    <form method="POST" action="{{ route('locale.update') }}">
                        @csrf
                        <input type="hidden" name="locale" value="sw">
                        <button class="locale-chip {{ app()->getLocale() === 'sw' ? 'locale-chip--active' : '' }}" type="submit" aria-label="{{ __('Switch to Kiswahili') }}">SW</button>
                    </form>
                </div>

                <button
                    class="menu-toggle"
                    type="button"
                    aria-expanded="false"
                    aria-controls="primary-navigation"
                    data-menu-toggle
                    data-open-label="{{ __('Menu') }}"
                    data-close-label="{{ __('Close') }}"
                >
                    <span class="sr-only" data-menu-announce>{{ __('Open menu') }}</span>
                    <span class="menu-toggle-icon" aria-hidden="true">
                        <span class="menu-toggle-bar"></span>
                        <span class="menu-toggle-bar"></span>
                        <span class="menu-toggle-bar"></span>
                    </span>
                    <span class="menu-toggle-text" data-menu-label>{{ __('Menu') }}</span>
                </button>
            </div>

            <nav class="top-nav nav-scroll" id="primary-navigation" data-mobile-menu>
                @auth
                    <a class="nav-link {{ $dashboardActive ? 'nav-link--active' : '' }}" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                    <a class="nav-link {{ $announcementsActive ? 'nav-link--active' : '' }}" href="{{ route('announcements.index') }}">{{ __('Announcements') }}</a>
                    <a class="nav-link {{ $messagesActive ? 'nav-link--active' : '' }}" href="{{ route('messages.index') }}">{{ __('Branch Chat') }}</a>
                    <a class="nav-link {{ $givingActive ? 'nav-link--active' : '' }}" href="{{ route('giving.index') }}">{{ __('Giving') }}</a>
                    <a class="nav-link {{ $accountActive ? 'nav-link--active' : '' }}" href="{{ route('account.profile.edit') }}">{{ __('My Account') }}</a>
                    <a class="nav-link {{ $passwordActive ? 'nav-link--active' : '' }}" href="{{ route('account.password.edit') }}">{{ __('My Password') }}</a>
                    @if(auth()->user()->hasSystemRole('super_admin'))
                        <a class="nav-link {{ $usersActive ? 'nav-link--active' : '' }}" href="{{ route('admin.users.index') }}">{{ __('Users') }}</a>
                        <a class="nav-link {{ $branchesActive ? 'nav-link--active' : '' }}" href="{{ route('branches.index') }}">{{ __('Branches') }}</a>
                        <a class="nav-link {{ $slidesActive ? 'nav-link--active' : '' }}" href="{{ route('sliders.index') }}">{{ __('Slides') }}</a>
                    @endif
                    @if(auth()->user()->hasAnySystemRole(['super_admin', 'regional_admin']))
                        <a class="nav-link {{ $assistantTopicsActive ? 'nav-link--active' : '' }}" href="{{ route('assistant.topics.index') }}">{{ __('Assistant Topics') }}</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn-rgc" type="submit">{{ __('Logout') }}</button>
                    </form>
                @else
                    <a class="nav-link {{ request()->routeIs('home') ? 'nav-link--active' : '' }}" href="{{ route('home') }}">{{ __('Home') }}</a>
                    <a class="nav-link {{ request()->routeIs('login') ? 'nav-link--active' : '' }}" href="{{ route('login') }}">{{ __('Login') }}</a>
                    <a class="btn-rgc" href="{{ route('register') }}">{{ __('Register') }}</a>
                @endauth
            </nav>
        </div>
    </div>
</header>

<main class="main-shell mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    <section
        class="install-prompt hidden"
        data-pwa-install-prompt
        data-ios-message="{{ __('To install this app on iPhone or iPad, open Share and choose Add to Home Screen.') }}"
        data-ready-message="{{ __('Install this app on your device for faster access.') }}"
        data-installed-message="{{ __('RGC Tanzania is already installed on this device.') }}"
    >
        <div class="install-prompt-copy">
            <strong data-pwa-install-title>{{ __('Install RGC Tanzania') }}</strong>
            <p data-pwa-install-message>{{ __('Install this app on your device for faster access.') }}</p>
        </div>
        <div class="install-prompt-actions">
            <button type="button" class="btn-rgc install-prompt-button" data-pwa-install-action>{{ __('Install') }}</button>
            <button type="button" class="btn-rgc-alt install-prompt-button install-prompt-button--quiet" data-pwa-install-dismiss>{{ __('Later') }}</button>
        </div>
    </section>

    @if (session('status'))
        <div class="notice-ok">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="notice-error">{{ $errors->first() }}</div>
    @endif

    @yield('content')

    <footer class="site-footer">
        <div class="site-footer-grid">
            <div class="site-footer-brand">
                <div class="site-footer-lockup">
                    <img src="{{ asset('images/rgc_logo.png') }}" alt="{{ __('RGC Logo') }}" class="site-footer-mark">
                    <div>
                        <strong>{{ __('RGC Tanzania') }}</strong>
                        <p>{{ __('Redeemed Gospel Church Inc. Tanzania official digital home.') }}</p>
                    </div>
                </div>
            </div>

            <div class="site-footer-links">
                <span class="site-footer-label">{{ __('Quick access') }}</span>
                <div class="site-footer-link-row">
                    <a href="{{ route('home') }}">{{ __('Home') }}</a>
                    @auth
                        <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                        <a href="{{ route('announcements.index') }}">{{ __('Announcements') }}</a>
                        <a href="{{ route('giving.index') }}">{{ __('Giving') }}</a>
                    @else
                        <a href="{{ route('login') }}">{{ __('Login') }}</a>
                        <a href="{{ route('register') }}">{{ __('Register') }}</a>
                    @endauth
                </div>
            </div>

            <div class="site-footer-meta">
                <span class="site-footer-label">{{ __('Church home') }}</span>
                <p class="site-footer-meta-copy">{{ __('Tanzania Mainland + Zanzibar. Church locations, updates, and member access in one place.') }}</p>
                <p class="site-footer-meta-copy">{{ __('Built for church communication, giving, and everyday member use across the full church family.') }}</p>
            </div>
        </div>

        <div class="site-footer-bottom">
            <span>{{ __('RGC Tanzania') }} © {{ now()->year }}</span>
            <span>{{ __('Church communication, giving, and member services.') }}</span>
        </div>
    </footer>
</main>

@php
    $assistantSuggestions = app(\App\Services\SystemAssistantService::class)->starterSuggestions(auth()->user(), app()->getLocale());
@endphp

<div
    class="assistant-widget"
    data-assistant-widget
    data-endpoint="{{ route('assistant.chat') }}"
    data-feedback-endpoint-template="{{ route('assistant.feedback', ['interaction' => '__ID__']) }}"
    data-error-label="{{ __('Something went wrong. Please try again in a moment.') }}"
    data-thinking-label="{{ __('Thinking...') }}"
    data-assistant-name="{{ __('RGC Assistant') }}"
    data-user-name="{{ __('You') }}"
    data-feedback-prompt="{{ __('Was this answer helpful?') }}"
    data-feedback-helpful="{{ __('Helpful') }}"
    data-feedback-unhelpful="{{ __('Not helpful') }}"
    data-feedback-saved="{{ __('Feedback saved') }}"
    data-feedback-saving="{{ __('Saving feedback...') }}"
    data-feedback-note-label="{{ __('Tell us what was missing (optional)') }}"
    data-feedback-note-placeholder="{{ __('Write a short note so we can improve this answer.') }}"
    data-feedback-note-save="{{ __('Save feedback') }}"
    data-feedback-note-skip="{{ __('Skip note') }}"
    data-feedback-note-title="{{ __('Feedback note') }}"
>
    <button
        type="button"
        class="assistant-launcher"
        data-assistant-launcher
        aria-expanded="false"
        aria-controls="assistant-panel"
    >
        <span class="assistant-launcher-mark" aria-hidden="true">?</span>
        <span class="assistant-launcher-copy">
            <strong>{{ __('RGC Assistant') }}</strong>
            <span>{{ __('Help and guidance') }}</span>
        </span>
    </button>

    <section class="assistant-panel" id="assistant-panel" data-assistant-panel hidden>
        <header class="assistant-panel-header">
            <div>
                <p class="assistant-panel-kicker">{{ __('Help center') }}</p>
                <h2>{{ __('RGC Assistant') }}</h2>
            </div>
            <button type="button" class="assistant-panel-close" data-assistant-close aria-label="{{ __('Close assistant') }}">×</button>
        </header>

        <div class="assistant-panel-body">
            <div class="assistant-messages" data-assistant-messages>
                <article class="assistant-message assistant-message--bot">
                    <span class="assistant-message-author">{{ __('RGC Assistant') }}</span>
                    <p>{{ __('I can help explain how this system works. Ask about registration, dashboard, branches, announcements, chat, or giving.') }}</p>
                </article>
            </div>

            <div class="assistant-hint">{{ __('Answers are based on this church system only.') }}</div>

            <div class="assistant-suggestions" data-assistant-suggestions>
                @foreach ($assistantSuggestions as $assistantSuggestion)
                    <button type="button" class="assistant-suggestion" data-assistant-suggestion>{{ $assistantSuggestion }}</button>
                @endforeach
            </div>

            <form class="assistant-form" data-assistant-form>
                <label class="sr-only" for="assistant-question">{{ __('Type your question about the system...') }}</label>
                <textarea
                    id="assistant-question"
                    rows="2"
                    class="assistant-input"
                    name="question"
                    data-assistant-input
                    placeholder="{{ __('Type your question about the system...') }}"
                ></textarea>
                <button type="submit" class="btn-rgc assistant-submit" data-assistant-submit>{{ __('Send question') }}</button>
            </form>
        </div>
    </section>
</div>
</body>
</html>
