@extends('admin.layout')

@section('title','Profil Pegawai')

@section('content')

@php
    $canReorderEmployees = $search === '' && \Illuminate\Support\Facades\Schema::hasColumn('employees', 'sort_order');
@endphp

<div class="admin-card data-panel" data-server-table="true">

    <div class="panel-header">
        <div>
            <div class="section-eyebrow">Manajemen Data</div>
            <h6 class="panel-title">
                <i class="bi bi-people"></i>
                Profil Pegawai
            </h6>
        </div>

        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
            Tambah
        </button>
    </div>

    <div class="panel-toolbar table-toolbar">
        <div class="panel-meta">
            Total {{ $employee->total() }} pegawai tersimpan.
            @if($canReorderEmployees)
                Anda dapat mengubah urutan tampilan pegawai dengan menyeret ikon geser pada foto profil pegawai.
            @endif
        </div>
        <form method="GET" action="{{ route('admin.employee') }}" class="table-controls" data-server-table-form>
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
                <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm table-search-input" placeholder="Cari pegawai...">
            </label>
        </form>
    </div>

    <div class="table-shell">
        <table class="table admin-data-table js-admin-table table-centered-content">

            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Jabatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody data-employee-sortable="{{ $canReorderEmployees ? 'true' : 'false' }}" data-start-order="{{ $employee->firstItem() ?? 1 }}">

                @forelse($employee as $emp)

                <tr class="{{ $canReorderEmployees ? 'js-sortable-row js-employee-sortable-row' : '' }}" draggable="false" data-employee-id="{{ $emp->id }}">

                    <td class="employee-photo-cell {{ $canReorderEmployees ? 'has-employee-sort-handle' : '' }}">
                        <div class="employee-photo-sort-group">
                            @if($canReorderEmployees)
                            <button type="button" class="btn btn-light btn-sm employee-sort-handle" title="Geser untuk ubah urutan" aria-label="Geser {{ $emp->name }} untuk ubah urutan">
                                <i class="bi bi-grip-vertical"></i>
                            </button>
                            @endif

                        @if($emp->image_path)
                        <img src="{{ asset('storage/'.$emp->image_path) }}" class="avatar" alt="{{ $emp->name }}">
                        @else
                        <span class="admin-list-avatar-placeholder">
                            <i class="bi bi-person"></i>
                        </span>
                        @endif
                        </div>
                    </td>

                    <td>{{ $emp->name }}</td>
                    <td>{{ $emp->nip ?? '-' }}</td>
                    <td>{{ $emp->role }}</td>

                    <td>
                        <div class="action-group">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editEmployeeModal{{ $emp->id }}">
                                Edit
                            </button>

                            <form action="{{ route('admin.employee.delete',$emp->id) }}" method="POST" class="d-inline js-confirm-delete" data-confirm-message="Yakin ingin menghapus data pegawai ini?">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-outline-danger">
                                    Hapus
                                </button>

                            </form>
                        </div>
                    </td>

                </tr>

                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Belum ada data pegawai yang sesuai dengan pencarian ini.</td>
                </tr>
                @endforelse

            </tbody>

        </table>

        <div class="table-footer">
            <div class="table-info">
                Menampilkan {{ $employee->firstItem() ?? 0 }} sampai {{ $employee->lastItem() ?? 0 }} dari {{ $employee->total() }} data
            </div>
            @include('admin.partials.server-table-pagination', ['paginator' => $employee])
        </div>
    </div>

</div>

