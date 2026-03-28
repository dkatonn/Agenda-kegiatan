<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Employee;
use App\Models\Agenda;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    public function index()
    {
        $setting = Setting::pluck('value', 'key')->toArray();
        $employee = Employee::all();
        $agenda = Agenda::latest()->get();
        $activeVideo = Schema::hasTable('videos')
            ? Video::query()->where('is_active', true)->latest()->first()
            : null;
        $activeVideoPath = $activeVideo?->file_path ?? ($setting['video'] ?? null);
        $activeVideoUrl = $activeVideoPath ? Storage::disk('public')->url($activeVideoPath) : null;
        $videoCount = Schema::hasTable('videos')
            ? Video::query()->count()
            : (! empty($setting['video']) ? 1 : 0);

        return view('admin.dashboard', compact('setting', 'employee', 'agenda', 'activeVideoUrl', 'videoCount'));
    }

    public function update(Request $request)
    {
        if ($request->hasFile('bg_image')) {
            $path = $request->file('bg_image')->store('background', 'public');
            Setting::updateOrCreate(
                ['key' => 'background'],
                ['value' => $path]
            );
        }

        // 🔥 tambah ini
        if ($request->filled('running_text')) {
            Setting::updateOrCreate(
                ['key' => 'running_text'],
                ['value' => $request->running_text]
            );
        }

        return back()->with('success', 'Setting berhasil disimpan');
    }
}
