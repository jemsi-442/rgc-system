@extends('layouts.app')

@section('title', 'Dashboard - RGC')

@section('content')
<section class="page-banner">
    <div class="page-banner-content">
        <span class="section-kicker !border-white/10 !bg-white/10 !text-rgc-yellow">Governance Dashboard</span>
        <h1 class="mt-5">{{ $roleLabel }} Workspace</h1>
        <p class="mt-4 max-w-3xl text-sm leading-7 text-white/82">
            This dashboard is automatically scoped to your governance level. Statistics, announcements, users, and operational shortcuts are filtered according to your approved region, district, or branch authority.
        </p>
    </div>
</section>

<section class="panel-grid cols-3 mt-8">
    <article class="stat-card">
        <span class="stat-label">Users In Scope</span>
        <strong>{{ $stats['users'] }}</strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">Branches In Scope</span>
        <strong>{{ $stats['branches'] }}</strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">Regions Visible</span>
        <strong>{{ $stats['regions'] }}</strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">Districts Visible</span>
        <strong>{{ $stats['districts'] }}</strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">Offerings</span>
        <strong>{{ number_format($stats['offerings'], 2) }}</strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">Expenses</span>
        <strong>{{ number_format($stats['expenses'], 2) }}</strong>
    </article>
</section>

<section class="tablet-stack two mt-8">
    <article class="card-rgc-strong">
        <span class="section-kicker">Operational Shortcuts</span>
        <h2 class="mt-5 font-[family-name:var(--font-display)] text-3xl leading-none">Move quickly through your approved scope.</h2>
        <div class="mt-6 flex flex-wrap gap-3 text-sm">
            <a class="btn-rgc" href="{{ route('announcements.index') }}">View announcements</a>
            <a class="btn-rgc-alt" href="{{ route('messages.index') }}">Open branch chat</a>
            @if(auth()->user()->hasSystemRole('super_admin'))
                <a class="btn-rgc-alt" href="{{ route('branches.index') }}">Manage branches</a>
                <a class="btn-rgc-alt" href="{{ route('sliders.index') }}">Homepage slider</a>
            @endif
            @if(auth()->user()->hasAnySystemRole(['super_admin', 'regional_admin', 'district_admin', 'branch_admin']))
                <a class="btn-rgc-alt" href="{{ route('offerings.index') }}">Offerings</a>
                <a class="btn-rgc-alt" href="{{ route('expenses.index') }}">Expenses</a>
                <a class="btn-rgc-alt" href="{{ route('events.index') }}">Events</a>
            @endif
        </div>
    </article>

    <article class="card-rgc">
        <span class="section-kicker">Current Scope</span>
        @if(auth()->user()->hasSystemRole('regional_admin'))
            <h2 class="mt-5 text-2xl font-semibold">Branches in your region</h2>
            <div class="branch-list mt-5">
                @forelse($scope as $branch)
                    <div class="branch-item">
                        <p class="font-semibold">{{ $branch->name }}</p>
                        <p class="mt-1 text-sm text-black/65">{{ $branch->district->name }}</p>
                    </div>
                @empty
                    <div class="branch-item text-sm text-black/65">No branches found in your region.</div>
                @endforelse
            </div>
        @elseif(auth()->user()->hasSystemRole('district_admin'))
            <h2 class="mt-5 text-2xl font-semibold">Branches in your district</h2>
            <div class="branch-list mt-5">
                @forelse($scope as $branch)
                    <div class="branch-item">
                        <p class="font-semibold">{{ $branch->name }}</p>
                    </div>
                @empty
                    <div class="branch-item text-sm text-black/65">No branches found in your district.</div>
                @endforelse
            </div>
        @elseif(auth()->user()->hasAnySystemRole(['branch_admin', 'pastor', 'bishop', 'accountant']))
            <h2 class="mt-5 text-2xl font-semibold">Users in your branch</h2>
            <div class="branch-list mt-5">
                @forelse($scope as $member)
                    <div class="branch-item">
                        <p class="font-semibold">{{ $member->name }}</p>
                        <p class="mt-1 text-sm text-black/65">{{ $member->email }}</p>
                    </div>
                @empty
                    <div class="branch-item text-sm text-black/65">No users found in your branch.</div>
                @endforelse
            </div>
        @elseif(auth()->user()->hasSystemRole('member'))
            <h2 class="mt-5 text-2xl font-semibold">My branch notices</h2>
            <div class="branch-list mt-5">
                @forelse($scope as $notice)
                    <div class="branch-item">
                        <p class="font-semibold">{{ $notice->title }}</p>
                    </div>
                @empty
                    <div class="branch-item text-sm text-black/65">No branch announcements yet.</div>
                @endforelse
            </div>
        @else
            <h2 class="mt-5 text-2xl font-semibold">Governance overview</h2>
            <p class="mt-4 text-sm leading-7 text-black/68">As data grows, this panel will keep reflecting the exact region, district, or branch operations available to your role.</p>
        @endif
    </article>
</section>

<section class="mt-8 card-rgc">
    <span class="section-kicker">Announcements</span>
    <div class="mt-5 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold">Recent activity across your scope</h2>
            <p class="mt-2 text-sm text-black/65">The latest official communication available to your current governance level.</p>
        </div>
    </div>

    <div class="branch-list mt-6">
        @forelse($announcements as $announcement)
            <article class="branch-item">
                <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                    <div>
                        <p class="text-lg font-semibold">{{ $announcement->title }}</p>
                        <p class="mt-2 text-sm leading-6 text-black/68">{{ \Illuminate\Support\Str::limit($announcement->body, 180) }}</p>
                    </div>
                    <div class="text-sm text-black/55">{{ $announcement->creator->name }}</div>
                </div>
            </article>
        @empty
            <article class="branch-item text-sm text-black/65">No announcements available in your scope yet.</article>
        @endforelse
    </div>

    <div class="mt-6">{{ $announcements->links() }}</div>
</section>
@endsection
