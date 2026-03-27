<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('admin.video', compact('settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'video' => ['required', 'file', 'mimetypes:video/mp4,video/webm,video/ogg', 'max:51200'],
        ]);

        if (! $request->hasFile('video')) {
            return back()->with('error', 'File video tidak ditemukan.');
        }

        $oldVideoPath = Setting::where('key', 'video')->value('value');
        $path = $request->file('video')->store('video', 'public');

        if ($oldVideoPath) {
            Storage::disk('public')->delete($oldVideoPath);
        }

        Setting::updateOrCreate(
            ['key' => 'video'],
            ['value' => $path]
        );

        return back()->with('success', 'Video berhasil diupload');
    }

    public function delete()
    {
        $videoPath = Setting::where('key', 'video')->value('value');

        if ($videoPath) {
            Storage::disk('public')->delete($videoPath);
            Setting::where('key', 'video')->delete();
        }

        return back()->with('success', 'Video berhasil dihapus');
    }
}
