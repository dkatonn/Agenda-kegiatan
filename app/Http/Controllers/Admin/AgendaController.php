<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Agenda::query();

        if ($request->query('lihat') === 'tu' && ($this->isSuperadmin($request) || $request->user()->disposition === 'data')) {
            $query->where('unit', 'tu');
        } elseif (! $this->isSuperadmin($request)) {
            $query->where('unit', $request->user()->disposition);
        }

        return response()->json(
            $query->latest('agenda_date')->latest('agenda_time')->latest()->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $agenda = Agenda::create($this->validated($request));

        return response()->json($agenda, 201);
    }

    public function show(Request $request, Agenda $agenda): JsonResponse
    {
        $this->authorizeAgenda($request, $agenda);

        return response()->json($agenda);
    }

    public function update(Request $request, Agenda $agenda): JsonResponse
    {
        $this->authorizeAgenda($request, $agenda);
        $agenda->update($this->validated($request));

        return response()->json($agenda->fresh());
    }

    public function destroy(Request $request, Agenda $agenda): JsonResponse
    {
        $this->authorizeAgenda($request, $agenda);
        $agenda->delete();

        return response()->json(status: 204);
    }

    private function authorizeAgenda(Request $request, Agenda $agenda): void
    {
        if (! $this->isSuperadmin($request) && $agenda->unit !== $request->user()->disposition) {
            abort(403, 'Anda tidak punya akses ke agenda ini.');
        }
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'agenda_date' => ['required', 'date'],
            'agenda_time' => ['required', 'date_format:H:i'],
            'location' => ['required', 'string', 'max:255'],
            'disposition' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'unit' => ['nullable', 'string', 'in:tu,data'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['unit'] = $this->isSuperadmin($request)
            ? ($validated['unit'] ?? $request->user()->disposition)
            : $request->user()->disposition;

        return $validated;
    }

    private function isSuperadmin(Request $request): bool
    {
        return $request->user()?->role === 'superadmin';
    }
}
