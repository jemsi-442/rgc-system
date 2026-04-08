<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnySystemRole(['super_admin', 'regional_admin', 'district_admin', 'branch_admin']);
    }

    public function view(User $user, User $target): bool
    {
        if ($user->id === $target->id) {
            return true;
        }

        if ($user->hasSystemRole('super_admin')) {
            return true;
        }

        if (! $user->outranks($target)) {
            return false;
        }

        $branchId = $target->effectiveBranchId();

        if ($branchId !== null) {
            return $user->canManageBranch((int) $branchId);
        }

        if ($target->district_id) {
            return $user->canManageDistrict((int) $target->district_id);
        }

        if ($target->region_id) {
            return $user->canManageRegion((int) $target->region_id);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, User $target): bool
    {
        return $this->view($user, $target);
    }

    public function delete(User $user, User $target): bool
    {
        return $user->hasSystemRole('super_admin') && $user->id !== $target->id;
    }
}
