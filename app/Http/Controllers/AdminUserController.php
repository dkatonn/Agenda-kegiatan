<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = User::query()->latest()->get();

        return view('admin.admins', compact('admins'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => ['required', 'digits:18', 'unique:users,nip'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'regex:/[.!@#$%^&*]/', 'confirmed'],
        ], [
            'nip.digits' => 'NIP harus terdiri dari tepat 18 digit angka.',
            'nip.unique' => 'NIP ini sudah digunakan oleh admin lain.',
            'email.email' => 'Email harus menggunakan format yang valid.',
            'email.unique' => 'Email ini sudah digunakan oleh admin lain.',
            'password.confirmed' => 'Validasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.regex' => 'Password minimal menggunakan satu karakter spesial: . ! @ # $ % ^ & *',
        ]);

        User::query()->create([
            'nip' => $validated['nip'],
            'name' => 'Admin ' . $validated['nip'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Admin berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $admin = User::query()->findOrFail($id);

        $validated = $request->validate([
            'nip' => ['required', 'digits:18', 'unique:users,nip,' . $admin->id],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $admin->id],
            'password' => ['nullable', 'string', 'min:8', 'regex:/[.!@#$%^&*]/', 'confirmed'],
        ], [
            'nip.digits' => 'NIP harus terdiri dari tepat 18 digit angka.',
            'nip.unique' => 'NIP ini sudah digunakan oleh admin lain.',
            'email.email' => 'Email harus menggunakan format yang valid.',
            'email.unique' => 'Email ini sudah digunakan oleh admin lain.',
            'password.confirmed' => 'Validasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.regex' => 'Password minimal menggunakan satu karakter spesial: . ! @ # $ % ^ & *',
        ]);

        $payload = [
            'nip' => $validated['nip'],
            'name' => 'Admin ' . $validated['nip'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $admin->update($payload);

        return back()->with('success', 'Data admin berhasil diperbarui.');
    }

    public function destroy($id)
    {
        User::query()->findOrFail($id)->delete();

        return back()->with('success', 'Admin berhasil dihapus.');
    }
}
