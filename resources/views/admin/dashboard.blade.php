@extends('admin.layout')

@section('title','Dasbor')

@section('content')

<div class="dashboard-hero admin-card">
    <div>
        <div class="dashboard-eyebrow">TV Broadcast Management</div>
        <h2 class="dashboard-heading">Kelola konten layar TV dari satu panel yang lebih rapi dan cepat.</h2>
        <p class="dashboard-subtitle">Atur agenda, pegawai, video, dan running text tanpa perlu berpindah-pindah tampilan.</p>
    </div>

    <div class="dashboard-hero-actions">
        <a href="{{ route('tv') }}" target="_blank" class="btn dashboard-preview-btn">
            <i class="bi bi-play-circle"></i>
            Buka Preview TV
        </a>
    </div>
</div>

<!-- QUICK INFO -->
<div class="row g-4 mb-4">

    <div class="col-md-3">
        <div class="admin-card metric-card">
            <div class="metric-icon"><i class="bi bi-calendar-week"></i></div>
            <div class="metric-value">{{ $agenda->count() }}</div>
            <div class="metric-label">Agenda</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="admin-card metric-card">
            <div class="metric-icon"><i class="bi bi-people"></i></div>
            <div class="metric-value">{{ $employee->count() }}</div>
            <div class="metric-label">Pegawai</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="admin-card metric-card">
            <div class="metric-icon"><i class="bi bi-film"></i></div>
            <div class="metric-value">{{ $videoCount }}</div>
            <div class="metric-label">Video</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="admin-card metric-card">
            <div class="metric-icon"><i class="bi bi-chat-left-text"></i></div>
            <div class="metric-value metric-value-text">{{ !empty($setting['running_text']) ? 'Aktif' : 'Kosong' }}</div>
            <div class="metric-label">Running Text</div>
        </div>
    </div>

</div>


<div class="row g-4 align-items-start">

    <!-- PREVIEW TV -->
    <div class="col-xl-8 col-lg-7 col-md-12">
        <div class="admin-card dashboard-panel-card live-canvas-card">

            <div class="section-heading">
                <div>
                    <div class="section-eyebrow">Live Canvas</div>
                    <h6 class="card-title">
                        <i class="bi bi-tv"></i>
                        Preview TV
                    </h6>
                </div>
                <div class="dashboard-sync-pill is-idle js-dashboard-sync-pill">Preview idle</div>
            </div>

            <div class="preview-tv-frame js-preview-tv-frame">
                <div class="preview-tv-fit">
                    <div class="preview-tv-scale js-preview-tv-scale">
                        <iframe
                            src="{{ route('tv') }}"
                            title="Preview TV"
                            class="preview-tv-iframe js-preview-tv-iframe"
                            loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <!-- DISPLAY SETTINGS -->
    <div class="col-xl-4 col-lg-5 col-md-12">

        <div class="admin-card dashboard-panel-card">

            <div class="section-heading">
                <div>
                    <div class="section-eyebrow">Pengaturan Cepat</div>
                    <h6 class="card-title">
                        <i class="bi bi-gear"></i>
                        Pengaturan Latar Belakang
                    </h6>
                </div>
            </div>

            <form action="{{ route('admin.setting.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Latar Belakang TV</label>
                    <input type="file" name="bg_image" class="form-control js-background-input" accept="image/*">
                </div>

                <div class="background-preview-card mb-3">
                    <div class="background-preview-label">Preview Upload</div>
                    <div class="background-preview-frame">
                        <img
                            src=""
                            alt="Preview Upload Latar Belakang TV"
                            class="background-preview-image js-background-preview d-none">

                        <div class="background-preview-empty js-background-preview-empty">
                            Pilih file untuk melihat preview sebelum disimpan.
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-primary w-100">
                        Simpan
                    </button>

                    <button
                        type="submit"
                        name="remove_background"
                        value="1"
                        class="btn btn-danger-soft"
                        data-confirm-submit="Yakin ingin menghapus latar belakang TV ini?"
                        {{ empty($setting['background']) ? 'disabled' : '' }}>
                        <i class="bi bi-trash"></i>
                        Hapus Latar Belakang
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

