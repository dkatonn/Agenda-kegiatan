@extends('admin.layout')

@section('title','Agenda')

@section('content')

<div class="admin-card data-panel">

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="panel-header">
        <div>
            <div class="section-eyebrow">Manajemen Jadwal</div>
            <h6 class="panel-title">
                <i class="bi bi-calendar-event"></i>
                Agenda Data
            </h6>
        </div>

        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createAgendaModal">
            Tambah Agenda
        </button>
    </div>

    <div class="panel-toolbar table-toolbar">
        <div class="panel-meta">Total {{ $agenda->count() }} agenda aktif di sistem.</div>
        <div class="table-controls">
            <label class="table-control-inline">
                <span>Show</span>
                <select class="form-select form-select-sm table-page-size">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                </select>
                <span>entries</span>
            </label>

            <label class="table-control-search">
                <span>Search:</span>
                <input type="text" class="form-control form-control-sm table-search-input" placeholder="Cari agenda...">
            </label>
        </div>
    </div>

    <div class="table-shell">
        <table class="table admin-data-table js-admin-table table-centered-content">

            <thead>
                <tr>
                    <th>Tanggal dan Waktu</th>
                    <th>Kegiatan</th>
                    <th>Lokasi</th>
                    <th>Disposisi</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                @foreach($agenda as $item)
                @php
                    $agendaDate = \Carbon\Carbon::parse($item->date)->startOfDay();
                    $isToday = $agendaDate->equalTo(now()->startOfDay());
                    $isTomorrow = $agendaDate->equalTo(now()->copy()->addDay()->startOfDay());
                @endphp

                <tr class="{{ $isToday ? 'agenda-row-today' : ($isTomorrow ? 'agenda-row-tomorrow' : '') }}">

                    <td>
                        <span class="agenda-date-badge {{ $isToday ? 'is-today' : ($isTomorrow ? 'is-tomorrow' : '') }}">
                            {{ \Carbon\Carbon::parse($item->date)->locale('id')->translatedFormat('d F Y') }}
                            | {{ \Carbon\Carbon::parse($item->time)->format('H:i') }} WIB
                        </span>
                    </td>

                    <td>{{ $item->name }}</td>

                    <td>{{ $item->location }}</td>

                    <td>{{ $item->disposition }}</td>

                    <td>
                        <div class="action-group">
                            <button
                                class="btn btn-sm btn-outline-primary admin-icon-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#editAgendaModal{{ $item->id }}"
                                title="Edit agenda"
                                aria-label="Edit agenda"
                            >
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            <form action="{{ route('admin.agenda.delete',$item->id) }}" method="POST" class="d-inline js-confirm-delete" data-confirm-message="Yakin ingin menghapus agenda ini?">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-outline-danger admin-icon-btn" title="Hapus agenda" aria-label="Hapus agenda">
                                    <i class="bi bi-trash3"></i>
                                </button>

                            </form>
                        </div>
                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

        <div class="table-footer">
            <div class="table-info"></div>
            <div class="table-pagination">
                <button type="button" class="btn btn-light btn-sm table-prev">Prev</button>
                <span class="table-page-indicator">1</span>
                <button type="button" class="btn btn-light btn-sm table-next">Next</button>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="createAgendaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content admin-form-modal">
            <form action="{{ route('admin.agenda.store') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <div>
                        <div class="modal-eyebrow">Tambah Jadwal</div>
                        <h5 class="modal-title">Tambah Agenda</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-section-note">
                        Isi agenda dengan ringkas dan jelas agar mudah dibaca pada tampilan TV.
                    </div>

                    <div class="admin-form-grid admin-form-grid-2">
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="date" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Waktu</label>
                            <input type="time" name="time" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kegiatan</label>
                        <input type="text" name="name" class="form-control agenda-char-limit" data-char-limit="45" maxlength="45" required>
                        <small class="text-muted">Maksimal 45 karakter. <span class="agenda-char-counter">0/45</span></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="location" class="form-control agenda-char-limit" data-char-limit="30" maxlength="30" required>
                        <small class="text-muted">Maksimal 30 karakter. <span class="agenda-char-counter">0/30</span></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Disposisi</label>
                        <input type="text" name="disposition" class="form-control agenda-char-limit" data-char-limit="30" maxlength="30" required>
                        <small class="text-muted">Maksimal 30 karakter. <span class="agenda-char-counter">0/30</span></small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($agenda as $item)
<div class="modal fade" id="editAgendaModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content admin-form-modal">
            <form action="{{ route('admin.agenda.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <div>
                        <div class="modal-eyebrow">Update Jadwal</div>
                        <h5 class="modal-title">Edit Agenda</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-section-note">
                        Pastikan informasi agenda tetap singkat, akurat, dan sesuai urutan tayang.
                    </div>

                    <div class="admin-form-grid admin-form-grid-2">
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="date" class="form-control" value="{{ $item->date }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Waktu</label>
                            <input type="time" name="time" class="form-control" value="{{ \Illuminate\Support\Str::limit($item->time, 5, '') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kegiatan</label>
                        <input type="text" name="name" class="form-control agenda-char-limit" data-char-limit="45" maxlength="45" value="{{ $item->name }}" required>
                        <small class="text-muted">Maksimal 45 karakter. <span class="agenda-char-counter">0/45</span></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="location" class="form-control agenda-char-limit" data-char-limit="30" maxlength="30" value="{{ $item->location }}" required>
                        <small class="text-muted">Maksimal 30 karakter. <span class="agenda-char-counter">0/30</span></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Disposisi</label>
                        <input type="text" name="disposition" class="form-control agenda-char-limit" data-char-limit="30" maxlength="30" value="{{ $item->disposition }}" required>
                        <small class="text-muted">Maksimal 30 karakter. <span class="agenda-char-counter">0/30</span></small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const limitInputCharacters = (input) => {
            const limit = Number(input.dataset.charLimit || 50);
            const counter = input.parentElement.querySelector('.agenda-char-counter');

            if (input.value.length > limit) {
                input.value = input.value.slice(0, limit);
            }

            const currentCount = input.value.length;
            if (counter) {
                counter.textContent = `${currentCount}/${limit}`;
            }
        };

        document.querySelectorAll('.agenda-char-limit').forEach((input) => {
            limitInputCharacters(input);
            input.addEventListener('input', () => limitInputCharacters(input));
            input.addEventListener('paste', () => {
                setTimeout(() => limitInputCharacters(input), 0);
            });
        });
    });
</script>
@endpush

@endsection
