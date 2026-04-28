<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Employee;
use App\Models\Agenda;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    public function index()
    {
        $setting = Setting::pluck('value', 'key')->toArray();
        $employee = Employee::all();
        $agenda = Agenda::latest()->get();
        $today = now()->startOfDay();
        $dashboardAgendaUpcoming = Agenda::query()
            ->whereDate('date', '>=', $today)
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->map(function (Agenda $item) use ($today) {
                $agendaDate = Carbon::parse($item->date)->startOfDay();
                $item->dashboard_bucket = $agendaDate->equalTo($today) ? 'today' : 'upcoming';

                return $item;
            });
        $dashboardAgendaPast = Agenda::query()
            ->whereDate('date', '<', $today)
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->map(function (Agenda $item) {
                $item->dashboard_bucket = 'past';

                return $item;
            });
        $dashboardAgendaUpcomingTotal = $dashboardAgendaUpcoming->count();
        $dashboardAgendaPastTotal = $dashboardAgendaPast->count();
        $dashboardAgendaUpcoming = $dashboardAgendaUpcoming->take(4)->values();
        $dashboardAgendaPast = $dashboardAgendaPast->take(4)->values();
        $activeVideo = Schema::hasTable('videos')
            ? Video::query()->where('is_active', true)->latest()->first()
            : null;
        $activeVideoPath = $activeVideo?->file_path ?? ($setting['video'] ?? null);
        $activeVideoUrl = $activeVideoPath ? Storage::disk('public')->url($activeVideoPath) : null;
        $videoCount = Schema::hasTable('videos')
            ? Video::query()->count()
            : (! empty($setting['video']) ? 1 : 0);

        return view('admin.dashboard', compact(
            'setting',
            'employee',
            'agenda',
            'activeVideoUrl',
            'videoCount',
            'dashboardAgendaUpcoming',
            'dashboardAgendaPast',
            'dashboardAgendaUpcomingTotal',
            'dashboardAgendaPastTotal',
        ));
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
