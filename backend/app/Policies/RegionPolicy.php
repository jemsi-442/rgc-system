<?php

namespace App\Policies;

use App\Models\Region;
use App\Models\User;

class RegionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRoleOrLegacy(['super_admin', 'admin', 'user']);
    }

    public function view(User $user, Region $region): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRoleOrLegacy(['super_admin']);
    }

    public function update(User $user, Region $region): bool
    {
        return $user->hasAnyRoleOrLegacy(['super_admin']);
    }

    public function delete(User $user, Region $region): bool
    {
        return $user->hasAnyRoleOrLegacy(['super_admin']);
    }
}
