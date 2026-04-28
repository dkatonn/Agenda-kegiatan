@extends('auth.layout')

@section('title', 'Login')

@section('content')
@if(request('reason') === 'idle')
<div class="alert alert-warning auth-alert">
    Sesi admin berakhir karena tidak ada aktivitas selama 2 menit. Silakan login kembali.
</div>
@elseif(request('reason') === 'tab_closed')
<div class="alert alert-warning auth-alert">
    Tab admin sebelumnya sudah ditutup. Silakan login kembali untuk membuka panel admin.
</div>
@endif

<form action="{{ route('login.store') }}" method="POST" class="auth-form">
    @csrf

    <div class="auth-field">
        <label class="form-label">NIP</label>
        <input type="text" name="nip" class="form-control" value="{{ old('nip') }}" inputmode="numeric" maxlength="18" required autofocus>
        <small class="text-muted d-block mt-2">Masukkan NIP 18 digit yang terdaftar untuk masuk ke panel admin.</small>
    </div>

    <div class="auth-field">
        <label class="form-label">Kata Sandi</label>
        <div class="input-group password-toggle-group">
            <input type="password" name="password" class="form-control" data-password-input required>
            <button class="btn btn-outline-secondary" type="button" data-password-toggle>
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>

    <div class="auth-row">
        <small class="text-muted">Sesi admin akan berakhir otomatis setelah 10 menit tidak aktif atau saat tab admin ditutup.</small>
        <a href="{{ route('password.request') }}" class="auth-link">Lupa kata sandi?</a>
    </div>

    <button type="submit" class="btn btn-primary auth-submit">Masuk</button>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const loginForm = document.querySelector('.auth-form');
        const csrfInput = loginForm?.querySelector('input[name="_token"]');
        let tokenRefreshInFlight = null;

        const refreshLoginToken = function () {
            if (!loginForm || !csrfInput) {
                return Promise.resolve();
            }

            if (tokenRefreshInFlight) {
                return tokenRefreshInFlight;
            }

            tokenRefreshInFlight = fetch(@json(route('csrf.token')), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Cache-Control': 'no-store',
                },
                cache: 'no-store',
                credentials: 'same-origin',
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Gagal menyegarkan token login.');
                    }

                    return response.json();
                })
                .then(function (payload) {
                    if (payload?.token) {
                        csrfInput.value = payload.token;
                    }
                })
                .finally(function () {
                    tokenRefreshInFlight = null;
                });

            return tokenRefreshInFlight;
        };

        refreshLoginToken().catch(function () {});

        loginForm?.addEventListener('submit', function (event) {
            event.preventDefault();

            refreshLoginToken()
                .catch(function () {})
                .finally(function () {
                    HTMLFormElement.prototype.submit.call(loginForm);
                });
        });

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
    });
</script>
@endpush
@endsection
