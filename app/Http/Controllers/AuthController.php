<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\Branch;
use App\Models\District;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login', [
            'demoCredentials' => $this->demoCredentials(),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => __('Invalid login credentials.')])->onlyInput('email');
        }

        $user = $request->user();

        if (! $user || ! $user->isActive()) {
            $this->logoutInactiveUser($request);

            return back()
                ->withErrors(['email' => __('Your account is inactive. Please contact church leadership.')])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $locale = $this->resolveSupportedLocale($user->locale, $request->session()->get('locale'));
        $request->session()->put('locale', $locale);
        app()->setLocale($locale);

        return redirect()->intended(route('dashboard'));
    }

    public function showRegister(): View
    {
        return view('auth.register', [
            'regions' => Region::query()->orderBy('name')->get(),
            'districts' => District::query()->orderBy('name')->get(),
            'branches' => Branch::query()->orderBy('name')->get(),
        ]);
    }

    public function register(RegisterUserRequest $request): RedirectResponse
    {
        $branchId = $request->integer('branch_id');
        $locale = $this->resolveSupportedLocale($request->session()->get('locale'), app()->getLocale());

        $user = User::query()->create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'password' => Hash::make($request->string('password')->toString()),
            'role' => 'member',
            'status' => 'active',
            'locale' => $locale,
            'region_id' => $request->integer('region_id'),
            'district_id' => $request->integer('district_id'),
            'branch_id' => $branchId,
            'church_id' => $branchId,
        ]);

        $user->syncRoles(['member']);

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('locale', $locale);
        app()->setLocale($locale);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function logoutInactiveUser(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    private function demoCredentials(): array
    {
        if (! app()->environment(['local', 'testing'])) {
            return [];
        }

        return [
            [
                'role' => __('Super Admin'),
                'email' => 'superadmin@rgc.or.tz',
                'password' => 'ChangeMe123!',
                'scope' => __('National workspace with full platform authority.'),
            ],
            [
                'role' => __('Regional Admin'),
                'email' => 'regionaladmin@rgc.or.tz',
                'password' => 'ChangeMe123!',
                'scope' => __('Regional dashboard for Dar es Salaam governance.'),
            ],
            [
                'role' => __('District Admin'),
                'email' => 'districtadmin@rgc.or.tz',
                'password' => 'ChangeMe123!',
                'scope' => __('District dashboard for Temeke branches.'),
            ],
            [
                'role' => __('Branch Admin'),
                'email' => 'branchadmin@rgc.or.tz',
                'password' => 'ChangeMe123!',
                'scope' => __('Single-branch dashboard for local operations.'),
            ],
        ];
    }

    private function resolveSupportedLocale(?string ...$candidates): string
    {
        $supported = config('app.supported_locales', ['en', 'sw']);

        foreach ($candidates as $candidate) {
            if ($candidate && in_array($candidate, $supported, true)) {
                return $candidate;
            }
        }

        return config('app.locale', 'sw');
    }
}
