<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('admin.settings.edit', ['settings' => SiteSetting::values()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_title' => ['required', 'string', 'max:120'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'logo_upload' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'dimensions:max_width=3000,max_height=1500', 'max:2048'],
            'logo_path' => ['nullable', 'string', 'max:255'],
            'banner_title' => ['required', 'string', 'max:160'],
            'banner_description' => ['nullable', 'string', 'max:500'],
            'footer_copyright' => ['required', 'string', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:160'],
            'seo_description' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('logo_upload')) {
            $data['logo_path'] = $request->file('logo_upload')->store('brand', 'public');
        } elseif ($request->filled('logo_path')) {
            $logoPath = trim((string) $request->input('logo_path'));
            $data['logo_path'] = preg_match('/^(javascript:|data:)/i', $logoPath) ? null : $logoPath;
        } else {
            $data['logo_path'] = null;
        }

        unset($data['logo_upload']);

        foreach ($data as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Website settings updated.');
    }
}
