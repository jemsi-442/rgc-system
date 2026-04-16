<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function editProfile()
    {
        return view('panel.account.profile');
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($user->id)
                    ->where(fn ($query) => $query->whereNull('deleted_at')),
            ],
            'phone' => ['required', 'string', 'regex:/^255\d{9}$/'],
        ]);

        $user->update([
            'name' => trim((string) $validated['name']),
            'email' => trim((string) $validated['email']),
            'phone' => $this->normalizeTanzaniaPhone($validated['phone']),
        ]);

        return redirect()
            ->route('account.profile.edit')
            ->with('status', __('Your contact details have been updated.'));
    }

    public function editPassword()
    {
        return view('panel.account.password');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        $user->update([
            'password' => $validated['password'],
            ...$user->invalidatedAuthAttributes(),
        ]);

        Auth::logoutOtherDevices($validated['password']);
        $request->session()->regenerate();

        return redirect()
            ->route('account.password.edit')
            ->with('status', __('Your password has been updated successfully.'));
    }

    private function normalizeTanzaniaPhone(mixed $value): string
    {
        $phone = preg_replace('/\D+/', '', (string) $value) ?? '';

        if (str_starts_with($phone, '0') && strlen($phone) === 10) {
            return '255' . substr($phone, 1);
        }

        if (strlen($phone) === 9 && in_array($phone[0], ['6', '7'], true)) {
            return '255' . $phone;
        }

        return $phone;
    }
}
