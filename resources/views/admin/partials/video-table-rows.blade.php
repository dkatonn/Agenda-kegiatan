@forelse($videos as $video)
<tr class="js-sortable-row" draggable="false" data-video-id="{{ $video->id }}">
    <td>
        <button type="button" class="btn btn-light btn-sm video-sort-handle" title="Geser untuk ubah urutan">
            <i class="bi bi-grip-vertical"></i>
            <span class="video-order-number">{{ $video->id }}</span>
        </button>
    </td>

    <td>
        @if($video->resolved_video_url)
        <video width="160" muted loop controls playsinline preload="metadata" class="js-video-preview">
            <source src="{{ $video->resolved_video_url }}" type="video/mp4">
        </video>
        @else
        <div class="video-preview-placeholder">
            Video tidak ditemukan
        </div>
        @endif
    </td>

    <td>{{ $video->title }}</td>

    <td>
        <span class="video-meta-pill js-video-duration">Memuat...</span>
    </td>

    <td>
        <span class="video-meta-pill">{{ $video->file_size_label }}</span>
    </td>

    <td>
        <span class="badge {{ $video->is_active ? 'bg-success' : 'bg-secondary' }}">
            {{ $video->is_active ? 'Aktif' : 'Nonaktif' }}
        </span>
    </td>

    <td>
        <div class="action-group">
            <form action="{{ route('admin.video.toggle', $video->id) }}" method="POST" data-video-toggle-form>
                @csrf
                @method('PATCH')
                <button
                    class="btn btn-sm admin-icon-btn {{ $video->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                    title="{{ $video->is_active ? 'Nonaktifkan video' : 'Aktifkan video' }}"
                    aria-label="{{ $video->is_active ? 'Nonaktifkan video' : 'Aktifkan video' }}"
                >
                    <i class="bi {{ $video->is_active ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                </button>
            </form>

            <button
                class="btn btn-sm btn-outline-primary admin-icon-btn"
                data-bs-toggle="modal"
                data-bs-target="#editVideoModal{{ $video->id }}"
                title="Edit video"
                aria-label="Edit video"
            >
                <i class="bi bi-pencil-square"></i>
            </button>

            <form
                action="{{ route('admin.video.delete', $video->id) }}"
                method="POST"
                class="js-confirm-delete"
                data-confirm-message="Yakin ingin menghapus video ini?"
                data-ajax-submit="true"
                data-video-delete-form
            >
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-outline-danger admin-icon-btn" title="Hapus video" aria-label="Hapus video">
                    <i class="bi bi-trash3"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center text-muted">Belum ada video yang diupload.</td>
</tr>
@endforelse
