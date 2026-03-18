@extends('layouts.app')

@section('title', 'Login - RGC')

@section('content')
<div class="auth-grid">
    <aside class="auth-aside">
        <span class="section-kicker !border-white/10 !bg-white/10 !text-rgc-yellow">Secure Sign In</span>
        <h2 class="mt-5">Enter the governance platform.</h2>
        <p class="mt-5 max-w-xl text-sm leading-7 text-white/80">
            Access is role-scoped from national leadership down to branch-level operations. Every session is tied to the user hierarchy already stored in the system.
        </p>

        <ul class="auth-list mt-8 text-sm text-white/82">
            <li><strong class="block text-white">National to branch hierarchy</strong> Users only see data within approved governance scope.</li>
            <li><strong class="block text-white">Session-protected web access</strong> Browser login powers dashboard, announcements, finance, and branch chat.</li>
            <li><strong class="block text-white">Operational continuity</strong> Regions, districts, and headquarters records are already structured for RGC Tanzania.</li>
        </ul>
    </aside>

    <section class="form-shell">
        <div class="form-panel">
            <span class="section-kicker">Account Access</span>
            <h1 class="mt-5 font-[family-name:var(--font-display)] text-4xl leading-none">Sign in to your RGC workspace</h1>
            <p class="mt-4 form-hint">Use your registered email and password to enter the platform.</p>

            <form class="mt-8 space-y-5" method="POST" action="{{ route('login.attempt') }}">
                @csrf

                <div>
                    <label class="field-label" for="email">Email address</label>
                    <input class="input-rgc" id="email" type="email" name="email" value="{{ old('email') }}" placeholder="name@rgc.or.tz" required>
                </div>

                <div>
                    <label class="field-label" for="password">Password</label>
                    <input class="input-rgc" id="password" type="password" name="password" placeholder="Enter your password" required>
                </div>

                <label class="flex items-center gap-3 text-sm text-black/70">
                    <input class="h-4 w-4 rounded border-black/20 text-rgc-red focus:ring-rgc-red" type="checkbox" name="remember">
                    Keep me signed in on this device
                </label>

                <div class="flex flex-wrap gap-3 pt-2">
                    <button class="btn-rgc min-w-[12rem]" type="submit">Login</button>
                    <a class="btn-rgc-alt" href="{{ route('register') }}">Create member account</a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
