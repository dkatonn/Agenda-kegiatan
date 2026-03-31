<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class UserSettingController extends Controller
{
    public function index()
    {
        $admins = User::query()->latest()->get();

        return view('admin.usersetting', compact('admins'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'digits:18', 'unique:users,nip'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'regex:/[.!@#$%^&*]/', 'confirmed'],
        ], $this->validationMessages());

        $payload = [
            'name' => $validated['name'],
            'nip' => $validated['nip'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ];

        if (Schema::hasColumn('users', 'is_active')) {
            $payload['is_active'] = true;
        }

        User::query()->create($payload);

        return back()->with('success', 'Admin berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $admin = User::query()->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'digits:18', Rule::unique('users', 'nip')->ignore($admin->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($admin->id)],
            'password' => ['nullable', 'string', 'min:8', 'regex:/[.!@#$%^&*]/', 'confirmed'],
        ], $this->validationMessages());

        $payload = [
            'name' => $validated['name'],
            'nip' => $validated['nip'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $admin->update($payload);

        return back()->with('success', 'Data admin berhasil diperbarui.');
    }

    public function toggle(int $id): RedirectResponse
    {
        $admin = User::query()->findOrFail($id);

        if (! Schema::hasColumn('users', 'is_active')) {
            return back()->with('error', 'Kolom status admin belum tersedia. Jalankan migrasi terbaru.');
        }

        if ((int) Auth::id() === (int) $admin->id && $admin->is_active) {
            return back()->with('error', 'Akun yang sedang digunakan tidak bisa dinonaktifkan.');
        }

        $admin->update([
            'is_active' => ! $admin->is_active,
        ]);

        $status = $admin->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Admin berhasil {$status}.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $admin = User::query()->findOrFail($id);

        if ((int) Auth::id() === (int) $admin->id) {
            return back()->with('error', 'Akun yang sedang digunakan tidak bisa dihapus.');
        }

        $admin->delete();

        return back()->with('success', 'Admin berhasil dihapus.');
    }

    public function editPassword()
    {
        return view('admin.password');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'regex:/[.!@#$%^&*]/', 'confirmed'],
        ], [
            'current_password.current_password' => 'Kata sandi lama tidak sesuai.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.regex' => 'Kata sandi minimal menggunakan satu karakter spesial: . ! @ # $ % ^ & *',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

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
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.regex' => 'Kata sandi minimal menggunakan satu karakter spesial: . ! @ # $ % ^ & *',
        ];
    }
}
