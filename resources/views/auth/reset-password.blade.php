@extends('auth.layout')

@section('title', 'Reset Kata Sandi')

@section('content')
<form action="{{ route('password.update') }}" method="POST" class="auth-form">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <div class="mb-3">
        <label class="form-label">Email Admin</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $request->email) }}" required autofocus>
    </div>

    <div class="mb-3">
        <label class="form-label">Kata Sandi Baru</label>
        <input type="password" name="password" class="form-control" required>
        <small class="text-muted d-block mt-2">
            Kata sandi minimal 8 karakter dan menggunakan satu karakter spesial: . ! @ # $ % ^ &amp; *
        </small>
    </div>

    <div class="mb-3">
        <label class="form-label">Konfirmasi Kata Sandi Baru</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary w-100">Simpan Kata Sandi Baru</button>
</form>
@endsection
