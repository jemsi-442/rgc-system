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

        return $user->canManageBranch((int) ($target->effectiveBranchId() ?? 0));
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
