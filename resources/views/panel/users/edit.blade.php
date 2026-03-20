@extends('layouts.app')

@section('title', __('Edit User') . ' - RGC')

@section('content')
<div class="form-shell max-w-5xl">
    <div class="form-panel">
        <div class="form-page-header">
            <div>
                <span class="section-kicker">{{ __('Super Admin') }}</span>
                <h1 class="mt-4 text-2xl font-semibold">{{ __('Edit user account') }}</h1>
                <p class="mt-2 text-sm text-black/65">{{ __('Promote a member into leadership, reassign their governance scope, or return them to normal member access from here.') }}</p>
            </div>
            <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('admin.users.index') }}">{{ __('Back to users') }}</a>
        </div>

        <form class="mt-6 form-stack" method="POST" action="{{ route('admin.users.update', $managedUser) }}">
            @csrf
            @method('PUT')
            @include('panel.users._form', [
                'managedUser' => $managedUser,
                'passwordRequired' => false,
                'passwordLabel' => __('Reset password'),
            ])
            <div class="form-actions pt-2">
                <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Save changes') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
