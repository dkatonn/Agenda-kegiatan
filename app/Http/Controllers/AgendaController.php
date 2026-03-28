<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agenda;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AgendaController extends Controller
{
    protected array $agendaCharacterLimits = [
        'name' => 45,
        'location' => 30,
        'disposition' => 30,
    ];

    public function index()
    {
        $today = now()->startOfDay();
        $tomorrow = now()->copy()->addDay()->startOfDay();

        $agenda = Agenda::query()
            ->get()
            ->sort(function ($left, $right) use ($today, $tomorrow) {
                $leftDate = Carbon::parse($left->date)->startOfDay();
                $rightDate = Carbon::parse($right->date)->startOfDay();

                $leftPriority = $leftDate->equalTo($today) ? 0 : ($leftDate->equalTo($tomorrow) ? 1 : 2);
                $rightPriority = $rightDate->equalTo($today) ? 0 : ($rightDate->equalTo($tomorrow) ? 1 : 2);

                if ($leftPriority !== $rightPriority) {
                    return $leftPriority <=> $rightPriority;
                }

                if (! $leftDate->equalTo($rightDate)) {
                    return $rightDate->timestamp <=> $leftDate->timestamp;
                }

                return strcmp((string) $left->time, (string) $right->time);
            })
            ->values();

        return view('admin.agenda', compact('agenda'));
    }

    public function store(Request $request)
    {
        $this->validateAgendaRequest($request);
        Agenda::create($this->buildAgendaPayload($request));

        return back();
    }

    public function update(Request $request, $id)
    {
        $this->validateAgendaRequest($request);
        $agenda = Agenda::findOrFail($id);

        $agenda->update($this->buildAgendaPayload($request));

        return back();
    }

    public function destroy($id)
    {
        Agenda::findOrFail($id)->delete();
        return back();
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }

    protected function validateAgendaRequest(Request $request): void
    {
        $limits = $this->agendaCharacterLimits;

        $request->validate([
            'date' => ['required', 'date'],
            'time' => ['required'],
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

    protected function buildAgendaPayload(Request $request): array
    {
        $payload = [
            'date' => $request->date,
            'time' => $request->time,
            'name' => $request->name,
            'location' => $request->location,
            'disposition' => $request->disposition,
        ];

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
}
