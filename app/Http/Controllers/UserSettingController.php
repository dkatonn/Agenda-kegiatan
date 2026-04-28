<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AdminActivityLogger;
use App\Services\EditLockService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserSettingController extends Controller
{
    public function __construct(
        protected AdminActivityLogger $activityLogger,
        protected EditLockService $editLockService,
    ) {}

    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $perPage = $this->resolvePerPage($request->query('per_page'));
        $admins = User::query()
            ->with(['updater', 'locker'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.usersetting', compact('admins', 'search', 'perPage'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'nip' => ['required', 'digits:18', 'unique:users,nip'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'image' => ['nullable', 'image', 'max:2048'],
            'password' => ['required', 'string', 'min:8', 'regex:/[.!@#$%^&*]/', 'confirmed'],
        ], $this->validationMessages());

        $payload = [
            'name' => $validated['name'],
            'nip' => $validated['nip'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ];

        if ($request->hasFile('image')) {
            $payload['image_path'] = $request->file('image')->store('admin', 'public');
        }

        if (Schema::hasColumn('users', 'is_active')) {
            $payload['is_active'] = true;
        }

        if (Schema::hasColumn('users', 'created_by')) {
            $payload['created_by'] = $request->user()?->id;
        }

        if (Schema::hasColumn('users', 'updated_by')) {
            $payload['updated_by'] = $request->user()?->id;
        }

        $admin = User::query()->create($payload);

        $this->activityLogger->log('User menambah admin dashboard', [
            'target_admin_id' => $admin->id,
            'target_admin_name' => $admin->name,
            'target_admin_nip' => $admin->nip,
            'target_admin_email' => $admin->email,
            'target_admin_has_photo' => filled($admin->image_path),
        ]);

        return back()->with('success', 'Admin berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $admin = User::query()->with(['locker', 'updater'])->findOrFail($id);

        if ($lockResponse = $this->lockViolationResponse($request, $admin, 'Admin')) {
            return $lockResponse;
        }

        if ($conflictResponse = $this->versionConflictResponse($request, $admin, 'Admin')) {
            return $conflictResponse;
        }

        $originalPasswordHash = $admin->password;

        $validated = $request->validate([
            'name' => ['required', 'string'],
            'nip' => ['required', 'digits:18', Rule::unique('users', 'nip')->ignore($admin->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($admin->id)],
            'image' => ['nullable', 'image', 'max:2048'],
            'password' => ['nullable', 'string', 'min:8', 'regex:/[.!@#$%^&*]/', 'confirmed'],
        ], $this->validationMessages());

        $payload = [
            'name' => $validated['name'],
            'nip' => $validated['nip'],
            'email' => $validated['email'],
        ];

        if (Schema::hasColumn('users', 'updated_by')) {
            $payload['updated_by'] = $request->user()?->id;
        }

        if (! empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $newImagePath = null;

        if ($request->hasFile('image')) {
            $newImagePath = $request->file('image')->store('admin', 'public');
            $payload['image_path'] = $newImagePath;
        }

        $admin->fill($payload);

        $passwordChanged = array_key_exists('password', $payload)
            && $payload['password'] !== $originalPasswordHash;

        if (! $admin->isDirty() && ! $passwordChanged) {
            if ($newImagePath !== null) {
                Storage::disk('public')->delete($newImagePath);
            }

            return back()->with('info', 'Tidak ada perubahan pada data admin.');
        }

        $oldImagePath = $admin->getOriginal('image_path');
        $admin->save();
        $this->editLockService->release($admin, $request->user(), true);

        if ($newImagePath !== null && filled($oldImagePath) && $oldImagePath !== $newImagePath) {
            Storage::disk('public')->delete($oldImagePath);
        }

        $this->activityLogger->log('User mengubah admin dashboard', [
            'target_admin_id' => $admin->id,
            'target_admin_name' => $admin->name,
            'target_admin_nip' => $admin->nip,
            'target_admin_email' => $admin->email,
            'target_admin_has_photo' => filled($admin->image_path),
            'password_changed' => $passwordChanged,
        ]);

        return back()->with('success', 'Data admin berhasil diperbarui.');
    }

    public function toggle(int $id): RedirectResponse
    {
        $admin = User::query()->with('locker')->findOrFail($id);

        if (! Schema::hasColumn('users', 'is_active')) {
            return back()->with('error', 'Kolom status admin belum tersedia. Jalankan migrasi terbaru.');
        }

        if ($lockResponse = $this->lockViolationResponse(request(), $admin, 'Admin')) {
            return $lockResponse;
        }

        if ((int) Auth::id() === (int) $admin->id && $admin->is_active) {
            return back()->with('error', 'Akun yang sedang digunakan tidak bisa dinonaktifkan.');
        }

        $payload = [
            'is_active' => ! $admin->is_active,
        ];

        if (Schema::hasColumn('users', 'updated_by')) {
            $payload['updated_by'] = request()->user()?->id;
        }

        $admin->update($payload);

        $status = $admin->is_active ? 'diaktifkan' : 'dinonaktifkan';

        $this->activityLogger->log('User mengubah status admin dashboard', [
            'target_admin_id' => $admin->id,
            'target_admin_name' => $admin->name,
            'target_admin_status' => $status,
        ]);

        return back()->with('success', "Admin berhasil {$status}.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $admin = User::query()->with('locker')->findOrFail($id);

        if ($lockResponse = $this->lockViolationResponse(request(), $admin, 'Admin')) {
            return $lockResponse;
        }

        if ((int) Auth::id() === (int) $admin->id) {
            return back()->with('error', 'Akun yang sedang digunakan tidak bisa dihapus.');
        }

        $this->activityLogger->log('User menghapus admin dashboard', [
            'target_admin_id' => $admin->id,
            'target_admin_name' => $admin->name,
            'target_admin_nip' => $admin->nip,
            'target_admin_email' => $admin->email,
        ]);

        if (filled($admin->image_path)) {
            Storage::disk('public')->delete($admin->image_path);
        }

        $admin->delete();

        return back()->with('success', 'Admin berhasil dihapus.');
    }

    public function lock(Request $request, int $id): JsonResponse
    {
        $admin = User::query()->with(['locker', 'updater'])->findOrFail($id);
        $result = $this->editLockService->acquire($admin, $request->user());

        if (! $result['acquired']) {
            return response()->json([
                'message' => $this->editLockService->lockMessage($admin),
                'lock' => $result['lock'],
            ], 423);
        }

        return response()->json([
            'message' => 'Lock edit berhasil diambil untuk admin ini.',
            'lock' => $result['lock'],
        ]);
    }

    public function unlock(Request $request, int $id): JsonResponse
    {
        $admin = User::query()->findOrFail($id);
        $this->editLockService->release($admin, $request->user());

        return response()->json(['released' => true]);
    }

    public function editPassword()
    {
        return view('admin.password');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'regex:/[.!@#$%^&*]/', 'confirmed', 'different:current_password'],
        ], [
            'current_password.current_password' => 'Kata sandi lama tidak sesuai.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.regex' => 'Kata sandi minimal menggunakan satu karakter spesial: . ! @ # $ % ^ & *',
            'password.different' => 'Kata sandi baru harus berbeda dari kata sandi lama.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->activityLogger->log('User mengubah kata sandi admin dashboard');

        return back()->with('success', 'Kata sandi berhasil diperbarui.');
    }

    protected function validationMessages(): array
    {
        return [
            'name.required' => 'Nama admin wajib diisi.',
            'nip.digits' => 'NIP harus terdiri dari tepat 18 digit angka.',
            'nip.unique' => 'NIP ini sudah digunakan oleh admin lain.',
            'email.email' => 'Email harus menggunakan format yang valid.',
            'email.unique' => 'Email ini sudah digunakan oleh admin lain.',
            'image.image' => 'Foto admin harus berupa gambar yang valid.',
            'image.max' => 'Ukuran foto admin maksimal 2 MB.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.regex' => 'Kata sandi minimal menggunakan satu karakter spesial: . ! @ # $ % ^ & *',
        ];
    }

    protected function resolvePerPage(mixed $perPage): int
    {
        $perPage = (int) $perPage;

        return in_array($perPage, [5, 10, 25], true) ? $perPage : 10;
    }

    protected function lockViolationResponse(Request $request, User $admin, string $entityLabel)
    {
        $userId = $request->user()?->id;

        if (! $this->editLockService->isLockedByAnother($admin, $userId)) {
            return null;
        }

        $message = $this->editLockService->lockMessage($admin);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'lock' => $this->editLockService->payload($admin, $userId),
            ], 423);
        }

        return back()->withErrors($message);
    }

    protected function versionConflictResponse(Request $request, User $admin, string $entityLabel)
    {
        $submittedVersion = trim((string) $request->input('updated_at_version'));
        $currentVersion = $admin->updated_at?->toIso8601String() ?? '';

        if ($submittedVersion === '' || $submittedVersion === $currentVersion) {
            return null;
        }

        $updatedBy = $admin->updater?->name ?: 'admin lain';
        $updatedAt = $admin->updated_at
            ? Carbon::parse($admin->updated_at)->locale('id')->translatedFormat('d F Y H:i')
            : 'waktu yang tidak diketahui';
        $message = "{$entityLabel} ini sudah diubah oleh {$updatedBy} pada {$updatedAt}. Muat ulang data sebelum menyimpan lagi.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'lock' => $this->editLockService->payload($admin, $request->user()?->id),
            ], 409);
        }

        return back()->withErrors($message);
    }
}
