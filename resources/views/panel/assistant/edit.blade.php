@extends('layouts.app')

@section('title', __('Edit Assistant Topic') . ' - RGC')

@section('content')
<div class="form-shell max-w-5xl">
    <div class="form-panel">
        <div class="form-page-header">
            <div>
                <span class="section-kicker">{{ $topic->is_system ? __('Default topic') : __('Custom topic') }}</span>
                <h1 class="mt-4 text-2xl font-semibold">{{ __('Edit assistant topic') }}</h1>
                <p class="mt-2 text-sm text-black/65">{{ __('Adjust the answer, matching phrases, or role scope so the assistant responds more accurately to your users.') }}</p>
            </div>
            <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('assistant.topics.index') }}">{{ __('Back to assistant knowledge') }}</a>
        </div>

        <form class="mt-6 form-stack" method="POST" action="{{ route('assistant.topics.update', $topic) }}">
            @csrf
            @method('PUT')
            @include('panel.assistant._form', ['topic' => $topic])
            <div class="form-actions pt-2">
                <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Update topic') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
