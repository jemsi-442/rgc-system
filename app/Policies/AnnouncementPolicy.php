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

        if ($user->hasSystemRole('super_admin')) {
            return true;
        }

        if ($model->is_global) {
            return $this->viewAny($user);
        }

        if ($model->targetBranches()->exists()) {
            return $user->effectiveBranchId() !== null
                && $model->targetBranches()->whereKey($user->effectiveBranchId())->exists();
        }

        return parent::view($user, $model);
    }

    public function update(User $user, mixed $model): bool
    {
        if (! $model instanceof Announcement) {
            return false;
        }

        if ($model->is_global || $model->targetBranches()->exists()) {
            return $user->hasSystemRole('super_admin');
        }

        return parent::update($user, $model);
    }

    public function delete(User $user, mixed $model): bool
    {
        if (! $model instanceof Announcement) {
            return false;
        }

        if ($model->is_global || $model->targetBranches()->exists()) {
            return $user->hasSystemRole('super_admin');
        }

        return parent::delete($user, $model);
    }
}