<div class="admin-card dashboard-agenda-card mt-4">
    <div class="section-heading">
        <div>
            <div class="section-eyebrow">Ringkasan Jadwal</div>
            <h6 class="card-title">
                <i class="bi bi-calendar3"></i>
                Urutan Agenda Dashboard
            </h6>
        </div>
        <div class="dashboard-agenda-meta">
            Preview Dasbor dibatasi agar tetap bagus dipantau cepat. Buka menu Agenda untuk daftar lengkap.
        </div>
    </div>

    <div class="dashboard-agenda-summary">
        <span>{{ $dashboardAgendaUpcomingTotal }} agenda hari ini dan mendatang</span>
        <span>{{ $dashboardAgendaPastTotal }} agenda lampau</span>
        <a href="{{ route('admin.agenda') }}" class="dashboard-agenda-link">Lihat daftar lengkap</a>
    </div>

    <div class="dashboard-agenda-stream">
        @forelse ($dashboardAgendaUpcoming as $item)
            @php
                $agendaDate = \Carbon\Carbon::parse($item->date)->startOfDay();
                $isToday = $item->dashboard_bucket === 'today';
            @endphp

            <article class="dashboard-agenda-item {{ $isToday ? 'is-today' : 'is-upcoming' }}">
                <div class="dashboard-agenda-date">
                    <span class="dashboard-agenda-badge {{ $isToday ? 'is-today' : 'is-upcoming' }}">
                        {{ $isToday ? 'Hari ini' : 'Akan datang' }}
                    </span>
                    <strong>{{ $agendaDate->locale('id')->translatedFormat('d F Y') }}</strong>
                    <small>{{ \Carbon\Carbon::parse($item->time)->format('H:i') }} WIB</small>
                </div>
                <div class="dashboard-agenda-copy">
                    <strong>{{ $item->name }}</strong>
                    <span>{{ $item->location }}</span>
                    <small>{{ $item->disposition }}</small>
                </div>
            </article>
        @empty
            <div class="dashboard-agenda-empty">
                Belum ada agenda untuk hari ini atau tanggal yang akan datang.
            </div>
        @endforelse

        @if ($dashboardAgendaUpcoming->isNotEmpty() && $dashboardAgendaPast->isNotEmpty())
            <div class="dashboard-agenda-divider" role="separator" aria-label="Pembatas agenda lampau">
                <span>Agenda lampau</span>
            </div>
        @endif

        @forelse ($dashboardAgendaPast as $item)
            @php
                $agendaDate = \Carbon\Carbon::parse($item->date)->startOfDay();
            @endphp

            <article class="dashboard-agenda-item is-past">
                <div class="dashboard-agenda-date">
                    <span class="dashboard-agenda-badge is-past">Lampau</span>
                    <strong>{{ $agendaDate->locale('id')->translatedFormat('d F Y') }}</strong>
                    <small>{{ \Carbon\Carbon::parse($item->time)->format('H:i') }} WIB</small>
                </div>
                <div class="dashboard-agenda-copy">
                    <strong>{{ $item->name }}</strong>
                    <span>{{ $item->location }}</span>
                    <small>{{ $item->disposition }}</small>
                </div>
            </article>
        @empty
            @if ($dashboardAgendaUpcoming->isEmpty())
                <div class="dashboard-agenda-empty">
                    Belum ada data agenda yang tersimpan di sistem.
                </div>
            @endif
        @endforelse
    </div>
</div>


<!-- INFO -->
<div class="admin-card mt-4">
    <div class="info-box">
        <strong>Tips:</strong> gunakan menu di samping untuk memperbarui data pegawai, agenda, video, dan tampilan layar TV secara terpusat.
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const frame = document.querySelector('.js-preview-tv-frame');
        const scaleLayer = document.querySelector('.js-preview-tv-scale');
        const previewIframe = document.querySelector('.js-preview-tv-iframe');
        const backgroundInput = document.querySelector('.js-background-input');
        const backgroundPreview = document.querySelector('.js-background-preview');
        const backgroundPreviewEmpty = document.querySelector('.js-background-preview-empty');

        if (frame && scaleLayer) {
            const baseWidth = 1920;
            const baseHeight = 1080;

            const fitPreview = () => {
                const availableWidth = Math.max(frame.clientWidth - 12, 0);
                const availableHeight = Math.max(frame.clientHeight - 12, 0);
                const scale = Math.min(availableWidth / baseWidth, availableHeight / baseHeight);

                scaleLayer.style.width = `${baseWidth * scale}px`;
                scaleLayer.style.height = `${baseHeight * scale}px`;
                scaleLayer.style.setProperty('--preview-scale', scale);
            };

            fitPreview();
            window.addEventListener('resize', fitPreview);
        }

        if (backgroundInput && backgroundPreview && backgroundPreviewEmpty) {
            backgroundInput.addEventListener('change', (event) => {
                const [file] = event.target.files || [];

                if (!file) {
                    backgroundPreview.src = '';
                    backgroundPreview.classList.add('d-none');
                    backgroundPreviewEmpty.classList.remove('d-none');
                    return;
                }

                const objectUrl = URL.createObjectURL(file);
                backgroundPreview.src = objectUrl;
                backgroundPreview.classList.remove('d-none');
                backgroundPreviewEmpty.classList.add('d-none');
            });
        }

        const syncPill = document.querySelector('.js-dashboard-sync-pill');
        let clearConsoleTimer = null;

        const setSyncStatus = (message, state = 'idle') => {
            if (!syncPill) {
                return;
            }

            syncPill.textContent = message;
            syncPill.className = `dashboard-sync-pill is-${state} js-dashboard-sync-pill`;
        };

        const clearQuietConsole = () => {
            window.clearTimeout(clearConsoleTimer);
            clearConsoleTimer = window.setTimeout(() => {
                console.clear();
            }, 1200);
        };

        window.addEventListener('message', (event) => {
            if (event.origin !== window.location.origin || event.data?.source !== 'tv-display') {
                return;
            }

            if (event.data.type === 'tv-reverb-connected') {
                setSyncStatus('Reverb terhubung ke preview TV.', 'connected');
                clearQuietConsole();
                return;
            }

            if (event.data.type === 'tv-sync-queued') {
                setSyncStatus('Perubahan diterima dan akan tayang sekitar 1 menit lagi.', 'queued');
                return;
            }

            if (event.data.type === 'tv-sync-applied') {
                setSyncStatus('Preview TV sudah sinkron dengan data terbaru.', 'synced');
                clearQuietConsole();
                return;
            }

            if (event.data.type === 'tv-reverb-error' || event.data.type === 'tv-sync-error') {
                setSyncStatus('Ada kendala sinkronisasi preview TV.', 'error');
                console.error('Dashboard preview: TV sync issue', event.data);
            }
        });

        previewIframe?.addEventListener('load', () => {
            setSyncStatus('Preview aktif dan menunggu pembaruan.', 'idle');
            clearQuietConsole();
        });
    });
</script>
@endpush
