@extends('admin.layout')

@section('title','Profil Pegawai')

@section('content')

<div class="admin-card data-panel">

    <div class="panel-header">
        <div>
            <div class="section-eyebrow">Manajemen Data</div>
            <h6 class="panel-title">
                <i class="bi bi-people"></i>
                Profil Pegawai
            </h6>
        </div>

        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
            Tambah
        </button>
    </div>

    <div class="panel-toolbar table-toolbar">
        <div class="panel-meta">Total {{ $employee->count() }} pegawai tersimpan.</div>
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
                <input type="text" class="form-control form-control-sm table-search-input" placeholder="Cari pegawai...">
            </label>
        </div>
    </div>

    <div class="table-shell">
        <table class="table admin-data-table js-admin-table table-centered-content">

            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Jabatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                @foreach($employee as $emp)

                <tr>

                    <td>
                        @if($emp->image_path)
                        <img src="{{ asset('storage/'.$emp->image_path) }}" class="avatar">
                        @endif
                    </td>

                    <td>{{ $emp->name }}</td>
                    <td>{{ $emp->nip ?? '-' }}</td>
                    <td>{{ $emp->role }}</td>

                    <td>
                        <div class="action-group">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editEmployeeModal{{ $emp->id }}">
                                Edit
                            </button>

                            <form action="{{ route('admin.employee.delete',$emp->id) }}" method="POST" class="d-inline js-confirm-delete" data-confirm-message="Yakin ingin menghapus data pegawai ini?">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-outline-danger">
                                    Hapus
                                </button>

                            </form>
                        </div>
                    </td>

                </tr>

                @endforeach

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

@foreach($employee as $emp)
<div class="modal fade" id="editEmployeeModal{{ $emp->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content admin-form-modal">
            <form action="{{ route('admin.employee.update', $emp->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <div>
                        <div class="modal-eyebrow">Update Profil</div>
                        <h5 class="modal-title">Edit Pegawai</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-section-note">
                        Perbarui identitas dan foto pegawai. Perubahan akan langsung tampil pada layar TV setelah disimpan.
                    </div>

                    <div class="admin-form-grid admin-form-grid-2">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" value="{{ $emp->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control" value="{{ $emp->nip }}" inputmode="numeric" maxlength="18">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="role" class="form-control" value="{{ $emp->role }}" required>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Foto</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Kosongkan jika tidak ingin mengganti foto pegawai.</small>
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

<div class="modal fade" id="createEmployeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content admin-form-modal">
            <form action="{{ route('admin.employee.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <div>
                        <div class="modal-eyebrow">Tambah Data</div>
                        <h5 class="modal-title">Tambah Pegawai</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-section-note">
                        Tambahkan profil pegawai baru untuk ditampilkan pada rotasi layar TV.
                    </div>

                    <div class="admin-form-grid admin-form-grid-2">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control" inputmode="numeric" maxlength="18">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="role" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Foto</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Gunakan foto yang jelas agar tampil rapi pada layar TV.</small>
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
