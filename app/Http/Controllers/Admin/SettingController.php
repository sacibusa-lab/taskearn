<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = AdminSetting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable', 'string'],
            'uploads' => ['nullable', 'array'],
            'uploads.*' => ['nullable', 'image', 'mimes:png,jpg,jpeg,ico,gif,svg,webp', 'max:2048'],
        ]);

        // Handle file uploads
        $uploadedKeys = [];
        if ($request->hasFile('uploads')) {
            foreach ($request->file('uploads') as $key => $file) {
                $setting = AdminSetting::where('key', $key)->first();
                if ($setting && $file->isValid()) {
                    // Delete old uploaded file if exists
                    $oldValue = $setting->value;
                    if ($oldValue && str_starts_with($oldValue, '/uploads/settings/')) {
                        $oldPath = public_path($oldValue);
                        if (file_exists($oldPath)) {
                            @unlink($oldPath);
                        }
                    }

                    // Store in public/uploads/settings/ for direct access
                    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_.]/', '', $file->getClientOriginalName());
                    $file->move(public_path('uploads/settings'), $filename);
                    $url = '/uploads/settings/' . $filename;
                    $setting->update(['value' => $url]);
                    $uploadedKeys[] = $key;
                }
            }
        }

        // Handle text settings (skip keys that were just uploaded)
        foreach ($request->settings as $key => $value) {
            if (in_array($key, $uploadedKeys)) {
                continue;
            }
            $setting = AdminSetting::where('key', $key)->first();
            if ($setting) {
                $setting->update(['value' => $value]);
            }
        }

        $tab = $request->query('tab', 'global');

        return redirect()->route('admin.settings.index', ['tab' => $tab])
            ->with('success', 'Settings saved successfully.');
    }
}
