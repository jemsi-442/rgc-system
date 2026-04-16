@extends('layouts.app')

@section('title', __('Edit Assistant Help Topic') . ' - RGC')

@section('content')
<div class="space-y-8 max-w-5xl assistant-console">
    <div class="form-panel assistant-topic-editor">
        <div class="form-page-header">
            <div>
                <span class="section-kicker">{{ $topic->is_system ? __('Default topic') : __('Custom topic') }}</span>
                <h1 class="mt-4 text-2xl font-semibold">{{ __('Edit assistant topic') }}</h1>
                <p class="mt-2 text-sm text-black/65">{{ __('Adjust the answer, matching phrases, or coverage so the assistant responds more accurately to your users.') }}</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    <span class="payment-status-badge is-pending">{{ $topic->scopeLabel() }}</span>
                    <span class="payment-status-badge {{ $topic->is_active ? 'is-completed' : 'is-failed' }}">{{ $topic->is_active ? __('Active') : __('Inactive') }}</span>
                </div>
            </div>
            <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('assistant.topics.index') }}">{{ __('Back to assistant topics') }}</a>
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

    <section class="card-rgc assistant-version-shell assistant-panel-card">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <span class="section-kicker">{{ __('Version history') }}</span>
                <h2 class="mt-4 text-2xl font-semibold">{{ __('Previous versions') }}</h2>
                <p class="mt-2 text-sm text-black/65">{{ __('Restore an older answer, keyword set, or coverage if a recent edit moved the assistant in the wrong direction.') }}</p>
            </div>
            <span class="payment-status-badge is-pending">{{ __('Showing latest :count versions', ['count' => $versions->count()]) }}</span>
        </div>

        <form class="mt-5 flex flex-col gap-3 lg:flex-row" method="GET" action="{{ route('assistant.topics.edit', $topic) }}">
            <input class="input-rgc" type="search" name="version_q" value="{{ $versionSearch }}" placeholder="{{ __('Search versions by title, answer, or action') }}">
            <select class="select-rgc" name="version_action">
                <option value="">{{ __('All actions') }}</option>
                @foreach($versionActionOptions as $actionOption)
                    <option value="{{ $actionOption }}" @selected($versionAction === $actionOption)>{{ __(\Illuminate\Support\Str::headline(str_replace('_', ' ', $actionOption))) }}</option>
                @endforeach
            </select>
            <button class="btn-rgc-alt w-full sm:w-auto" type="submit">{{ __('Filter history') }}</button>
        </form>

        <div class="assistant-version-list mt-6">
            @forelse($versions as $version)
                <article class="assistant-version-item">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <h3 class="font-semibold text-black">{{ $version->title }}</h3>
                            <p class="mt-1 text-sm text-black/60">
                                #{{ $version->id }} · {{ strtoupper($version->locale) }} · {{ __('Action') }}: {{ __(\Illuminate\Support\Str::headline(str_replace('_', ' ', $version->action))) }}
                            </p>
                            <p class="mt-1 text-xs text-black/50">
                                {{ __('Updated by') }}: {{ $version->creator?->name ?? __('System') }} · {{ $version->created_at?->diffForHumans() }}
                            </p>
                        </div>
                        <form method="POST" action="{{ route('assistant.topics.versions.restore', ['topic' => $topic, 'version' => $version]) }}" onsubmit="return confirm('{{ __('Restore this version?') }}');">
                            @csrf
                            <button class="btn-rgc-alt w-full sm:w-auto" type="submit">{{ __('Restore this version') }}</button>
                        </form>
                    </div>

                    <div class="assistant-history-meta mt-4 flex flex-wrap gap-2">
                        <span class="payment-status-badge {{ $version->is_active ? 'is-completed' : 'is-failed' }}">{{ $version->is_active ? __('Active') : __('Inactive') }}</span>
                        <span class="payment-status-badge is-pending">{{ $version->is_system ? __('Default') : __('Custom') }}</span>
                        <span class="payment-status-badge is-pending">{{ $version->scopeLabel() }}</span>
                        <span class="payment-status-badge is-pending">{{ __('Keywords') }}: {{ count($version->keywords ?? []) }}</span>
                        <span class="payment-status-badge is-pending">{{ __('Sort order') }}: {{ $version->sort_order }}</span>
                    </div>

                    @if(!empty($version->roles))
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach(collect($version->roles)->map(fn ($role) => \Illuminate\Support\Str::headline(str_replace('_', ' ', $role))) as $roleLabel)
                                <span class="announcement-audience is-branch">{{ __($roleLabel) }}</span>
                            @endforeach
                        </div>
                    @endif

                    <p class="mt-4 text-sm text-black/70">{{ \Illuminate\Support\Str::limit($version->answer, 260) }}</p>

                    @if($version->restored_from_version_id)
                        <p class="mt-3 text-xs text-black/55">{{ __('Restored from version #:id', ['id' => $version->restored_from_version_id]) }}</p>
                    @endif
                </article>
            @empty
                <div class="branch-item text-sm text-black/60">{{ __('No previous versions are available yet for this topic.') }}</div>
            @endforelse
        </div>
    </section>
</div>
@endsection
