<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy extends BranchScopedPolicy
{
    public function view(User $user, mixed $model): bool
    {
        if (! $model instanceof Announcement) {
            return false;
        }

        if ($model->is_global) {
            return $this->viewAny($user);
        }

        return parent::view($user, $model);
    }

    public function update(User $user, mixed $model): bool
    {
        if (! $model instanceof Announcement) {
            return false;
        }

        if ($model->is_global) {
            return $user->hasSystemRole('super_admin');
        }

        return parent::update($user, $model);
    }

    public function delete(User $user, mixed $model): bool
    {
        if (! $model instanceof Announcement) {
            return false;
        }

        if ($model->is_global) {
            return $user->hasSystemRole('super_admin');
        }

        return parent::delete($user, $model);
    }
}
