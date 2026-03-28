<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV Informasi</title>

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/tv.css') }}" rel="stylesheet">
</head>

<body>
    <div class="tv-container wallpaper"
        data-tv-revision="{{ $tvRevision }}"
        data-tv-state-url="{{ route('tv.state') }}"
        data-tv-payload-url="{{ route('tv.payload') }}"
        @if(!empty($settings['background']))
        style="background-image:url('{{ asset('storage/'.$settings['background']) . '?v=' . urlencode($tvRevision) }}'); background-size: cover; background-position: center; background-repeat: no-repeat;"
        @endif>

        <!-- ROW 1 -->
        <div class="row-datetime">
            <div class="datetime-box">
                <span id="date"></span>
                <span class="divider">|</span>
                <span id="clock"></span>
            </div>
        </div>

        <!-- ROW 2 -->
        <div class="row-top">

            <!-- PROFILE -->
            <div id="tv-employee-section">
                @include('sections.employee')
            </div>

            <!-- LOGO -->
            <div class="logo-box">
                <img src="{{ asset('assets/images/logo-dat.png') }}" alt="Logo">
            </div>

            <!-- VIDEO -->
            <div id="tv-video-section">
                @include('sections.video')
            </div>

        </div>

        <!-- ROW 3 -->
        <div class="row-agenda">
            <div id="tv-agenda-tu-section">
                @include('sections.agendatu')
            </div>
            <div id="tv-agenda-data-section">
                @include('sections.agendadata')
            </div>
        </div>

        <!-- ROW 4 -->
        <div id="tv-runningtext-section">
            @include('sections.runningtext')
        </div>

    </div>

    <!-- JS CLOCK -->
    <script src="{{ asset('js/tv/dateclock.js') }}"></script>

    <!-- JS SLIDER -->
    <script src="{{ asset('js/tv/employee_slider.js') }}"></script>
    <script src="{{ asset('js/tv/agenda_slider.js') }}"></script>
    <script src="{{ asset('js/tv/live_sync.js') }}"></script>

</body>

</html>
