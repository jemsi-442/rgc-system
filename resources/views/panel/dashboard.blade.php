@extends('layouts.app')

@section('title', __('Dashboard') . ' - RGC')

@section('content')
@php
    $dashboardUser = auth()->user();
    $isSuperAdmin = $dashboardUser->hasSystemRole('super_admin');
    $isAccountant = $dashboardUser->hasSystemRole('accountant');
    $canCreateBranchPayments = $dashboardUser->hasAnySystemRole(['super_admin', 'branch_admin', 'pastor', 'bishop', 'accountant']);
    $canOpenBranchBooks = $dashboardUser->hasAnySystemRole(['super_admin', 'branch_admin', 'pastor', 'bishop', 'accountant']);
    $isRegionalAdmin = $dashboardUser->hasSystemRole('regional_admin');
    $isDistrictAdmin = $dashboardUser->hasSystemRole('district_admin');
    $paymentRequestHeading = $canCreateBranchPayments ? __('Recent Payment Requests') : __('Recent payment activity');
    $bannerKicker = $isSuperAdmin ? __('Platform Overview') : __('Leadership Dashboard');
    $bannerCopy = $isSuperAdmin
        ? __('See the full church platform at a glance, keep an eye on growth, and move into the tools that shape branches, users, and public presentation.')
        : __('This dashboard is automatically scoped to your role so your numbers, updates, and shortcuts stay aligned with the region, district, or branch you are approved to serve.');

    $activitySeries = collect($charts['activity'] ?? []);
    $activityPeak = max(1, (int) ($charts['activity_peak'] ?? 1));
    $activityChartWidth = 420;
    $activityChartHeight = 220;
    $activityChartPaddingX = 18;
    $activityChartPaddingTop = 22;
    $activityChartPaddingBottom = 44;
    $activityPlotWidth = $activityChartWidth - ($activityChartPaddingX * 2);
    $activityPlotHeight = $activityChartHeight - ($activityChartPaddingTop + $activityChartPaddingBottom);
    $activityDivisor = max(1, $activitySeries->count() - 1);
    $activityCoordinates = $activitySeries->values()->map(function (array $day, int $index) use ($activityChartPaddingX, $activityChartPaddingTop, $activityPlotWidth, $activityPlotHeight, $activityDivisor, $activityPeak) {
        $x = $activityChartPaddingX + (($activityPlotWidth / $activityDivisor) * $index);
        $y = $activityChartPaddingTop + $activityPlotHeight - (((int) $day['count'] / $activityPeak) * $activityPlotHeight);

        return [
            'x' => round($x, 2),
            'y' => round($y, 2),
            'label' => $day['label'],
            'date' => $day['date'],
            'count' => $day['count'],
        ];
    });
    $activityLinePoints = $activityCoordinates
        ->map(fn (array $point) => $point['x'] . ',' . $point['y'])
        ->implode(' ');
    $activityAreaPath = $activityCoordinates->isNotEmpty()
        ? 'M ' . $activityCoordinates->first()['x'] . ' ' . ($activityChartHeight - $activityChartPaddingBottom)
            . ' L ' . $activityCoordinates
                ->map(fn (array $point) => $point['x'] . ' ' . $point['y'])
                ->implode(' L ')
            . ' L ' . $activityCoordinates->last()['x'] . ' ' . ($activityChartHeight - $activityChartPaddingBottom)
            . ' Z'
        : '';
    $statusMix = collect($charts['status_mix'] ?? []);
    $statusTotal = max(1, (int) $statusMix->sum('count'));
    $statusColors = [
        'pending' => '#f0b429',
        'completed' => '#8f1111',
        'failed' => '#2f2f2f',
    ];
    $statusOffset = 0;
    $statusGradient = $statusMix->map(function (array $item) use (&$statusOffset, $statusTotal, $statusColors) {
        $portion = ((int) $item['count'] / $statusTotal) * 100;
        $start = round($statusOffset, 2);
        $end = round($statusOffset + $portion, 2);
        $statusOffset += $portion;
        $color = $statusColors[$item['key']] ?? '#801115';

        return $color . ' ' . $start . '% ' . $end . '%';
    })->implode(', ');
    $statusGradient = $statusGradient !== '' ? $statusGradient : 'rgba(23, 23, 23, 0.12) 0% 100%';
