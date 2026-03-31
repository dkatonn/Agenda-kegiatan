<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>TV Informasi</title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        rel="stylesheet">


    <link href="{{ asset('css/tv.css') }}" rel="stylesheet">

</head>


<body>

    <div
        class="tv-container wallpaper"
        @if(!empty($settings['background']))
        style="background-image:url('{{ asset('storage/'.$settings['background']) }}');"
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

            @include('sections.employee')



            <!-- LOGO -->

            <div class="logo-box">

                <img
                    src="{{ asset('assets/images/logo-dat.png') }}"
                    alt="Logo">

            </div>



            <!-- VIDEO -->

            @include('sections.video')



        </div>



        <!-- ROW 3 -->

        <div class="row-agenda">


            @include('sections.agendatu')


            @include('sections.agendadata')


        </div>



        <!-- ROW 4 -->

        @include('sections.runningtext')



    </div>



    <script>
        function updateClock() {

            const now = new Date();

            const hours = String(now.getHours()).padStart(2, "0");
            const minutes = String(now.getMinutes()).padStart(2, "0");
            const seconds = String(now.getSeconds()).padStart(2, "0");

            document.getElementById("clock").innerText =
                `${hours}:${minutes}:${seconds}`;


            document.getElementById("date").innerText =
                now.toLocaleDateString("id-ID", {

                    weekday: "long",

                    day: "numeric",

                    month: "long",

                    year: "numeric"

                });


        }


        setInterval(updateClock, 1000);

        updateClock();
    </script>


</body>

</html>
