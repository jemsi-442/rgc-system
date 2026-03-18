@extends('layouts.app')

@section('title', __('Dashboard') . ' - RGC')

@section('content')
<section class="page-banner">
    <div class="page-banner-content">
        <span class="section-kicker !border-white/10 !bg-white/10 !text-rgc-yellow">{{ __('Governance Dashboard') }}</span>
        <h1 class="mt-5">{{ $roleLabel }} {{ __('Workspace') }}</h1>
        <p class="mt-4 max-w-3xl text-sm leading-7 text-white/82">
            {{ __('This dashboard is automatically scoped to your governance level. Statistics, announcements, users, and operational shortcuts are filtered according to your approved region, district, or branch authority.') }}
        </p>
    </div>
</section>

<section class="panel-grid cols-3 mt-8">
    <article class="stat-card">
        <span class="stat-label">{{ __('Users In Scope') }}</span>
        <strong>{{ $stats['users'] }}</strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">{{ __('Branches In Scope') }}</span>
        <strong>{{ $stats['branches'] }}</strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">{{ __('Regions Visible') }}</span>
        <strong>{{ $stats['regions'] }}</strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">{{ __('Districts Visible') }}</span>
        <strong>{{ $stats['districts'] }}</strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">{{ __('Offerings') }}</span>
        <strong>{{ number_format($stats['offerings'], 2) }}</strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">{{ __('Expenses') }}</span>
        <strong>{{ number_format($stats['expenses'], 2) }}</strong>
    </article>
</section>

<section class="tablet-stack two mt-8">
    <article class="card-rgc-strong">
        <span class="section-kicker">{{ __('Operational Shortcuts') }}</span>
        <h2 class="mt-5 font-[family-name:var(--font-display)] text-3xl leading-none">{{ __('Move quickly through your approved scope.') }}</h2>
        <div class="shortcut-grid mt-6 text-sm">
            <a class="btn-rgc w-full sm:w-auto" href="{{ route('announcements.index') }}">{{ __('View announcements') }}</a>
            <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('messages.index') }}">{{ __('Open branch chat') }}</a>
            <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('account.password.edit') }}">{{ __('Change my password') }}</a>
            @if(auth()->user()->hasSystemRole('super_admin'))
                <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('admin.users.index') }}">{{ __('Manage users') }}</a>
                <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('branches.index') }}">{{ __('Manage branches') }}</a>
                <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('sliders.index') }}">{{ __('Homepage slider') }}</a>
            @endif
            @if(auth()->user()->hasAnySystemRole(['super_admin', 'regional_admin', 'district_admin', 'branch_admin']))
                <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('offerings.index') }}">{{ __('Offerings') }}</a>
                <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('expenses.index') }}">{{ __('Expenses') }}</a>
                <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('events.index') }}">{{ __('Events') }}</a>
            @endif
        </div>
    </article>

    <article class="card-rgc">
        <span class="section-kicker">{{ __('Current Scope') }}</span>
        @if(auth()->user()->hasSystemRole('regional_admin'))
            <h2 class="mt-5 text-2xl font-semibold">{{ __('Branches in your region') }}</h2>
            <div class="branch-list mt-5">
                @forelse($scope as $branch)
                    <div class="branch-item">
                        <p class="font-semibold">{{ $branch->name }}</p>
                        <p class="mt-1 text-sm text-black/65">{{ $branch->district->name }}</p>
                    </div>
                @empty
                    <div class="branch-item text-sm text-black/65">{{ __('No branches found in your region.') }}</div>
                @endforelse
            </div>
        @elseif(auth()->user()->hasSystemRole('district_admin'))
            <h2 class="mt-5 text-2xl font-semibold">{{ __('Branches in your district') }}</h2>
            <div class="branch-list mt-5">
                @forelse($scope as $branch)
                    <div class="branch-item">
                        <p class="font-semibold">{{ $branch->name }}</p>
                    </div>
                @empty
                    <div class="branch-item text-sm text-black/65">{{ __('No branches found in your district.') }}</div>
                @endforelse
            </div>
        @elseif(auth()->user()->hasAnySystemRole(['branch_admin', 'pastor', 'bishop', 'accountant']))
            <h2 class="mt-5 text-2xl font-semibold">{{ __('Users in your branch') }}</h2>
            <div class="branch-list mt-5">
                @forelse($scope as $member)
                    <div class="branch-item">
                        <p class="font-semibold">{{ $member->name }}</p>
                        <p class="mt-1 text-sm text-black/65 break-all">{{ $member->email }}</p>
                    </div>
                @empty
                    <div class="branch-item text-sm text-black/65">{{ __('No users found in your branch.') }}</div>
                @endforelse
            </div>
        @elseif(auth()->user()->hasSystemRole('member'))
            <h2 class="mt-5 text-2xl font-semibold">{{ __('My branch notices') }}</h2>
            <div class="branch-list mt-5">
                @forelse($scope as $notice)
                    <article class="branch-item dashboard-announcement {{ $notice->hasPin() ? 'is-pinned' : '' }}">
                        <div class="dashboard-announcement-meta">
                            <div class="announcement-meta-badges">
                                <span class="announcement-audience {{ $notice->is_global ? 'is-global' : '' }}">{{ $notice->audienceLabel() }}</span>
                                @if($notice->hasPin())
                                    <span class="announcement-pin-chip">{{ __('Pinned') }}</span>
                                @endif
                            </div>
                            <div class="announcement-meta-trail">
                                @if($notice->hasExpiry())
                                    <span class="announcement-expiry-chip {{ $notice->isExpired() ? 'is-expired' : '' }}">
                                        {{ $notice->isExpired() ? __('Expired') : __('Expires :date', ['date' => $notice->expires_at->translatedFormat('d M Y')]) }}
                                    </span>
                                @endif
                                <span>{{ optional($notice->created_at)->diffForHumans() }}</span>
                            </div>
                        </div>
                        @if($notice->hasImage())
                            <button
                                class="dashboard-announcement-media announcement-media-button"
                                type="button"
                                data-announcement-lightbox-trigger
                                data-image-src="{{ route('announcements.image', $notice) }}"
                                data-image-alt="{{ $notice->title }}"
                                data-image-title="{{ $notice->title }}"
                            >
                                <img src="{{ route('announcements.image', $notice) }}" alt="{{ $notice->title }}">
                                <span class="announcement-media-caption">{{ __('View full image') }}</span>
                            </button>
                        @endif
                        <div>
                            <p class="font-semibold"><a href="{{ route('announcements.show', $notice) }}">{{ $notice->title }}</a></p>
                            @if(filled($notice->body))
                                <p class="mt-2 text-sm text-black/65">{{ \Illuminate\Support\Str::limit($notice->body, 120) }}</p>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="branch-item text-sm text-black/65">{{ __('No branch announcements yet.') }}</div>
                @endforelse
            </div>
        @else
            <h2 class="mt-5 text-2xl font-semibold">{{ __('Governance overview') }}</h2>
            <p class="mt-4 text-sm leading-7 text-black/68">{{ __('As data grows, this panel will keep reflecting the exact region, district, or branch operations available to your role.') }}</p>
        @endif
    </article>
