@extends('layouts.app')

@section('title', __('Add User') . ' - RGC')

@section('content')
<div class="form-shell form-shell--executive max-w-5xl">
    <div class="form-panel">
        <div class="form-page-header">
            <div>
                <span class="section-kicker">{{ __('Super Admin') }}</span>
                <h1 class="mt-4 text-2xl font-semibold">{{ __('Create a new user account') }}</h1>
                <p class="mt-2 text-sm text-black/65">{{ __('Create the person as a normal member or assign branch, district, or regional leadership access from this same screen.') }}</p>
            </div>
            <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('admin.users.index') }}">{{ __('Back to users') }}</a>
        </div>

        <form class="mt-6 form-stack" method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            @include('panel.users._form', [
                'managedUser' => null,
                'passwordRequired' => true,
                'passwordLabel' => __('Temporary password'),
            ])
            <div class="form-actions pt-2">
                <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Create user') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
