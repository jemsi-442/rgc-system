@extends('layouts.app')

@section('title', __('Add Assistant Topic') . ' - RGC')

@section('content')
<div class="form-shell max-w-5xl">
    <div class="form-panel">
        <div class="form-page-header">
            <div>
                <span class="section-kicker">{{ __('System guide') }}</span>
                <h1 class="mt-4 text-2xl font-semibold">{{ __('Create assistant topic') }}</h1>
                <p class="mt-2 text-sm text-black/65">{{ __('Add a new topic so the offline assistant can answer with language that fits your church workflow clearly and safely.') }}</p>
            </div>
            <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('assistant.topics.index') }}">{{ __('Back to assistant knowledge') }}</a>
        </div>

        <form class="mt-6 form-stack" method="POST" action="{{ route('assistant.topics.store') }}">
            @csrf
            @include('panel.assistant._form', ['topic' => $topic])
            <div class="form-actions pt-2">
                <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Create topic') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
