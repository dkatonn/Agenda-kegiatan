<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\Video;
use App\Services\KemendagriPegawaiService;
use App\Services\TataUsahaAgendaService;
use App\Services\TvRevisionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class TVController extends Controller
{
    public function __construct(
        protected TataUsahaAgendaService $tataUsahaAgendaService,
        protected KemendagriPegawaiService $kemendagriPegawaiService,
        protected TvRevisionService $tvRevisionService,
    ) {}

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
        return $this->tvRevisionService->current();
    }

    protected function getTvViewData(): array
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $activeVideo = $this->findActiveVideo();

        $settings['video'] = $activeVideo
            ? $this->resolveVideoUrlFromModel($activeVideo)
            : $this->resolveVideoUrlFromPath($settings['video'] ?? null);
        $tickerText = $this->kemendagriPegawaiService->buildTickerText($settings['running_text'] ?? null);

        $employees = Employee::query()
            ->when(
                Schema::hasTable('employees') && Schema::hasColumn('employees', 'sort_order'),
                fn ($query) => $query->orderBy('sort_order'),
                fn ($query) => $query->latest()
            )
            ->orderByDesc('id')
            ->get();
        $tvRevision = $this->getTvRevision();
        $agendaTu = $this->tataUsahaAgendaService->fetchAgenda(10);
        $agendaData = Agenda::query()
            ->whereDate('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->values();

        ['pinned' => $agendaTuPinned, 'slides' => $agendaTuSlides] = $this->buildAgendaSlides($agendaTu);
        ['pinned' => $agendaDataPinned, 'slides' => $agendaDataSlides] = $this->buildAgendaSlides($agendaData);

        $agendaPerSlide = $this->agendaPerSlide;
        $agendaTuRemainingSlots = max(0, $this->agendaPerSlide - $agendaTuPinned->count());
        $agendaDataRemainingSlots = max(0, $this->agendaPerSlide - $agendaDataPinned->count());

        return compact(
            'settings',
            'tickerText',
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

    protected function findActiveVideo(): ?Video
    {
        if (! Schema::hasTable('videos')) {
            return null;
        }

        $query = Video::query();

        if (Schema::hasColumn('videos', 'is_active')) {
            $activeVideo = (clone $query)->where('is_active', true)->latest('id')->first();

            if ($activeVideo instanceof Video) {
                return $activeVideo;
            }
        }

        if (Schema::hasColumn('videos', 'sort_order')) {
            $query->orderBy('sort_order');
        }

        if (Schema::hasColumn('videos', 'display_order')) {
            $query->orderBy('display_order');
        }

        return $query->latest('id')->first();
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
            return $this->publicStorageUrl($path);
        }

        $publicPrefixedPath = preg_replace('#^storage/#', '', ltrim($path, '/'));
        if (is_string($publicPrefixedPath) && Storage::disk('public')->exists($publicPrefixedPath)) {
            return $this->publicStorageUrl($publicPrefixedPath);
        }

        return asset(ltrim($path, '/'));
    }

    protected function publicStorageUrl(string $path): string
    {
        return asset('storage/' . ltrim($path, '/'));
    }
}
