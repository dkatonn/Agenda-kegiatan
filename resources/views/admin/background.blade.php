@extends('admin.layout')

@section('title','Latar Belakang')

@section('content')

<div class="row g-4">
    <div class="col-lg-7">
        <div class="admin-card">
            <div class="section-heading">
                <div>
                    <div class="section-eyebrow">Manajemen Tampilan Latar Belakang</div>
                    <h6 class="panel-title">
                        <i class="bi bi-image"></i>
                        Pengaturan Latar Belakang
                    </h6>
                </div>
            </div>

            <form action="{{ route('admin.setting.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-section-note">
                    Upload gambar latar belakang untuk tampilan TV. Perubahan akan ikut tersinkron ke layar setelah disimpan.
                </div>

                <div class="mb-3">
                    <label class="form-label">File Latar Belakang</label>
                    <input type="file" name="bg_image" class="form-control js-background-input" accept="image/*">
                    <small class="text-muted">Gunakan rasio 16:9 agar hasil tampil lebih proporsional di TV.</small>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i>
                        Simpan Latar Belakang
                    </button>

                    <button
                        type="submit"
                        name="remove_background"
                        value="1"
                        class="btn btn-danger-soft background-remove-btn"
                        data-confirm-submit="Yakin ingin menghapus latar belakang TV ini?"
                        {{ empty($settings['background']) ? 'disabled' : '' }}>
                        <i class="bi bi-trash"></i>
                        Hapus Latar Belakang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="admin-card running-text-preview-card">
            <div class="section-heading">
                <div>
                    <div class="section-eyebrow">Preview</div>
                    <h6 class="panel-title">
                        <i class="bi bi-tv"></i>
                        Simulasi Latar Belakang
                    </h6>
                </div>
            </div>

            <div class="background-preview-card mb-3">
                <div class="background-preview-label">Preview Upload</div>
                <div class="background-preview-frame">
                    <img
                        src="{{ !empty($settings['background']) ? asset('storage/' . $settings['background']) : '' }}"
                        alt="Preview Latar Belakang TV"
                        class="background-preview-image js-background-preview {{ empty($settings['background']) ? 'd-none' : '' }}">

                    <div class="background-preview-empty js-background-preview-empty {{ !empty($settings['background']) ? 'd-none' : '' }}">
                        Belum ada latar belakang yang dipilih.
                    </div>
                </div>
            </div>

            <a href="{{ route('tv') }}" target="_blank" class="btn btn-light">
                <i class="bi bi-play-circle"></i>
                Buka Preview TV
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const backgroundInput = document.querySelector('.js-background-input');
        const backgroundPreview = document.querySelector('.js-background-preview');
        const backgroundPreviewEmpty = document.querySelector('.js-background-preview-empty');

        if (!backgroundInput || !backgroundPreview || !backgroundPreviewEmpty) {
            return;
        }

        backgroundInput.addEventListener('change', (event) => {
            const [file] = event.target.files || [];

            if (!file) {
                backgroundPreview.classList.add('d-none');
                backgroundPreviewEmpty.classList.remove('d-none');
                return;
            }

            const objectUrl = URL.createObjectURL(file);
            backgroundPreview.src = objectUrl;
            backgroundPreview.classList.remove('d-none');
            backgroundPreviewEmpty.classList.add('d-none');
        });
    });
</script>
@endpush

@endsection
