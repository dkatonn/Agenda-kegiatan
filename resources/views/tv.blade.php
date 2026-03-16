<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agenda Kegiatan - TV</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/tv.css') }}">
</head>

<body>
    <div class="tv-screen">
        <div class="tv-bg" id="tv-bg"></div>

        <div class="main-layout">
<header class="header">
    <div class="brand-text">
        <h1><span>Biro</span> Kepegawaian Data & Informasi</h1>
        <p><span>Agenda</span> Kegiatan Harian</p>

    </div>

    <div class="clock-panel">
        <h2 class="clock" id="clock">00:00:00</h2>
        <p class="date" id="date">Selasa, 10 Maret 2026</p>
    </div>
</header>

<section class="top-row">

    <div class="logo-box">
        <img 
            src="{{ asset('assets/images/logo-dat.png') }}" 
            alt="Logo Data dan Informasi">
    </div>

    <article class="card">
        @php
            $initials = collect(explode(' ', $profile->name))
                ->filter()
                ->take(2)
                ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                ->implode('');
        @endphp

        @if (!empty($profile->photo_path))
            <img class="avatar-image" src="{{ asset($profile->photo_path) }}" alt="{{ $profile->name }}">
        @else
            <div class="avatar">{{ $initials }}</div>
        @endif
        <h2>{{ $profile->name }}</h2>
        <p>{{ $profile->position }}</p>
    </article>

    @php
        $sumberVideo = '';
        if (!empty($video->source_path)) {
            $sumberVideo = \Illuminate\Support\Str::startsWith($video->source_path, ['http://', 'https://'])
                ? $video->source_path
                : asset($video->source_path);
        }
    @endphp

    <article class="video">
        @if ($sumberVideo)
            <video class="video-player" autoplay muted loop playsinline>
                <source src="{{ $sumberVideo }}">
                Browser ini tidak mendukung pemutar video.
            </video>
        @endif
        <button class="play" aria-label="Putar video"></button>
    </article>

</section>

            <section class="tables">
                <article class="agenda">
                    <div class="agenda-title">Agenda Kegiatan Tata Usaha</div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                            <tr>
                                <th style="width: 38%;">Tanggal Tempat</th>
                                <th style="width: 39%;">Nama Kegiatan</th>
                                <th style="width: 23%;">Disposisi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($tuAgendas as $agenda)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($agenda->agenda_date)->format('d-m-y') }} {{ substr((string) $agenda->agenda_time, 0, 5) }} WIB - {{ $agenda->location }}</td>
                                    <td>{{ $agenda->title }}</td>
                                    <td>{{ $agenda->disposition }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="empty-state">Belum ada agenda TU.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>

                <article class="agenda">
                    <div class="agenda-title">Agenda Kegiatan Data & Informasi</div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                            <tr>
                                <th style="width: 38%;">Tanggal Tempat</th>
                                <th style="width: 39%;">Nama Kegiatan</th>
                                <th style="width: 23%;">Disposisi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($dataAgendas as $agenda)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($agenda->agenda_date)->format('d-m-y') }} {{ substr((string) $agenda->agenda_time, 0, 5) }} WIB - {{ $agenda->location }}</td>
                                    <td>{{ $agenda->title }}</td>
                                    <td>{{ $agenda->disposition }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="empty-state">Belum ada agenda Data.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            </section>
        </div>

        <footer class="ticker">
            <div class="ticker-label">PENGUMUMAN</div>
            <div class="ticker-track">
                <div class="ticker-content">
                    {{ $runningText }}
                </div>
            </div>
        </footer>
    </div>

    <script>
        const monthName = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];

        const dayName = [
            "Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"
        ];

        function updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, "0");
            const m = String(now.getMinutes()).padStart(2, "0");
            const s = String(now.getSeconds()).padStart(2, "0");
            const d = now.getDate();
            const mm = monthName[now.getMonth()];
            const y = now.getFullYear();
            const day = dayName[now.getDay()];

            document.getElementById("clock").textContent = `${h}:${m}:${s}`;
            document.getElementById("date").textContent = `${day}, ${d} ${mm} ${y}`;
        }

        function useFirstExistingImage(candidates, onFound) {
            let index = 0;

            const next = () => {
                if (index >= candidates.length) {
                    return;
                }

                const url = candidates[index++];
                const img = new Image();
                img.onload = () => onFound(url);
                img.onerror = next;
                img.src = url;
            };

            next();
        }

        updateClock();
        setInterval(updateClock, 1000);

    </script>
</body>
</html>
