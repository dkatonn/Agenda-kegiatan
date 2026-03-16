<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RunningText;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RunningTextController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->baseQuery($request)->orderBy('priority')->latest()->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $runningText = RunningText::create($this->validated($request));

        return response()->json($runningText, 201);
    }

    public function show(Request $request, RunningText $runningText): JsonResponse
    {
        $this->authorizeItem($request, $runningText->unit);

        return response()->json($runningText);
    }

    public function update(Request $request, RunningText $runningText): JsonResponse
    {
        $this->authorizeItem($request, $runningText->unit);
        $runningText->update($this->validated($request));

        return response()->json($runningText->fresh());
    }

    public function destroy(Request $request, RunningText $runningText): JsonResponse
    {
        $this->authorizeItem($request, $runningText->unit);
        $runningText->delete();

        return response()->json(status: 204);
    }

    private function baseQuery(Request $request)
    {
        $query = RunningText::query();

        if (! $this->isSuperadmin($request)) {
            $query->where('unit', $request->user()->disposition);
        }

        return $query;
    }

    private function authorizeItem(Request $request, string $unit): void
    {
        if (! $this->isSuperadmin($request) && $unit !== $request->user()->disposition) {
            abort(403, 'Anda tidak punya akses ke data ini.');
        }
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'unit' => ['nullable', 'string', 'in:tu,data'],
            'priority' => ['sometimes', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
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
