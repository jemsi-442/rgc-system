@extends('layouts.app')

@section('title', __('Change Password') . ' - RGC')

@section('content')
<div class="form-shell max-w-3xl">
    <div class="form-panel">
        <span class="section-kicker">{{ __('Account Security') }}</span>
        <h1 class="mt-4 text-2xl font-semibold">{{ __('Change my password') }}</h1>
        <p class="mt-2 text-sm text-black/65">{{ __('Update your own password from the dashboard with current-password verification.') }}</p>

        <form class="mt-6 form-stack" method="POST" action="{{ route('account.password.update') }}">
            @csrf
            @method('PUT')
            <section class="form-section">
                <div class="form-section-heading">
                    <h2>{{ __('Password reset') }}</h2>
                    <p>{{ __('This form stays simple on mobile so you can safely change your password from any device.') }}</p>
                </div>

                <div class="form-grid-responsive">
                    <div class="md:col-span-2">
                        <label class="field-label" for="current_password">{{ __('Current password') }}</label>
                        <input class="input-rgc" id="current_password" type="password" name="current_password" required>
                    </div>
                    <div>
                        <label class="field-label" for="password">{{ __('New password') }}</label>
                        <input class="input-rgc" id="password" type="password" name="password" required>
                    </div>
                    <div>
                        <label class="field-label" for="password_confirmation">{{ __('Confirm new password') }}</label>
                        <input class="input-rgc" id="password_confirmation" type="password" name="password_confirmation" required>
                    </div>
                </div>
            </section>

            <div class="form-actions pt-2">
                <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Update password') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
