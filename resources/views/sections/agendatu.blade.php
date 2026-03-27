<div class="agenda-box">

    <div class="agenda-title">

        <i class="fa-solid fa-user"></i>

        Agenda Tata Usaha

    </div>


    @forelse($agendaTu as $agenda)
    <div class="agenda-item">
        <div class="agenda-date">
            {{ \Carbon\Carbon::parse($agenda->date)->format('d') }}<br>{{ strtoupper(\Carbon\Carbon::parse($agenda->date)->translatedFormat('M')) }}
        </div>

        <div class="agenda-content">
            <div class="agenda-name">
                {{ $agenda->name }}
            </div>

            <div class="agenda-info">
                {{ $agenda->location }}
            </div>

            <div class="agenda-info">
                Disposisi : {{ $agenda->disposition }}
            </div>
        </div>

        <div class="agenda-tag">
            {{ $agenda->time }}
        </div>
    </div>
    @empty
    <div class="agenda-empty">
        Belum ada agenda yang ditampilkan.
    </div>
    @endforelse


</div>
