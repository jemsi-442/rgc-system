@extends('layouts.app')

@section('title', $announcement->title . ' - RGC')

@section('content')
<section class="page-banner">
    <div class="page-banner-content">
        <span class="section-kicker !border-white/10 !bg-white/10 !text-rgc-yellow">{{ __('Announcement Details') }}</span>
        <h1 class="mt-5">{{ $announcement->title }}</h1>
        <p class="mt-4 max-w-3xl text-sm leading-7 text-white/82">{{ __('Read the full announcement, view its image, and confirm who received it from one place.') }}</p>
    </div>
</section>

<section class="announcement-detail-shell mt-8">
    <article class="card-rgc announcement-detail-main {{ $announcement->hasPin() ? 'is-pinned' : '' }}">
        <div class="announcement-detail-header">
            <div class="announcement-meta-badges">
                <span class="announcement-audience is-{{ $announcement->audienceVariant() }}">{{ $announcement->audienceLabel() }}</span>
                @if($announcement->hasPin())
                    <span class="announcement-pin-chip">{{ __('Pinned') }}</span>
                @endif
                @if($announcement->isArchived())
                    <span class="announcement-archived-chip">{{ __('Archived') }}</span>
                @endif
                @if($announcement->hasExpiry())
                    <span class="announcement-expiry-chip {{ $announcement->isExpired() ? 'is-expired' : '' }}">
                        {{ $announcement->isExpired() ? __('Expired') : __('Expires :date', ['date' => $announcement->expires_at->translatedFormat('d M Y')]) }}
                    </span>
                @endif
            </div>
            <div class="announcement-actions">
                <a class="btn-rgc-alt" href="{{ route('announcements.index') }}">{{ __('Back to announcements') }}</a>
                <a class="btn-rgc-alt" href="{{ route('announcements.pdf', $announcement) }}">{{ __('Download PDF') }}</a>
                @if($announcement->hasImage())
                    <a class="btn-rgc-alt" href="{{ route('announcements.image', ['announcement' => $announcement, 'download' => 1]) }}">{{ __('Download image') }}</a>
                @endif
                <button
                    class="btn-rgc-alt"
                    type="button"
                    data-share-button
                    data-share-url="{{ route('announcements.show', $announcement) }}"
                    data-share-title="{{ $announcement->title }}"
                    data-share-success="{{ __('Announcement link copied.') }}"
                    data-share-failure="{{ __('Unable to share this announcement right now.') }}"
                >
                    {{ __('Share announcement') }}
                </button>
                <span class="announcement-share-status" data-share-status aria-live="polite"></span>
                @can('update', $announcement)
                    <a class="btn-rgc-alt" href="{{ route('announcements.edit', $announcement) }}">{{ __('Edit') }}</a>
                @endcan
                @can('delete', $announcement)
                    <form method="POST" action="{{ route('announcements.destroy', $announcement) }}" onsubmit="return confirm('{{ __('Delete this announcement?') }}')">
                        @csrf
                        @method('DELETE')
                        <button class="announcement-delete" type="submit">{{ __('Delete') }}</button>
                    </form>
                @endcan
            </div>
        </div>

        <p class="announcement-detail-summary">{{ $announcement->deliverySummary() }}</p>

        @if($announcement->hasImage())
            <button
                class="announcement-detail-image announcement-media-button"
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

        <div class="announcement-detail-copy">
            <span class="section-kicker">{{ __('Full announcement') }}</span>
            @if(filled($announcement->body))
                <div class="announcement-detail-body">{{ $announcement->body }}</div>
            @else
                <p class="announcement-card-copy mt-4">{{ __('This announcement was published as an image-led update without additional body text.') }}</p>
            @endif
        </div>
    </article>

    <aside class="card-rgc announcement-detail-sidebar">
        <span class="section-kicker">{{ __('Audience details') }}</span>
        <div class="announcement-detail-meta mt-5">
            <div>
                <strong>{{ __('Published') }}</strong>
                <p>{{ optional($announcement->created_at)->translatedFormat('d M Y, H:i') }}</p>
            </div>
            <div>
                <strong>{{ __('Audience') }}</strong>
                <p>{{ $announcement->audienceLabel() }}</p>
            </div>
            <div>
                <strong>{{ __('Audience summary') }}</strong>
                <p>{{ $announcement->deliverySummary() }}</p>
            </div>
            <div>
                <strong>{{ __('Created by') }}</strong>
                <p>{{ $announcement->creator?->name ?? __('System') }}</p>
            </div>
            <div>
                <strong>{{ __('Expiry Date') }}</strong>
                <p>
                    @if($announcement->hasExpiry())
                        {{ $announcement->expires_at->translatedFormat('d M Y') }}
                    @else
                        {{ __('No expiry date') }}
                    @endif
                </p>
            </div>
            <div>
                <strong>{{ __('Archive Status') }}</strong>
                <p>{{ $announcement->isArchived() ? __('Archived automatically after expiry') : __('Active announcement') }}</p>
            </div>
            @if($announcement->hasExplicitBranchTargets())
                <div>
                    <strong>{{ __('Selected branches') }}</strong>
                    <div class="announcement-target-list mt-2">
                        @foreach($announcement->targetBranches as $targetBranch)
                            <span>
                                {{ $targetBranch->name }}
                                @if($targetBranch->district || $targetBranch->region)
                                    <small>{{ collect([$targetBranch->district?->name, $targetBranch->region?->name])->filter()->implode(', ') }}</small>
                                @endif
                            </span>
                        @endforeach
                    </div>
                </div>
            @else
                @if($announcement->region)
                    <div>
                        <strong>{{ __('Region') }}</strong>
                        <p>{{ $announcement->region->name }}</p>
                    </div>
                @endif
                @if($announcement->district)
                    <div>
                        <strong>{{ __('District') }}</strong>
                        <p>{{ $announcement->district->name }}</p>
                    </div>
                @endif
                @if($announcement->branch)
                    <div>
                        <strong>{{ __('Branch') }}</strong>
                        <p>{{ $announcement->branch->name }}</p>
                    </div>
                @endif
            @endif
        </div>
    </aside>
</section>

@include('panel.announcements._lightbox')
@endsection
