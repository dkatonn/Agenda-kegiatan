@foreach($videos as $video)
<div class="modal fade" id="editVideoModal{{ $video->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content admin-form-modal">
            <form action="{{ route('admin.video.update', $video->id) }}" method="POST" data-video-edit-form>
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <div>
                        <div class="modal-eyebrow">Update Media</div>
                        <h5 class="modal-title">Edit Video</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-section-note">
                        Perbarui nama video agar lebih mudah dikenali di panel admin.
                    </div>

                    <div class="admin-form-grid">
                        <div class="mb-0">
                            <label class="form-label">Nama Video</label>
                            <input type="text" name="title" class="form-control" value="{{ $video->title }}" required>
                        </div>
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
