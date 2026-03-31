@extends('admin.layout')

@section('title','User Settings')

@section('content')

<div class="admin-card data-panel">
    <div class="panel-header">
        <div>
            <div class="section-eyebrow">Manajemen Admin</div>
            <h6 class="panel-title">
                <i class="bi bi-person-gear"></i>
                User Settings
            </h6>
        </div>

        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createAdminModal">
            Tambah Admin
        </button>
    </div>

    <div class="panel-toolbar table-toolbar">
        <div class="panel-meta">Tampilan CRUD admin ini masih versi UI dan belum terhubung ke database.</div>
        <div class="table-controls">
            <label class="table-control-inline">
                <span>Show</span>
                <select class="form-select form-select-sm table-page-size">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                </select>
                <span>entries</span>
            </label>

            <label class="table-control-search">
                <span>Search:</span>
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
                    <th>Username</th>
                    <th>NIP</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach($admins as $admin)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $admin->name }}</td>
                    <td>{{ $admin->username }}</td>
                    <td>{{ $admin->nip }}</td>
                    <td>{{ $admin->email }}</td>
                    <td>{{ $admin->role }}</td>
                    <td>
                        <span class="badge bg-success">{{ $admin->status }}</span>
                    </td>
                    <td>
                        <div class="action-group">
                            <button
                                type="button"
                                class="btn btn-sm admin-icon-btn {{ $admin->status === 'Aktif' ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                                data-bs-toggle="modal"
                                data-bs-target="#comingSoonUserCrudModal"
                                title="{{ $admin->status === 'Aktif' ? 'Nonaktifkan admin' : 'Aktifkan admin' }}"
                                aria-label="{{ $admin->status === 'Aktif' ? 'Nonaktifkan admin' : 'Aktifkan admin' }}"
                            >
                                <i class="bi {{ $admin->status === 'Aktif' ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                            </button>
                            <button
                                class="btn btn-sm btn-outline-primary admin-icon-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#editAdminModal{{ $admin->id }}"
                                title="Edit admin"
                                aria-label="Edit admin"
                            >
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-danger admin-icon-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#comingSoonUserCrudModal"
                                title="Hapus admin"
                                aria-label="Hapus admin"
                            >
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="table-footer">
            <div class="table-info"></div>
            <div class="table-pagination">
                <button type="button" class="btn btn-light btn-sm table-prev">Prev</button>
                <span class="table-page-indicator">1</span>
                <button type="button" class="btn btn-light btn-sm table-next">Next</button>
            </div>
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="info-box">
        <strong>Catatan:</strong> halaman ini baru menyiapkan tampilan CRUD admin. Nanti saat tabel user/admin sudah tersedia, form dan aksinya tinggal disambungkan ke backend.
    </div>
</div>

<div class="modal fade" id="createAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content admin-form-modal">
            <form>
                <div class="modal-header">
                    <div>
                        <div class="modal-eyebrow">Setup Akun</div>
                        <h5 class="modal-title">Tambah Admin</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-section-note">
                        Rancang data akun admin dari sisi UI terlebih dahulu. Saat backend siap, struktur ini tinggal disambungkan ke database.
                    </div>

                    <div class="admin-form-grid admin-form-grid-2">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" class="form-control" placeholder="Masukkan nama admin">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" placeholder="Masukkan username">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NIP</label>
                            <input type="text" class="form-control" placeholder="Masukkan NIP">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" placeholder="Masukkan email">
                        </div>
                    </div>

                    <div class="admin-form-grid admin-form-grid-2">
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select">
                                <option>Super Admin</option>
                                <option>Operator</option>
                                <option>Editor</option>
                            </select>
                        </div>
                    </div>

                    <div class="admin-form-divider">Pengaturan Password</div>

                    <div class="admin-form-grid admin-form-grid-2">
                        <div class="mb-3">
                            <label class="form-label">Password Lama</label>
                            <div class="password-input-shell">
                                <input type="password" class="form-control password-toggle-input" placeholder="Masukkan password lama">
                                <button type="button" class="password-toggle-btn" aria-label="Tampilkan password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <div class="password-input-shell">
                                <input type="password" class="form-control password-toggle-input" placeholder="Masukkan password baru">
                                <button type="button" class="password-toggle-btn" aria-label="Tampilkan password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <div class="password-input-shell">
                                <input type="password" class="form-control password-toggle-input" placeholder="Ulangi password baru">
                                <button type="button" class="password-toggle-btn" aria-label="Tampilkan password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted">Saat backend aktif, password baru akan di-hash di server sebelum disimpan.</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#comingSoonUserCrudModal">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($admins as $admin)
<div class="modal fade" id="editAdminModal{{ $admin->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content admin-form-modal">
            <form>
                <div class="modal-header">
                    <div>
                        <div class="modal-eyebrow">Update Akun</div>
                        <h5 class="modal-title">Edit Admin</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-section-note">
                        Perbarui detail akun admin. Nantinya perubahan ini akan terhubung ke proses autentikasi saat backend sudah aktif.
                    </div>

                    <div class="admin-form-grid admin-form-grid-2">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" class="form-control" value="{{ $admin->name }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" value="{{ $admin->username }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NIP</label>
                            <input type="text" class="form-control" value="{{ $admin->nip }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="{{ $admin->email }}">
                        </div>
                    </div>

                    <div class="admin-form-grid admin-form-grid-2">
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select">
                                <option {{ $admin->role === 'Super Admin' ? 'selected' : '' }}>Super Admin</option>
                                <option {{ $admin->role === 'Operator' ? 'selected' : '' }}>Operator</option>
                                <option {{ $admin->role === 'Editor' ? 'selected' : '' }}>Editor</option>
                            </select>
                        </div>
                    </div>

                    <div class="admin-form-divider">Pengaturan Password</div>

                    <div class="admin-form-grid admin-form-grid-2">
                        <div class="mb-3">
                            <label class="form-label">Password Lama</label>
                            <div class="password-input-shell">
                                <input type="password" class="form-control password-toggle-input" placeholder="Masukkan password lama">
                                <button type="button" class="password-toggle-btn" aria-label="Tampilkan password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <div class="password-input-shell">
                                <input type="password" class="form-control password-toggle-input" placeholder="Masukkan password baru">
                                <button type="button" class="password-toggle-btn" aria-label="Tampilkan password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <div class="password-input-shell">
                                <input type="password" class="form-control password-toggle-input" placeholder="Ulangi password baru">
                                <button type="button" class="password-toggle-btn" aria-label="Tampilkan password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted">Saat backend aktif, password baru akan di-hash di server sebelum disimpan.</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#comingSoonUserCrudModal">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<div class="modal fade" id="comingSoonUserCrudModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content delete-confirm-modal">
            <div class="modal-body text-center">
                <div class="delete-confirm-icon account-coming-soon-icon">
                    <i class="bi bi-database-gear"></i>
                </div>
                <h5 class="delete-confirm-title">CRUD Admin Belum Aktif</h5>
                <p class="delete-confirm-message">
                    Tampilan halaman ini sudah siap. Aksi tambah, edit, dan hapus admin akan diaktifkan setelah tabel database dan backend-nya selesai dibuat.
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">Mengerti</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.password-toggle-btn').forEach((button) => {
            button.addEventListener('click', () => {
                const shell = button.closest('.password-input-shell');
                const input = shell?.querySelector('.password-toggle-input');
                const icon = button.querySelector('i');

                if (!input || !icon) return;

                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
                button.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
            });
        });
    });
</script>
@endpush

@endsection
