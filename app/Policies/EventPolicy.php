<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy extends BranchScopedPolicy
{
    private function canManageEvents(User $user): bool
    {
        return $user->hasAnySystemRole(['super_admin', 'branch_admin', 'pastor', 'bishop', 'accountant']);
    }

    public function viewAny(User $user): bool
    {
        return $this->canManageEvents($user);
    }

    public function create(User $user): bool
    {
        return $this->canManageEvents($user);
    }

    public function view(User $user, mixed $model): bool
    {
        return $model instanceof Event
            && $this->canManageEvents($user)
            && $this->scoped($user, $model);
    }

    public function update(User $user, mixed $model): bool
    {
        return $model instanceof Event
            && $this->canManageEvents($user)
            && $this->scoped($user, $model);
    }

    public function delete(User $user, mixed $model): bool
    {
        return $model instanceof Event
            && $this->canManageEvents($user)
            && $this->scoped($user, $model);
    }
}
