@extends('admin.layout')

@section('title', 'Ubah Kata Sandi')
@section('suppressGlobalErrors', '1')

@section('content')

<div class="admin-card">
    <div class="panel-header">
        <div>
            <div class="section-eyebrow">Keamanan Akun</div>
            <h6 class="panel-title">
                <i class="bi bi-key"></i>
                Ubah Kata Sandi
            </h6>
        </div>
    </div>

    <form action="{{ route('admin.password.update') }}" method="POST">
        @csrf
        @method('PUT')

        @if(session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
        @endif

        @if(session('info'))
        <div class="alert alert-info mb-4">
            {{ session('info') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger mb-4">
            {{ $errors->first() }}
        </div>
        @endif

        <div class="form-section-note mb-4">
            Gunakan kata sandi baru yang kuat agar akses panel admin tetap aman.
        </div>

        <div class="admin-form-grid admin-form-grid-2">
            <div class="mb-3">
                <label class="form-label">NIP</label>
                <input type="text" class="form-control" value="{{ auth()->user()->nip }}" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" value="{{ auth()->user()->email }}" disabled>
            </div>

            <div class="mb-3 admin-form-grid-full">
                <label class="form-label">Kata Sandi Lama</label>
                <div class="input-group password-toggle-group">
                    <input
                        type="password"
                        name="current_password"
                        class="form-control @error('current_password') is-invalid @enderror"
                        data-password-input
                        autocomplete="current-password"
                        readonly
                        onfocus="this.removeAttribute('readonly');"
                        required>
                    <button class="btn btn-outline-secondary" type="button" data-password-toggle>
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('current_password')
                <small class="text-danger d-block mt-2">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Kata Sandi Baru</label>
                <div class="input-group password-toggle-group">
                    <input
                        type="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        data-password-input
                        autocomplete="new-password"
                        readonly
                        onfocus="this.removeAttribute('readonly');"
                        required>
                    <button class="btn btn-outline-secondary" type="button" data-password-toggle>
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <small class="text-muted d-block mt-2">Kata sandi minimal 8 karakter dan menggunakan satu karakter spesial: . ! @ # $ % ^ &amp; *</small>
                @error('password')
                <small class="text-danger d-block mt-2">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Konfirmasi Kata Sandi Baru</label>
                <div class="input-group password-toggle-group">
                    <input
                        type="password"
                        name="password_confirmation"
                        class="form-control @error('password_confirmation') is-invalid @enderror"
                        data-password-input
                        data-password-confirmation
                        autocomplete="new-password"
                        readonly
                        onfocus="this.removeAttribute('readonly');"
                        required>
                    <button class="btn btn-outline-secondary" type="button" data-password-toggle>
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <small class="text-danger d-none" data-password-match></small>
                @error('password_confirmation')
                <small class="text-danger d-block mt-2">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.user-settings') }}" class="btn btn-light">Kembali</a>
            <button type="submit" class="btn btn-primary">Simpan Kata Sandi Baru</button>
        </div>
    </form>
</div>

@endsection

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

    const passwordInput = document.querySelector('input[name="password"]');
    const passwordConfirmationInput = document.querySelector('[data-password-confirmation]');
    const passwordMatchStatus = document.querySelector('[data-password-match]');

    const updatePasswordMatch = function() {
        if (!passwordInput || !passwordConfirmationInput || !passwordMatchStatus) {
            return;
        }

        if (!passwordConfirmationInput.value || passwordInput.value === passwordConfirmationInput.value) {
            passwordMatchStatus.textContent = '';
            passwordMatchStatus.className = 'text-danger d-none';
            return;
        }

        passwordMatchStatus.textContent = 'Konfirmasi kata sandi tidak sama.';
        passwordMatchStatus.className = 'text-danger';
    };

    passwordInput?.addEventListener('input', updatePasswordMatch);
    passwordConfirmationInput?.addEventListener('input', updatePasswordMatch);
    updatePasswordMatch();
</script>
@endpush

