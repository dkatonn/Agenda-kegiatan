<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\AdminActivityLogger;
use App\Services\KemendagriPegawaiService;
use App\Services\TvBroadcastService;
use App\Services\TvRevisionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function __construct(
        protected KemendagriPegawaiService $kemendagriPegawaiService,
        protected TvRevisionService $tvRevisionService,
        protected TvBroadcastService $tvBroadcastService,
        protected AdminActivityLogger $activityLogger,
    ) {
    }

    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $birthdayTickerText = $this->kemendagriPegawaiService->birthdaySegments()->implode(' | ');
        $tickerText = $this->kemendagriPegawaiService->buildTickerText($settings['running_text'] ?? null);

        return view('admin.running-text', compact('settings', 'tickerText', 'birthdayTickerText'));
    }

    public function background()
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('admin.background', compact('settings'));
    }

    public function update(Request $request)
    {
        $updatedKeys = [];

        if ($request->hasFile('bg_image')) {
            $currentBackground = Setting::where('key', 'background')->value('value');

            if ($currentBackground) {
                Storage::disk('public')->delete($currentBackground);
            }

            $path = $request->file('bg_image')->store('background', 'public');
            Setting::updateOrCreate(['key' => 'background'], ['value' => $path]);
            $updatedKeys[] = 'background';
        }

        if ($request->boolean('remove_background')) {
            $currentBackground = Setting::where('key', 'background')->value('value');

            if ($currentBackground) {
                Storage::disk('public')->delete($currentBackground);
                Setting::where('key', 'background')->delete();
                $updatedKeys[] = 'background_removed';
            }
        }

        foreach ($request->except('_token', 'bg_image', 'remove_background') as $key => $value) {
            $currentValue = Setting::where('key', $key)->value('value');

            if ((string) $currentValue === (string) $value) {
                continue;
            }

            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            $updatedKeys[] = $key;
        }

        $updatedKeys = array_values(array_unique($updatedKeys));

        if ($updatedKeys === []) {
            return back()->with('info', 'Tidak ada perubahan pada pengaturan.');
        }

        $this->activityLogger->log('User mengubah pengaturan dashboard/TV', [
            'updated_keys' => $updatedKeys,
        ]);
        $this->tvBroadcastService->dispatchUpdate($this->tvRevisionService->current());

        return back();
    }
}
