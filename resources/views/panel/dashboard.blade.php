@extends('layouts.app')

@section('title', __('Dashboard') . ' - RGC')

@section('content')
@php
    $dashboardUser = auth()->user();
    $isMember = $dashboardUser->hasSystemRole('member');
    $isSuperAdmin = $dashboardUser->hasSystemRole('super_admin');
    $isAccountant = $dashboardUser->hasSystemRole('accountant');
    $canCreateBranchPayments = $dashboardUser->hasAnySystemRole(['super_admin', 'branch_admin', 'pastor', 'bishop', 'accountant']);
    $canOpenBranchBooks = $dashboardUser->hasAnySystemRole(['super_admin', 'branch_admin', 'pastor', 'bishop', 'accountant']);
    $isRegionalAdmin = $dashboardUser->hasSystemRole('regional_admin');
    $isDistrictAdmin = $dashboardUser->hasSystemRole('district_admin');
    $paymentRequestHeading = $canCreateBranchPayments ? __('Recent Giving Requests') : __('Recent Giving Activity');
    $bannerKicker = match (true) {
        $isMember => __('Branch Home'),
        $isSuperAdmin => __('Church Overview'),
        default => __('Church Leadership'),
    };
    $bannerCopy = match (true) {
        $isMember => __('Welcome back. Start with a calm view of your branch, then continue into giving, announcements, or branch chat.'),
        $isSuperAdmin => __('Welcome back. Start with a calm view of the church, then move into the page you need.'),
        default => __('Welcome back. Start here, then move into the page that matches today’s church work.'),
    };
    $overviewHeading = $isMember
        ? __('A calm view of your branch life')
        : __('Important church signals, arranged clearly');
    $overviewCopy = $isMember
        ? __('Start here when you want a quick sense of what is active around you before you move into giving, branch chat, or notices.')
        : __('See the clearest signals here first, then continue into the page you need.');
    $welcomeGuidance = match (true) {
        $isSuperAdmin => [
            'title' => __('Welcome to church oversight'),
            'body' => __('Begin here, then continue into users, branches, giving, or announcements as needed.'),
            'scripture' => __('Whatever you do, work heartily, as for the Lord.'),
            'reference' => __('Colossians 3:23'),
        ],
        $isRegionalAdmin => [
            'title' => __('Welcome to your region'),
            'body' => __('Begin here, then continue into the page that helps you serve districts, branches, and church updates.'),
            'scripture' => __('Let all that you do be done in love.'),
            'reference' => __('1 Corinthians 16:14'),
        ],
        $isDistrictAdmin => [
            'title' => __('Welcome to your district'),
            'body' => __('Use this home page for a clear district snapshot, then move into the page you need.'),
            'scripture' => __('Be steadfast, immovable, always abounding in the work of the Lord.'),
            'reference' => __('1 Corinthians 15:58'),
        ],
        $isAccountant => [
            'title' => __('Welcome to your branch books'),
            'body' => __('Keep this page light, then move into offerings or expenses when you are ready to work in detail.'),
            'scripture' => __('It is required of stewards that they be found faithful.'),
            'reference' => __('1 Corinthians 4:2'),
        ],
        $isMember => [
            'title' => __('Welcome to your branch home'),
            'body' => __('Let this page welcome you and point you into branch chat, giving, notices, and the next church moments.'),
            'scripture' => __('I was glad when they said to me, “Let us go to the house of the Lord!”'),
            'reference' => __('Psalm 122:1'),
        ],
        default => [
            'title' => __('Welcome to your church home'),
            'body' => __('Use this page as a calm place to begin, then continue into the page that supports your service today.'),
            'scripture' => __('Let all that you do be done in love.'),
            'reference' => __('1 Corinthians 16:14'),
        ],
    };

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
    $memberTrendSeries = collect(data_get($memberDashboard, 'charts.trend', collect()));
    $memberTrendPeak = max(1, (int) data_get($memberDashboard, 'charts.trend_peak', 1));
    $memberTrendWidth = 360;
    $memberTrendHeight = 180;
    $memberTrendPaddingX = 18;
    $memberTrendPaddingTop = 16;
    $memberTrendPaddingBottom = 34;
    $memberTrendPlotWidth = $memberTrendWidth - ($memberTrendPaddingX * 2);
    $memberTrendPlotHeight = $memberTrendHeight - ($memberTrendPaddingTop + $memberTrendPaddingBottom);
    $memberTrendDivisor = max(1, $memberTrendSeries->count() - 1);
    $memberTrendCoordinates = $memberTrendSeries->values()->map(function (array $month, int $index) use ($memberTrendPaddingX, $memberTrendPaddingTop, $memberTrendPlotWidth, $memberTrendPlotHeight, $memberTrendDivisor, $memberTrendPeak) {
        $x = $memberTrendPaddingX + (($memberTrendPlotWidth / $memberTrendDivisor) * $index);
        $y = $memberTrendPaddingTop + $memberTrendPlotHeight - (((float) $month['amount'] / $memberTrendPeak) * $memberTrendPlotHeight);

        return [
            'x' => round($x, 2),
            'y' => round($y, 2),
            'label' => $month['label'],
            'display_amount' => $month['display_amount'],
        ];
    });
    $memberTrendPoints = $memberTrendCoordinates
        ->map(fn (array $point) => $point['x'] . ',' . $point['y'])
        ->implode(' ');
    $memberTrendAreaPath = $memberTrendCoordinates->isNotEmpty()
        ? 'M ' . $memberTrendCoordinates->first()['x'] . ' ' . ($memberTrendHeight - $memberTrendPaddingBottom)
            . ' L ' . $memberTrendCoordinates
                ->map(fn (array $point) => $point['x'] . ' ' . $point['y'])
                ->implode(' L ')
            . ' L ' . $memberTrendCoordinates->last()['x'] . ' ' . ($memberTrendHeight - $memberTrendPaddingBottom)
            . ' Z'
        : '';
    $memberMix = collect(data_get($memberDashboard, 'charts.mix', collect()));
    $memberMixTotal = max(1, (int) $memberMix->sum('count'));
    $memberMixPalette = ['#8f1111', '#f0b429', '#171717', '#c86f2d'];
    $memberMixOffset = 0;
    $memberMixGradient = $memberMix->values()->map(function (array $item, int $index) use (&$memberMixOffset, $memberMixTotal, $memberMixPalette) {
        $portion = ((int) $item['count'] / $memberMixTotal) * 100;
        $start = round($memberMixOffset, 2);
        $end = round($memberMixOffset + $portion, 2);
        $memberMixOffset += $portion;
        $color = $memberMixPalette[$index] ?? '#8f1111';

        return $color . ' ' . $start . '% ' . $end . '%';
    })->implode(', ');
    $memberMixGradient = $memberMixGradient !== '' ? $memberMixGradient : 'rgba(23, 23, 23, 0.12) 0% 100%';
    $prayerPrefill = __('Prayer request: Hello leadership team, I would like prayer support for ');
    $followUpPrefill = __('Follow-up request: Hello leadership team, I would appreciate a follow-up conversation about ');
    $dashboardStatCards = $isMember
        ? [
            ['label' => __('Branch notices'), 'value' => $scope->count()],
            ['label' => __('Upcoming moments'), 'value' => $memberDashboard['upcoming_events']->count()],
            ['label' => __('This month'), 'value' => 'TZS ' . number_format((float) ($memberDashboard['giving']['month_total'] ?? 0), 2)],
            ['label' => __('All contributions'), 'value' => $memberDashboard['giving']['count'] ?? 0],
        ]
        : [
            ['label' => __('People in view'), 'value' => $stats['users']],
            ['label' => __('Branches in view'), 'value' => $stats['branches']],
            ['label' => __('Pending payments'), 'value' => $stats['pending_payments']],
            ['label' => __('Completed payments'), 'value' => $stats['completed_payments']],
        ];
    $scopePreview = collect($scope)->take($isSuperAdmin ? 4 : 5);
    $memberNoticePreview = collect($scope)->take(4);
    $announcementPreview = collect($announcements->items())->take(3);
    $memberBranchName = $dashboardUser->branch?->name ?: __('your branch');
    $memberLastPayment = data_get($memberDashboard, 'giving.last_payment');
