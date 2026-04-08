<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#5e0d0d">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="RGC Platform">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="icon" type="image/png" href="{{ asset('images/rgc_logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-180.png') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell min-h-screen">
<header class="site-header text-rgc-white">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="brand-lockup">
            <img src="{{ asset('images/rgc_logo.png') }}" alt="{{ __('RGC Logo') }}" class="brand-mark">
            <span>
                <span class="brand-subtitle">{{ __('Redeemed Gospel Church') }}</span>
                <span class="brand-title block">{{ __('Inc. Tanzania Platform') }}</span>
            </span>
        </a>

        <div class="header-actions">
            <div class="locale-row">
                <div class="locale-switcher flex items-center gap-2">
                    <form method="POST" action="{{ route('locale.update') }}">
                        @csrf
                        <input type="hidden" name="locale" value="en">
                        <button class="nav-link {{ app()->getLocale() === 'en' ? 'font-semibold underline underline-offset-4' : '' }}" type="submit">{{ __('English') }}</button>
                    </form>
                    <span class="text-rgc-white/45">|</span>
                    <form method="POST" action="{{ route('locale.update') }}">
                        @csrf
                        <input type="hidden" name="locale" value="sw">
                        <button class="nav-link {{ app()->getLocale() === 'sw' ? 'font-semibold underline underline-offset-4' : '' }}" type="submit">{{ __('Kiswahili') }}</button>
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
                    <a class="nav-link" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                    <a class="nav-link" href="{{ route('announcements.index') }}">{{ __('Announcements') }}</a>
                    <a class="nav-link" href="{{ route('messages.index') }}">{{ __('Branch Chat') }}</a>
                    <a class="nav-link" href="{{ route('giving.index') }}">{{ __('Giving') }}</a>
                    <a class="nav-link" href="{{ route('account.password.edit') }}">{{ __('My Password') }}</a>
                    @if(auth()->user()->hasSystemRole('super_admin'))
                        <a class="nav-link" href="{{ route('admin.users.index') }}">{{ __('Users') }}</a>
                        <a class="nav-link" href="{{ route('branches.index') }}">{{ __('Branches') }}</a>
                        <a class="nav-link" href="{{ route('sliders.index') }}">{{ __('Slides') }}</a>
                    @endif
                    @if(auth()->user()->hasAnySystemRole(['super_admin', 'regional_admin']))
                        <a class="nav-link" href="{{ route('assistant.topics.index') }}">{{ __('Assistant Knowledge') }}</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn-rgc" type="submit">{{ __('Logout') }}</button>
                    </form>
                @else
                    <a class="nav-link" href="{{ route('home') }}">{{ __('Home') }}</a>
                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
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
        data-installed-message="{{ __('RGC Platform is already installed on this device.') }}"
    >
        <div class="install-prompt-copy">
            <strong data-pwa-install-title>{{ __('Install RGC Platform') }}</strong>
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
                        <strong>{{ __('RGC Platform') }}</strong>
                        <p>{{ __('Redeemed Gospel Church Inc. Tanzania official digital platform.') }}</p>
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
                <span class="site-footer-label">{{ __('Church platform') }}</span>
                <p class="site-footer-meta-copy">{{ __('Tanzania Mainland + Zanzibar. Church locations, updates, and member access in one place.') }}</p>
                <p class="site-footer-meta-copy">{{ __('Built for church communication, giving, and everyday member use across the full church family.') }}</p>
            </div>
        </div>

        <div class="site-footer-bottom">
            <span>{{ __('RGC Platform') }} © {{ now()->year }}</span>
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

            <div class="assistant-hint">{{ __('Answers are based on this platform only.') }}</div>

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
