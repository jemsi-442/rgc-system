<?php

namespace App\Policies;

use App\Models\SystemAssistantTopic;
use App\Models\User;

class SystemAssistantTopicPolicy
{
    private function canManageWorkspace(User $user): bool
    {
        if ($user->hasSystemRole('super_admin')) {
            return true;
        }

        return $user->hasSystemRole('regional_admin') && ! blank($user->region_id);
    }

    public function viewAny(User $user): bool
    {
        return $this->canManageWorkspace($user);
    }

    public function create(User $user): bool
    {
        return $this->canManageWorkspace($user);
    }

    public function view(User $user, SystemAssistantTopic $topic): bool
    {
        if ($user->hasSystemRole('super_admin')) {
            return true;
        }

        return $user->hasSystemRole('regional_admin')
            && ! blank($user->region_id)
            && (int) $topic->region_id === (int) $user->region_id;
    }

    public function update(User $user, SystemAssistantTopic $topic): bool
    {
        return $this->view($user, $topic);
    }

    public function delete(User $user, SystemAssistantTopic $topic): bool
    {
        return $this->view($user, $topic);
    }

    public function export(User $user): bool
    {
        return $this->canManageWorkspace($user);
    }

    public function import(User $user): bool
    {
        return $user->hasSystemRole('super_admin');
    }

    public function restoreDefaults(User $user): bool
    {
        return $user->hasSystemRole('super_admin');
    }

    public function restoreVersion(User $user, SystemAssistantTopic $topic): bool
    {
        return $this->update($user, $topic);
    }
}
