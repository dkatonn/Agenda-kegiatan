<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Services\AdminActivityLogger;
use App\Services\EditLockService;
use App\Services\TvBroadcastService;
use App\Services\TvRevisionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AgendaController extends Controller
{
    public function __construct(
        protected TvRevisionService $tvRevisionService,
        protected TvBroadcastService $tvBroadcastService,
        protected AdminActivityLogger $activityLogger,
        protected EditLockService $editLockService,
    ) {}

    protected array $agendaCharacterLimits = [
        'name' => 45,
        'location' => 30,
        'disposition' => 30,
    ];

    public function index(Request $request)
    {
        $today = now()->startOfDay();
        $search = trim((string) $request->query('q', ''));
        $perPage = $this->resolvePerPage($request->query('per_page'));

        $agenda = Agenda::query()
            ->with(['updater', 'locker'])
            ->select('agendas.*')
            ->selectRaw('CASE WHEN date >= ? THEN 0 ELSE 1 END as period_group', [$today->toDateString()])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhere('disposition', 'like', "%{$search}%")
                        ->orWhere('date', 'like', "%{$search}%")
                        ->orWhere('time', 'like', "%{$search}%");
                });
            })
            ->orderByRaw('CASE WHEN date >= ? THEN 0 ELSE 1 END asc', [$today->toDateString()])
            ->orderBy('date')
            ->orderBy('time')
            ->paginate($perPage)
            ->withQueryString();

        $currentItems = $agenda->getCollection()->values();

        return view('admin.agenda', compact('agenda', 'search', 'perPage', 'currentItems'));
    }

    public function store(Request $request)
    {
        $this->validateAgendaRequest($request);
        $agenda = Agenda::create($this->buildAgendaPayload($request, true));
        $this->activityLogger->log('User menambah data agenda', [
            'agenda_id' => $agenda->id,
            'agenda_name' => $agenda->name,
            'agenda_date' => $agenda->date,
            'agenda_time' => $agenda->time,
        ]);
        $this->tvBroadcastService->dispatchUpdate($this->tvRevisionService->current());

        return back();
    }

    public function update(Request $request, $id)
    {
        $this->validateAgendaRequest($request);
        $agenda = Agenda::with(['locker', 'updater'])->findOrFail($id);

        if ($lockResponse = $this->lockViolationResponse($request, $agenda, 'Agenda')) {
            return $lockResponse;
        }

        if ($conflictResponse = $this->versionConflictResponse($request, $agenda, 'Agenda')) {
            return $conflictResponse;
        }

        $payload = $this->buildAgendaPayload($request);
        $agenda->fill($payload);

        if (! $agenda->isDirty()) {
            return back()->with('info', 'Tidak ada perubahan pada data agenda.');
        }

        $agenda->save();
        $this->editLockService->release($agenda, $request->user(), true);
        $this->activityLogger->log('User mengubah data agenda', [
            'agenda_id' => $agenda->id,
            'agenda_name' => $agenda->name,
            'agenda_date' => $agenda->date,
            'agenda_time' => $agenda->time,
        ]);
        $this->tvBroadcastService->dispatchUpdate($this->tvRevisionService->current());

        return back();
    }

    public function destroy($id)
    {
        $agenda = Agenda::with('locker')->findOrFail($id);

        if ($lockResponse = $this->lockViolationResponse(request(), $agenda, 'Agenda')) {
            return $lockResponse;
        }

        $this->activityLogger->log('User menghapus data agenda', [
            'agenda_id' => $agenda->id,
            'agenda_name' => $agenda->name,
            'agenda_date' => $agenda->date,
            'agenda_time' => $agenda->time,
        ]);
        $agenda->delete();
        $this->tvBroadcastService->dispatchUpdate($this->tvRevisionService->current());

        return back();
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }

    public function lock(Request $request, int $id): JsonResponse
    {
        $agenda = Agenda::with(['locker', 'updater'])->findOrFail($id);
        $result = $this->editLockService->acquire($agenda, $request->user());

        if (! $result['acquired']) {
            return response()->json([
                'message' => $this->editLockService->lockMessage($agenda),
                'lock' => $result['lock'],
            ], 423);
        }

        return response()->json([
            'message' => 'Lock edit berhasil diambil untuk agenda ini.',
            'lock' => $result['lock'],
        ]);
    }

    public function unlock(Request $request, int $id): JsonResponse
    {
        $agenda = Agenda::findOrFail($id);
        $this->editLockService->release($agenda, $request->user());

        return response()->json(['released' => true]);
    }

    protected function validateAgendaRequest(Request $request): void
    {
        $limits = $this->agendaCharacterLimits;
        $normalizedTime = $this->normalizeTimeValue($request->input('time'));

        $request->validate([
            'date' => ['required', 'date'],
            'time' => [
                'required',
                function (string $attribute, mixed $value, \Closure $fail) use ($normalizedTime) {
                    if (! preg_match('/^\d{2}:\d{2}$/', $normalizedTime)) {
                        $fail('Kolom waktu wajib menggunakan format HH:MM.');
                    }
                },
            ],
            'name' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) use ($limits) {
                    if (mb_strlen(trim((string) $value)) > $limits['name']) {
                        $fail("Kolom {$attribute} maksimal {$limits['name']} karakter.");
                    }
                },
            ],
            'location' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) use ($limits) {
                    if (mb_strlen(trim((string) $value)) > $limits['location']) {
                        $fail("Kolom {$attribute} maksimal {$limits['location']} karakter.");
                    }
                },
            ],
            'disposition' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) use ($limits) {
                    if (mb_strlen(trim((string) $value)) > $limits['disposition']) {
                        $fail("Kolom {$attribute} maksimal {$limits['disposition']} karakter.");
                    }
                },
            ],
        ], [
            'name.required' => 'Kolom kegiatan wajib diisi.',
            'location.required' => 'Kolom lokasi wajib diisi.',
            'disposition.required' => 'Kolom disposisi wajib diisi.',
            'date.required' => 'Tanggal wajib diisi.',
            'time.required' => 'Waktu wajib diisi.',
        ], [
            'name' => 'kegiatan',
            'location' => 'lokasi',
            'disposition' => 'disposisi',
        ]);
    }

    protected function buildAgendaPayload(Request $request, bool $isCreating = false): array
    {
        $payload = [
            'date' => $request->date,
            'time' => $this->normalizeTimeValue($request->time),
            'name' => $request->name,
            'location' => $request->location,
            'disposition' => $request->disposition,
        ];

        if (Schema::hasColumn('agendas', 'created_by') && $isCreating) {
            $payload['created_by'] = $request->user()?->id;
        }

        if (Schema::hasColumn('agendas', 'updated_by')) {
            $payload['updated_by'] = $request->user()?->id;
        }

        if (Schema::hasColumn('agendas', 'title')) {
            $payload['title'] = $request->name;
        }

        if (Schema::hasColumn('agendas', 'agenda_date')) {
            $payload['agenda_date'] = $request->date;
        }

        if (Schema::hasColumn('agendas', 'agenda_time')) {
            $payload['agenda_time'] = $request->time;
        }

        if (Schema::hasColumn('agendas', 'description')) {
            $payload['description'] = $request->disposition ?: '-';
        }

        if (Schema::hasColumn('agendas', 'unit')) {
            $payload['unit'] = 'data';
        }

        if (Schema::hasColumn('agendas', 'is_active')) {
            $payload['is_active'] = true;
        }

        return $payload;
    }

    protected function resolvePerPage(mixed $perPage): int
    {
        $perPage = (int) $perPage;

        return in_array($perPage, [5, 10, 25], true) ? $perPage : 10;
    }

    protected function normalizeTimeValue(mixed $value): string
    {
        $time = trim((string) $value);
        $time = str_replace('.', ':', $time);

        if (preg_match('/^\d{1,2}:\d{2}$/', $time) === 1) {
            [$hours, $minutes] = explode(':', $time, 2);

            return str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . $minutes;
        }

        return $time;
    }

    protected function lockViolationResponse(Request $request, Agenda $agenda, string $entityLabel)
    {
        $userId = $request->user()?->id;

        if (! $this->editLockService->isLockedByAnother($agenda, $userId)) {
            return null;
        }

        $message = $this->editLockService->lockMessage($agenda);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'lock' => $this->editLockService->payload($agenda, $userId),
            ], 423);
        }

        return back()->withErrors($message);
    }

    protected function versionConflictResponse(Request $request, Agenda $agenda, string $entityLabel)
    {
        $submittedVersion = trim((string) $request->input('updated_at_version'));
        $currentVersion = $agenda->updated_at?->toIso8601String() ?? '';

        if ($submittedVersion === '' || $submittedVersion === $currentVersion) {
            return null;
        }

        $updatedBy = $agenda->updater?->name ?: 'admin lain';
        $updatedAt = $agenda->updated_at
            ? Carbon::parse($agenda->updated_at)->locale('id')->translatedFormat('d F Y H:i')
            : 'waktu yang tidak diketahui';
        $message = "{$entityLabel} ini sudah diubah oleh {$updatedBy} pada {$updatedAt}. Muat ulang data sebelum menyimpan lagi.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'lock' => $this->editLockService->payload($agenda, $request->user()?->id),
            ], 409);
        }

        return back()->withErrors($message);
    }
}
