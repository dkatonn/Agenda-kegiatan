@extends('auth.layout')

@section('title', 'Lupa Password')

@section('content')
<form action="{{ route('password.email') }}" method="POST" class="auth-form">
    @csrf

    <div class="mb-3">
        <label class="form-label">Email Admin</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
        <small class="text-muted d-block mt-2">
            Link reset password akan dikirim ke email ini. Untuk mode tes saat ini, email dicatat ke log aplikasi.
        </small>
    </div>

    <button type="submit" class="btn btn-primary w-100">Kirim Link Reset</button>
</form>

<div class="auth-footer-link">
    <a href="{{ route('login') }}" class="auth-link">Kembali ke login</a>
</div>
@endsection
