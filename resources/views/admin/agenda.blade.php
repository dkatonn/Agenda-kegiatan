@extends('admin.layout')

@section('title','Agenda')

@section('content')

<div class="admin-card">

    <div class="card-header">

        <h6>
            <i class="bi bi-calendar-event"></i>
            Agenda Data
        </h6>

        <button class="btn btn-primary btn-sm">
            Tambah Agenda
        </button>

    </div>

    <table class="table">

        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kegiatan</th>
                <th>Lokasi</th>
                <th>Disposisi</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

            @foreach($agenda as $agenda)

            <tr>

                <td>{{ \Carbon\Carbon::parse($agenda->date)->format('d/m/Y') }}</td>

                <td>{{ $agenda->name }}</td>

                <td>{{ $agenda->location }}</td>

                <td>{{ $agenda->disposition }}</td>

                <td>

                    <button class="btn btn-sm btn-outline-primary">
                        Edit
                    </button>

                    <form action="{{ route('admin.agenda.delete',$agenda->id) }}" method="POST" class="d-inline">
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