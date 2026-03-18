<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Models\Jumuiya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingController extends Controller
{
    /**
     * Display all settings grouped
     */
    public function index()
    {
        // Get settings grouped by category
        $generalSettings = Setting::byGroup('general')->get()->keyBy('key');
        $contactSettings = Setting::byGroup('contact')->get()->keyBy('key');
        $financialSettings = Setting::byGroup('financial')->get()->keyBy('key');
        $systemSettings = Setting::byGroup('system')->get()->keyBy('key');
        

        // Get jumuiyas for admin with members count
        $jumuiyas = Jumuiya::with('leader')->withCount('members')->orderBy('name')->get();

        return view('panel.settings.index', compact(
            'generalSettings',
            'contactSettings',
            'financialSettings',
            'systemSettings',
            'jumuiyas'
        ));
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'church_name' => 'required|string|max:255',
            'church_slogan' => 'nullable|string|max:255',
            'church_description' => 'nullable|string|max:1000',
            'fiscal_year_start' => 'nullable|string|max:10',
        ], [
            'church_name.required' => 'Tafadhali ingiza jina la kanisa',
            'church_name.max' => 'Jina la kanisa ni refu mno',
            'church_slogan.max' => 'Kauli mbiu ni ndefu mno',
            'church_description.max' => 'Maelezo ni marefu mno',
            'fiscal_year_start.max' => 'Mwanzo wa mwaka wa fedha si sahihi',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value, 'text', 'general');
        }

        return redirect()->route('settings.index')
            ->with('success', 'Mipangilio ya jumla imebadilishwa kikamilifu');
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Tafadhali ingiza jina lako',
            'name.max' => 'Jina ni refu mno',
            'email.required' => 'Tafadhali ingiza barua pepe',
            'email.email' => 'Barua pepe si sahihi',
            'email.max' => 'Barua pepe ni ndefu mno',
            'email.unique' => 'Barua pepe tayari imetumika',
            'phone.max' => 'Nambari ya simu ni ndefu mno',
        ]);

        $user->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'Wasifu wako umebadilishwa kikamilifu');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => [
                'required',
                'confirmed',
                Password::min(6)
                    ->mixedCase()      // Uppercase and lowercase
                    ->numbers()        // At least one number
                    ->symbols(),       // At least one special character
            ],
        ], [
            'current_password.required' => 'Tafadhali ingiza nywila ya sasa',
            'password.required' => 'Tafadhali ingiza nywila mpya',
            'password.confirmed' => 'Nywila mpya hazifanani',
            'password.min' => 'Nywila lazima iwe na angalau herufi 6',
        ]);

        $user = Auth::user();

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->back()
                ->with('error', 'Nywila ya sasa si sahihi')
                ->withInput();
        }

        // Update password and mark as changed
        $user->update([
            'password' => Hash::make($validated['password']),
            'password_changed' => true
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Nywila imebadilishwa kikamilifu');
    }

    /**
     * Show church-specific settings
     */
    public function church()
    {
        $churchSettings = Setting::byGroup('church')->get()->keyBy('key');

        return view('panel.settings.church', compact('churchSettings'));
    }

    /**
     * Update church settings
     */
    public function updateChurch(Request $request)
    {
        $validated = $request->validate([
            'church_address' => 'nullable|string|max:255',
            'church_city' => 'nullable|string|max:100',
            'church_region' => 'nullable|string|max:100',
            'church_country' => 'nullable|string|max:100',
            'church_postal_code' => 'nullable|string|max:20',
            'church_phone' => 'nullable|string|max:20',
            'church_email' => 'nullable|email|max:255',
            'church_website' => 'nullable|url|max:255',
            'founded_year' => 'nullable|integer|min:1800|max:' . date('Y'),
        ], [
            'church_address.max' => 'Anwani ni ndefu mno',
            'church_city.max' => 'Jina la mji ni refu mno',
            'church_region.max' => 'Jina la mkoa ni refu mno',
            'church_country.max' => 'Jina la nchi ni refu mno',
            'church_postal_code.max' => 'Msimbo wa posta ni mrefu mno',
            'church_phone.max' => 'Nambari ya simu ni ndefu mno',
            'church_email.email' => 'Barua pepe si sahihi',
            'church_email.max' => 'Barua pepe ni ndefu mno',
            'church_website.url' => 'Tovuti si sahihi',
            'church_website.max' => 'Tovuti ni ndefu mno',
            'founded_year.integer' => 'Mwaka wa kuanzishwa si sahihi',
            'founded_year.min' => 'Mwaka wa kuanzishwa si sahihi',
            'founded_year.max' => 'Mwaka wa kuanzishwa si sahihi',
        ]);

        foreach ($validated as $key => $value) {
            $type = in_array($key, ['founded_year']) ? 'integer' : 'text';
            Setting::set($key, $value, $type, 'church');
        }

        return redirect()->route('settings.church')
            ->with('success', 'Mipangilio ya kanisa imebadilishwa kikamilifu');
    }

    /**
     * Show system settings
     */
    public function system()
    {
        $systemSettings = Setting::byGroup('system')->get()->keyBy('key');

        return view('panel.settings.system', compact('systemSettings'));
    }

    /**
     * Update system settings
     */
    public function updateSystem(Request $request)
    {
        $validated = $request->validate([
            'timezone' => 'nullable|string|max:100',
            'date_format' => 'nullable|string|max:50',
            'time_format' => 'nullable|string|max:50',
            'currency' => 'nullable|string|max:10',
            'currency_symbol' => 'nullable|string|max:10',
            'language' => 'nullable|string|max:10',
            'items_per_page' => 'nullable|integer|min:5|max:100',
        ], [
            'timezone.max' => 'Eneo la muda ni refu mno',
            'date_format.max' => 'Muundo wa tarehe si sahihi',
            'time_format.max' => 'Muundo wa muda si sahihi',
            'currency.max' => 'Jina la sarafu ni refu mno',
            'currency_symbol.max' => 'Alama ya sarafu ni ndefu mno',
            'language.max' => 'Lugha si sahihi',
            'items_per_page.integer' => 'Idadi ya vitu kwa ukurasa lazima iwe nambari',
            'items_per_page.min' => 'Idadi ya vitu kwa ukurasa ni ndogo mno',
            'items_per_page.max' => 'Idadi ya vitu kwa ukurasa ni kubwa mno',
        ]);

        foreach ($validated as $key => $value) {
            $type = in_array($key, ['items_per_page']) ? 'integer' : 'text';
            Setting::set($key, $value, $type, 'system');
        }

        return redirect()->route('settings.system')
            ->with('success', 'Mipangilio ya mfumo imebadilishwa kikamilifu');
    }
}
