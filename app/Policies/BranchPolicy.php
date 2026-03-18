<?php

namespace App\Policies;

use App\Models\Branch;
use App\Models\User;

class BranchPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnySystemRole(['super_admin', 'regional_admin', 'district_admin', 'branch_admin']);
    }

    public function view(User $user, Branch $branch): bool
    {
        return $user->canManageBranch($branch->id);
    }

    public function create(User $user): bool
    {
        return $user->hasSystemRole('super_admin');
    }

    public function update(User $user, Branch $branch): bool
    {
        return $user->hasSystemRole('super_admin') || $user->canManageBranch($branch->id);
    }

    public function delete(User $user, Branch $branch): bool
    {
        return $user->hasSystemRole('super_admin') && ! $branch->is_headquarters;
    }
}
