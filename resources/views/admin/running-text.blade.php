@extends('admin.layout')

@section('title','Teks Berjalan')

@section('content')

<div class="row g-4">
    <div class="col-lg-7">
        <div class="admin-card">
            <div class="section-heading">
                <div>
                    <div class="section-eyebrow">Manajemen Informasi</div>
                    <h6 class="panel-title">
                        <i class="bi bi-chat-left-text"></i>
                        Pengaturan Teks Berjalan
                    </h6>
                </div>
            </div>

            <form action="{{ route('admin.setting.update') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="running_text" class="form-label">Isi Teks</label>
                    <textarea
                        id="running_text"
                        name="running_text"
                        class="form-control running-text-input"
                        rows="6"
                        placeholder="Masukkan teks berjalan yang akan tampil di layar TV...">{{ $settings['running_text'] ?? '' }}</textarea>
                    <small class="text-muted">Teks ini akan bergerak di bagian bawah layar TV.</small>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i>
                        Simpan Teks
                    </button>

                    <a href="{{ route('tv') }}" target="_blank" class="btn btn-light">
                        <i class="bi bi-tv"></i>
                        Preview TV
                    </a>
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
                        <i class="bi bi-broadcast"></i>
                        Simulasi Teks Berjalan
                    </h6>
                </div>
            </div>

            <div class="running-text-stage">
                <div class="running-text-stage-label">PENGUMUMAN</div>
                <div class="running-text-stage-track">
                    <div class="running-text-stage-content js-running-text-preview">
                        {{ $settings['running_text'] ?? 'Belum ada teks berjalan yang disimpan.' }}
                    </div>
                </div>
            </div>

            <div class="info-box mt-4">
                Gunakan kalimat singkat, jelas, dan mudah dibaca karena teks akan terus bergerak di layar TV.
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('running_text');
        const preview = document.querySelector('.js-running-text-preview');

        if (!input || !preview) return;

        const syncPreview = () => {
            const fallbackText = 'Belum ada teks berjalan yang disimpan.';
            preview.textContent = input.value.trim() || fallbackText;
        };

        syncPreview();
        input.addEventListener('input', syncPreview);
    });
</script>
@endpush

@endsection
