<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = Str::lower(trim((string) $request->input('email')));
        $cooldownKey = 'password-reset-request:' . sha1($email);
        $cooldownSeconds = max((int) config('auth.passwords.users.request_cooldown', 86400), 60);
        $cooldownUntil = Cache::get($cooldownKey);

        if (is_string($cooldownUntil)) {
            $cooldownUntil = Carbon::parse($cooldownUntil);
        }

        if ($cooldownUntil instanceof Carbon && now()->lt($cooldownUntil)) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'Link reset untuk email ini sudah diminta. Permintaan berikutnya bisa dilakukan lagi setelah '
                    . $cooldownUntil->copy()->timezone(config('app.timezone'))->locale('id')->translatedFormat('d F Y H:i')
                    . '.',
            ]);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            $nextAllowedAt = now()->addSeconds($cooldownSeconds);

            Cache::put($cooldownKey, $nextAllowedAt->toIso8601String(), $nextAllowedAt);

            return back()->with('status', 'Link reset password berhasil dikirim. Email ini dapat meminta link baru lagi setelah '
                . $nextAllowedAt->copy()->timezone(config('app.timezone'))->locale('id')->translatedFormat('d F Y H:i')
                . '.');
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => $status === Password::INVALID_USER
                ? 'Email admin tidak ditemukan.'
                : 'Link reset belum bisa dikirim saat ini. Silakan coba lagi.',
        ]);
    }
}
