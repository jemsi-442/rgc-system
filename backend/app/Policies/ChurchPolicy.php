<?php

namespace App\Policies;

use App\Models\Church;
use App\Models\User;

class ChurchPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRoleOrLegacy(['super_admin', 'regional_admin', 'district_admin', 'branch_admin', 'admin']);
    }

    public function view(User $user, Church $church): bool
    {
        if ($user->hasAnyRoleOrLegacy(['super_admin', 'admin'])) {
            return true;
        }

        if ($user->hasAnyRoleOrLegacy(['regional_admin'])) {
            return (int) $user->region_id === (int) $church->region_id;
        }

        if ($user->hasAnyRoleOrLegacy(['district_admin'])) {
            return (int) $user->district_id === (int) $church->district_id;
        }

        if ($user->hasAnyRoleOrLegacy(['branch_admin'])) {
            return (int) $user->branch_id === (int) $church->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRoleOrLegacy(['super_admin']);
    }

    public function update(User $user, Church $church): bool
    {
        return $this->view($user, $church);
    }

    public function delete(User $user, Church $church): bool
    {
        return $user->hasAnyRoleOrLegacy(['super_admin']);
    }
}
