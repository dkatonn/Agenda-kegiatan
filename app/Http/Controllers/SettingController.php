<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('admin.running-text', compact('settings'));
    }

    public function update(Request $request)
    {
        if ($request->hasFile('bg_image')) {
            $currentBackground = Setting::where('key', 'background')->value('value');

            if ($currentBackground) {
                Storage::disk('public')->delete($currentBackground);
            }

            $path = $request->file('bg_image')->store('background', 'public');
            Setting::updateOrCreate(['key' => 'background'], ['value' => $path]);
        }

        if ($request->boolean('remove_background')) {
            $currentBackground = Setting::where('key', 'background')->value('value');

            if ($currentBackground) {
                Storage::disk('public')->delete($currentBackground);
            }

            Setting::where('key', 'background')->delete();
        }

        foreach ($request->except('_token', 'bg_image', 'remove_background') as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back();
    }
}
