<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class VideoController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('admin.video', compact('settings'));
    }

    public function store(Request $request)
    {
        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('video', 'public');

            Setting::updateOrCreate(
                ['key' => 'video'],
                ['value' => $path]
            );
        }

        return back()->with('success', 'Video berhasil diupload');
    }
}