</section>

<section class="mt-8 card-rgc">
    <span class="section-kicker">{{ __('Announcements') }}</span>
    <div class="mt-5 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold">{{ __('Recent activity across your scope') }}</h2>
            <p class="mt-2 text-sm text-black/65">{{ __('The latest official communication available to your current governance level.') }}</p>
        </div>
    </div>

    <div class="announcement-grid mt-6">
        @forelse($announcements as $announcement)
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
                            <span class="announcement-audience {{ $announcement->is_global ? 'is-global' : '' }}">{{ $announcement->audienceLabel() }}</span>
                            @if($announcement->hasPin())
                                <span class="announcement-pin-chip">{{ __('Pinned') }}</span>
                            @endif
                        </div>
                        <div class="announcement-meta-trail">
                            @if($announcement->hasExpiry())
                                <span class="announcement-expiry-chip {{ $announcement->isExpired() ? 'is-expired' : '' }}">
                                    {{ $announcement->isExpired() ? __('Expired') : __('Expires :date', ['date' => $announcement->expires_at->translatedFormat('d M Y')]) }}
                                </span>
                            @endif
                            <span>{{ $announcement->creator?->name ?? __('System') }}</span>
                        </div>
                    </div>
                    <div class="announcement-card-heading">
                        <div>
                            <h3><a href="{{ route('announcements.show', $announcement) }}">{{ $announcement->title }}</a></h3>
                            <p>{{ optional($announcement->created_at)->diffForHumans() }}</p>
                        </div>
                    </div>
                    @if(filled($announcement->body))
                        <p class="announcement-card-copy">{{ \Illuminate\Support\Str::limit($announcement->body, 180) }}</p>
                    @endif
                </div>
            </article>
        @empty
            <article class="announcement-empty-state">
                <strong>{{ __('No announcements available in your scope yet.') }}</strong>
                <p>{{ __('When leaders publish new updates, they will appear here with their delivery scope and details.') }}</p>
            </article>
        @endforelse
    </div>

    <div class="mt-6">{{ $announcements->links() }}</div>
</section>
@include('panel.announcements._lightbox')
@endsection
