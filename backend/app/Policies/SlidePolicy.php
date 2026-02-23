<?php

namespace App\Policies;

use App\Models\Slide;
use App\Models\User;

class SlidePolicy
{
    public function viewAny(?User $user = null): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRoleOrLegacy(['super_admin', 'admin']);
    }

    public function update(User $user, Slide $slide): bool
    {
        return $user->hasAnyRoleOrLegacy(['super_admin', 'admin']);
    }

    public function delete(User $user, Slide $slide): bool
    {
        return $user->hasAnyRoleOrLegacy(['super_admin', 'admin']);
    }
}
