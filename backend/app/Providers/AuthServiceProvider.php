<?php

namespace App\Providers;

use App\Models\Church;
use App\Models\Region;
use App\Models\Slide;
use App\Policies\ChurchPolicy;
use App\Policies\RegionPolicy;
use App\Policies\SlidePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Region::class => RegionPolicy::class,
        Church::class => ChurchPolicy::class,
        Slide::class => SlidePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, string $ability) {
            if ($user->hasAnyRoleOrLegacy(['super_admin'])) {
                return true;
            }

            return null;
        });
    }
}
