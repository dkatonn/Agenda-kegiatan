<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class TVController extends Controller
{
    protected int $agendaPerSlide = 7;

    public function index()
    {
        return view('tv', $this->getTvViewData());
    }

    public function state(): JsonResponse
    {
        return response()->json([
            'revision' => $this->getTvRevision(),
        ]);
    }

    public function payload(): JsonResponse
    {
        $data = $this->getTvViewData();

        return response()->json([
            'revision' => $data['tvRevision'],
            'backgroundUrl' => !empty($data['settings']['background']) ? asset('storage/' . $data['settings']['background']) . '?v=' . urlencode($data['tvRevision']) : null,
            'employeeHtml' => view('sections.employee', $data)->render(),
            'videoHtml' => view('sections.video', $data)->render(),
            'agendaTuHtml' => view('sections.agendatu', $data)->render(),
            'agendaDataHtml' => view('sections.agendadata', $data)->render(),
            'runningTextHtml' => view('sections.runningtext', $data)->render(),
        ]);
    }

    protected function buildAgendaSlides($agendas, ?int $perSlide = null)
    {
        $perSlide ??= $this->agendaPerSlide;
        $today = now()->startOfDay();

        $pinned = $agendas
            ->filter(fn ($agenda) => Carbon::parse($agenda->date)->startOfDay()->equalTo($today))
            ->values();

        $others = $agendas
            ->reject(fn ($agenda) => Carbon::parse($agenda->date)->startOfDay()->equalTo($today))
            ->values();

        $visiblePinned = $pinned->take($perSlide)->values();
        $remainingSlots = max(0, $perSlide - $visiblePinned->count());

        $slides = $remainingSlots > 0
            ? $others->chunk($remainingSlots)->filter(fn ($chunk) => $chunk->isNotEmpty())->values()
            : collect();

        return [
            'pinned' => $visiblePinned,
            'slides' => $slides,
        ];
    }

    protected function getTvRevision(): string
    {
        $agendaCount = Agenda::query()->count();
        $employeeCount = Employee::query()->count();
        $settingCount = Setting::query()->count();

        $agendaUpdatedAt = $this->normalizeRevisionTimestamp(Agenda::query()->latest('updated_at')->value('updated_at'));
        $employeeUpdatedAt = $this->normalizeRevisionTimestamp(Employee::query()->latest('updated_at')->value('updated_at'));
        $settingUpdatedAt = $this->normalizeRevisionTimestamp(Setting::query()->latest('updated_at')->value('updated_at'));

        return sha1(implode('|', [
            $agendaCount,
            $employeeCount,
            $settingCount,
            $agendaUpdatedAt,
            $employeeUpdatedAt,
            $settingUpdatedAt,
        ]));
    }

    protected function normalizeRevisionTimestamp($value): string
    {
        return $value ? Carbon::parse($value)->toDateTimeString() : 'none';
    }

    protected function getTvViewData(): array
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $activeVideo = Schema::hasTable('videos')
            ? Video::query()->where('is_active', true)->latest()->first()
            : null;

        $settings['video'] = $activeVideo
            ? $this->resolveVideoUrlFromModel($activeVideo)
            : $this->resolveVideoUrlFromPath($settings['video'] ?? null);

        $employees = Employee::latest()->get();
        $tvRevision = $this->getTvRevision();
        $agendas = Agenda::query()
            ->whereDate('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->sortBy(function ($agenda) {
                $agendaDate = Carbon::parse($agenda->date)->startOfDay();
                $today = now()->startOfDay();
                $tomorrow = now()->copy()->addDay()->startOfDay();

                if ($agendaDate->equalTo($today)) {
                    return '0-' . $agendaDate->format('Ymd') . '-' . $agenda->time;
                }

                if ($agendaDate->equalTo($tomorrow)) {
                    return '1-' . $agendaDate->format('Ymd') . '-' . $agenda->time;
                }

                if ($agendaDate->greaterThan($tomorrow)) {
                    return '2-' . $agendaDate->format('Ymd') . '-' . $agenda->time;
                }

                return '3-' . $agendaDate->format('Ymd') . '-' . $agenda->time;
            })
            ->values();

        $agendaTu = $agendas
            ->values()
            ->filter(fn ($agenda, $index) => $index % 2 === 0)
            ->values();

        $agendaData = $agendas
            ->values()
            ->filter(fn ($agenda, $index) => $index % 2 === 1)
            ->values();

        ['pinned' => $agendaTuPinned, 'slides' => $agendaTuSlides] = $this->buildAgendaSlides($agendaTu);
        ['pinned' => $agendaDataPinned, 'slides' => $agendaDataSlides] = $this->buildAgendaSlides($agendaData);

        $agendaPerSlide = $this->agendaPerSlide;
        $agendaTuRemainingSlots = max(0, $this->agendaPerSlide - $agendaTuPinned->count());
        $agendaDataRemainingSlots = max(0, $this->agendaPerSlide - $agendaDataPinned->count());

        return compact(
            'settings',
            'employees',
            'tvRevision',
            'agendaPerSlide',
            'agendaTuPinned',
            'agendaTuSlides',
            'agendaDataPinned',
            'agendaDataSlides',
            'agendaTuRemainingSlots',
            'agendaDataRemainingSlots'
        );
    }

    protected function resolveVideoUrlFromModel(Video $video): ?string
    {
        $path = null;

        if (Schema::hasColumn('videos', 'file_path') && is_string($video->file_path) && trim($video->file_path) !== '') {
            $path = trim($video->file_path);
        }

        if ($path === null && Schema::hasColumn('videos', 'source_path') && is_string($video->source_path) && trim($video->source_path) !== '') {
            $path = trim($video->source_path);
        }

        return $this->resolveVideoUrlFromPath($path);
    }

    protected function resolveVideoUrlFromPath(?string $path): ?string
    {
        $path = is_string($path) ? trim($path) : '';

        if ($path === '') {
            return null;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        $publicPrefixedPath = preg_replace('#^storage/#', '', ltrim($path, '/'));
        if (is_string($publicPrefixedPath) && Storage::disk('public')->exists($publicPrefixedPath)) {
            return Storage::disk('public')->url($publicPrefixedPath);
        }

        return asset(ltrim($path, '/'));
    }
}
