@extends('admin.layout')

@section('title','Video Kegiatan')

@section('content')

<div class="admin-card">

    <div class="card-header">

        <h6>
            <i class="bi bi-film"></i>
            Video Kegiatan
        </h6>

        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadVideoModal">
            Upload
        </button>

    </div>

    <table class="table">

        <thead>
            <tr>
                <th>Preview</th>
                <th>File</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>

            @if(isset($settings['video']))

            <tr>

                <td>
                    <video width="120" muted loop controls>
                        <source src="{{ asset($settings['video']) }}" type="video/mp4">
                    </video>
                </td>

                <td>{{ $settings['video'] }}</td>

                <td>
                    <span class="badge bg-success">
                        Aktif
                    </span>
                </td>

            </tr>

            @endif

        </tbody>

    </table>

</div>

@endsection