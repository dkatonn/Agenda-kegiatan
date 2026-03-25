<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Employee;
use App\Models\Agenda;

class AdminController extends Controller
{
    public function index()
    {
        $setting = Setting::pluck('value', 'key')->toArray();
        $employee = Employee::all();
        $agenda = Agenda::latest()->get();

        return view('admin.dashboard', compact('setting', 'employee', 'agenda'));
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