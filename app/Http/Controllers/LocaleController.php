<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $supported = config('app.supported_locales', ['en', 'sw']);
        $locale = $request->input('locale');

        if (in_array($locale, $supported, true)) {
            $request->session()->put('locale', $locale);

            if ($request->user()) {
                $request->user()->forceFill(['locale' => $locale])->save();
            }
        }

        return back();
    }
}
