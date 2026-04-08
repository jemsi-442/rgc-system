<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Branch;
use App\Models\District;
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
        $roleLabel = __(str($user->normalizedRoleName() ?? 'member')->replace('_', ' ')->title()->toString());

        return view('panel.dashboard', compact('stats', 'announcements', 'scope', 'recentPayments', 'paymentAlerts', 'memberPayments', 'roleLabel'));
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
}
