@extends('admin.layout')

@section('title','Agenda')

@section('content')

<div class="admin-card">

    <div class="card-header">

        <h6>
            <i class="bi bi-calendar-event"></i>
            Agenda Data
        </h6>

        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createAgendaModal">
            Tambah Agenda
        </button>

    </div>

    <table class="table">

        <thead>
            <tr>
                <th>Tanggal dan Waktu</th>
                <th>Kegiatan</th>
                <th>Lokasi</th>
                <th>Disposisi</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

            @foreach($agenda as $item)

            <tr>

                <td>{{ \Carbon\Carbon::parse($item->date.' '.$item->time)->format('d/m/Y H:i') }} WIB</td>

                <td>{{ $item->name }}</td>

                <td>{{ $item->location }}</td>

                <td>{{ $item->disposition }}</td>

                <td>

                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editAgendaModal{{ $item->id }}">
                        Edit
                    </button>

                    <form action="{{ route('admin.agenda.delete',$item->id) }}" method="POST" class="d-inline">
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

<div class="modal fade" id="createAgendaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.agenda.store') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Agenda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Waktu</label>
                        <input type="time" name="time" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kegiatan</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="location" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Disposisi</label>
                        <input type="text" name="disposition" class="form-control" required>
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

@foreach($agenda as $item)
<div class="modal fade" id="editAgendaModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.agenda.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Agenda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="date" class="form-control" value="{{ $item->date }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Waktu</label>
                        <input type="time" name="time" class="form-control" value="{{ \Illuminate\Support\Str::limit($item->time, 5, '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kegiatan</label>
                        <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="location" class="form-control" value="{{ $item->location }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Disposisi</label>
                        <input type="text" name="disposition" class="form-control" value="{{ $item->disposition }}" required>
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

@endsection
