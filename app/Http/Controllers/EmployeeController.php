<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\AdminActivityLogger;
use App\Services\EditLockService;
use App\Services\TvBroadcastService;
use App\Services\TvRevisionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function __construct(
        protected TvRevisionService $tvRevisionService,
        protected TvBroadcastService $tvBroadcastService,
        protected AdminActivityLogger $activityLogger,
        protected EditLockService $editLockService,
    ) {}

    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $perPage = $this->resolvePerPage($request->query('per_page'));
        $employee = $this->orderedEmployeesQuery()
            ->with(['updater', 'locker'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%");
                });
            })
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.employee', compact('employee', 'search', 'perPage'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'digits:18', 'unique:employees,nip'],
            'role' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image'],
        ], [
            'nip.digits' => 'NIP pegawai harus terdiri dari tepat 18 digit angka.',
            'nip.unique' => 'NIP pegawai ini sudah digunakan.',
        ]);

        $path = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('employee', 'public');
        }

        $attributes = [
            'name' => $validated['name'],
            'nip' => $validated['nip'] ?? null,
            'role' => $validated['role'],
            'image_path' => $path,
            'created_by' => Schema::hasColumn('employees', 'created_by') ? $request->user()?->id : null,
            'updated_by' => Schema::hasColumn('employees', 'updated_by') ? $request->user()?->id : null,
        ];

        if ($this->hasSortOrderColumn()) {
            $attributes['sort_order'] = $this->nextSortOrder();
        }

        $employee = Employee::create($attributes);

        $this->activityLogger->log('User menambah data pegawai', [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'employee_nip' => $employee->nip,
            'employee_role' => $employee->role,
        ]);
        $this->tvBroadcastService->dispatchUpdate($this->tvRevisionService->current());

        return back();
    }

    public function reorder(Request $request): JsonResponse
    {
        abort_unless($this->hasSortOrderColumn(), 404);

        $validated = $request->validate([
            'ordered_ids' => ['required', 'array'],
            'ordered_ids.*' => ['integer', 'distinct', 'exists:employees,id'],
            'start_order' => ['nullable', 'integer', 'min:1'],
        ]);

        $startOrder = (int) ($validated['start_order'] ?? 1);

        DB::transaction(function () use ($validated, $request, $startOrder) {
            foreach ($validated['ordered_ids'] as $index => $employeeId) {
                $updatePayload = [
                    'sort_order' => $startOrder + $index,
                ];

                if (Schema::hasColumn('employees', 'updated_by')) {
                    $updatePayload['updated_by'] = $request->user()?->id;
                }

                Employee::query()
                    ->where('id', $employeeId)
                    ->update($updatePayload);
            }
        });

        $this->activityLogger->log('User mengubah urutan pegawai', [
            'ordered_ids' => $validated['ordered_ids'],
            'start_order' => $startOrder,
        ]);
        $this->tvBroadcastService->dispatchUpdate($this->tvRevisionService->current());

        return response()->json([
            'message' => 'Urutan pegawai berhasil diperbarui.',
        ]);
    }

    public function update(Request $request, $id)
    {
        $emp = Employee::with(['locker', 'updater'])->findOrFail($id);

        if ($lockResponse = $this->lockViolationResponse($request, $emp, 'Pegawai')) {
            return $lockResponse;
        }

        if ($conflictResponse = $this->versionConflictResponse($request, $emp, 'Pegawai')) {
            return $conflictResponse;
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'digits:18', 'unique:employees,nip,'.$emp->id],
            'role' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image'],
        ], [
            'nip.digits' => 'NIP pegawai harus terdiri dari tepat 18 digit angka.',
            'nip.unique' => 'NIP pegawai ini sudah digunakan.',
        ]);

        $newImagePath = null;
        if ($request->hasFile('image')) {
            $newImagePath = $request->file('image')->store('employee', 'public');
        }

        $emp->fill([
            'name' => $validated['name'],
            'nip' => $validated['nip'] ?? null,
            'role' => $validated['role'],
            'updated_by' => Schema::hasColumn('employees', 'updated_by') ? $request->user()?->id : $emp->updated_by,
        ]);

        if ($newImagePath !== null) {
            if ($emp->image_path) {
                Storage::disk('public')->delete($emp->image_path);
            }

            $emp->image_path = $newImagePath;
        }

        if (! $emp->isDirty()) {
            if ($newImagePath !== null) {
                Storage::disk('public')->delete($newImagePath);
            }

            return back()->with('info', 'Tidak ada perubahan pada data pegawai.');
        }

        $emp->save();
        $this->editLockService->release($emp, $request->user(), true);

        $this->activityLogger->log('User mengubah data pegawai', [
            'employee_id' => $emp->id,
            'employee_name' => $emp->name,
            'employee_nip' => $emp->nip,
            'employee_role' => $emp->role,
        ]);
        $this->tvBroadcastService->dispatchUpdate($this->tvRevisionService->current());

        return back();
    }

    public function destroy($id)
    {
        $employee = Employee::with('locker')->findOrFail($id);

        if ($lockResponse = $this->lockViolationResponse(request(), $employee, 'Pegawai')) {
            return $lockResponse;
        }

        $this->activityLogger->log('User menghapus data pegawai', [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'employee_nip' => $employee->nip,
            'employee_role' => $employee->role,
        ]);
        $employee->delete();
        $this->tvBroadcastService->dispatchUpdate($this->tvRevisionService->current());

        return back();
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }

    public function lock(Request $request, int $id): JsonResponse
    {
        $employee = Employee::with(['locker', 'updater'])->findOrFail($id);
        $result = $this->editLockService->acquire($employee, $request->user());

        if (! $result['acquired']) {
            return response()->json([
                'message' => $this->editLockService->lockMessage($employee),
                'lock' => $result['lock'],
            ], 423);
        }

        return response()->json([
            'message' => 'Lock edit berhasil diambil untuk pegawai ini.',
            'lock' => $result['lock'],
        ]);
    }

    public function unlock(Request $request, int $id): JsonResponse
    {
        $employee = Employee::findOrFail($id);
        $this->editLockService->release($employee, $request->user());

        return response()->json(['released' => true]);
    }

    protected function resolvePerPage(mixed $perPage): int
    {
        $perPage = (int) $perPage;

        return in_array($perPage, [5, 10, 25], true) ? $perPage : 10;
    }

    protected function hasSortOrderColumn(): bool
    {
        return Schema::hasTable('employees') && Schema::hasColumn('employees', 'sort_order');
    }

    protected function nextSortOrder(): int
    {
        return ((int) Employee::query()->max('sort_order')) + 1;
    }

    protected function orderedEmployeesQuery()
    {
        $query = Employee::query();

        if ($this->hasSortOrderColumn()) {
            return $query->orderBy('sort_order')->orderByDesc('id');
        }

        return $query->latest();
    }

    protected function lockViolationResponse(Request $request, Employee $employee, string $entityLabel)
    {
        $userId = $request->user()?->id;

        if (! $this->editLockService->isLockedByAnother($employee, $userId)) {
            return null;
        }

        $message = $this->editLockService->lockMessage($employee);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'lock' => $this->editLockService->payload($employee, $userId),
            ], 423);
        }

        return back()->withErrors($message);
    }

    protected function versionConflictResponse(Request $request, Employee $employee, string $entityLabel)
    {
        $submittedVersion = trim((string) $request->input('updated_at_version'));
        $currentVersion = $employee->updated_at?->toIso8601String() ?? '';

        if ($submittedVersion === '' || $submittedVersion === $currentVersion) {
            return null;
        }

        $updatedBy = $employee->updater?->name ?: 'admin lain';
        $updatedAt = $employee->updated_at
            ? Carbon::parse($employee->updated_at)->locale('id')->translatedFormat('d F Y H:i')
            : 'waktu yang tidak diketahui';
        $message = "{$entityLabel} ini sudah diubah oleh {$updatedBy} pada {$updatedAt}. Muat ulang data sebelum menyimpan lagi.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'lock' => $this->editLockService->payload($employee, $request->user()?->id),
            ], 409);
        }

        return back()->withErrors($message);
    }
}