@foreach($employee as $emp)
<div class="modal fade" id="editEmployeeModal{{ $emp->id }}" tabindex="-1" aria-hidden="true" data-edit-lock-modal>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content admin-form-modal">
            <form
                action="{{ route('admin.employee.update', $emp->id) }}"
                method="POST"
                enctype="multipart/form-data"
                data-edit-lock-form
                data-lock-endpoint="{{ route('admin.employee.lock', $emp->id) }}"
                data-unlock-endpoint="{{ route('admin.employee.unlock', $emp->id) }}"
            >
                @csrf
                @method('PUT')
                <input type="hidden" name="updated_at_version" value="{{ $emp->updated_at?->toIso8601String() }}">

                <div class="modal-header">
                    <div>
                        <div class="modal-eyebrow">Update Profil</div>
                        <h5 class="modal-title">Edit Pegawai</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-info d-none js-edit-lock-status" role="alert"></div>

                    <div class="admin-form-grid admin-form-grid-2">
                        <div class="mb-3 admin-form-grid-full">
                            <label class="form-label">Foto Pegawai</label>
                            <div class="admin-photo-field">
                                <div class="admin-photo-preview-shell">
                                    @if($emp->image_path)
                                    <img src="{{ asset('storage/' . $emp->image_path) }}" class="admin-photo-preview" alt="{{ $emp->name }}" data-image-preview data-default-image="{{ asset('storage/' . $emp->image_path) }}">
                                    @else
                                    <span class="admin-photo-preview admin-photo-preview-placeholder" data-image-preview-placeholder>
                                        <i class="bi bi-person"></i>
                                    </span>
                                    @endif
                                </div>
                                <div class="admin-photo-input-copy">
                                    <input type="file" name="image" class="form-control" accept="image/*" data-image-input>
                                    <small class="text-muted d-block mt-2">Kosongkan jika tidak ingin mengganti foto pegawai.</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" value="{{ $emp->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NIP</label>
                            <div class="input-counter-field">
                                <input type="text" name="nip" class="form-control" value="{{ $emp->nip }}" inputmode="numeric" maxlength="18" data-char-limit="18" data-nip-input>
                                <small class="text-muted input-counter-meta">
                                    <span>NIP pegawai</span>
                                    <span data-char-counter>0/18</span>
                                </small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="role" class="form-control" value="{{ $emp->role }}" required>
                        </div>
                    </div>

                    <div class="form-section-note text-start mb-0" data-last-updated-meta>
                        Terakhir diubah oleh {{ $emp->updater?->name ?? 'sistem' }}
                        @if($emp->updated_at)
                        pada {{ $emp->updated_at->locale('id')->translatedFormat('d F Y H:i') }}
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

<div class="modal fade" id="createEmployeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content admin-form-modal">
            <form action="{{ route('admin.employee.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <div>
                        <div class="modal-eyebrow">Tambah Data</div>
                        <h5 class="modal-title">Tambah Pegawai</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-section-note">
                        Tambahkan profil pegawai baru untuk ditampilkan pada rotasi layar TV.
                    </div>

                    <div class="admin-form-grid admin-form-grid-2">
                        <div class="mb-3 admin-form-grid-full">
                            <label class="form-label">Foto Pegawai</label>
                            <div class="admin-photo-field">
                                <div class="admin-photo-preview-shell">
                                    <span class="admin-photo-preview admin-photo-preview-placeholder" data-image-preview-placeholder>
                                        <i class="bi bi-person"></i>
                                    </span>
                                </div>
                                <div class="admin-photo-input-copy">
                                    <input type="file" name="image" class="form-control" accept="image/*" data-image-input>
                                    <small class="text-muted d-block mt-2">Tambahkan foto pegawai agar tampil juga pada layar TV.</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NIP</label>
                            <div class="input-counter-field">
                                <input type="text" name="nip" class="form-control" inputmode="numeric" maxlength="18" data-char-limit="18" data-nip-input>
                                <small class="text-muted input-counter-meta">
                                    <span>NIP pegawai</span>
                                    <span data-char-counter>0/18</span>
                                </small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="role" class="form-control" required>
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

@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-image-input]').forEach(function(input) {
        input.addEventListener('change', function() {
            const form = input.closest('form');
            const previewImage = form.querySelector('[data-image-preview]');
            const previewPlaceholder = form.querySelector('[data-image-preview-placeholder]');
            const file = input.files && input.files[0] ? input.files[0] : null;

            if (!file) {
                if (previewImage && previewImage.dataset.defaultImage) {
                    previewImage.src = previewImage.dataset.defaultImage;
                }

                if (previewPlaceholder) {
                    previewPlaceholder.classList.remove('d-none');
                }

                return;
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                if (previewImage) {
                    previewImage.src = event.target?.result || previewImage.src;
                    return;
                }

                if (previewPlaceholder) {
                    previewPlaceholder.outerHTML = '<img src="' + (event.target?.result || '') + '" class="admin-photo-preview" data-image-preview alt="Preview foto pegawai">';
                }
            };

            reader.readAsDataURL(file);
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const tableBody = document.querySelector('[data-employee-sortable="true"]');
        if (!tableBody) return;

        const sortableRows = () => Array.from(tableBody.querySelectorAll('.js-employee-sortable-row'));
        let draggedRow = null;

        const parseJsonResponse = async (response) => {
            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                const firstError = payload?.errors
                    ? Object.values(payload.errors).flat()[0]
                    : null;

                throw new Error(firstError || payload?.message || `Request gagal (${response.status}).`);
            }

            return payload;
        };

        const sendOrderUpdate = () => {
            const orderedIds = sortableRows().map((row) => Number(row.dataset.employeeId));
            if (!orderedIds.length) return;

            fetch('{{ route('admin.employee.reorder') }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    ordered_ids: orderedIds,
                    start_order: Number(tableBody.dataset.startOrder || 1),
                }),
            })
                .then(parseJsonResponse)
                .catch((error) => {
                    console.error(error);
                    window.alert('Urutan pegawai gagal diperbarui. Muat ulang halaman lalu coba lagi.');
                });
        };

        sortableRows().forEach((row) => {
            const handle = row.querySelector('.employee-sort-handle');

            if (handle) {
                handle.addEventListener('mousedown', () => {
                    row.setAttribute('draggable', 'true');
                });
            }

            row.addEventListener('dragstart', () => {
                draggedRow = row;
                row.classList.add('is-dragging');
            });

            row.addEventListener('dragend', () => {
                row.classList.remove('is-dragging');
                row.setAttribute('draggable', 'false');
                draggedRow = null;
                sendOrderUpdate();
            });

            row.addEventListener('dragover', (event) => {
                event.preventDefault();
                if (!draggedRow || draggedRow === row) return;

                const rect = row.getBoundingClientRect();
                const offset = event.clientY - rect.top;

                tableBody.insertBefore(
                    draggedRow,
                    offset < rect.height / 2 ? row : row.nextSibling
                );
            });
        });
    });
</script>
@endpush
