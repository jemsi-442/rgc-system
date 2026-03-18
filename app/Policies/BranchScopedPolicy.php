<?php

namespace App\Policies;

use App\Models\User;

class BranchScopedPolicy
{
    protected function scoped(User $user, mixed $model): bool
    {
        if ($user->hasSystemRole('super_admin')) {
            return true;
        }

        $branchId = $model->branch_id ?? $model->church_id ?? null;

        if ($branchId) {
            return $user->canManageBranch((int) $branchId);
        }

        if (isset($model->district_id) && $model->district_id) {
            return $user->canManageDistrict((int) $model->district_id);
        }

        if (isset($model->region_id) && $model->region_id) {
            return $user->canManageRegion((int) $model->region_id);
        }

        return false;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnySystemRole(['super_admin', 'regional_admin', 'district_admin', 'branch_admin', 'pastor', 'bishop', 'accountant', 'member']);
    }

    public function view(User $user, mixed $model): bool
    {
        return $this->scoped($user, $model);
    }

    public function create(User $user): bool
    {
        return $user->hasAnySystemRole(['super_admin', 'regional_admin', 'district_admin', 'branch_admin']);
    }

    public function update(User $user, mixed $model): bool
    {
        return $this->scoped($user, $model);
    }

    public function delete(User $user, mixed $model): bool
    {
        return $this->scoped($user, $model) && ! $user->hasSystemRole('member');
    }
}
