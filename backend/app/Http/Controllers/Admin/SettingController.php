<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index(): View
    {
        $all = Setting::orderBy('group')->get()->groupBy('group');

        return view('admin.settings.index', [
            // Branding (logo + icon) gets a dedicated uploader UI, so exclude it
            // from the generic key/value editor below.
            'groups' => $all->except('branding'),
            'logo' => Setting::get('site.logo'),
            'icon' => Setting::get('site.icon'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'settings' => ['sometimes', 'array'],
            'logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'icon' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg,ico', 'max:1024'],
        ]);

        // Plain key/value settings
        foreach ((array) $request->input('settings', []) as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            if ($setting) {
                $setting->update(['value' => is_array($value) ? json_encode($value) : (string) $value]);
            }
        }

        // Branding uploads — stored on the public disk, path saved as a setting.
        foreach (['logo' => 'site.logo', 'icon' => 'site.icon'] as $field => $key) {
            if ($request->hasFile($field)) {
                $this->storeBranding($request, $field, $key);
            }
        }

        return back()->with('status', 'Settings saved.');
    }

    /** Remove a branding image and reset to the default. */
    public function removeBranding(Request $request): RedirectResponse
    {
        $key = $request->input('key');
        if (! in_array($key, ['site.logo', 'site.icon'], true)) {
            return back();
        }

        $current = Setting::get($key);
        if ($current && str_starts_with((string) $current, '/storage/branding/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $current));
        }
        Setting::updateOrCreate(['key' => $key], ['group' => 'branding', 'value' => '', 'type' => 'string', 'is_public' => true]);

        return back()->with('status', 'Branding reset to default.');
    }

    private function storeBranding(Request $request, string $field, string $key): void
    {
        // Delete the previous uploaded file if it lived on our public disk.
        $current = Setting::get($key);
        if ($current && str_starts_with((string) $current, '/storage/branding/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $current));
        }

        $path = $request->file($field)->store('branding', 'public');

        Setting::updateOrCreate(
            ['key' => $key],
            ['group' => 'branding', 'value' => '/storage/'.$path, 'type' => 'string', 'is_public' => true]
        );
    }
}
