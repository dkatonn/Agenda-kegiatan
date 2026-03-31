@extends('auth.layout')

@section('title', 'Login')

@section('content')
<form action="{{ route('login.store') }}" method="POST" class="auth-form">
    @csrf

    <div class="mb-3">
        <label class="form-label">NIP</label>
        <input type="text" name="nip" class="form-control" value="{{ old('nip') }}" inputmode="numeric" maxlength="18" required autofocus>
        <small class="text-muted d-block mt-2">Masukkan NIP 18 digit untuk login admin.</small>
    </div>

    <div class="mb-3">
        <label class="form-label">Password</label>
        <div class="input-group password-toggle-group">
            <input type="password" name="password" class="form-control" data-password-input required>
            <button class="btn btn-outline-secondary" type="button" data-password-toggle>
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
            <label class="form-check-label" for="remember">Ingat saya</label>
        </div>

        <a href="{{ route('password.request') }}" class="auth-link">Lupa password?</a>
    </div>

    <button type="submit" class="btn btn-primary w-100">Masuk</button>
</form>

@push('scripts')
<script>
    document.querySelectorAll('[data-password-toggle]').forEach(function(toggleButton) {
        toggleButton.addEventListener('click', function() {
            const wrapper = toggleButton.closest('.password-toggle-group');
            const input = wrapper.querySelector('[data-password-input]');
            const isPassword = input.getAttribute('type') === 'password';

            input.setAttribute('type', isPassword ? 'text' : 'password');
            toggleButton.innerHTML = isPassword
                ? '<i class="bi bi-eye-slash"></i>'
                : '<i class="bi bi-eye"></i>';
        });
    });
</script>
@endpush
@endsection