@endphp

<section class="page-banner">
    <div class="page-banner-content">
        <span class="section-kicker section-kicker--icon border-white/10 bg-white/10 text-rgc-yellow">@include('partials.ui.icon', ['name' => $isMember ? 'home' : 'sparkles', 'class' => 'section-kicker-icon'])<span>{{ $bannerKicker }}</span></span>
        <h1 class="mt-5">{{ $roleLabel }} {{ __('Home') }}</h1>
        <p class="mt-4 max-w-3xl text-sm leading-7 text-white/82">
            {{ $bannerCopy }}
        </p>
        <div class="dashboard-quick-nav mt-6">
            <a href="#dashboard-overview">{{ __('At a Glance') }}</a>
            <a href="#dashboard-actions">{{ __('Quick Actions') }}</a>
            @if(! $isMember)
                <a href="#dashboard-notices">{{ __('Updates') }}</a>
            @endif
        </div>
    </div>
</section>

<section class="mt-8 card-rgc-strong">
    <span class="section-kicker">{{ __('Welcome') }}</span>
    <h2 class="mt-5 font-[family-name:var(--font-display)] text-3xl leading-none">{{ $welcomeGuidance['title'] }}</h2>
    <p class="mt-4 max-w-3xl text-sm leading-7 text-white/84">{{ $welcomeGuidance['body'] }}</p>
    <div class="member-scripture-callout mt-6">
        <p class="member-scripture-text">"{{ $welcomeGuidance['scripture'] }}"</p>
        <span class="member-scripture-reference">{{ $welcomeGuidance['reference'] }}</span>
    </div>
