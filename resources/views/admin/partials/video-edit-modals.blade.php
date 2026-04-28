@foreach($videos as $video)
<div class="modal fade" id="editVideoModal{{ $video->id }}" tabindex="-1" aria-hidden="true" data-edit-lock-modal>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content admin-form-modal">
            <form
                action="{{ route('admin.video.update', $video->id) }}"
                method="POST"
                data-video-edit-form
                data-edit-lock-form
                data-lock-endpoint="{{ route('admin.video.lock', $video->id) }}"
                data-unlock-endpoint="{{ route('admin.video.unlock', $video->id) }}"
            >
                @csrf
                @method('PUT')
                <input type="hidden" name="updated_at_version" value="{{ $video->updated_at?->toIso8601String() }}">

                <div class="modal-header">
                    <div>
                        <div class="modal-eyebrow">Update Media</div>
                        <h5 class="modal-title">Edit Video</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-info d-none js-edit-lock-status" role="alert"></div>
                    <div class="form-section-note">
                        Perbarui nama video agar lebih mudah dikenali di panel admin.
                    </div>

                    <div class="admin-form-grid">
                        <div class="mb-0">
                            <label class="form-label">Nama Video</label>
                            <div class="input-counter-field">
                                <input type="text" name="title" class="form-control" value="{{ $video->title }}" maxlength="255" data-char-limit="255" required>
                                <small class="text-muted input-counter-meta">
                                    <span>Nama video</span>
                                    <span data-char-counter>0/255</span>
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-note text-start mb-0" data-last-updated-meta>
                        Terakhir diubah oleh {{ $video->updater?->name ?? 'sistem' }}
                        @if($video->updated_at)
                        pada {{ $video->updated_at->locale('id')->translatedFormat('d F Y H:i') }}
                        @endif
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
