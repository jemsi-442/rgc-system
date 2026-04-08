<?php

namespace App\Policies;

use App\Models\HomeSlider;
use App\Models\User;

class HomeSliderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasSystemRole('super_admin');
    }

    public function view(User $user, HomeSlider $slider): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, HomeSlider $slider): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, HomeSlider $slider): bool
    {
        return $this->viewAny($user);
    }
}
