<?php

namespace App\Policies;

use App\Models\Offering;
use App\Models\User;

class OfferingPolicy extends BranchScopedPolicy
{
    private function canManageOfferingRecords(User $user): bool
    {
        return $user->hasAnySystemRole(['super_admin', 'branch_admin', 'pastor', 'bishop', 'accountant']);
    }

    public function viewAny(User $user): bool
    {
        return $this->canManageOfferingRecords($user);
    }

    public function create(User $user): bool
    {
        return $this->canManageOfferingRecords($user);
    }

    public function view(User $user, mixed $model): bool
    {
        return $model instanceof Offering
            && $this->canManageOfferingRecords($user)
            && $this->scoped($user, $model);
    }

    public function update(User $user, mixed $model): bool
    {
        return $model instanceof Offering
            && $this->canManageOfferingRecords($user)
            && $this->scoped($user, $model);
    }

    public function delete(User $user, mixed $model): bool
    {
        return $model instanceof Offering
            && $this->canManageOfferingRecords($user)
            && $this->scoped($user, $model);
    }
}
