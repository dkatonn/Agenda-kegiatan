<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    public function __construct(protected AdminActivityLogger $activityLogger)
    {
    }

    public function create(Request $request)
    {
        $request->session()->regenerateToken();

        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    public function token(Request $request)
    {
        $request->session()->regenerateToken();

        return response()->json([
            'token' => csrf_token(),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
          ->header('Pragma', 'no-cache')
          ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'nip' => ['required', 'digits:18'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('nip', $credentials['nip'])->first();

        if ($user && Schema::hasColumn('users', 'is_active') && ! $user->is_active) {
            throw ValidationException::withMessages([
                'nip' => 'Akun admin ini sedang dinonaktifkan.',
            ]);
        }

        if (! Auth::attempt($credentials, false)) {
            $this->activityLogger->log('Login admin gagal', [
                'login_nip' => $credentials['nip'],
            ]);

            throw ValidationException::withMessages([
                'nip' => 'NIP atau password tidak sesuai.',
            ]);
        }

        $request->session()->regenerate();
        $request->session()->regenerateToken();
        $request->session()->put('admin_tab_bootstrap_pending', true);

        $this->activityLogger->log('Login admin berhasil');

        return redirect()->intended(route('admin.index'));
    }

    public function closeTab(Request $request)
    {
        $this->activityLogger->log('Admin logout karena tab ditutup');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }

    public function destroy(Request $request)
    {
        $this->activityLogger->log('Admin logout manual');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
