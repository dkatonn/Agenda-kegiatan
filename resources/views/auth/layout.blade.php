<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - TV Agenda</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="auth-page">
    <div class="auth-shell">
        <div class="auth-showcase">
            <div class="auth-showcase-badge">
                <i class="bi bi-broadcast-pin"></i>
                <span>Pusat Kendali TV Agenda</span>
            </div>
            <h1>Kelola tampilan TV internal dari satu panel yang rapi.</h1>
            <p>Masuk untuk mengatur pegawai, agenda, video, teks berjalan, dan pengaturan admin dengan tampilan yang selaras dengan dashboard utama.</p>
            <div class="auth-showcase-points">
                <div class="auth-showcase-point">
                    <strong>Data Langsung</strong>
                    <span>Perubahan dari panel admin langsung tercermin ke layar TV.</span>
                </div>
                <div class="auth-showcase-point">
                    <strong>Akses Terpusat</strong>
                    <span>Semua modul penting ada dalam satu alur kerja yang sama.</span>
                </div>
                <div class="auth-showcase-point">
                    <strong>Desain Konsisten</strong>
                    <span>Nuansa visual disamakan dengan panel admin terbaru.</span>
                </div>
            </div>
        </div>

        <div class="auth-card">
            <div class="auth-brand">
                <div class="auth-kicker">Panel Admin</div>
                <h2>@yield('title')</h2>
                <p>Gunakan akun admin yang aktif untuk melanjutkan.</p>
            </div>

            @if(session('status'))
            <div class="alert alert-success auth-alert">
                {{ session('status') }}
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger auth-alert">
                {{ $errors->first() }}
            </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
