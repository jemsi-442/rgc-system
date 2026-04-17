@extends('layouts.app')

@section('title', __('Announcements') . ' - RGC')

@section('content')
<section class="page-banner">
    <div class="page-banner-content">
        <span class="section-kicker section-kicker--icon !border-white/10 !bg-white/10 !text-rgc-yellow">@include('partials.ui.icon', ['name' => 'megaphone', 'class' => 'section-kicker-icon'])<span>{{ __('Announcements') }}</span></span>
        <h1 class="mt-5">{{ __('Announcements') }}</h1>
        <p class="mt-4 max-w-3xl text-sm leading-7 text-white/82">{{ __('Church updates, guidance, and reminders that help God’s people stay informed, prepared, and joined together in one direction.') }}</p>
        <div class="member-scripture-callout mt-5 max-w-3xl">
            <p class="member-scripture-text">"{{ __('Let all things be done decently and in order.') }}"</p>
            <span class="member-scripture-reference">{{ __('1 Corinthians 14:40') }}</span>
        </div>
    </div>
</section>

<section class="card-rgc mt-8 announcement-newsroom">
    <div class="announcement-toolbar">
        <div>
            <h2 class="text-2xl font-semibold">{{ $showArchived ? __('Archived announcements in your view') : __('Recent announcements in your view') }}</h2>
            <p class="mt-2 text-sm text-black/65">{{ $showArchived ? __('These announcements have already passed, but they remain here for reference.') : __('Church-wide announcements appear here together with the notices shared for the churches connected to your care.') }}</p>
            @if(! $showArchived)
                <p class="mt-3 text-sm text-black/70">{{ __('Read these updates with readiness so prayer, worship, gatherings, and church service can move together in peace.') }}</p>
            @endif
        </div>
        <div class="announcement-toolbar-actions">
            <a class="btn-rgc-alt w-full sm:w-auto" href="{{ $showArchived ? route('announcements.index') : route('announcements.index', ['archived' => 1]) }}">@include('partials.ui.icon', ['name' => 'archive', 'class' => 'button-icon'])<span>{{ $showArchived ? __('View active announcements') : __('View archived announcements') }}</span></a>
            @can('create', App\Models\Announcement::class)
                <a class="btn-rgc w-full sm:w-auto" href="{{ route('announcements.create') }}">@include('partials.ui.icon', ['name' => 'plus', 'class' => 'button-icon'])<span>{{ __('Share announcement') }}</span></a>
            @endcan
        </div>
    </div>

    <div class="announcement-grid mt-8">
        @forelse($announcements as $announcement)
            @php($targetNames = $announcement->targetBranchNames())
            <article class="announcement-card {{ $announcement->hasPin() ? 'is-pinned' : '' }}">
                @if($announcement->hasImage())
                    <button
                        class="announcement-media announcement-media-button"
                        type="button"
                        data-announcement-lightbox-trigger
                        data-image-src="{{ route('announcements.image', $announcement) }}"
                        data-image-alt="{{ $announcement->title }}"
                        data-image-title="{{ $announcement->title }}"
                    >
                        <img src="{{ route('announcements.image', $announcement) }}" alt="{{ $announcement->title }}">
                        <span class="announcement-media-caption">{{ __('View full image') }}</span>
                    </button>
                @endif

                <div class="announcement-card-body">
                    <div class="announcement-card-meta">
                        <div class="announcement-meta-badges">
                            <span class="announcement-audience is-{{ $announcement->audienceVariant() }}">{{ $announcement->audienceLabel() }}</span>
                            @if($announcement->hasPin())
                                <span class="announcement-pin-chip">{{ __('Pinned') }}</span>
                            @endif
                            @if($announcement->isArchived())
                                <span class="announcement-archived-chip">{{ __('Archived') }}</span>
                            @endif
                        </div>
                        <div class="announcement-meta-trail">
                            @if($announcement->hasExpiry())
                                <span class="announcement-expiry-chip {{ $announcement->isExpired() ? 'is-expired' : '' }}">
                                    {{ $announcement->isExpired() ? __('Expired') : __('Expires :date', ['date' => $announcement->expires_at->translatedFormat('d M Y')]) }}
                                </span>
                            @endif
                            <span>{{ optional($announcement->created_at)->diffForHumans() }}</span>
                        </div>
                    </div>

                    <div class="announcement-card-heading">
                        <div>
                            <h3><a href="{{ route('announcements.show', $announcement) }}">{{ $announcement->title }}</a></h3>
                            <p>{{ $announcement->creator?->name ?? __('System') }}</p>
                        </div>
                    </div>

                    <p class="announcement-delivery-summary">{{ $announcement->deliverySummary() }}</p>

                    @if($announcement->hasExplicitBranchTargets())
                        <div class="announcement-scope-stack">
                            @foreach($targetNames as $name)
                                <span>{{ $name }}</span>
                            @endforeach
                            @if($announcement->targetBranchCount() > count($targetNames))
                                <span>{{ __('+:count more', ['count' => $announcement->targetBranchCount() - count($targetNames)]) }}</span>
                            @endif
                        </div>
                    @elseif($announcement->region || $announcement->district || $announcement->branch)
                        <div class="announcement-scope-stack">
                            @if($announcement->region)
                                <span>{{ $announcement->region->name }}</span>
                            @endif
                            @if($announcement->district)
                                <span>{{ $announcement->district->name }}</span>
                            @endif
                            @if($announcement->branch)
                                <span>{{ $announcement->branch->name }}</span>
                            @endif
                        </div>
                    @endif

                    @if(filled($announcement->body))
                        <p class="announcement-card-copy">{{ $announcement->body }}</p>
                    @endif

                    <div class="announcement-actions">
                        <a class="btn-rgc-alt" href="{{ route('announcements.show', $announcement) }}">@include('partials.ui.icon', ['name' => 'eye', 'class' => 'button-icon'])<span>{{ __('Open details') }}</span></a>
                        @canany(['update', 'delete'], $announcement)
                            @can('update', $announcement)
                                <a class="btn-rgc-alt" href="{{ route('announcements.edit', $announcement) }}">@include('partials.ui.icon', ['name' => 'edit', 'class' => 'button-icon'])<span>{{ __('Edit') }}</span></a>
                            @endcan
                            @can('delete', $announcement)
                                <form method="POST" action="{{ route('announcements.destroy', $announcement) }}" onsubmit="return confirm('{{ __('Delete this announcement?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="announcement-delete" type="submit">@include('partials.ui.icon', ['name' => 'trash', 'class' => 'button-icon'])<span>{{ __('Delete') }}</span></button>
                                </form>
                            @endcan
                        @endcanany
                    </div>
                </div>
            </article>
        @empty
            <article class="announcement-empty-state">
                <strong>{{ __('No announcements are available here yet.') }}</strong>
                <p>{{ __('When church leaders share a new update, it will appear here with its image and details.') }}</p>
                <p>{{ __('Until then, stay prayerful and keep close to your church fellowship.') }}</p>
            </article>
        @endforelse
    </div>

    <div class="mt-8">{{ $announcements->links() }}</div>
</section>

@include('panel.announcements._lightbox')
@endsection
