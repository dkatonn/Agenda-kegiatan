@extends('auth.layout')

@section('title', 'Lupa Password')

@section('content')
<form action="{{ route('password.email') }}" method="POST" class="auth-form">
    @csrf

    <div class="mb-3">
        <label class="form-label">Email Admin</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
        <small class="text-muted d-block mt-2">
            Link reset password akan dikirim ke email ini. Jika mailer lokal masih `log`, isi email dapat dilihat di `storage/logs/laravel.log`.
        </small>
        <small class="text-muted d-block mt-1">
            Demi keamanan, satu email hanya dapat meminta link reset sekali dalam 24 jam.
        </small>
    </div>

    <button type="submit" class="btn btn-primary w-100">Kirim Link Reset</button>
</form>

<div class="auth-footer-link">
    <a href="{{ route('login') }}" class="auth-link">Kembali ke login</a>
</div>
@endsection
