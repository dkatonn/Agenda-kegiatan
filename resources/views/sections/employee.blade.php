<div class="profile-box">
    @forelse($employees as $employee)
    <div class="profile-slide">
        <div class="profile-avatar @if($employee->image_path) profile-avatar-image @endif"
            @if($employee->image_path)
            style="background-image:url('{{ asset('storage/'.$employee->image_path) }}');"
            @endif>
            @if(!$employee->image_path)
            <i class="fa-solid fa-user"></i>
            @endif
        </div>

        <div class="profile-name">
            {{ $employee->name }}
        </div>

        <div class="profile-role">
            {{ $employee->role }}
        </div>
    </div>
    @empty
    <div class="profile-slide">
        <div class="profile-avatar">
            <i class="fa-solid fa-user"></i>
        </div>

        <div class="profile-name">
            Data pegawai belum tersedia
        </div>

        <div class="profile-role">
            Silakan tambahkan dari panel admin
        </div>
    </div>
    @endforelse

    <div class="profile-indicator">
        @foreach($employees as $employee)
        <span class="dot {{ $loop->first ? 'active' : '' }}"></span>
        @endforeach

        @if($employees->isEmpty())
        <span class="dot active"></span>
        @endif
    </div>

</div>


<script>
    const slides = document.querySelectorAll(".profile-slide");
    const dots = document.querySelectorAll(".dot");
    let index = 0;

    function rotateEmployee() {
        if (!slides.length) {
            return;
        }

        slides.forEach(slide => {
            slide.style.display = "none";
        });

        dots.forEach(dot => {
            dot.classList.remove("active");
        });

        slides[index].style.display = "flex";

        if (dots[index]) {
            dots[index].classList.add("active");
        }

        index = (index + 1) % slides.length;
    }

    setInterval(rotateEmployee, 8000);
    rotateEmployee();
</script>
