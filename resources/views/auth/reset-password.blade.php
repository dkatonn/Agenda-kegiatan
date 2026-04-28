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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const resetForm = document.querySelector('.auth-form');
        const csrfInput = resetForm?.querySelector('input[name="_token"]');
        let tokenRefreshInFlight = null;

        const refreshCsrfToken = function () {
            if (!resetForm || !csrfInput) {
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
                        throw new Error('Gagal menyegarkan token reset password.');
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

        refreshCsrfToken().catch(function () {});

        resetForm?.addEventListener('submit', function (event) {
            event.preventDefault();

            refreshCsrfToken()
                .catch(function () {})
                .finally(function () {
                    HTMLFormElement.prototype.submit.call(resetForm);
                });
        });
    });
</script>
@endpush
@endsection
