<div class="profile-box">

    <div class="profile-slide">

        <div class="profile-avatar">
            <i class="fa-solid fa-user"></i>
        </div>

        <div class="profile-name">
            Nama ASN 1
        </div>

        <div class="profile-role">
            Kepala Biro SDM
        </div>

    </div>


    <div class="profile-slide">

        <div class="profile-avatar">
            <i class="fa-solid fa-user"></i>
        </div>

        <div class="profile-name">
            Nama ASN 2
        </div>

        <div class="profile-role">
            Analis Kepegawaian
        </div>

    </div>


    <div class="profile-slide">

        <div class="profile-avatar">
            <i class="fa-solid fa-user"></i>
        </div>

        <div class="profile-name">
            Nama ASN 3
        </div>

        <div class="profile-role">
            Staff Administrasi
        </div>

    </div>


    <div class="profile-indicator">

        <span class="dot active"></span>
        <span class="dot"></span>
        <span class="dot"></span>

    </div>

</div>


<script>
    let slides =
        document.querySelectorAll(".profile-slide");

    let dots =
        document.querySelectorAll(".dot");

    let index = 0;


    function rotateEmployee() {

        slides.forEach(slide =>
            slide.style.display = "none"
        );

        dots.forEach(dot =>
            dot.classList.remove("active")
        );

        slides[index].style.display = "flex";

        dots[index].classList.add("active");

        index++;

        if (index >= slides.length) {

            index = 0;

        }

    }


    setInterval(rotateEmployee, 8000);

    rotateEmployee();
</script>