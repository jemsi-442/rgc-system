<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
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
}
