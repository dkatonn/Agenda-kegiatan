@extends('admin.layout')

@section('title','Profil Pegawai')

@section('content')

<div class="admin-card">

    <div class="card-header">

        <h6>
            <i class="bi bi-people"></i>
            Profil Pegawai
        </h6>

        <button class="btn btn-primary btn-sm">
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

                    <button class="btn btn-sm btn-outline-primary">
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

@endsection