</section>

@if($isMember)
<section class="member-home-grid mt-8">
    <article class="member-home-hero">
        <div class="member-home-hero-copy">
            <span class="section-kicker !border-white/10 !bg-white/10 !text-rgc-yellow">{{ __('Today’s Encouragement') }}</span>
            <h3>{{ data_get($memberDashboard, 'encouragement.title') }}</h3>
            <p>{{ data_get($memberDashboard, 'encouragement.body') }}</p>

            <div class="member-home-chips">
                <span>{{ __('Branch: :branch', ['branch' => $memberBranchName]) }}</span>
                <span>{{ __('Notices: :count', ['count' => $memberNoticePreview->count()]) }}</span>
                <span>{{ __('Upcoming moments: :count', ['count' => $memberDashboard['upcoming_events']->count()]) }}</span>
            </div>

            <div class="member-scripture-panel">
                <p class="member-scripture-text">"{{ data_get($memberDashboard, 'encouragement.verse') }}"</p>
                <span class="member-scripture-reference">{{ data_get($memberDashboard, 'encouragement.reference') }}</span>
                <p class="member-spiritual-action">{{ data_get($memberDashboard, 'encouragement.action') }}</p>
            </div>
        </div>
    </article>

    <article class="member-giving-card">
        <div class="member-card-topline">
            <div>
                <span class="section-kicker">{{ __('Giving Snapshot') }}</span>
                <h3 class="mt-4 text-2xl font-semibold">{{ __('A simple view of your recent giving') }}</h3>
            </div>
            <span class="member-card-mark">{{ __('Member Giving') }}</span>
        </div>

        <div class="member-giving-stats mt-5">
            <div class="member-giving-stat">
                <span>{{ __('This month') }}</span>
                <strong>TZS {{ number_format((float) ($memberDashboard['giving']['month_total'] ?? 0), 2) }}</strong>
            </div>
            <div class="member-giving-stat">
                <span>{{ __('All contributions') }}</span>
                <strong>{{ $memberDashboard['giving']['count'] ?? 0 }}</strong>
            </div>
        </div>

        <p class="text-sm text-black/68">
            @if($memberLastPayment)
                {{ __('Your last giving was :type for TZS :amount on :date.', [
                    'type' => $memberLastPayment->paymentTypeLabel(),
                    'amount' => number_format((float) $memberLastPayment->amount, 2),
                    'date' => optional($memberLastPayment->created_at)->translatedFormat('d M Y'),
                ]) }}
            @else
                {{ __('You have not sent a giving prompt yet. When you are ready, you can start from here.') }}
            @endif
        </p>

        <div class="member-giving-actions mt-5">
            <a class="btn-rgc w-full sm:w-auto" href="{{ route('giving.index') }}">@include('partials.ui.icon', ['name' => 'giving', 'class' => 'button-icon'])<span>{{ __('Give now') }}</span></a>
            @if($memberLastPayment)
                <a class="btn-rgc-outline w-full sm:w-auto" href="{{ route('offerings.payments.public.show', $memberLastPayment->public_reference) }}">
                    @include('partials.ui.icon', ['name' => 'eye', 'class' => 'button-icon'])<span>{{ __('Latest status') }}</span>
                </a>
            @else
                <span class="btn-rgc-outline w-full sm:w-auto opacity-70 pointer-events-none">@include('partials.ui.icon', ['name' => 'eye', 'class' => 'button-icon'])<span>{{ __('Latest status') }}</span></span>
            @endif
        </div>
    </article>
