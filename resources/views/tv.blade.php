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
        <div class="avatar">SP</div>
        <h2>Shaun Patrick Hendra</h2>
        <p>Magang Biro SDM</p>
    </article>

    <article class="video">
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
                            <tr><td>10-03-26 10:15 WIB  - Ruang Rapat</td><td>Koordinasi Internal</td><td>Kasubag TU</td></tr>
                            <tr><td>10-03-26 10:15 WIB  - Aula</td><td>Briefing Pegawai</td><td>Staff TU</td></tr>
                            <tr><td>10-03-26 10:15 WIB  - Lt.2</td><td>Validasi Dokumen</td><td>Admin TU</td></tr>
                            <tr><td>11-03-26 10:15 WIB  - Ruang Arsip</td><td>Pemeriksaan Berkas</td><td>Arsiparis</td></tr>
                            <tr><td>11-03-26 10:15 WIB  - Zoom</td><td>Sinkronisasi Data</td><td>Operator</td></tr>
                            <tr><td>12-03-26 10:15 WIB  - Aula</td><td>Evaluasi Mingguan</td><td>Kasubag TU</td></tr>
                            <tr><td>12/03 - Ruang Rapat</td><td>Rencana Program</td><td>Tim TU</td></tr>
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
                            <tr><td>10-03-26 10:15 WIB - Command Center</td><td>Monitoring Dashboard</td><td>Analis Data</td></tr>
                            <tr><td>10-03-26 10:15 WIB  - Ruang Server</td><td>Update Integrasi API</td><td>Programmer</td></tr>
                            <tr><td>10-03-26 10:15 WIB - Lab Data</td><td>Uji Validitas Dataset</td><td>Data Engineer</td></tr>
                            <tr><td>11-03-26 10:15 WIB - Zoom</td><td>Rapat Lintas Unit</td><td>Koordinator</td></tr>
                            <tr><td>11-03-26 10:15 WIB - Lt.3</td><td>Pemetaan KPI</td><td>Analis Sistem</td></tr>
                            <tr><td>12-03-26 10:15 WIB - Ruang Rapat</td><td>Review SLA Layanan</td><td>Supervisor</td></tr>
                            <tr><td>12-03-26 10:15 WIB - Command Center</td><td>Pelaporan Mingguan</td><td>Tim Data</td></tr>
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
                    Peringatan Hari Lahir Pancasila akan dilaksanakan di Lapangan Monas pukul 08:00 WIB.
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
