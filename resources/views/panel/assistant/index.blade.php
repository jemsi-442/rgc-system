@extends('layouts.app')

@section('title', __('Assistant Knowledge') . ' - RGC')

@section('content')
<div class="space-y-8">
    <section class="card-rgc">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <span class="section-kicker">{{ __('System guide') }}</span>
                <h1 class="mt-4 text-2xl font-semibold">{{ __('Assistant Knowledge') }}</h1>
                <p class="mt-2 text-sm text-black/65">{{ __('Manage what the offline assistant knows, which roles each topic serves, and how recent questions are flowing through the platform.') }}</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:justify-end">
                <a class="btn-rgc-outline w-full sm:w-auto" href="{{ route('assistant.topics.export', request()->query()) }}">{{ __('Export topics backup') }}</a>
                <form method="POST" action="{{ route('assistant.topics.restore-defaults') }}">
                    @csrf
                    <button class="btn-rgc-outline w-full sm:w-auto" type="submit">{{ __('Restore defaults') }}</button>
                </form>
                <a class="btn-rgc w-full sm:w-auto" href="{{ route('assistant.topics.create') }}">{{ __('Add topic') }}</a>
            </div>
        </div>

        <div class="panel-grid cols-4 mt-6">
            <article class="stat-card">
                <span class="stat-label">{{ __('Knowledge topics') }}</span>
                <strong>{{ $stats['topics_total'] }}</strong>
            </article>
            <article class="stat-card">
                <span class="stat-label">{{ __('Active topics') }}</span>
                <strong>{{ $stats['topics_active'] }}</strong>
            </article>
            <article class="stat-card">
                <span class="stat-label">{{ __('Questions asked') }}</span>
                <strong>{{ $stats['questions_total'] }}</strong>
            </article>
            <article class="stat-card">
                <span class="stat-label">{{ __('Questions today') }}</span>
                <strong>{{ $stats['questions_today'] }}</strong>
            </article>
            <article class="stat-card">
                <span class="stat-label">{{ __('Fallback replies') }}</span>
                <strong>{{ $stats['fallback_count'] }}</strong>
            </article>
            <article class="stat-card">
                <span class="stat-label">{{ __('Helpful feedback') }}</span>
                <strong>{{ $stats['helpful_count'] }}</strong>
            </article>
            <article class="stat-card">
                <span class="stat-label">{{ __('Needs improvement') }}</span>
                <strong>{{ $stats['unhelpful_count'] }}</strong>
            </article>
        </div>
    </section>

    <section class="card-rgc assistant-import-panel">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <span class="section-kicker">{{ __('Backup and restore') }}</span>
                <h2 class="mt-4 text-2xl font-semibold">{{ __('Import assistant topics') }}</h2>
                <p class="mt-2 text-sm text-black/65">{{ __('Upload a JSON backup to restore or move assistant knowledge between environments. Existing topics with the same slug and locale will be updated safely.') }}</p>
            </div>
            <form method="POST" action="{{ route('assistant.topics.import') }}" enctype="multipart/form-data" class="assistant-import-form">
                @csrf
                <input class="input-rgc" type="file" name="topics_file" accept=".json,application/json,text/plain" required>
                <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Import topics backup') }}</button>
            </form>
        </div>
    </section>

    <section class="card-rgc">
        <form class="flex flex-col gap-3 lg:flex-row" method="GET" action="{{ route('assistant.topics.index') }}">
            <input class="input-rgc" type="search" name="q" value="{{ $search }}" placeholder="{{ __('Search title, slug, or answer') }}">
            <select class="select-rgc" name="locale">
                <option value="">{{ __('All locales') }}</option>
                @foreach($localeOptions as $localeOption)
                    <option value="{{ $localeOption }}" @selected($localeFilter === $localeOption)>{{ strtoupper($localeOption) }}</option>
                @endforeach
            </select>
            <button class="btn-rgc-alt w-full sm:w-auto" type="submit">{{ __('Search') }}</button>
        </form>

        <div class="table-wrap mt-5">
            <table class="responsive-table w-full text-sm">
                <thead>
                    <tr>
                        <th>{{ __('Topic') }}</th>
                        <th>{{ __('Locale') }}</th>
                        <th>{{ __('Roles') }}</th>
                        <th>{{ __('Keywords') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Source') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topics as $topic)
                        <tr class="border-t">
                            <td>
                                <div class="font-semibold">{{ $topic->title }}</div>
                                <div class="mt-1 text-xs text-black/50">{{ $topic->slug }}</div>
                            </td>
                            <td>{{ strtoupper($topic->locale) }}</td>
                            <td>
                                @if(empty($topic->roles))
                                    <span class="text-sm text-black/60">{{ __('All users') }}</span>
                                @else
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($topic->roleLabels() as $roleLabel)
                                            <span class="announcement-audience is-branch">{{ __($roleLabel) }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td>{{ count($topic->keywords ?? []) }}</td>
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    <span class="payment-status-badge {{ $topic->is_active ? 'is-completed' : 'is-failed' }}">{{ $topic->is_active ? __('Active') : __('Inactive') }}</span>
                                    <span class="payment-status-badge is-pending">{{ $topic->is_system ? __('Default') : __('Custom') }}</span>
                                </div>
                            </td>
                            <td>{{ $topic->updated_at?->diffForHumans() }}</td>
                            <td>
                                <div class="flex flex-col gap-2 sm:flex-row">
                                    <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('assistant.topics.edit', $topic) }}">{{ __('Edit') }}</a>
                                    <form method="POST" action="{{ route('assistant.topics.destroy', $topic) }}" onsubmit="return confirm('{{ __('Delete this assistant topic?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-black/60">{{ __('No assistant topics found for the current filter.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">{{ $topics->links() }}</div>
    </section>

    <section class="tablet-stack two">
        <article class="card-rgc">
            <span class="section-kicker">{{ __('Recent assistant questions') }}</span>
            <h2 class="mt-5 text-2xl font-semibold">{{ __('Latest chat history') }}</h2>
            <div class="mt-5 space-y-4">
                @forelse($recentInteractions as $interaction)
                    <div class="branch-item">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="font-semibold text-black">{{ $interaction->question }}</p>
                                <p class="mt-1 text-sm text-black/60">
                                    {{ $interaction->user?->name ?? __('Guest user') }} · {{ strtoupper($interaction->locale) }} · {{ $interaction->matched_slug ?? __('No matched topic') }}
                                </p>
                            </div>
                            <span class="text-xs text-black/50">{{ $interaction->created_at?->diffForHumans() }}</span>
                        </div>
                        <div class="assistant-history-meta mt-3 flex flex-wrap gap-2">
                            <span class="payment-status-badge is-pending">{{ __('Confidence') }}: {{ $interaction->confidence }}</span>
                            <span class="payment-status-badge {{ $interaction->helpful === true ? 'is-completed' : ($interaction->helpful === false ? 'is-failed' : 'is-pending') }}">{{ $interaction->feedbackLabel() }}</span>
                        </div>
                        <p class="mt-3 text-sm text-black/70">{{ \Illuminate\Support\Str::limit($interaction->answer, 180) }}</p>
                    </div>
                @empty
                    <div class="branch-item text-sm text-black/60">{{ __('No assistant questions have been logged yet.') }}</div>
                @endforelse
            </div>
        </article>

        <article class="card-rgc">
            <span class="section-kicker">{{ __('Frequent patterns') }}</span>
            <h2 class="mt-5 text-2xl font-semibold">{{ __('Most asked questions') }}</h2>
            <div class="mt-5 space-y-3">
                @forelse($topQuestions as $question)
                    <div class="branch-item flex items-center justify-between gap-4">
                        <div>
                            <p class="font-semibold text-black">{{ $question->normalized_question }}</p>
                            <p class="mt-1 text-sm text-black/60">{{ __('Used :count times', ['count' => $question->total]) }}</p>
                        </div>
                        <span class="payment-status-badge is-pending">{{ $question->total }}</span>
                    </div>
                @empty
                    <div class="branch-item text-sm text-black/60">{{ __('Usage patterns will appear here after people begin asking the assistant questions.') }}</div>
                @endforelse
            </div>
        </article>
    </section>
</div>
@endsection
