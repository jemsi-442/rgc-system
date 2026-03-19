<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\Branch;
use App\Models\BranchMessage;
use App\Models\Event;
use App\Models\Expense;
use App\Models\Offering;
use App\Models\OfferingPayment;
use App\Models\User;
use App\Policies\AnnouncementPolicy;
use App\Policies\BranchMessagePolicy;
use App\Policies\BranchPolicy;
use App\Policies\EventPolicy;
use App\Policies\ExpensePolicy;
use App\Policies\OfferingPaymentPolicy;
use App\Policies\OfferingPolicy;
use App\Policies\UserPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('login', fn (Request $request) => [
            Limit::perMinute(5)->by($this->credentialThrottleKey($request)),
            Limit::perMinute(20)->by($request->ip()),
        ]);

        RateLimiter::for('register', fn (Request $request) => [
            Limit::perMinute(3)->by($request->ip()),
        ]);

        RateLimiter::for('api-login', fn (Request $request) => [
            Limit::perMinute(6)->by($this->credentialThrottleKey($request)),
            Limit::perMinute(30)->by($request->ip()),
        ]);

        RateLimiter::for('api', fn (Request $request) => [
            Limit::perMinute(30)->by((string) ($request->user()?->id ?? $request->ip())),
        ]);

        Gate::policy(Branch::class, BranchPolicy::class);
        Gate::policy(Announcement::class, AnnouncementPolicy::class);
        Gate::policy(Event::class, EventPolicy::class);
        Gate::policy(Offering::class, OfferingPolicy::class);
        Gate::policy(OfferingPayment::class, OfferingPaymentPolicy::class);
        Gate::policy(Expense::class, ExpensePolicy::class);
        Gate::policy(BranchMessage::class, BranchMessagePolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        Model::preventLazyLoading(! app()->isProduction());
    }

    private function credentialThrottleKey(Request $request): string
    {
        $email = Str::lower(trim((string) $request->input('email')));

        return $email . '|' . $request->ip();
    }
}