</section>

<section class="member-insight-grid mt-8">
    <article class="card-rgc dashboard-visual-card">
        <span class="section-kicker">{{ __('Giving Journey') }}</span>
        <h3 class="mt-4 text-2xl font-semibold">{{ __('Your last six months at a glance') }}</h3>
        <p class="mt-2 text-sm text-black/65">{{ __('This trend helps you see your recent giving rhythm over time.') }}</p>

        <div class="dashboard-line-chart mt-5">
            <svg class="dashboard-line-chart-svg" viewBox="0 0 {{ $memberTrendWidth }} {{ $memberTrendHeight }}" role="img" aria-label="{{ __('Giving trend for the last six months') }}">
                <line class="dashboard-line-chart-axis" x1="{{ $memberTrendPaddingX }}" y1="{{ $memberTrendHeight - $memberTrendPaddingBottom }}" x2="{{ $memberTrendWidth - $memberTrendPaddingX }}" y2="{{ $memberTrendHeight - $memberTrendPaddingBottom }}"></line>
                @if($memberTrendAreaPath !== '')
                    <path d="{{ $memberTrendAreaPath }}" fill="rgba(143, 17, 17, 0.12)"></path>
                @endif
                @if($memberTrendPoints !== '')
                    <polyline class="dashboard-line-chart-path" fill="none" points="{{ $memberTrendPoints }}"></polyline>
                @endif
                @foreach($memberTrendCoordinates as $point)
                    <circle class="dashboard-line-chart-dot" cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="4.2"></circle>
                    <text class="dashboard-line-chart-label" x="{{ $point['x'] }}" y="{{ $memberTrendHeight - 12 }}" text-anchor="middle">{{ $point['label'] }}</text>
                @endforeach
            </svg>
        </div>
    </article>

    <article class="card-rgc dashboard-mix-card">
        <span class="section-kicker">{{ __('Contribution Mix') }}</span>
        <h3 class="mt-4 text-2xl font-semibold">{{ __('How your giving is distributed') }}</h3>
        <p class="mt-2 text-sm text-black/65">{{ __('A quick summary of the giving types you have used most recently.') }}</p>

        <div class="dashboard-ring-layout member-mix-layout mt-5">
            <div class="dashboard-status-donut-block">
                <div class="dashboard-status-donut" style="background: conic-gradient({{ $memberMixGradient }});">
                    <div class="dashboard-status-donut-center">
                        <strong>{{ $memberMix->sum('count') }}</strong>
                        <span>{{ __('Entries') }}</span>
                    </div>
                </div>
            </div>

            <div class="dashboard-mix-list">
                @forelse($memberMix as $index => $item)
                    <div class="dashboard-mix-row">
                        <div class="dashboard-mix-labels">
                            <strong>{{ $item['label'] }}</strong>
                            <span>TZS {{ number_format((float) $item['amount'], 2) }}</span>
                        </div>
                        <div class="dashboard-mix-bar-shell">
                            <span class="dashboard-mix-bar" style="width: {{ $memberMixTotal > 0 ? max(10, (int) round(($item['count'] / $memberMixTotal) * 100)) : 0 }}%; background: {{ ['#8f1111', '#f0b429', '#171717', '#c86f2d'][$index] ?? '#8f1111' }};"></span>
                        </div>
                    </div>
                @empty
                    <div class="branch-item text-sm text-black/65">{{ __('Your contribution mix will appear here after you start using giving prompts.') }}</div>
                @endforelse
            </div>
        </div>
    </article>
