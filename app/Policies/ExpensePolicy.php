<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy extends BranchScopedPolicy
{
    private function canManageExpenseRecords(User $user): bool
    {
        return $user->hasAnySystemRole(['super_admin', 'branch_admin', 'pastor', 'bishop', 'accountant']);
    }

    public function viewAny(User $user): bool
    {
        return $this->canManageExpenseRecords($user);
    }

    public function create(User $user): bool
    {
        return $this->canManageExpenseRecords($user);
    }

    public function view(User $user, mixed $model): bool
    {
        return $model instanceof Expense
            && $this->canManageExpenseRecords($user)
            && $this->scoped($user, $model);
    }

    public function update(User $user, mixed $model): bool
    {
        return $model instanceof Expense
            && $this->canManageExpenseRecords($user)
            && $this->scoped($user, $model);
    }

    public function delete(User $user, mixed $model): bool
    {
        return $model instanceof Expense
            && $this->canManageExpenseRecords($user)
            && $this->scoped($user, $model);
    }
}