@endphp

<section class="page-banner">
    <div class="page-banner-content">
        <span class="section-kicker border-white/10 bg-white/10 text-rgc-yellow">{{ $bannerKicker }}</span>
        <h1 class="mt-5">{{ $roleLabel }} {{ __('Workspace') }}</h1>
        <p class="mt-4 max-w-3xl text-sm leading-7 text-white/82">
            {{ $bannerCopy }}
        </p>
    </div>
</section>

@if($isSuperAdmin)
<section class="mt-8 admin-focus-card">
    <div class="admin-focus-copy">
        <span class="section-kicker">{{ __('Executive Summary') }}</span>
        <h2 class="mt-5">{{ __('Keep the platform healthy and easy to run.') }}</h2>
        <p class="mt-3">{{ __('Use this space to watch coverage, jump into user and branch administration, and keep public-facing content tidy without scanning every section on the page.') }}</p>
    </div>

    <div class="admin-focus-grid">
        <article class="admin-focus-metric">
            <span>{{ __('Active regions') }}</span>
            <strong>{{ $stats['regions'] }}</strong>
            <p>{{ __('Church-wide coverage currently visible from headquarters.') }}</p>
        </article>
        <article class="admin-focus-metric">
            <span>{{ __('District network') }}</span>
            <strong>{{ $stats['districts'] }}</strong>
            <p>{{ __('District structure connected across the platform.') }}</p>
        </article>
        <article class="admin-focus-metric">
            <span>{{ __('Branch network') }}</span>
            <strong>{{ $stats['branches'] }}</strong>
            <p>{{ __('Branches currently attached to the system.') }}</p>
        </article>
        <article class="admin-focus-metric">
            <span>{{ __('People accounts') }}</span>
            <strong>{{ $stats['users'] }}</strong>
            <p>{{ __('User accounts visible to headquarters oversight.') }}</p>
        </article>
    </div>

    <div class="admin-tools-row">
        <a class="btn-rgc w-full sm:w-auto" href="{{ route('admin.users.index') }}">{{ __('Manage users') }}</a>
        <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('branches.index') }}">{{ __('Manage branches') }}</a>
        <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('sliders.index') }}">{{ __('Homepage slider') }}</a>
        <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('assistant.topics.index') }}">{{ __('Assistant knowledge') }}</a>
    </div>
</section>
@endif

<section class="panel-grid cols-4 mt-8">
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
    <article class="stat-card">
        <span class="stat-label">{{ __('Payment Requests') }}</span>
        <strong>{{ $stats['payment_requests'] }}</strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">{{ __('Pending Payments') }}</span>
        <strong>{{ $stats['pending_payments'] }}</strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">{{ __('Completed Payments') }}</span>
        <strong>{{ $stats['completed_payments'] }}</strong>
    </article>
</section>

