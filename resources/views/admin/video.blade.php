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

            @if(!empty($settings['video']))

            <tr>

                <td>
                    <video width="120" muted loop controls playsinline preload="metadata">
                        <source src="{{ Storage::disk('public')->url($settings['video']) }}" type="video/mp4">
                    </video>
                </td>

                <td>{{ $settings['video'] }}</td>

                <td>
                    <div class="d-flex gap-2 align-items-center">
                        <span class="badge bg-success">
                            Aktif
                        </span>

                        <form action="{{ route('admin.video.delete', 1) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </div>
                </td>

            </tr>

            @else
            <tr>
                <td colspan="3" class="text-center text-muted">Belum ada video yang diupload.</td>
            </tr>
            @endif

        </tbody>

    </table>

</div>

<div class="modal fade" id="uploadVideoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.video.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Upload Video</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">File Video</label>
                        <input type="file" name="video" class="form-control" accept="video/mp4,video/webm,video/ogg" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
