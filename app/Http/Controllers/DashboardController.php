<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Branch;
use App\Models\District;
use App\Models\Expense;
use App\Models\Offering;
use App\Models\Region;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $branchId = $user->effectiveBranchId();

        $stats = $this->buildStats($user, $branchId);
        $announcements = $this->buildAnnouncements($user, $branchId);
        $scope = $this->buildScope($user, $branchId);
        $roleLabel = __(str($user->normalizedRoleName() ?? 'member')->replace('_', ' ')->title()->toString());

        return view('panel.dashboard', compact('stats', 'announcements', 'scope', 'roleLabel'));
    }

    private function buildStats(User $user, ?int $branchId): array
    {
        if ($user->hasSystemRole('super_admin')) {
            return [
                'users' => User::query()->count(),
                'regions' => Region::query()->count(),
                'districts' => District::query()->count(),
                'branches' => Branch::query()->count(),
                'offerings' => Offering::query()->sum('amount'),
                'expenses' => Expense::query()->sum('amount'),
            ];
        }

        if ($user->hasSystemRole('regional_admin')) {
            $branchIds = Branch::query()
                ->where('region_id', $user->region_id)
                ->pluck('id');

            return [
                'users' => User::query()->where('region_id', $user->region_id)->count(),
                'regions' => 1,
                'districts' => District::query()->where('region_id', $user->region_id)->count(),
                'branches' => $branchIds->count(),
                'offerings' => Offering::query()->whereIn('church_id', $branchIds)->sum('amount'),
                'expenses' => Expense::query()->whereIn('church_id', $branchIds)->sum('amount'),
            ];
        }

        if ($user->hasSystemRole('district_admin')) {
            $branchIds = Branch::query()
                ->where('district_id', $user->district_id)
                ->pluck('id');

            return [
                'users' => User::query()->where('district_id', $user->district_id)->count(),
                'regions' => $user->region_id ? 1 : 0,
                'districts' => 1,
                'branches' => $branchIds->count(),
                'offerings' => Offering::query()->whereIn('church_id', $branchIds)->sum('amount'),
                'expenses' => Expense::query()->whereIn('church_id', $branchIds)->sum('amount'),
            ];
        }

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
        ];
    }

    private function buildAnnouncements(User $user, ?int $branchId)
    {
        return Announcement::query()
            ->with(['creator', 'region', 'district', 'branch'])
            ->visibleTo($user)
            ->dashboardVisible()
            ->orderedForDisplay()
            ->paginate(10);
    }

    private function buildScope(User $user, ?int $branchId)
    {
        if ($user->hasSystemRole('regional_admin')) {
            return Branch::query()
                ->with('district')
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
                ->with(['creator', 'region', 'district', 'branch'])
                ->visibleTo($user)
                ->dashboardVisible()
                ->orderedForDisplay()
                ->limit(20)
                ->get();
        }

        return collect();
    }
}
