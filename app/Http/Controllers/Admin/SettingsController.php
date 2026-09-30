<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\ImageResolver;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
            'logo_path' => $request->hasFile('logo_upload')
                ? ['nullable', 'string', 'max:255']
                : ['nullable', 'string', 'max:255', function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_string($value)) {
                        $fail('The logo path or URL is invalid.');

                        return;
                    }

                    $path = trim($value);
                    if ($path === '') {
                        return;
                    }

                    if (preg_match('/[\x00-\x1F\\\\]/', $path)) {
                        $fail('The logo path or URL is invalid.');

                        return;
                    }

                    $parsedPath = parse_url($path);
                    if ($parsedPath === false) {
                        $fail('The logo path or URL is invalid.');

                        return;
                    }

                    $scheme = $parsedPath['scheme'] ?? null;
                    if ($scheme !== null && ! ImageResolver::isExternalUrl($path)) {
                        $fail('The logo URL must use HTTP or HTTPS.');

                        return;
                    }

                    if ($scheme === null && (str_starts_with($path, '//') || preg_match('#(^|/)\.\.?(/|$)#', $path))) {
                        $fail('The logo path must stay within the public assets.');
                    }
                }],
            'banner_title' => ['required', 'string', 'max:160'],
            'banner_description' => ['nullable', 'string', 'max:500'],
            'footer_copyright' => ['required', 'string', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:160'],
            'seo_description' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('logo_upload')) {
            $logoPath = $request->file('logo_upload')->store('brand', 'public');
            if (! $logoPath) {
                throw ValidationException::withMessages(['logo_upload' => 'The logo upload could not be stored.']);
            }

            $data['logo_path'] = $logoPath;
        } elseif ($request->filled('logo_path')) {
            $data['logo_path'] = trim((string) $request->input('logo_path'));
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
