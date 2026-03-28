<div class="agenda-box">

    <div class="agenda-title">
        <i class="fa-solid fa-user"></i>
        Agenda Tata Usaha
    </div>

    <div class="agenda-head">
        <div class="agenda-head-date">Hari / Tanggal</div>
        <div class="agenda-head-content">
            <div class="agenda-head-cell">Kegiatan</div>
            <div class="agenda-head-cell">Lokasi</div>
            <div class="agenda-head-cell">Disposisi</div>
        </div>
        <div class="agenda-head-time">Jam</div>
    </div>

    @foreach($agendaTuPinned as $agenda)
    @php
        $agendaDate = \Carbon\Carbon::parse($agenda->date)->startOfDay();
        $today = now()->startOfDay();
        $tomorrow = now()->copy()->addDay()->startOfDay();
        $dateClass = $agendaDate->equalTo($today)
            ? 'is-today'
            : ($agendaDate->equalTo($tomorrow) ? 'is-tomorrow' : 'is-other');
    @endphp
    <div class="agenda-item agenda-item-pinned">
        <div class="agenda-date {{ $dateClass }}">
            <span class="agenda-date-day">{{ \Carbon\Carbon::parse($agenda->date)->locale('id')->translatedFormat('l') }}</span>
            <span class="agenda-date-full">{{ \Carbon\Carbon::parse($agenda->date)->locale('id')->translatedFormat('d F Y') }}</span>
        </div>

        <div class="agenda-content">
            <div class="agenda-cell">
                <div class="agenda-value agenda-name" title="{{ $agenda->name }}">
                    <span class="agenda-text">{{ $agenda->name }}</span>
                </div>
            </div>

            <div class="agenda-cell">
                <div class="agenda-value agenda-info" title="{{ $agenda->location }}">
                    <span class="agenda-text">{{ $agenda->location }}</span>
                </div>
            </div>

            <div class="agenda-cell">
                <div class="agenda-value agenda-info" title="{{ $agenda->disposition }}">
                    <span class="agenda-text">{{ $agenda->disposition }}</span>
                </div>
            </div>
        </div>

        <div class="agenda-tag">
            {{ $agenda->time }}
        </div>
    </div>
    @endforeach

    @if($agendaTuSlides->isNotEmpty())
    <div class="agenda-slider" style="height: calc((var(--agenda-row-height) * {{ max(1, $agendaTuRemainingSlots) }}) + 2px);">

        @foreach($agendaTuSlides as $index => $chunk)
        <div class="agenda-slide {{ $loop->first ? 'active' : '' }}" style="grid-template-rows: repeat({{ max(1, $agendaTuRemainingSlots) }}, var(--agenda-row-height));">

            @foreach($chunk as $agenda)
            @php
                $agendaDate = \Carbon\Carbon::parse($agenda->date)->startOfDay();
                $today = now()->startOfDay();
                $tomorrow = now()->copy()->addDay()->startOfDay();
                $dateClass = $agendaDate->equalTo($today)
                    ? 'is-today'
                    : ($agendaDate->equalTo($tomorrow) ? 'is-tomorrow' : 'is-other');
            @endphp
            <div class="agenda-item">
                <div class="agenda-date {{ $dateClass }}">
                    <span class="agenda-date-day">{{ \Carbon\Carbon::parse($agenda->date)->locale('id')->translatedFormat('l') }}</span>
                    <span class="agenda-date-full">{{ \Carbon\Carbon::parse($agenda->date)->locale('id')->translatedFormat('d F Y') }}</span>
                </div>

                <div class="agenda-content">
                    <div class="agenda-cell">
                        <div class="agenda-value agenda-name" title="{{ $agenda->name }}">
                            <span class="agenda-text">{{ $agenda->name }}</span>
                        </div>
                    </div>

                    <div class="agenda-cell">
                        <div class="agenda-value agenda-info" title="{{ $agenda->location }}">
                            <span class="agenda-text">{{ $agenda->location }}</span>
                        </div>
                    </div>

                    <div class="agenda-cell">
                        <div class="agenda-value agenda-info" title="{{ $agenda->disposition }}">
                            <span class="agenda-text">{{ $agenda->disposition }}</span>
                        </div>
                    </div>
                </div>

                <div class="agenda-tag">
                    {{ $agenda->time }}
                </div>
            </div>
            @endforeach

        </div>
        @endforeach

    </div>
    @endif

</div>
