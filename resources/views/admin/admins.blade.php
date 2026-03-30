@extends('admin.layout')

@section('title','Admin')
@section('suppressGlobalErrors', '1')

@section('content')

<div class="admin-card">
    <div class="card-header">
        <h6>
            <i class="bi bi-shield-lock"></i>
            Kelola Admin
        </h6>

        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createAdminModal">
            Tambah Admin
        </button>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>NIP</th>
                <th>Email Sistem</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse($admins as $admin)
            <tr>
                <td>{{ $admin->nip ?? '-' }}</td>
                <td>{{ $admin->email }}</td>
                <td>{{ optional($admin->created_at)->format('d M Y H:i') }}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editAdminModal{{ $admin->id }}">
                        Edit
                    </button>

                    <form action="{{ route('admin.admins.delete', $admin->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus admin ini?')">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted">Belum ada admin.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@foreach($admins as $admin)
<div class="modal fade" id="editAdminModal{{ $admin->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.admins.update', $admin->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_context" value="edit-admin-{{ $admin->id }}">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @if(old('form_context') === 'edit-admin-'.$admin->id && $errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip" class="form-control" value="{{ old('form_context') === 'edit-admin-'.$admin->id ? old('nip', $admin->nip) : $admin->nip }}" inputmode="numeric" maxlength="18" data-nip-input required>
                        <div class="form-helper">
                            <small class="text-muted">NIP wajib 18 digit.</small>
                            <small class="text-muted"><span data-nip-counter>0</span>/18</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <div class="input-group password-toggle-group">
                            <input type="password" name="password" class="form-control" data-password-input placeholder="Kosongkan jika tidak diubah">
                            <button class="btn btn-outline-secondary" type="button" data-password-toggle>
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">Minimal 8 karakter dan wajib menggunakan spesial karakter: ". ! @ # $ % ^ &amp; *"</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Validasi Password Baru</label>
                        <div class="input-group password-toggle-group">
                            <input type="password" name="password_confirmation" class="form-control" data-password-input data-password-confirmation placeholder="Ulangi password baru">
                            <button class="btn btn-outline-secondary" type="button" data-password-toggle>
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="form-helper">
                            <small class="text-muted">Minimal 8 karakter jika password diubah.</small>
                            <small class="text-danger d-none" data-password-match></small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<div class="modal fade" id="createAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.admins.store') }}" method="POST">
                @csrf
                <input type="hidden" name="form_context" value="create-admin">

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @if(old('form_context') === 'create-admin' && $errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip" class="form-control" value="{{ old('form_context') === 'create-admin' ? old('nip') : '' }}" inputmode="numeric" maxlength="18" data-nip-input required>
                        <div class="form-helper">
                            <small class="text-muted">NIP wajib 18 digit.</small>
                            <small class="text-muted"><span data-nip-counter>0</span>/18</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password yang akan digunakan</label>
                        <div class="input-group password-toggle-group">
                            <input type="password" name="password" class="form-control" data-password-input required>
                            <button class="btn btn-outline-secondary" type="button" data-password-toggle>
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">Minimal 8 karakter dan wajib menggunakan spesial karakter: ". ! @ # $ % ^ &amp; *"</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Validasi Password</label>
                        <div class="input-group password-toggle-group">
                            <input type="password" name="password_confirmation" class="form-control" data-password-input data-password-confirmation required>
                            <button class="btn btn-outline-secondary" type="button" data-password-toggle>
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="form-helper">
                            <small class="text-muted">Password minimal 8 karakter dan harus sama.</small>
                            <small class="text-danger d-none" data-password-match></small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-nip-input]').forEach(function(input) {
        const updateCounter = function() {
            input.value = input.value.replace(/\D/g, '').slice(0, 18);
            const counter = input.parentElement.querySelector('[data-nip-counter]');

            if (counter) {
                counter.textContent = input.value.length;
            }
        };

        input.addEventListener('input', updateCounter);
        updateCounter();
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

    document.querySelectorAll('.modal-content form').forEach(function(form) {
        const passwordInput = form.querySelector('input[name="password"]');
        const passwordConfirmationInput = form.querySelector('[data-password-confirmation]');
        const passwordMatchStatus = form.querySelector('[data-password-match]');

        if (!passwordInput || !passwordConfirmationInput || !passwordMatchStatus) {
            return;
        }

        const updatePasswordMatch = function() {
            const password = passwordInput.value;
            const confirmation = passwordConfirmationInput.value;

            if (!confirmation) {
                passwordMatchStatus.textContent = '';
                passwordMatchStatus.className = 'text-danger d-none';
                return;
            }

            if (password === confirmation) {
                passwordMatchStatus.textContent = '';
                passwordMatchStatus.className = 'text-danger d-none';
                return;
            }

            passwordMatchStatus.textContent = 'Validasi password tidak sama';
            passwordMatchStatus.className = 'text-danger';
        };

        passwordInput.addEventListener('input', updatePasswordMatch);
        passwordConfirmationInput.addEventListener('input', updatePasswordMatch);
        updatePasswordMatch();
    });

    const formContext = @json(old('form_context'));

    if (formContext) {
        const modalId = formContext === 'create-admin'
            ? 'createAdminModal'
            : 'editAdminModal' + formContext.replace('edit-admin-', '');
        const modalElement = document.getElementById(modalId);

        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        }
    }
</script>
@endpush
