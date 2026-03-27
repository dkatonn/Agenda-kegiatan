@extends('admin.layout')

@section('title','Profil Pegawai')

@section('content')

<div class="admin-card">

    <div class="card-header">

        <h6>
            <i class="bi bi-people"></i>
            Profil Pegawai
        </h6>

        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
            Tambah
        </button>

    </div>

    <table class="table">

        <thead>
            <tr>
                <th>Foto</th>
                <th>Nama</th>
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
                <td>{{ $emp->role }}</td>

                <td>

                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editEmployeeModal{{ $emp->id }}">
                        Edit
                    </button>

                    <form action="{{ route('admin.employee.delete',$emp->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-sm btn-outline-danger">
                            Hapus
                        </button>

                    </form>

                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</div>

@foreach($employee as $emp)
<div class="modal fade" id="editEmployeeModal{{ $emp->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.employee.update', $emp->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" value="{{ $emp->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="role" class="form-control" value="{{ $emp->role }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
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
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.employee.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="role" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
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
