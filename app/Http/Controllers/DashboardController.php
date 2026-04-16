<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Branch;
use App\Models\District;
use App\Models\Event;
use App\Models\Expense;
use App\Models\Offering;
use App\Models\OfferingPayment;
use App\Models\Region;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $branchId = $user->effectiveBranchId();

        $stats = $this->buildStats($user, $branchId);
        $announcements = $this->buildAnnouncements($user, $branchId);
        $scope = $this->buildScope($user, $branchId);
        $recentPayments = $this->buildRecentPayments($user, $branchId);
        $paymentAlerts = $this->buildPaymentAlerts($user, $branchId);
        $memberPayments = $this->buildMemberPayments($user);
        $memberDashboard = $this->buildMemberDashboard($user, $branchId, $memberPayments);
        $charts = $this->buildCharts($user, $branchId, $stats);
        $roleLabel = __(str($user->normalizedRoleName() ?? 'member')->replace('_', ' ')->title()->toString());

        return view('panel.dashboard', compact('stats', 'announcements', 'scope', 'recentPayments', 'paymentAlerts', 'memberPayments', 'memberDashboard', 'charts', 'roleLabel'));
    }

    private function buildStats(User $user, ?int $branchId): array
    {
        if ($user->hasSystemRole('super_admin')) {
            $paymentQuery = OfferingPayment::query();

            return [
                'users' => User::query()->count(),
                'regions' => Region::query()->count(),
                'districts' => District::query()->count(),
                'branches' => Branch::query()->count(),
                'offerings' => Offering::query()->sum('amount'),
                'expenses' => Expense::query()->sum('amount'),
                'payment_requests' => (clone $paymentQuery)->count(),
                'pending_payments' => (clone $paymentQuery)->where('status', 'pending')->count(),
                'completed_payments' => (clone $paymentQuery)->where('status', 'completed')->count(),
            ];
        }

        if ($user->hasSystemRole('regional_admin')) {
            $branchIds = Branch::query()
                ->where('region_id', $user->region_id)
                ->pluck('id');

            $paymentQuery = OfferingPayment::query()->whereIn('church_id', $branchIds);

            return [
                'users' => User::query()->where('region_id', $user->region_id)->count(),
                'regions' => 1,
                'districts' => District::query()->where('region_id', $user->region_id)->count(),
                'branches' => $branchIds->count(),
                'offerings' => Offering::query()->whereIn('church_id', $branchIds)->sum('amount'),
                'expenses' => Expense::query()->whereIn('church_id', $branchIds)->sum('amount'),
                'payment_requests' => (clone $paymentQuery)->count(),
                'pending_payments' => (clone $paymentQuery)->where('status', 'pending')->count(),
                'completed_payments' => (clone $paymentQuery)->where('status', 'completed')->count(),
            ];
        }

        if ($user->hasSystemRole('district_admin')) {
            $branchIds = Branch::query()
                ->where('district_id', $user->district_id)
                ->pluck('id');

            $paymentQuery = OfferingPayment::query()->whereIn('church_id', $branchIds);

            return [
                'users' => User::query()->where('district_id', $user->district_id)->count(),
                'regions' => $user->region_id ? 1 : 0,
                'districts' => 1,
                'branches' => $branchIds->count(),
                'offerings' => Offering::query()->whereIn('church_id', $branchIds)->sum('amount'),
                'expenses' => Expense::query()->whereIn('church_id', $branchIds)->sum('amount'),
                'payment_requests' => (clone $paymentQuery)->count(),
                'pending_payments' => (clone $paymentQuery)->where('status', 'pending')->count(),
                'completed_payments' => (clone $paymentQuery)->where('status', 'completed')->count(),
            ];
        }

        $paymentQuery = $branchId
            ? OfferingPayment::query()->where('church_id', $branchId)
            : OfferingPayment::query()->whereRaw('1 = 0');

        return [
            'users' => User::query()
                ->where(function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId)->orWhere('church_id', $branchId);
                })
                ->count(),
            'regions' => $user->region_id ? 1 : 0,
            'districts' => $user->district_id ? 1 : 0,
            'branches' => $branchId ? 1 : 0,
            'offerings' => $branchId ? Offering::query()->where('church_id', $branchId)->sum('amount') : 0,
            'expenses' => $branchId ? Expense::query()->where('church_id', $branchId)->sum('amount') : 0,
            'payment_requests' => (clone $paymentQuery)->count(),
            'pending_payments' => (clone $paymentQuery)->where('status', 'pending')->count(),
            'completed_payments' => (clone $paymentQuery)->where('status', 'completed')->count(),
        ];
    }

    private function buildAnnouncements(User $user, ?int $branchId)
    {
        return Announcement::query()
            ->with(['creator', 'region', 'district', 'branch', 'targetBranches'])
            ->visibleTo($user)
            ->dashboardVisible()
            ->orderedForDisplay()
            ->paginate(10);
    }

    private function buildScope(User $user, ?int $branchId)
    {
        if ($user->hasSystemRole('super_admin')) {
            return Region::query()
                ->withCount([
                    'districts',
                    'branches as active_branches_count' => function (Builder $query): void {
                        $query->where('status', 'active');
                    },
                ])
                ->orderBy('name')
                ->limit(20)
                ->get();
        }

        if ($user->hasSystemRole('regional_admin')) {
            return District::query()
                ->withCount([
                    'branches as active_branches_count' => function (Builder $query): void {
                        $query->where('status', 'active');
                    },
                ])
                ->where('region_id', $user->region_id)
                ->orderBy('name')
                ->limit(20)
                ->get();
        }

        if ($user->hasSystemRole('district_admin')) {
            return Branch::query()
                ->where('district_id', $user->district_id)
                ->orderBy('name')
                ->limit(20)
                ->get();
        }

        if ($user->hasAnySystemRole(['branch_admin', 'pastor', 'bishop', 'accountant'])) {
            return User::query()
                ->where(function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId)->orWhere('church_id', $branchId);
                })
                ->orderBy('name')
                ->limit(20)
                ->get();
        }

        if ($user->hasSystemRole('member')) {
            return Announcement::query()
                ->with(['creator', 'region', 'district', 'branch', 'targetBranches'])
                ->visibleTo($user)
                ->dashboardVisible()
                ->orderedForDisplay()
                ->limit(20)
                ->get();
        }

        return collect();
    }

    private function buildRecentPayments(User $user, ?int $branchId): Collection
    {
        if (! $user->hasAnySystemRole(['super_admin', 'regional_admin', 'district_admin', 'branch_admin', 'pastor', 'bishop', 'accountant'])) {
            return collect();
        }

        return $this->paymentScopeQuery($user, $branchId)
            ->with(['branch', 'offering'])
            ->latest()
            ->limit(6)
            ->get();
    }

    private function buildPaymentAlerts(User $user, ?int $branchId): Collection
    {
        if (! $user->hasAnySystemRole(['super_admin', 'regional_admin', 'district_admin', 'branch_admin', 'pastor', 'bishop', 'accountant'])) {
            return collect();
        }

        return $this->paymentScopeQuery($user, $branchId)
            ->with(['branch', 'user'])
            ->whereNull('reviewed_at')
            ->where(function (Builder $query): void {
                $query
                    ->where(function (Builder $inner): void {
                        $inner->where('status', 'completed')->where('paid_at', '>=', now()->subDays(7));
                    })
                    ->orWhere(function (Builder $inner): void {
                        $inner->where('status', 'failed')->where('updated_at', '>=', now()->subDays(7));
                    })
                    ->orWhere(function (Builder $inner): void {
                        $inner->where('status', 'pending')->where('created_at', '>=', now()->subDays(2));
                    });
            })
            ->latest('updated_at')
            ->limit(6)
            ->get();
    }

    private function buildMemberPayments(User $user): Collection
    {
        if (! $user->hasSystemRole('member')) {
            return collect();
        }

        return OfferingPayment::query()
            ->where('user_id', $user->id)
            ->latest()
            ->limit(3)
            ->get();
    }

    private function buildMemberDashboard(User $user, ?int $branchId, Collection $memberPayments): array
    {
        if (! $user->hasSystemRole('member')) {
            return [
                'encouragement' => null,
                'giving' => [],
                'highlight' => null,
                'upcoming_events' => collect(),
            ];
        }

        $monthStart = now()->startOfMonth();
        $allMemberPayments = OfferingPayment::query()
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $highlight = Announcement::query()
            ->with(['creator', 'region', 'district', 'branch', 'targetBranches'])
            ->visibleTo($user)
            ->dashboardVisible()
            ->orderedForDisplay()
            ->first();

        $upcomingEvents = Event::query()
            ->where('event_date', '>=', now()->startOfDay())
            ->where(function (Builder $query) use ($user, $branchId): void {
                if ($branchId) {
                    $query->where('church_id', $branchId);
                }

                if ($user->district_id) {
                    $query->orWhere(function (Builder $districtQuery) use ($user): void {
                        $districtQuery
                            ->where('district_id', $user->district_id)
                            ->whereNull('church_id');
                    });
                }

                if ($user->region_id) {
                    $query->orWhere(function (Builder $regionQuery) use ($user): void {
                        $regionQuery
                            ->where('region_id', $user->region_id)
                            ->whereNull('district_id')
                            ->whereNull('church_id');
                    });
                }
            })
            ->orderBy('event_date')
            ->limit(3)
            ->get();

        return [
            'encouragement' => $this->memberEncouragement(),
            'giving' => [
                'count' => $allMemberPayments->count(),
                'month_total' => (float) $allMemberPayments
                    ->filter(fn (OfferingPayment $payment) => $payment->created_at && $payment->created_at->gte($monthStart))
                    ->sum('amount'),
                'last_payment' => $allMemberPayments->first(),
            ],
            'highlight' => $highlight,
            'upcoming_events' => $upcomingEvents,
        ];
    }

    private function buildCharts(User $user, ?int $branchId, array $stats): array
    {
        if (! $user->hasAnySystemRole(['super_admin', 'regional_admin', 'district_admin', 'branch_admin', 'pastor', 'bishop', 'accountant'])) {
            return [
                'activity' => collect(),
                'activity_peak' => 1,
                'finance_mix' => collect(),
                'status_mix' => collect(),
            ];
        }

        $paymentQuery = $this->paymentScopeQuery($user, $branchId);
        $branchIds = $this->scopeBranchIds($user, $branchId);
        $activityStart = now()->startOfDay()->subDays(6);

        $activityRows = (clone $paymentQuery)
            ->where('created_at', '>=', $activityStart)
            ->selectRaw('DATE(created_at) as activity_date, COUNT(*) as aggregate')
            ->groupBy('activity_date')
            ->pluck('aggregate', 'activity_date');

        $activity = collect(range(0, 6))
            ->map(function (int $offset) use ($activityStart, $activityRows): array {
                $date = (clone $activityStart)->addDays($offset);
                $key = $date->toDateString();

                return [
                    'label' => $date->translatedFormat('D'),
                    'date' => $date->translatedFormat('d M'),
                    'count' => (int) ($activityRows[$key] ?? 0),
                ];
            });

        $activityPeak = max(1, (int) $activity->max('count'));

        $statusRows = (clone $paymentQuery)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $statusLabels = [
            'pending' => __('Pending'),
            'completed' => __('Completed'),
            'failed' => __('Failed'),
        ];

        $statusTotal = max(1, (int) collect($statusLabels)->keys()->sum(fn (string $status) => (int) ($statusRows[$status] ?? 0)));
        $statusMix = collect($statusLabels)->map(function (string $label, string $status) use ($statusRows, $statusTotal): array {
            $count = (int) ($statusRows[$status] ?? 0);

            return [
                'key' => $status,
                'label' => $label,
                'count' => $count,
                'width' => $count > 0 ? max(10, (int) round(($count / $statusTotal) * 100)) : 0,
            ];
        })->values();

        $offeringsTotal = $this->scopedAmount(Offering::query(), $branchIds);
        $expensesTotal = $this->scopedAmount(Expense::query(), $branchIds);
        $completedCollectionsTotal = $this->scopedAmount(
            OfferingPayment::query()->where('status', 'completed'),
            $branchIds
        );

        $financeMix = collect([
            [
                'key' => 'offerings',
                'label' => __('Offerings'),
                'value' => (float) $offeringsTotal,
            ],
            [
                'key' => 'expenses',
                'label' => __('Expenses'),
                'value' => (float) $expensesTotal,
            ],
            [
                'key' => 'collections',
                'label' => __('Completed collections'),
                'value' => (float) $completedCollectionsTotal,
            ],
        ]);

        $financePeak = max(1, (float) $financeMix->max('value'));
        $financeMix = $financeMix
            ->map(function (array $item) use ($financePeak): array {
                $value = (float) $item['value'];

                return [
                    ...$item,
                    'display_value' => number_format($value, 2),
                    'width' => $value > 0 ? max(10, (int) round(($value / $financePeak) * 100)) : 0,
                ];
            })
            ->values();

        return [
            'activity' => $activity,
            'activity_peak' => $activityPeak,
            'finance_mix' => $financeMix,
            'status_mix' => $statusMix,
        ];
    }

    private function paymentScopeQuery(User $user, ?int $branchId): Builder
    {
        $query = OfferingPayment::query();

        if ($user->hasSystemRole('super_admin')) {
            return $query;
        }

        if ($user->hasSystemRole('regional_admin')) {
            $branchIds = Branch::query()->where('region_id', $user->region_id)->pluck('id');

            return $query->whereIn('church_id', $branchIds);
        }

        if ($user->hasSystemRole('district_admin')) {
            $branchIds = Branch::query()->where('district_id', $user->district_id)->pluck('id');

            return $query->whereIn('church_id', $branchIds);
        }

        return $query->where('church_id', $branchId);
    }

    private function scopeBranchIds(User $user, ?int $branchId): ?Collection
    {
        if ($user->hasSystemRole('super_admin')) {
            return null;
        }

        if ($user->hasSystemRole('regional_admin')) {
            return Branch::query()->where('region_id', $user->region_id)->pluck('id');
        }

        if ($user->hasSystemRole('district_admin')) {
            return Branch::query()->where('district_id', $user->district_id)->pluck('id');
        }

        return $branchId ? collect([$branchId]) : collect();
    }

    private function scopedAmount(Builder $query, ?Collection $branchIds): float
    {
        if ($branchIds === null) {
            return (float) $query->sum('amount');
        }

        if ($branchIds->isEmpty()) {
            return 0.0;
        }

        return (float) $query->whereIn('church_id', $branchIds)->sum('amount');
    }

    private function memberEncouragement(): array
    {
        $messages = [
            0 => [
                'title' => __('Start the week with purpose'),
                'body' => __('Stay close to your branch updates, upcoming gatherings, and giving plans so the week begins with clarity and peace.'),
            ],
            1 => [
                'title' => __('Keep your branch connection active'),
                'body' => __('Open the latest notices, check upcoming moments, and stay ready to support what your church family is doing this week.'),
            ],
            2 => [
                'title' => __('Walk with your branch day by day'),
                'body' => __('A simple check-in here keeps you close to church communication, encouragement, and practical ways to stay involved.'),
            ],
            3 => [
                'title' => __('Stay encouraged and prepared'),
                'body' => __('Use this space to follow branch life, keep up with church moments, and respond quickly when something important is happening.'),
            ],
            4 => [
                'title' => __('Finish the week with awareness'),
                'body' => __('Before the weekend gathers pace, take a moment to review notices, events, and any giving plans you want to complete.'),
            ],
            5 => [
                'title' => __('Weekend branch moments are near'),
                'body' => __('This is a good time to check service updates, branch events, and the things your church family is preparing together.'),
            ],
            6 => [
                'title' => __('Welcome to today’s church home'),
                'body' => __('Stay close to the life of your branch today through updates, upcoming gatherings, and the giving tools prepared for members.'),
            ],
        ];

        return $messages[now()->dayOfWeek] ?? $messages[0];
    }
}