@if(auth()->user()->hasAnySystemRole(['super_admin', 'regional_admin', 'district_admin', 'branch_admin', 'pastor', 'bishop', 'accountant']))
<section class="tablet-stack two mt-8">
    <article class="card-rgc dashboard-visual-card">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <span class="section-kicker">{{ __('Activity Trend') }}</span>
                <h2 class="mt-4 text-2xl font-semibold">{{ __('Payment activity in the last 7 days') }}</h2>
                <p class="mt-2 text-sm text-black/65">{{ __('Use this chart to see whether payment prompts and follow-up activity are rising, slowing down, or staying steady in your scope.') }}</p>
            </div>
            <div class="assistant-usage-summary">
                <span class="payment-status-badge is-pending">{{ __('Peak day') }}: {{ $charts['activity_peak'] }}</span>
            </div>
        </div>

        <div class="dashboard-line-chart mt-6">
            <svg viewBox="0 0 {{ $activityChartWidth }} {{ $activityChartHeight }}" class="dashboard-line-chart-svg" role="img" aria-label="{{ __('Payment activity trend chart') }}">
                <defs>
                    <linearGradient id="dashboard-activity-fill" x1="0" x2="0" y1="0" y2="1">
                        <stop offset="0%" stop-color="rgba(192, 0, 0, 0.38)" />
                        <stop offset="100%" stop-color="rgba(255, 215, 0, 0.04)" />
                    </linearGradient>
                </defs>

                <line x1="{{ $activityChartPaddingX }}" y1="{{ $activityChartHeight - $activityChartPaddingBottom }}" x2="{{ $activityChartWidth - $activityChartPaddingX }}" y2="{{ $activityChartHeight - $activityChartPaddingBottom }}" class="dashboard-line-chart-axis" />

                @if($activityAreaPath !== '')
                    <path d="{{ $activityAreaPath }}" fill="url(#dashboard-activity-fill)" class="dashboard-line-chart-area" />
                    <polyline points="{{ $activityLinePoints }}" fill="none" class="dashboard-line-chart-path" />
                @endif

                @foreach($activityCoordinates as $point)
                    <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="4.5" class="dashboard-line-chart-dot" />
                    <text x="{{ $point['x'] }}" y="{{ $point['y'] - 12 }}" text-anchor="middle" class="dashboard-line-chart-count">{{ $point['count'] }}</text>
                    <text x="{{ $point['x'] }}" y="{{ $activityChartHeight - 18 }}" text-anchor="middle" class="dashboard-line-chart-label">{{ $point['label'] }}</text>
                @endforeach
            </svg>
        </div>
    </article>

    <article class="card-rgc dashboard-mix-card">
        <div class="dashboard-ring-layout">
            <div>
                <span class="section-kicker">{{ __('Payment Status') }}</span>
                <h2 class="mt-4 text-2xl font-semibold">{{ __('Outcome mix across your scope') }}</h2>
                <p class="mt-2 text-sm text-black/65">{{ __('This donut helps you read the balance between pending, completed, and failed payment outcomes at a glance.') }}</p>
            </div>

            <div class="dashboard-status-donut-block">
                <div class="dashboard-status-donut" style="background: conic-gradient({{ $statusGradient }})">
                    <div class="dashboard-status-donut-center">
                        <strong>{{ $statusTotal }}</strong>
                        <span>{{ __('Total') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-status-inline mt-6">
            @foreach($charts['status_mix'] as $statusItem)
                <div class="dashboard-status-pill">
                    <span class="payment-status-badge is-{{ $statusItem['key'] }}">{{ $statusItem['label'] }}</span>
                    <strong>{{ $statusItem['count'] }}</strong>
                </div>
            @endforeach
        </div>

        <div class="dashboard-mix-list mt-6">
            @foreach($charts['finance_mix'] as $item)
                <div class="dashboard-mix-row">
                    <div class="dashboard-mix-labels">
                        <strong>{{ $item['label'] }}</strong>
                        <span>TZS {{ $item['display_value'] }}</span>
                    </div>
                    <div class="dashboard-mix-bar-shell">
                        <span class="dashboard-mix-bar is-{{ $item['key'] }}" style="width: {{ $item['width'] }}%"></span>
                    </div>
                </div>
            @endforeach
        </div>
    </article>
</section>
@endif

@if(auth()->user()->hasAnySystemRole(['super_admin', 'regional_admin', 'district_admin', 'branch_admin', 'pastor', 'bishop', 'accountant']))
<section class="mt-8 card-rgc">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <span class="section-kicker">{{ __('Snippe payments') }}</span>
            <h2 class="mt-5 text-2xl font-semibold">{{ $paymentRequestHeading }}</h2>
            <p class="mt-2 text-sm text-black/65">
                {{ $canCreateBranchPayments
                    ? __('Monitor direct payment prompts that are still waiting for approval and confirmed collections already posted into offerings.')
                    : __('Review recent payment activity across the church locations visible to your role.') }}
            </p>
        </div>
        @if($canCreateBranchPayments)
            <a class="btn-rgc-outline w-full sm:w-auto" href="{{ route('offerings.create') }}">{{ __('Send payment prompt') }}</a>
        @endif
    </div>

    <div class="payment-request-grid mt-6">
        @forelse($recentPayments as $payment)
            <article class="payment-request-card">
                <div class="payment-request-topline">
                    <span class="payment-status-badge is-{{ $payment->status }}">{{ $payment->statusLabel() }}</span>
                    <span class="text-xs text-black/50">{{ $payment->public_reference }}</span>
                </div>
                <h3>TZS {{ number_format((float) $payment->amount, 2) }}</h3>
                <p>{{ $payment->description ?: __('Offering payment') }}</p>
                <div class="payment-request-meta">
                    <span>{{ $payment->branch?->name }}</span>
                    <span>{{ optional($payment->created_at)->diffForHumans() }}</span>
                </div>
                <div class="payment-request-actions">
                    <a class="btn-rgc-outline w-full sm:w-auto" href="{{ route('offerings.payments.public.show', $payment->public_reference) }}">{{ __('Status page') }}</a>
                    @if($payment->checkout_url && $payment->isPending())
                        <a class="btn-rgc w-full sm:w-auto" href="{{ $payment->checkout_url }}" target="_blank" rel="noopener">{{ __('Open checkout') }}</a>
                    @elseif($payment->isPending())
                        <span class="btn-rgc w-full sm:w-auto pointer-events-none opacity-80">{{ __('Prompt sent') }}</span>
                    @endif
                </div>
            </article>
        @empty
            <article class="announcement-empty-state">
                <strong>{{ $canCreateBranchPayments ? __('No Snippe payment requests created yet.') : __('No recent payment activity yet.') }}</strong>
                <p>
                    {{ $canCreateBranchPayments
                        ? __('Create your first payment prompt from the offerings workspace when you want donors to pay through mobile money.')
                        : __('Payment prompts and collections from the branches in your scope will appear here when there is fresh activity.') }}
                </p>
            </article>
        @endforelse
    </div>
</section>
@endif

@if(auth()->user()->hasAnySystemRole(['super_admin', 'regional_admin', 'district_admin', 'branch_admin', 'pastor', 'bishop', 'accountant']))
<section class="mt-8 card-rgc">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <span class="section-kicker">{{ __('Payment alerts') }}</span>
            <h2 class="mt-5 text-2xl font-semibold">{{ __('Collections and checkout activity') }}</h2>
            <p class="mt-2 text-sm text-black/65">{{ __('This panel helps you spot newly completed collections, pending links, and failed payments across your approved scope.') }}</p>
        </div>
        @if($paymentAlerts->isNotEmpty())
            <form method="POST" action="{{ route('offerings.payments.review-all') }}" class="w-full sm:w-auto">
                @csrf
                @method('PATCH')
                <button class="btn-rgc-outline w-full sm:w-auto" type="submit">{{ __('Mark all as reviewed') }}</button>
            </form>
        @endif
    </div>

    <div class="payment-alert-grid mt-6">
        @forelse($paymentAlerts as $payment)
            <article class="payment-alert-card is-{{ $payment->status }}">
                <div class="payment-request-topline">
                    <span class="payment-status-badge is-{{ $payment->status }}">{{ $payment->statusLabel() }}</span>
                    <span class="text-xs text-black/50">{{ $payment->public_reference }}</span>
                </div>
                <h3>
                    @if($payment->status === 'completed')
                        {{ __('New collection received') }}
                    @elseif($payment->status === 'failed')
                        {{ __('Payment failed') }}
                    @else
                        {{ __('Pending payment link') }}
                    @endif
                </h3>
                <p>{{ $payment->paymentTypeLabel() }} · TZS {{ number_format((float) $payment->amount, 2) }}</p>
                <div class="payment-request-meta">
                    <span>{{ $payment->branch?->name ?? __('Unknown branch') }}</span>
                    <span>{{ $payment->payer_name ?: $payment->user?->name ?: __('Unknown payer') }}</span>
                </div>
                <div class="payment-request-meta">
                    <span>{{ optional($payment->updated_at)->diffForHumans() }}</span>
                    @if($payment->receipt_emailed_at)
                        <span>{{ __('Receipt emailed') }}</span>
                    @endif
                </div>
                <div class="payment-request-actions">
                    <a class="btn-rgc-outline w-full sm:w-auto" href="{{ route('offerings.payments.public.show', $payment->public_reference) }}">{{ __('Status page') }}</a>
                    <form method="POST" action="{{ route('offerings.payments.review', $payment) }}" class="w-full sm:w-auto">
                        @csrf
                        @method('PATCH')
                        <button class="btn-rgc-outline w-full sm:w-auto" type="submit">{{ __('Mark as reviewed') }}</button>
                    </form>
                    @if($payment->checkout_url && $payment->isPending())
                        <a class="btn-rgc w-full sm:w-auto" href="{{ $payment->checkout_url }}" target="_blank" rel="noopener">{{ __('Open checkout') }}</a>
                    @elseif($payment->isPending())
                        <span class="btn-rgc w-full sm:w-auto pointer-events-none opacity-80">{{ __('Prompt sent') }}</span>
                    @elseif($payment->isCompleted() && $canOpenBranchBooks)
                        <a class="btn-rgc w-full sm:w-auto" href="{{ route('offerings.index') }}">{{ __('Open offerings') }}</a>
                    @endif
                </div>
            </article>
        @empty
            <article class="announcement-empty-state">
                <strong>{{ __('No payment alerts right now.') }}</strong>
                <p>{{ __('Completed collections, active payment links, and failed checkout attempts will appear here when there is fresh activity in your scope.') }}</p>
            </article>
        @endforelse
    </div>
</section>
@endif

<section class="tablet-stack two mt-8">
    <article class="card-rgc-strong">
        <span class="section-kicker">{{ __('Operational Shortcuts') }}</span>
        <h2 class="mt-5 font-[family-name:var(--font-display)] text-3xl leading-none">
            {{ $isSuperAdmin ? __('Platform actions and everyday tools.') : __('Move quickly through your approved scope.') }}
        </h2>
        <div class="shortcut-grid mt-6 text-sm">
            <a class="btn-rgc w-full sm:w-auto" href="{{ route('announcements.index') }}">{{ __('View announcements') }}</a>
            <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('messages.index') }}">{{ __('Open branch chat') }}</a>
            <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('account.password.edit') }}">{{ __('Change my password') }}</a>
            <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('giving.index') }}">{{ __('Give now') }}</a>
            @if(auth()->user()->hasSystemRole('super_admin'))
                <div class="admin-secondary-links">
                    <a href="{{ route('admin.users.index') }}">{{ __('Manage users') }}</a>
                    <a href="{{ route('branches.index') }}">{{ __('Manage branches') }}</a>
                    <a href="{{ route('sliders.index') }}">{{ __('Homepage slider') }}</a>
                </div>
            @endif
            @if(auth()->user()->hasAnySystemRole(['super_admin', 'regional_admin']))
                @if(! $isSuperAdmin)
                    <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('assistant.topics.index') }}">{{ __('Assistant knowledge') }}</a>
                @endif
            @endif
            @if($canOpenBranchBooks)
                <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('offerings.index') }}">{{ __('Offerings') }}</a>
                <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('expenses.index') }}">{{ __('Expenses') }}</a>
                @if(! $isAccountant)
                    <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('events.index') }}">{{ __('Events') }}</a>
                @endif
            @endif
        </div>
    </article>

    <article class="card-rgc">
        <span class="section-kicker">{{ __('Current Scope') }}</span>
        @if($isSuperAdmin)
            <h2 class="mt-5 text-2xl font-semibold">{{ __('Regions across the platform') }}</h2>
            <p class="mt-2 text-sm text-black/65">{{ __('This overview keeps the full church map close at hand so you can see where districts and active branches are already connected.') }}</p>
            <div class="branch-list mt-5">
                @forelse($scope as $region)
                    <div class="branch-item">
                        <p class="font-semibold">{{ $region->name }}</p>
                        <p class="mt-1 text-sm text-black/65">
                            {{ trans_choice(':count district|:count districts', $region->districts_count, ['count' => $region->districts_count]) }}
                            ·
                            {{ trans_choice(':count active branch|:count active branches', $region->active_branches_count, ['count' => $region->active_branches_count]) }}
                        </p>
                    </div>
                @empty
                    <div class="branch-item text-sm text-black/65">{{ __('No regions have been configured yet.') }}</div>
                @endforelse
            </div>
        @elseif($isRegionalAdmin)
            <h2 class="mt-5 text-2xl font-semibold">{{ __('Districts in your region') }}</h2>
            <p class="mt-2 text-sm text-black/65">{{ __('This summary helps you understand district coverage first, then follow branch activity through announcements, users, and payment activity.') }}</p>
            <div class="branch-list mt-5">
                @forelse($scope as $district)
                    <div class="branch-item">
                        <p class="font-semibold">{{ $district->name }}</p>
                        <p class="mt-1 text-sm text-black/65">{{ trans_choice(':count active branch|:count active branches', $district->active_branches_count, ['count' => $district->active_branches_count]) }}</p>
                    </div>
                @empty
                    <div class="branch-item text-sm text-black/65">{{ __('No districts found in your region yet.') }}</div>
                @endforelse
            </div>
        @elseif($isDistrictAdmin)
            <h2 class="mt-5 text-2xl font-semibold">
                {{ __('Branches in :district', ['district' => $dashboardUser->district?->name ?: __('your district')]) }}
            </h2>
            <p class="mt-2 text-sm text-black/65">{{ __('This list shows the church locations currently connected to your district so you can follow announcements, users, and payment activity without leaving your coverage area.') }}</p>
            <div class="branch-list mt-5">
                @forelse($scope as $branch)
                    <div class="branch-item">
                        <p class="font-semibold">{{ $branch->name }}</p>
                        <p class="mt-1 text-sm text-black/65">{{ ucfirst($branch->status ?? 'active') }}</p>
                    </div>
                @empty
                    <div class="branch-item text-sm text-black/65">{{ __('No branches found in your district.') }}</div>
                @endforelse
            </div>
        @elseif($isAccountant)
            <h2 class="mt-5 text-2xl font-semibold">
                {{ __('Finance desk for :branch', ['branch' => $dashboardUser->branch?->name ?: __('your branch')]) }}
            </h2>
            <p class="mt-2 text-sm text-black/65">{{ __('Keep the branch books close at hand with a quick view of payment follow-up, recorded collections, and recorded expenses.') }}</p>
            <div class="branch-list mt-5">
                <div class="branch-item">
                    <p class="font-semibold">{{ __('Pending payment requests') }}</p>
                    <p class="mt-1 text-sm text-black/65">{{ __(':count waiting for follow-up', ['count' => $stats['pending_payments']]) }}</p>
                </div>
                <div class="branch-item">
                    <p class="font-semibold">{{ __('Completed payments') }}</p>
                    <p class="mt-1 text-sm text-black/65">{{ __(':count confirmed in your branch scope', ['count' => $stats['completed_payments']]) }}</p>
                </div>
                <div class="branch-item">
                    <p class="font-semibold">{{ __('Offerings recorded') }}</p>
                    <p class="mt-1 text-sm text-black/65">{{ __('TZS :amount', ['amount' => number_format($stats['offerings'], 2)]) }}</p>
                </div>
                <div class="branch-item">
                    <p class="font-semibold">{{ __('Expenses recorded') }}</p>
                    <p class="mt-1 text-sm text-black/65">{{ __('TZS :amount', ['amount' => number_format($stats['expenses'], 2)]) }}</p>
                </div>
            </div>
        @elseif(auth()->user()->hasAnySystemRole(['branch_admin', 'pastor', 'bishop']))
            <h2 class="mt-5 text-2xl font-semibold">
                {{ __('People in :branch', ['branch' => $dashboardUser->branch?->name ?: __('your branch')]) }}
            </h2>
            <p class="mt-2 text-sm text-black/65">{{ __('This quick roster helps you confirm who is currently attached to your branch before you move into announcements, records, or branch chat.') }}</p>
            <div class="branch-list mt-5">
                @forelse($scope as $member)
                    <div class="branch-item">
                        <p class="font-semibold">{{ $member->name }}</p>
                        <p class="mt-1 text-sm text-black/65">
                            {{ __(str($member->normalizedRoleName() ?: 'member')->replace('_', ' ')->title()->toString()) }}
                            ·
                            {{ ucfirst($member->status ?? 'active') }}
                        </p>
                        <p class="mt-1 text-sm text-black/65 break-all">{{ $member->email }}</p>
                    </div>
                @empty
                    <div class="branch-item text-sm text-black/65">{{ __('No users found in your branch.') }}</div>
                @endforelse
            </div>
        @elseif(auth()->user()->hasSystemRole('member'))
            <h2 class="mt-5 text-2xl font-semibold">
                {{ __('Notices for :branch', ['branch' => $dashboardUser->branch?->name ?: __('your branch')]) }}
            </h2>
            <p class="mt-2 text-sm text-black/65">{{ __('Updates from your branch will appear here together with your recent giving activity so you can keep up without opening several pages.') }}</p>
            <div class="announcement-callout mt-5 space-y-3">
                <p class="font-semibold text-black">{{ __('Ready to give to your branch?') }}</p>
                <p class="text-sm text-black/70">{{ __('Open the member giving workspace to create a secure payment link for sadaka, offerings, thanksgiving, and other approved contributions.') }}</p>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <a class="btn-rgc w-full sm:w-auto" href="{{ route('giving.index') }}">{{ __('Give now') }}</a>
                </div>
            </div>
            @if($memberPayments->isNotEmpty())
                <div class="payment-request-grid mt-5">
                    @foreach($memberPayments as $payment)
                        <article class="payment-request-card">
                            <div class="payment-request-topline">
                                <span class="payment-status-badge is-{{ $payment->status }}">{{ $payment->statusLabel() }}</span>
                                <span class="text-xs text-black/50">{{ $payment->public_reference }}</span>
                            </div>
                            <h3>TZS {{ number_format((float) $payment->amount, 2) }}</h3>
                            <p>{{ $payment->paymentTypeLabel() }}</p>
                            <div class="payment-request-actions">
                                <a class="btn-rgc-outline w-full sm:w-auto" href="{{ route('offerings.payments.public.show', $payment->public_reference) }}">{{ __('Status page') }}</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
            <div class="branch-list mt-5">
                @forelse($scope as $notice)
                    <article class="branch-item dashboard-announcement {{ $notice->hasPin() ? 'is-pinned' : '' }}">
                        <div class="dashboard-announcement-meta">
                            <div class="announcement-meta-badges">
                                <span class="announcement-audience is-{{ $notice->audienceVariant() }}">{{ $notice->audienceLabel() }}</span>
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
                            <p class="announcement-delivery-summary announcement-delivery-summary--compact">{{ $notice->deliverySummary() }}</p>
                            @if(filled($notice->body))
                                <p class="mt-2 text-sm text-black/65">{{ str($notice->body)->limit(120) }}</p>
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
                    @endif
                    @if(filled($announcement->body))
                        <p class="announcement-card-copy">{{ str($announcement->body)->limit(180) }}</p>
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
