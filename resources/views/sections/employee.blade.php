<div class="profile-box">

    <div class="profile-slider">
        <div class="profile-track">

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

                <div class="profile-nip">
                    NIP: {{ $employee->nip ?? '-' }}
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

                <div class="profile-nip">
                    NIP akan tampil di sini
                </div>

                <div class="profile-role">
                    Silakan tambahkan dari panel admin
                </div>
            </div>
            @endforelse

        </div>
    </div>

    <div class="profile-indicator">
        @foreach($employees as $employee)
        <span class="dot {{ $loop->first ? 'active' : '' }}"></span>
        @endforeach

        @if($employees->isEmpty())
        <span class="dot active"></span>
        @endif
    </div>

</div>
