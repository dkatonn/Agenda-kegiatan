@extends('admin.layout')

@section('title', 'Pengaturan Admin')
@section('suppressGlobalErrors', '1')

@section('content')

<div class="admin-card data-panel" data-server-table="true">
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
        <div class="panel-meta">{{ $admins->total() }} admin terdaftar di sistem.</div>
        <form method="GET" action="{{ route('admin.user-settings') }}" class="table-controls" data-server-table-form>
            <label class="table-control-inline">
                <span>Tampilkan</span>
                <select name="per_page" class="form-select form-select-sm table-page-size">
                    <option value="5" {{ $perPage === 5 ? 'selected' : '' }}>5</option>
                    <option value="10" {{ $perPage === 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $perPage === 25 ? 'selected' : '' }}>25</option>
                </select>
                <span>data</span>
            </label>

            <label class="table-control-search">
                <span>Cari:</span>
                <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm table-search-input" placeholder="Cari admin...">
            </label>
        </form>
    </div>

    <div class="table-shell">
        <table class="table admin-data-table js-admin-table table-centered-content">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Foto</th>
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
                    <td>{{ ($admins->firstItem() ?? 1) + $loop->index }}</td>
                    <td>
                        <div class="admin-list-photo-shell">
                            @if($admin->image_path)
                                <img src="{{ asset('storage/' . $admin->image_path) }}" class="avatar admin-list-avatar" alt="{{ $admin->name }}">
                            @else
                                <span class="admin-list-avatar-placeholder">
                                    <i class="bi bi-person"></i>
                                </span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="admin-name-cell">
                            <strong>{{ $admin->name }}</strong>
                        </div>
                    </td>
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
                    <td colspan="8" class="text-center text-muted">Belum ada admin yang tersimpan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="table-footer">
            <div class="table-info">
                Menampilkan {{ $admins->firstItem() ?? 0 }} sampai {{ $admins->lastItem() ?? 0 }} dari {{ $admins->total() }} data
            </div>
            @include('admin.partials.server-table-pagination', ['paginator' => $admins])
        </div>
    </div>
</div>

