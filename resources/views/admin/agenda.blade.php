@extends('admin.layout')

@section('title','Agenda')

@section('content')

<div class="admin-card data-panel" data-server-table="true">

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
        <div class="panel-meta">Total {{ $agenda->total() }} agenda aktif di sistem.</div>
        <form method="GET" action="{{ route('admin.agenda') }}" class="table-controls" data-server-table-form>
            <label class="table-control-inline">
                <span>Tampilkan</span>
                <select name="per_page" class="form-select form-select-sm table-page-size">
                    <option value="5" {{ $perPage === 5 ? 'selected' : '' }}>5</option>
                    <option value="10" {{ $perPage === 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $perPage === 25 ? 'selected' : '' }}>25</option>
                </select>
                <span>data</span>
            </label>

            <label class="table-control-search">
                <span>Cari:</span>
                <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm table-search-input" placeholder="Cari agenda...">
            </label>
        </form>
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

                @forelse($currentItems as $item)
                @php
                    $agendaDate = \Carbon\Carbon::parse($item->date)->startOfDay();
                    $isToday = $agendaDate->equalTo(now()->startOfDay());
                    $isTomorrow = $agendaDate->equalTo(now()->copy()->addDay()->startOfDay());
                    $previousBucket = $loop->index > 0 ? (int) $currentItems[$loop->index - 1]->period_group : null;
                    $currentBucket = (int) $item->period_group;
                @endphp

                @if($loop->index > 0 && $previousBucket !== $currentBucket)
                <tr class="agenda-divider-row">
                    <td colspan="5">
                        <div class="agenda-divider-marker">Agenda Yang Sudah Terlewat</div>
                    </td>
                </tr>
                @endif

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

                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Belum ada agenda yang sesuai dengan pencarian ini.</td>
                </tr>
                @endforelse

            </tbody>

        </table>

        <div class="table-footer">
            <div class="table-info">
                Menampilkan {{ $agenda->firstItem() ?? 0 }} sampai {{ $agenda->lastItem() ?? 0 }} dari {{ $agenda->total() }} data
            </div>
            @include('admin.partials.server-table-pagination', ['paginator' => $agenda])
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
                            <input type="text" name="time" class="form-control" placeholder="HH:MM" inputmode="numeric" pattern="^\d{2}:\d{2}$" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kegiatan</label>
                        <div class="input-counter-field">
                            <input type="text" name="name" class="form-control" data-char-limit="45" maxlength="45" required>
                            <small class="text-muted input-counter-meta">
                                <span>Maksimal 45 karakter</span>
                                <span data-char-counter>0/45</span>
                            </small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <div class="input-counter-field">
                            <input type="text" name="location" class="form-control" data-char-limit="30" maxlength="30" required>
                            <small class="text-muted input-counter-meta">
                                <span>Maksimal 30 karakter</span>
                                <span data-char-counter>0/30</span>
                            </small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Disposisi</label>
                        <div class="input-counter-field">
                            <input type="text" name="disposition" class="form-control" data-char-limit="30" maxlength="30" required>
                            <small class="text-muted input-counter-meta">
                                <span>Maksimal 30 karakter</span>
                                <span data-char-counter>0/30</span>
                            </small>
                        </div>
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
<div class="modal fade" id="editAgendaModal{{ $item->id }}" tabindex="-1" aria-hidden="true" data-edit-lock-modal>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content admin-form-modal">
            <form
                action="{{ route('admin.agenda.update', $item->id) }}"
                method="POST"
                data-edit-lock-form
                data-lock-endpoint="{{ route('admin.agenda.lock', $item->id) }}"
                data-unlock-endpoint="{{ route('admin.agenda.unlock', $item->id) }}"
            >
                @csrf
                @method('PUT')
                <input type="hidden" name="updated_at_version" value="{{ $item->updated_at?->toIso8601String() }}">

                <div class="modal-header">
                    <div>
                        <div class="modal-eyebrow">Update Jadwal</div>
                        <h5 class="modal-title">Edit Agenda</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-info d-none js-edit-lock-status" role="alert"></div>

                    <div class="admin-form-grid admin-form-grid-2">
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="date" class="form-control" value="{{ $item->date }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Waktu</label>
                            <input type="text" name="time" class="form-control" value="{{ str_replace('.', ':', \Illuminate\Support\Str::limit($item->time, 5, '')) }}" placeholder="HH:MM" inputmode="numeric" pattern="^\d{2}:\d{2}$" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kegiatan</label>
                        <div class="input-counter-field">
                            <input type="text" name="name" class="form-control" data-char-limit="45" maxlength="45" value="{{ $item->name }}" required>
                            <small class="text-muted input-counter-meta">
                                <span>Maksimal 45 karakter</span>
                                <span data-char-counter>0/45</span>
                            </small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <div class="input-counter-field">
                            <input type="text" name="location" class="form-control" data-char-limit="30" maxlength="30" value="{{ $item->location }}" required>
                            <small class="text-muted input-counter-meta">
                                <span>Maksimal 30 karakter</span>
                                <span data-char-counter>0/30</span>
                            </small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Disposisi</label>
                        <div class="input-counter-field">
                            <input type="text" name="disposition" class="form-control" data-char-limit="30" maxlength="30" value="{{ $item->disposition }}" required>
                            <small class="text-muted input-counter-meta">
                                <span>Maksimal 30 karakter</span>
                                <span data-char-counter>0/30</span>
                            </small>
                        </div>
                    </div>

                    <div class="form-section-note text-start mb-0" data-last-updated-meta>
                        Terakhir diubah oleh {{ $item->updater?->name ?? 'sistem' }}
                        @if($item->updated_at)
                        pada {{ $item->updated_at->locale('id')->translatedFormat('d F Y H:i') }}
                        @endif
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

@endsection
