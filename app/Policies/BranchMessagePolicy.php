<?php

namespace App\Policies;

use App\Models\BranchMessage;
use App\Models\User;

class BranchMessagePolicy extends BranchScopedPolicy
{
    public function create(User $user): bool
    {
        return $user->hasAnySystemRole([
            'super_admin',
            'regional_admin',
            'district_admin',
            'branch_admin',
            'pastor',
            'bishop',
            'accountant',
            'member',
        ]);
    }

    public function update(User $user, mixed $model): bool
    {
        if (! $model instanceof BranchMessage) {
            return false;
        }

        if (! $this->scoped($user, $model)) {
            return false;
        }

        if ($user->id !== $model->user_id) {
            return false;
        }

        if (! $model->created_at) {
            return false;
        }

        return $model->created_at->copy()->addMinutes(15)->isFuture();
    }

    public function delete(User $user, mixed $model): bool
    {
        if (! $model instanceof BranchMessage) {
            return false;
        }

        if (! $this->scoped($user, $model)) {
            return false;
        }

        if ($user->id === $model->user_id) {
            return true;
        }

        return $user->hasAnySystemRole(['super_admin', 'regional_admin', 'district_admin', 'branch_admin']);
    }
}