</section>
@endif

@if($isSuperAdmin)
<section class="mt-8 admin-focus-card">
    <div class="admin-focus-copy">
        <span class="section-kicker">{{ __('Church Summary') }}</span>
        <h2 class="mt-5">{{ __('A calm headquarters summary.') }}</h2>
        <p class="mt-3">{{ __('See the church shape here, then continue into the page that needs your attention.') }}</p>
    </div>

    <div class="admin-focus-grid">
        <article class="admin-focus-metric">
            <span>{{ __('Regions') }}</span>
            <strong>{{ $stats['regions'] }}</strong>
            <p>{{ __('Connected in view.') }}</p>
        </article>
        <article class="admin-focus-metric">
            <span>{{ __('Branches') }}</span>
            <strong>{{ $stats['branches'] }}</strong>
            <p>{{ __('Active across the church.') }}</p>
        </article>
        <article class="admin-focus-metric">
            <span>{{ __('People') }}</span>
            <strong>{{ $stats['users'] }}</strong>
            <p>{{ __('Accounts in view.') }}</p>
        </article>
    </div>

    <div class="admin-secondary-links admin-secondary-links--spacious">
        <a href="{{ route('admin.users.index') }}">{{ __('Users') }}</a>
        <a href="{{ route('branches.index') }}">{{ __('Branches') }}</a>
        <a href="{{ route('sliders.index') }}">{{ __('Homepage slider') }}</a>
        <a href="{{ route('assistant.topics.index') }}">{{ __('Assistant') }}</a>
    </div>
</section>
@endif

<section id="dashboard-overview" class="dashboard-overview-shell dashboard-overview-shell--executive mt-8">
    <div class="dashboard-overview-copy">
        <span class="section-kicker">{{ __('At a Glance') }}</span>
        <h2>{{ $overviewHeading }}</h2>
        <p>{{ $overviewCopy }}</p>
    </div>

    <div class="panel-grid cols-4 dashboard-stat-grid dashboard-stat-grid--executive">
        @foreach($dashboardStatCards as $card)
            <article class="stat-card">
                <span class="stat-label">{{ $card['label'] }}</span>
                <strong>{{ $card['value'] }}</strong>
            </article>
        @endforeach
    </div>
</section>