@foreach($admins as $admin)
<div class="modal fade" id="editAdminModal{{ $admin->id }}" tabindex="-1" aria-hidden="true" data-edit-lock-modal>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content admin-form-modal">
            <form
                action="{{ route('admin.user-settings.update', $admin->id) }}"
                method="POST"
                enctype="multipart/form-data"
                autocomplete="off"
                data-upload-form
                data-edit-lock-form
                data-lock-endpoint="{{ route('admin.user-settings.lock', $admin->id) }}"
                data-unlock-endpoint="{{ route('admin.user-settings.unlock', $admin->id) }}"
            >
                @csrf
                @method('PUT')
                <input type="hidden" name="form_context" value="edit-admin-{{ $admin->id }}">
                <input type="hidden" name="updated_at_version" value="{{ $admin->updated_at?->toIso8601String() }}">

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

                    <div class="alert alert-info d-none js-edit-lock-status" role="alert"></div>

                    <div class="admin-form-grid admin-form-grid-2">
                        <div class="mb-3 admin-form-grid-full">
                            <label class="form-label">Foto Admin</label>
                            <div class="admin-photo-field">
                                <div class="admin-photo-preview-shell">
                                    @if($admin->image_path)
                                        <img src="{{ asset('storage/' . $admin->image_path) }}" class="admin-photo-preview" alt="{{ $admin->name }}" data-image-preview data-default-image="{{ asset('storage/' . $admin->image_path) }}">
                                    @else
                                        <span class="admin-photo-preview admin-photo-preview-placeholder" data-image-preview-placeholder>
                                            <i class="bi bi-person"></i>
                                        </span>
                                    @endif
                                </div>
                                <div class="admin-photo-input-copy">
                                    <input type="file" name="image" class="form-control" accept="image/*" data-image-input>
                                    <small class="text-muted d-block mt-2">Kosongkan jika tidak ingin mengganti foto admin. Ukuran maksimal 2 MB.</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" value="{{ old('form_context') === 'edit-admin-'.$admin->id ? old('name', $admin->name) : $admin->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NIP</label>
                            <div class="input-counter-field">
                                <input type="text" name="nip" class="form-control" value="{{ old('form_context') === 'edit-admin-'.$admin->id ? old('nip', $admin->nip) : $admin->nip }}" inputmode="numeric" maxlength="18" data-char-limit="18" data-nip-input required>
                                <small class="text-muted input-counter-meta">
                                    <span>NIP admin</span>
                                    <span data-char-counter>0/18</span>
                                </small>
                            </div>
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
                            <input type="password" name="password" class="form-control" data-password-input placeholder="Kosongkan Kata Sandi jika tidak ada perubahan" autocomplete="new-password" data-lpignore="true">
                            <button class="btn btn-outline-secondary" type="button" data-password-toggle>
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">Kata sandi minimal 8 karakter dan menggunakan satu karakter spesial: . ! @ # $ % ^ &amp; *</small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Konfirmasi Kata Sandi Baru</label>
                        <div class="input-group password-toggle-group">
                            <input type="password" name="password_confirmation" class="form-control" data-password-input data-password-confirmation placeholder="Kosongkan konfirmasi Kata Sandi jika tidak ada pembaruan Kata Sandi" autocomplete="new-password" data-lpignore="true">
                            <button class="btn btn-outline-secondary" type="button" data-password-toggle>
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="text-danger d-none" data-password-match></small>
                    </div>

                    <div class="upload-spinner-card d-none" data-upload-spinner>
                        <div class="upload-spinner-head">
                            <div class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></div>
                            <span data-upload-spinner-label>Menyimpan perubahan admin...</span>
                        </div>
                        <div class="upload-spinner-note">Mohon tunggu, file sedang diproses.</div>
                    </div>

                    <div class="form-section-note text-start mb-0" data-last-updated-meta>
                        Terakhir diubah oleh {{ $admin->updater?->name ?? 'sistem' }}
                        @if($admin->updated_at)
                        pada {{ $admin->updated_at->locale('id')->translatedFormat('d F Y H:i') }}
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" data-upload-submit-label="Simpan Perubahan" data-upload-busy-label="Menyimpan...">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<div class="modal fade" id="createAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content admin-form-modal">
            <form action="{{ route('admin.user-settings.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off" data-upload-form>
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
                        <div class="mb-3 admin-form-grid-full">
                            <label class="form-label">Foto Admin</label>
                            <div class="admin-photo-field">
                                <div class="admin-photo-preview-shell">
                                    <span class="admin-photo-preview admin-photo-preview-placeholder" data-image-preview-placeholder>
                                        <i class="bi bi-person"></i>
                                    </span>
                                </div>
                                <div class="admin-photo-input-copy">
                                    <input type="file" name="image" class="form-control" accept="image/*" data-image-input>
                                    <small class="text-muted d-block mt-2">Tambahkan foto admin agar avatar tampil di tabel dan menu akun. Ukuran maksimal 2 MB.</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" value="{{ old('form_context') === 'create-admin' ? old('name') : '' }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NIP</label>
                            <div class="input-counter-field">
                                <input type="text" name="nip" class="form-control" value="{{ old('form_context') === 'create-admin' ? old('nip') : '' }}" inputmode="numeric" maxlength="18" data-char-limit="18" data-nip-input required>
                                <small class="text-muted input-counter-meta">
                                    <span>NIP admin</span>
                                    <span data-char-counter>0/18</span>
                                </small>
                            </div>
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
                            <input type="password" name="password" class="form-control" data-password-input autocomplete="new-password" data-lpignore="true" required>
                            <button class="btn btn-outline-secondary" type="button" data-password-toggle>
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">Kata sandi minimal 8 karakter dan menggunakan satu karakter spesial: . ! @ # $ % ^ &amp; *</small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Konfirmasi Kata Sandi</label>
                        <div class="input-group password-toggle-group">
                            <input type="password" name="password_confirmation" class="form-control" data-password-input data-password-confirmation autocomplete="new-password" data-lpignore="true" required>
                            <button class="btn btn-outline-secondary" type="button" data-password-toggle>
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="text-danger d-none" data-password-match></small>
                    </div>

                    <div class="upload-spinner-card d-none" data-upload-spinner>
                        <div class="upload-spinner-head">
                            <div class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></div>
                            <span data-upload-spinner-label>Menyimpan admin baru...</span>
                        </div>
                        <div class="upload-spinner-note">Mohon tunggu, file sedang diproses.</div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" data-upload-submit-label="Simpan" data-upload-busy-label="Menyimpan...">Simpan</button>
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

    document.querySelectorAll('[data-image-input]').forEach(function(input) {
        input.addEventListener('change', function() {
            const form = input.closest('form');
            const previewImage = form.querySelector('[data-image-preview]');
            const previewPlaceholder = form.querySelector('[data-image-preview-placeholder]');
            const file = input.files && input.files[0] ? input.files[0] : null;

            if (!file) {
                if (previewImage && previewImage.dataset.defaultImage) {
                    previewImage.src = previewImage.dataset.defaultImage;
                }

                if (previewPlaceholder) {
                    previewPlaceholder.classList.remove('d-none');
                }

                return;
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                if (previewImage) {
                    previewImage.src = event.target?.result || previewImage.src;
                    return;
                }

                if (previewPlaceholder) {
                    previewPlaceholder.outerHTML = '<img src="' + (event.target?.result || '') + '" class="admin-photo-preview" data-image-preview alt="Preview foto admin">';
                }
            };

            reader.readAsDataURL(file);
        });
    });

    document.querySelectorAll('[id^="editAdminModal"]').forEach(function(modalElement) {
        modalElement.addEventListener('show.bs.modal', function() {
            const form = modalElement.querySelector('form');
            const passwordInput = form?.querySelector('input[name="password"]');
            const passwordConfirmationInput = form?.querySelector('input[name="password_confirmation"]');

            if (passwordInput) {
                passwordInput.value = '';
                passwordInput.dispatchEvent(new Event('input', { bubbles: true }));
            }

            if (passwordConfirmationInput) {
                passwordConfirmationInput.value = '';
                passwordConfirmationInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });
    });

    document.querySelectorAll('form[data-upload-form]').forEach(function(form) {
        form.addEventListener('submit', function() {
            const spinnerCard = form.querySelector('[data-upload-spinner]');
            const submitButton = form.querySelector('button[type="submit"]');
            const busyLabel = submitButton?.dataset.uploadBusyLabel;

            if (spinnerCard) {
                spinnerCard.classList.remove('d-none');
            }

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.dataset.originalLabel = submitButton.innerHTML;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + (busyLabel || 'Memproses...');
            }
        });
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
