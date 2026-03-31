@extends('admin.layout')

@section('title', 'Pengaturan Admin')
@section('suppressGlobalErrors', '1')

@section('content')

<div class="admin-card data-panel">
    <div class="panel-header">
        <div>
            <div class="section-eyebrow">Manajemen Admin</div>
            <h6 class="panel-title">
                <i class="bi bi-person-gear"></i>
                Pengaturan Admin
            </h6>
        </div>

        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createAdminModal">
            Tambah Admin
        </button>
    </div>

    <div class="panel-toolbar table-toolbar">
        <div class="panel-meta">{{ $admins->count() }} admin terdaftar di sistem.</div>
        <div class="table-controls">
            <label class="table-control-inline">
                <span>Tampilkan</span>
                <select class="form-select form-select-sm table-page-size">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                </select>
                <span>data</span>
            </label>

            <label class="table-control-search">
                <span>Cari:</span>
                <input type="text" class="form-control form-control-sm table-search-input" placeholder="Cari admin...">
            </label>
        </div>
    </div>

    <div class="table-shell">
        <table class="table admin-data-table js-admin-table table-centered-content">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($admins as $admin)
                @php
                    $isActive = \Illuminate\Support\Facades\Schema::hasColumn('users', 'is_active') ? (bool) $admin->is_active : true;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $admin->name }}</td>
                    <td>{{ $admin->nip ?? '-' }}</td>
                    <td>{{ $admin->email }}</td>
                    <td>
                        <span class="badge {{ $isActive ? 'bg-success' : 'bg-secondary' }}">
                            {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>{{ optional($admin->created_at)->format('d M Y H:i') ?? '-' }}</td>
                    <td>
                        <div class="action-group">
                            <form action="{{ route('admin.user-settings.toggle', $admin->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button
                                    type="submit"
                                    class="btn btn-sm admin-icon-btn {{ $isActive ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                                    data-confirm-submit="{{ $isActive ? 'Nonaktifkan admin ini?' : 'Aktifkan kembali admin ini?' }}"
                                    data-confirm-action-label="{{ $isActive ? 'Ya, nonaktifkan' : 'Ya, aktifkan' }}"
                                    title="{{ $isActive ? 'Nonaktifkan admin' : 'Aktifkan admin' }}"
                                    aria-label="{{ $isActive ? 'Nonaktifkan admin' : 'Aktifkan admin' }}"
                                >
                                    <i class="bi {{ $isActive ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                </button>
                            </form>

                            <button
                                class="btn btn-sm btn-outline-primary admin-icon-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#editAdminModal{{ $admin->id }}"
                                title="Edit admin"
                                aria-label="Edit admin"
                            >
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            <form action="{{ route('admin.user-settings.delete', $admin->id) }}" method="POST" class="d-inline js-confirm-delete" data-confirm-message="Hapus admin ini?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger admin-icon-btn" title="Hapus admin" aria-label="Hapus admin">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Belum ada admin yang tersimpan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="table-footer">
            <div class="table-info"></div>
            <div class="table-pagination">
                <button type="button" class="btn btn-light btn-sm table-prev">Sebelumnya</button>
                <span class="table-page-indicator">1</span>
                <button type="button" class="btn btn-light btn-sm table-next">Berikutnya</button>
            </div>
        </div>
    </div>
</div>

@foreach($admins as $admin)
<div class="modal fade" id="editAdminModal{{ $admin->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content admin-form-modal">
            <form action="{{ route('admin.user-settings.update', $admin->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_context" value="edit-admin-{{ $admin->id }}">

                <div class="modal-header">
                    <div>
                        <div class="modal-eyebrow">Update Akun</div>
                        <h5 class="modal-title">Edit Admin</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @if(old('form_context') === 'edit-admin-'.$admin->id && $errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                    @endif

                    <div class="admin-form-grid admin-form-grid-2">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" value="{{ old('form_context') === 'edit-admin-'.$admin->id ? old('name', $admin->name) : $admin->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control" value="{{ old('form_context') === 'edit-admin-'.$admin->id ? old('nip', $admin->nip) : $admin->nip }}" inputmode="numeric" maxlength="18" data-nip-input required>
                        </div>

                        <div class="mb-3 admin-form-grid-full">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('form_context') === 'edit-admin-'.$admin->id ? old('email', $admin->email) : $admin->email }}" required>
                        </div>
                    </div>

                    <div class="admin-form-divider">Ubah Kata Sandi</div>

                    <div class="mb-3">
                        <label class="form-label">Kata Sandi Baru</label>
                        <div class="input-group password-toggle-group">
                            <input type="password" name="password" class="form-control" data-password-input placeholder="Kosongkan jika tidak diubah">
                            <button class="btn btn-outline-secondary" type="button" data-password-toggle>
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">Kata sandi minimal 8 karakter dan menggunakan satu karakter spesial: . ! @ # $ % ^ &amp; *</small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Konfirmasi Kata Sandi Baru</label>
                        <div class="input-group password-toggle-group">
                            <input type="password" name="password_confirmation" class="form-control" data-password-input data-password-confirmation placeholder="Ulangi kata sandi baru">
                            <button class="btn btn-outline-secondary" type="button" data-password-toggle>
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="text-danger d-none" data-password-match></small>
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
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content admin-form-modal">
            <form action="{{ route('admin.user-settings.store') }}" method="POST">
                @csrf
                <input type="hidden" name="form_context" value="create-admin">

                <div class="modal-header">
                    <div>
                        <div class="modal-eyebrow">Tambah Admin</div>
                        <h5 class="modal-title">Admin Baru</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @if(old('form_context') === 'create-admin' && $errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                    @endif

                    <div class="admin-form-grid admin-form-grid-2">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" value="{{ old('form_context') === 'create-admin' ? old('name') : '' }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control" value="{{ old('form_context') === 'create-admin' ? old('nip') : '' }}" inputmode="numeric" maxlength="18" data-nip-input required>
                        </div>

                        <div class="mb-3 admin-form-grid-full">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('form_context') === 'create-admin' ? old('email') : '' }}" required>
                        </div>
                    </div>

                    <div class="admin-form-divider">Kata Sandi Awal</div>

                    <div class="mb-3">
                        <label class="form-label">Kata Sandi</label>
                        <div class="input-group password-toggle-group">
                            <input type="password" name="password" class="form-control" data-password-input required>
                            <button class="btn btn-outline-secondary" type="button" data-password-toggle>
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">Kata sandi minimal 8 karakter dan menggunakan satu karakter spesial: . ! @ # $ % ^ &amp; *</small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Konfirmasi Kata Sandi</label>
                        <div class="input-group password-toggle-group">
                            <input type="password" name="password_confirmation" class="form-control" data-password-input data-password-confirmation required>
                            <button class="btn btn-outline-secondary" type="button" data-password-toggle>
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="text-danger d-none" data-password-match></small>
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
        input.addEventListener('input', function() {
            input.value = input.value.replace(/\D/g, '').slice(0, 18);
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

            if (!confirmation || password === confirmation) {
                passwordMatchStatus.textContent = '';
                passwordMatchStatus.className = 'text-danger d-none';
                return;
            }

            passwordMatchStatus.textContent = 'Konfirmasi kata sandi tidak sama.';
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
