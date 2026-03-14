<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard TV Biro SDM</title>

    <!-- BOOTSTRAP + ICON -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/tv.css') }}">

</head>

<body>

    <div class="tv-container wallpaper">

        <!-- ROW 1 : DATE TIME -->
        <div class=" row-tv row-datetime">
            <div class="datetime-box">
                <i class="fa-solid fa-calendar-days"></i>
                26 FEB 2026
                <span class="divider">|</span>
                12:30
                <i class="fa-solid fa-play"></i>
            </div>
        </div>

        <!-- ROW 2 : LOGO PROFILE VIDEO -->
        <div class="row-tv row-top">

            <div class="profile-box">

                <div class="profile-slide">

                    <div class="profile-avatar">
                        <i class="fa-solid fa-user"></i>
                    </div>

                    <div class="profile-name">
                        Shaun Patrick Hendra
                    </div>

                    <div class="profile-role">
                        Anak Magang Biro SDM
                    </div>

                </div>

                <div class="profile-indicator">
                    <span class="dot active"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </div>

            </div>

            <!-- LOGO -->
            <div class="logo-box">
                <img src="/assets/images/logo-dat.png">
            </div>

            <!-- VIDEO -->
            <div class="video-box">

                <div class="video-player">

                    <div class="video-screen"></div>

                </div>

            </div>

        </div>

        <!-- ROW 3 : AGENDA -->
        <div class="row-tv row-agenda">

            <!-- AGENDA LEFT -->
            <div class="agenda-box">

                <div class="agenda-title">
                    <i class="fa-solid fa-user"></i>
                    Agenda Tata Usaha
                </div>

                <div class="agenda-item">

                    <div class="agenda-date">
                        26<br>FEB
                    </div>

                    <div class="agenda-content">
                        <div class="agenda-name">Rapat Koordinasi</div>
                        <div class="agenda-info">Ruang Rapat</div>
                        <div class="agenda-info">Disposisi : Kepala Biro</div>
                    </div>

                    <div class="agenda-tag">PENIN</div>

                </div>

                <div class="agenda-item">

                    <div class="agenda-date">
                        26<br>FEB
                    </div>

                    <div class="agenda-content">
                        <div class="agenda-name">Rapat Koordinasi</div>
                        <div class="agenda-info">Ruang Rapat</div>
                        <div class="agenda-info">Disposisi : Kepala Biro</div>
                    </div>

                    <div class="agenda-tag">PENIN</div>

                </div>

                <div class="agenda-item">

                    <div class="agenda-date">
                        26<br>FEB
                    </div>

                    <div class="agenda-content">
                        <div class="agenda-name">Rapat Koordinasi</div>
                        <div class="agenda-info">Ruang Rapat</div>
                        <div class="agenda-info">Disposisi : Kepala Biro</div>
                    </div>

                    <div class="agenda-tag">PENIN</div>

                </div>

                <div class="agenda-item">

                    <div class="agenda-date">
                        26<br>FEB
                    </div>

                    <div class="agenda-content">
                        <div class="agenda-name">Rapat Koordinasi</div>
                        <div class="agenda-info">Ruang Rapat</div>
                        <div class="agenda-info">Disposisi : Kepala Biro</div>
                    </div>

                    <div class="agenda-tag">PENIN</div>

                </div>

                <div class="agenda-item">

                    <div class="agenda-date">
                        26<br>FEB
                    </div>

                    <div class="agenda-content">
                        <div class="agenda-name">Rapat Koordinasi</div>
                        <div class="agenda-info">Ruang Rapat</div>
                        <div class="agenda-info">Disposisi : Kepala Biro</div>
                    </div>

                    <div class="agenda-tag">PENIN</div>

                </div>

                <div class="agenda-item">

                    <div class="agenda-date">
                        26<br>FEB
                    </div>

                    <div class="agenda-content">
                        <div class="agenda-name">Rapat Koordinasi</div>
                        <div class="agenda-info">Ruang Rapat</div>
                        <div class="agenda-info">Disposisi : Kepala Biro</div>
                    </div>

                    <div class="agenda-tag">PENIN</div>

                </div>

            </div>


            <!-- AGENDA RIGHT -->
            <div class="agenda-box">

                <div class="agenda-title">
                    <i class="fa-solid fa-user"></i>
                    Agenda Data & Informasi
                </div>

                <div class="agenda-item">

                    <div class="agenda-date blue">
                        01<br>MAR
                    </div>

                    <div class="agenda-content">
                        <div class="agenda-name">Briefing Agenda Baru</div>
                        <div class="agenda-info">Ruang Meeting</div>
                        <div class="agenda-info">Disposisi : Staff TU</div>
                    </div>

                    <div class="agenda-tag yellow">SETUJU</div>

                </div>

                <div class="agenda-item">

                    <div class="agenda-date blue">
                        01<br>MAR
                    </div>

                    <div class="agenda-content">
                        <div class="agenda-name">Briefing Agenda Baru</div>
                        <div class="agenda-info">Ruang Meeting</div>
                        <div class="agenda-info">Disposisi : Staff TU</div>
                    </div>

                    <div class="agenda-tag yellow">SETUJU</div>

                </div>

                <div class="agenda-item">

                    <div class="agenda-date blue">
                        01<br>MAR
                    </div>

                    <div class="agenda-content">
                        <div class="agenda-name">Briefing Agenda Baru</div>
                        <div class="agenda-info">Ruang Meeting</div>
                        <div class="agenda-info">Disposisi : Staff TU</div>
                    </div>

                    <div class="agenda-tag yellow">SETUJU</div>

                </div>

                <div class="agenda-item">

                    <div class="agenda-date blue">
                        01<br>MAR
                    </div>

                    <div class="agenda-content">
                        <div class="agenda-name">Briefing Agenda Baru</div>
                        <div class="agenda-info">Ruang Meeting</div>
                        <div class="agenda-info">Disposisi : Staff TU</div>
                    </div>

                    <div class="agenda-tag yellow">SETUJU</div>

                </div>

                <div class="agenda-item">

                    <div class="agenda-date blue">
                        01<br>MAR
                    </div>

                    <div class="agenda-content">
                        <div class="agenda-name">Briefing Agenda Baru</div>
                        <div class="agenda-info">Ruang Meeting</div>
                        <div class="agenda-info">Disposisi : Staff TU</div>
                    </div>

                    <div class="agenda-tag yellow">SETUJU</div>

                </div>

                <div class="agenda-item">

                    <div class="agenda-date blue">
                        01<br>MAR
                    </div>

                    <div class="agenda-content">
                        <div class="agenda-name">Briefing Agenda Baru</div>
                        <div class="agenda-info">Ruang Meeting</div>
                        <div class="agenda-info">Disposisi : Staff TU</div>
                    </div>

                    <div class="agenda-tag yellow">SETUJU</div>

                </div>


            </div>

        </div>

        <!-- ROW 4 : RUNNING TEXT -->
        <footer class="ticker">
            <div class="ticker-label">PENGUMUMAN</div>
            <div class="ticker-track">
                <div class="ticker-content">
                    Peringatan Hari Lahir Pancasila akan dilaksanakan di Lapangan Monas pukul 08:00 WIB.
                </div>
            </div>
        </footer>

    </div>

</body>

</html>