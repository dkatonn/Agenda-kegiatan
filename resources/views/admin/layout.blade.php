<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Admin') - TV Agenda</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    {{-- tambahan kalau nanti butuh css per halaman --}}
    @stack('styles')
</head>

<body>

    <div class="admin-wrapper">

        <!-- SIDEBAR -->
        <aside class="sidebar">

            <div class="logo">
                <i class="bi bi-display"></i>
                <span>TV Agenda</span>
            </div>

            <nav class="menu">

                <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.index') ? 'active' : '' }}">
                    <i class="bi bi-house"></i>
                    Dashboard
                </a>

                <a href="{{ route('admin.employee') }}" class="{{ request()->routeIs('admin.employee*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    Pegawai
                </a>

                <a href="{{ route('admin.video') }}" class="{{ request()->routeIs('admin.video*') ? 'active' : '' }}">
                    <i class="bi bi-film"></i>
                    Video
                </a>

                <a href="{{ route('admin.agenda') }}" class="{{ request()->routeIs('admin.agenda*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event"></i>
                    Agenda
                </a>

                <a href="{{ route('admin.admins') }}" class="{{ request()->routeIs('admin.admins*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock"></i>
                    Admin
                </a>

            </nav>

        </aside>


        <!-- MAIN -->
        <div class="main">

            <!-- TOPBAR -->
            <header class="topbar d-flex justify-content-between align-items-center">
                <h5 class="mb-0">@yield('title')</h5>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('tv') }}" target="_blank" class="btn btn-primary btn-sm">
                        <i class="bi bi-tv"></i>
                        Preview TV
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-box-arrow-right"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </header>


            <!-- CONTENT -->
            <div class="content container-fluid">

                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif

                @if($errors->any() && !View::hasSection('suppressGlobalErrors'))
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
                @endif

                @yield('content')

            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- tambahan kalau nanti butuh js per halaman --}}
    @stack('scripts')

</body>

</html>