<section id="dashboard-actions" class="dashboard-actions-shell {{ auth()->user()->hasSystemRole('member') ? 'dashboard-actions-shell--member' : 'tablet-stack two' }} mt-8">
    <article class="card-rgc-strong">
        <span class="section-kicker section-kicker--icon">@include('partials.ui.icon', ['name' => 'sparkles', 'class' => 'section-kicker-icon'])<span>{{ __('Quick Actions') }}</span></span>
        <h2 class="mt-5 font-[family-name:var(--font-display)] text-3xl leading-none">
            {{ $isMember ? __('Simple next steps for your branch life.') : ($isSuperAdmin ? __('Main church actions and everyday tools.') : __('Open the tools prepared for your area of service.')) }}
        </h2>
        <div class="{{ $isMember ? 'member-home-action-grid mt-6' : 'dashboard-action-grid mt-6' }}">
            @if($isMember)
                <a class="member-home-action-card is-primary" href="{{ route('giving.index') }}">
                    <span class="member-home-action-icon">@include('partials.ui.icon', ['name' => 'giving'])</span>
                    <strong>{{ __('Give now') }}</strong>
                    <p>{{ __('Open giving and send your next prompt with peace and clarity.') }}</p>
                </a>
                <a class="member-home-action-card" href="{{ route('announcements.index') }}">
                    <span class="member-home-action-icon">@include('partials.ui.icon', ['name' => 'megaphone'])</span>
                    <strong>{{ __('Open announcements') }}</strong>
                    <p>{{ __('Read the latest updates prepared for your branch.') }}</p>
                </a>
                <a class="member-home-action-card" href="{{ route('messages.index') }}">
                    <span class="member-home-action-icon">@include('partials.ui.icon', ['name' => 'chat'])</span>
                    <strong>{{ __('Branch chat') }}</strong>
                    <p>{{ __('Stay close to prayer, coordination, and branch conversation.') }}</p>
                </a>
                <a class="member-home-action-card" href="{{ route('messages.index', ['prefill' => $prayerPrefill]) }}">
                    <span class="member-home-action-icon">@include('partials.ui.icon', ['name' => 'sparkles'])</span>
                    <strong>{{ __('Ask for prayer') }}</strong>
                    <p>{{ __('Open branch chat with a prayer request already prepared.') }}</p>
                </a>
                <a class="member-home-action-card" href="{{ route('messages.index', ['prefill' => $followUpPrefill]) }}">
                    <span class="member-home-action-icon">@include('partials.ui.icon', ['name' => 'assistant'])</span>
                    <strong>{{ __('Request follow-up') }}</strong>
                    <p>{{ __('Start a caring follow-up message to branch leadership.') }}</p>
                </a>
                <a class="member-home-action-card" href="{{ route('account.profile.edit') }}">
                    <span class="member-home-action-icon">@include('partials.ui.icon', ['name' => 'user'])</span>
                    <strong>{{ __('Update my contact') }}</strong>
                    <p>{{ __('Keep your phone and email ready for branch communication.') }}</p>
                </a>
            @else
                <a class="dashboard-action-card is-primary" href="{{ route('announcements.index') }}">
                    <span class="dashboard-action-icon">@include('partials.ui.icon', ['name' => 'megaphone'])</span>
                    <strong>{{ __('Announcements') }}</strong>
                    <p>{{ __('Read current church updates.') }}</p>
                </a>
                <a class="dashboard-action-card" href="{{ route('messages.index') }}">
                    <span class="dashboard-action-icon">@include('partials.ui.icon', ['name' => 'chat'])</span>
                    <strong>{{ __('Branch chat') }}</strong>
                    <p>{{ __('Open branch conversation.') }}</p>
                </a>
                <a class="dashboard-action-card" href="{{ route('giving.index') }}">
                    <span class="dashboard-action-icon">@include('partials.ui.icon', ['name' => 'giving'])</span>
                    <strong>{{ __('Giving') }}</strong>
                    <p>{{ __('Open the giving page.') }}</p>
                </a>
                <a class="dashboard-action-card" href="{{ route('account.profile.edit') }}">
                    <span class="dashboard-action-icon">@include('partials.ui.icon', ['name' => 'user'])</span>
                    <strong>{{ __('My account') }}</strong>
                    <p>{{ __('Update your details.') }}</p>
                </a>
                @if(auth()->user()->hasSystemRole('super_admin'))
                    <div class="admin-secondary-links">
                        <a href="{{ route('admin.users.index') }}">{{ __('Manage users') }}</a>
                        <a href="{{ route('branches.index') }}">{{ __('Manage branches') }}</a>
                        <a href="{{ route('sliders.index') }}">{{ __('Homepage slider') }}</a>
                    </div>
                @endif
                @if(auth()->user()->hasAnySystemRole(['super_admin', 'regional_admin']))
                    @if(! $isSuperAdmin)
                        <a class="dashboard-action-card" href="{{ route('assistant.topics.index') }}">
                            <span class="dashboard-action-icon">@include('partials.ui.icon', ['name' => 'assistant'])</span>
                            <strong>{{ __('Assistant knowledge') }}</strong>
                            <p>{{ __('Open assistant topics.') }}</p>
                        </a>
                    @endif
                @endif
                @if($canOpenBranchBooks)
                    @if(! $isAccountant)
                        <a class="dashboard-action-card" href="{{ route('events.index') }}">
                            <span class="dashboard-action-icon">@include('partials.ui.icon', ['name' => 'sparkles'])</span>
                            <strong>{{ __('Events') }}</strong>
                            <p>{{ __('Open church moments.') }}</p>
                        </a>
                    @endif
                @endif
            @endif
        </div>
    </article>

    @if($isMember)
    <article class="card-rgc">
        <span class="section-kicker">{{ __('Next in Your Branch') }}</span>
        <h2 class="mt-5 text-2xl font-semibold">{{ __('A few things to notice before you continue.') }}</h2>
        <p class="mt-2 text-sm text-black/65">{{ __('Use these short previews, then continue into announcements, giving, or branch chat when you are ready.') }}</p>

        @if($memberDashboard['highlight'])
            <article class="member-highlight-card mt-5">
                <div class="member-highlight-header">
                    <div>
                        <span class="section-kicker">{{ __('Pinned Update') }}</span>
                        <h3 class="mt-4">{{ $memberDashboard['highlight']->title }}</h3>
                    </div>
                    <span class="announcement-audience is-{{ $memberDashboard['highlight']->audienceVariant() }}">{{ $memberDashboard['highlight']->audienceLabel() }}</span>
                </div>
                <p class="mt-3 text-sm text-black/72">{{ str($memberDashboard['highlight']->body)->limit(180) }}</p>
                <div class="member-highlight-actions mt-5">
                    <a class="member-inline-link" href="{{ route('announcements.show', $memberDashboard['highlight']) }}">@include('partials.ui.icon', ['name' => 'eye', 'class' => 'button-icon'])<span>{{ __('Open update') }}</span></a>
                </div>
            </article>
        @endif

        <div class="member-notice-card mt-5">
            <div class="member-notice-header">
                <div>
                    <span class="section-kicker">{{ __('Notices for :branch', ['branch' => $memberBranchName]) }}</span>
                    <h3>{{ __('Recent branch updates') }}</h3>
                </div>
                <a class="member-inline-link" href="{{ route('announcements.index') }}">@include('partials.ui.icon', ['name' => 'megaphone', 'class' => 'button-icon'])<span>{{ __('See all') }}</span></a>
            </div>

            <div class="branch-list mt-5">
                @forelse($memberNoticePreview as $announcement)
                    <div class="branch-item">
                        <strong class="block text-base">{{ $announcement->title }}</strong>
                        <p class="mt-2 text-sm text-black/65">{{ str($announcement->body)->limit(120) }}</p>
                        <p class="mt-2 text-xs text-black/50">{{ optional($announcement->created_at)->diffForHumans() }}</p>
                    </div>
                @empty
                    <div class="branch-item text-sm text-black/65">{{ __('No branch notices are available right now.') }}</div>
                @endforelse
            </div>
        </div>

        <div class="member-upcoming-card mt-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <span class="section-kicker">{{ __('Upcoming Moments') }}</span>
                    <h3 class="mt-4 text-2xl font-semibold">{{ __('What is coming up next') }}</h3>
                </div>
                <a class="member-inline-link" href="{{ route('announcements.index') }}">@include('partials.ui.icon', ['name' => 'megaphone', 'class' => 'button-icon'])<span>{{ __('Open announcements') }}</span></a>
            </div>

            <div class="member-upcoming-list mt-5">
                @forelse($memberDashboard['upcoming_events']->take(3) as $event)
                    <article class="member-upcoming-item">
                        <div class="member-upcoming-date">
                            <strong>{{ $event->event_date->translatedFormat('d') }}</strong>
                            <span>{{ $event->event_date->translatedFormat('M') }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-black">{{ $event->title }}</p>
                            <p class="mt-1 text-sm text-black/65">{{ $event->event_date->translatedFormat('l, d M Y · H:i') }}</p>
                        </div>
                    </article>
                @empty
                    <div class="branch-item text-sm text-black/65">{{ __('No upcoming events have been added for your branch yet.') }}</div>
                @endforelse
            </div>
        </div>
    </article>
    @else
    <article class="card-rgc">
        <span class="section-kicker">{{ __('Your Church Area') }}</span>
        @if($isSuperAdmin)
            <h2 class="mt-5 text-2xl font-semibold">{{ __('Regions across the church') }}</h2>
            <p class="mt-2 text-sm text-black/65">{{ __('A quick coverage preview.') }}</p>
            <div class="branch-list mt-5">
                @forelse($scopePreview->take(2) as $region)
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
            <p class="mt-2 text-sm text-black/65">{{ __('A quick district preview.') }}</p>
            <div class="branch-list mt-5">
                @forelse($scopePreview->take(2) as $district)
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
            <p class="mt-2 text-sm text-black/65">{{ __('A quick branch preview.') }}</p>
            <div class="branch-list mt-5">
                @forelse($scopePreview->take(2) as $branch)
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
            <p class="mt-2 text-sm text-black/65">{{ __('A quick finance preview.') }}</p>
            <div class="branch-list mt-5">
                <div class="branch-item">
                    <p class="font-semibold">{{ __('Pending payment requests') }}</p>
                    <p class="mt-1 text-sm text-black/65">{{ __(':count waiting for follow-up', ['count' => $stats['pending_payments']]) }}</p>
                </div>
                <div class="branch-item">
                    <p class="font-semibold">{{ __('Completed payments') }}</p>
                    <p class="mt-1 text-sm text-black/65">{{ __(':count confirmed for your branch', ['count' => $stats['completed_payments']]) }}</p>
                </div>
            </div>
        @elseif(auth()->user()->hasAnySystemRole(['branch_admin', 'pastor', 'bishop']))
            <h2 class="mt-5 text-2xl font-semibold">
                {{ __('People in :branch', ['branch' => $dashboardUser->branch?->name ?: __('your branch')]) }}
            </h2>
            <p class="mt-2 text-sm text-black/65">{{ __('A quick people preview.') }}</p>
            <div class="branch-list mt-5">
                @forelse($scopePreview->take(2) as $member)
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
        @else
            <h2 class="mt-5 text-2xl font-semibold">{{ __('Church overview') }}</h2>
            <p class="mt-4 text-sm leading-7 text-black/68">{{ __('As more church information is added, this page will keep reflecting the region, district, or branch work connected to your role.') }}</p>
        @endif
    </article>
    @endif
</section>

@if(! $isMember)
<section id="dashboard-notices" class="mt-8 card-rgc">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <span class="section-kicker">{{ __('Announcements') }}</span>
            <h2 class="text-2xl font-semibold">{{ __('Recent church updates') }}</h2>
            <p class="mt-2 text-sm text-black/65">{{ __('Home now keeps only a few recent updates. Open the announcements page when you want the full list.') }}</p>
        </div>
        <a class="btn-rgc-outline w-full sm:w-auto" href="{{ route('announcements.index') }}">{{ __('Open announcements') }}</a>
    </div>

    <div class="announcement-grid mt-6">
        @forelse($announcementPreview as $announcement)
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
                <strong>{{ __('No announcements are available here yet.') }}</strong>
                <p>{{ __('When leaders share new updates, they will appear here with the audience and key details.') }}</p>
            </article>
        @endforelse
    </div>
</section>
@endif
@include('panel.announcements._lightbox')
@endsection
