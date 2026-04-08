<?php

namespace App\Policies;

class OfferingPaymentPolicy extends BranchScopedPolicy
{
    public function reviewAny(\App\Models\User $user): bool
    {
        return $user->hasAnySystemRole(['super_admin', 'regional_admin', 'district_admin', 'branch_admin', 'pastor', 'bishop', 'accountant']);
    }

    public function review(\App\Models\User $user, mixed $model): bool
    {
        return $model instanceof \App\Models\OfferingPayment
            && $this->reviewAny($user)
            && $this->scoped($user, $model);
    }

    public function sync(\App\Models\User $user, mixed $model): bool
    {
        return $this->review($user, $model);
    }
}
