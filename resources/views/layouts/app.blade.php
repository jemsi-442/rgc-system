<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell min-h-screen">
<header class="site-header text-rgc-white">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="brand-lockup">
            <span class="brand-mark">R</span>
            <span>
                <span class="brand-subtitle">Redeemed Gospel Church</span>
                <span class="brand-title block">Inc. Tanzania Platform</span>
            </span>
        </a>

        <nav class="top-nav">
            @auth
                <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="nav-link" href="{{ route('announcements.index') }}">Announcements</a>
                <a class="nav-link" href="{{ route('messages.index') }}">Branch Chat</a>
                @if(auth()->user()->hasSystemRole('super_admin'))
                    <a class="nav-link" href="{{ route('branches.index') }}">Branches</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn-rgc" type="submit">Logout</button>
                </form>
            @else
                <a class="nav-link" href="{{ route('home') }}">Home</a>
                <a class="nav-link" href="{{ route('login') }}">Login</a>
                <a class="btn-rgc" href="{{ route('register') }}">Register</a>
            @endauth
        </nav>
    </div>
</header>

<main class="main-shell mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    @if (session('status'))
        <div class="notice-ok">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="notice-error">{{ $errors->first() }}</div>
    @endif

    @yield('content')

    <footer class="site-footer">
        <div class="flex flex-col gap-3 text-sm md:flex-row md:items-center md:justify-between">
            <div>
                <strong>RGC Platform</strong><br>
                Redeemed Gospel Church Inc. Tanzania governance and administration system.
            </div>
            <div class="text-left md:text-right">
                Tanzania Mainland + Zanzibar<br>
                Region, district, and branch-scoped operations.
            </div>
        </div>
    </footer>
</main>
</body>
</html>
