@extends('admin.layout')

@section('title','Video Kegiatan')

@section('content')

<div class="admin-card data-panel">

    <div class="panel-header">
        <div>
            <div class="section-eyebrow">Manajemen Media</div>
            <h6 class="panel-title">
                <i class="bi bi-film"></i>
                Video Kegiatan
            </h6>
        </div>

        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadVideoModal">
            Upload
        </button>
    </div>

    <div class="panel-toolbar table-toolbar">
        <div class="panel-meta js-video-panel-meta">{{ isset($videos) && $videos->count() ? $videos->count() . ' video tersimpan di sistem. Geser baris untuk mengatur urutan.' : 'Belum ada video yang diupload.' }}</div>
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
                <input type="text" class="form-control form-control-sm table-search-input" placeholder="Cari video...">
            </label>
        </div>
    </div>

    <div class="table-shell">
        <table class="table admin-data-table js-admin-table table-centered-content">

            <thead>
                <tr>
                    <th class="video-id-column">ID</th>
                    <th>Preview</th>
                    <th>Nama Video</th>
                    <th>Durasi</th>
                    <th>Ukuran File</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @include('admin.partials.video-table-rows', ['videos' => $videos])
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

<div class="modal fade" id="uploadVideoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content admin-form-modal">
            <form action="{{ route('admin.video.store') }}" method="POST" enctype="multipart/form-data" class="video-upload-form">
                @csrf

                <div class="modal-header">
                    <div>
                        <div class="modal-eyebrow">Tambah Media</div>
                        <h5 class="modal-title">Upload Video</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-section-note">
                        Upload video yang akan diputar pada layar TV. Gunakan format yang ringan dan stabil saat diputar berulang.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Video</label>
                        <input type="text" name="title" class="form-control" placeholder="Masukkan nama video">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">File Video</label>
                        <input type="file" name="video" class="form-control" accept="video/mp4,video/webm,video/ogg" required>
                        <small class="text-muted">Maksimal 500 MB per video.</small>
                    </div>

                    <div class="upload-progress d-none">
                        <div class="upload-progress-head">
                            <span class="upload-progress-label">Mengupload video...</span>
                            <span class="upload-progress-percent">0%</span>
                        </div>
                        <div class="upload-progress-track">
                            <div class="upload-progress-bar"></div>
                        </div>
                        <div class="upload-progress-note">Mohon tunggu, file sedang dipindahkan ke server.</div>
                    </div>

                    <div class="upload-feedback d-none"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary upload-submit-btn">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="js-video-edit-modals">
    @include('admin.partials.video-edit-modals', ['videos' => $videos])
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('.video-upload-form');
        const tableBody = document.querySelector('.js-admin-table tbody');
        const panelMeta = document.querySelector('.js-video-panel-meta');
        const editModalsContainer = document.querySelector('.js-video-edit-modals');
        const tablePanel = document.querySelector('.data-panel');
        if (!form) return;

        const fileInput = form.querySelector('input[name="video"]');
        const submitButton = form.querySelector('.upload-submit-btn');
        const progressWrap = form.querySelector('.upload-progress');
        const progressBar = form.querySelector('.upload-progress-bar');
        const progressPercent = form.querySelector('.upload-progress-percent');
        const progressLabel = form.querySelector('.upload-progress-label');
        const feedback = form.querySelector('.upload-feedback');
        const uploadModalElement = document.getElementById('uploadVideoModal');

        const resetFeedback = () => {
            feedback.classList.add('d-none');
            feedback.className = 'upload-feedback d-none';
            feedback.textContent = '';
        };

        const showError = (message) => {
            feedback.className = 'upload-feedback upload-feedback-error';
            feedback.textContent = message;
        };

        if (!tableBody) return;

        const sortableRows = () => Array.from(tableBody.querySelectorAll('.js-sortable-row'));
        let draggedRow = null;

        const formatDuration = (seconds) => {
            if (!Number.isFinite(seconds) || seconds <= 0) {
                return '-';
            }

            const totalSeconds = Math.floor(seconds);
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const secs = totalSeconds % 60;

            if (hours > 0) {
                return [hours, minutes, secs]
                    .map((part) => String(part).padStart(2, '0'))
                    .join(':');
            }

            return [minutes, secs]
                .map((part) => String(part).padStart(2, '0'))
                .join(':');
        };

        const bindVideoDurations = () => {
            document.querySelectorAll('.js-sortable-row').forEach((row) => {
                const video = row.querySelector('.js-video-preview');
                const durationLabel = row.querySelector('.js-video-duration');

                if (!durationLabel) return;

                if (!video) {
                    durationLabel.textContent = '-';
                    return;
                }

                const applyDuration = () => {
                    durationLabel.textContent = formatDuration(video.duration);
                };

                if (video.readyState >= 1) {
                    applyDuration();
                } else {
                    video.addEventListener('loadedmetadata', applyDuration, { once: true });
                    video.addEventListener('error', () => {
                        durationLabel.textContent = '-';
                    }, { once: true });
                }
            });
        };

        const updateVideoUI = (payload) => {
            if (payload?.tableRowsHtml) {
                tableBody.innerHTML = payload.tableRowsHtml;
            }

            if (panelMeta && payload?.panelMeta) {
                panelMeta.textContent = payload.panelMeta;
            }

            if (editModalsContainer && typeof payload?.editModalsHtml === 'string') {
                editModalsContainer.innerHTML = payload.editModalsHtml;
            }

            bindVideoDurations();
            bindSortableRows();
            tablePanel?.dispatchEvent(new CustomEvent('admin-table:refresh', { bubbles: true }));
        };

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

        const sendFormRequest = async (targetForm) => {
            const response = await fetch(targetForm.action, {
                method: (targetForm.method || 'POST').toUpperCase(),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: new FormData(targetForm),
            });

            return parseJsonResponse(response);
        };

        const updateRowOrderLabels = () => {
            sortableRows().forEach((row) => {
                const orderBadge = row.querySelector('.video-order-number');
                if (orderBadge) {
                    orderBadge.textContent = row.dataset.videoId;
                }
            });
        };

        const sendOrderUpdate = () => {
            const orderedIds = sortableRows().map((row) => Number(row.dataset.videoId));
            if (!orderedIds.length) return;

            fetch('{{ route('admin.video.reorder') }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ ordered_ids: orderedIds }),
            })
                .then(parseJsonResponse)
                .then((payload) => {
                    updateVideoUI(payload);
                })
                .catch(() => {
                    showError('Urutan video gagal diperbarui.');
                });
        };

        const bindSortableRows = () => {
            updateRowOrderLabels();

            sortableRows().forEach((row) => {
                const handle = row.querySelector('.video-sort-handle');

                if (row.dataset.sortBound === 'true') {
                    return;
                }

                row.dataset.sortBound = 'true';

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
                    updateRowOrderLabels();
                    sendOrderUpdate();
                });

                row.addEventListener('dragover', (event) => {
                    event.preventDefault();
                    if (!draggedRow || draggedRow === row) return;

                    const rect = row.getBoundingClientRect();
                    const offset = event.clientY - rect.top;
                    const insertBefore = offset < rect.height / 2;

                    if (insertBefore) {
                        tableBody.insertBefore(draggedRow, row);
                    } else {
                        tableBody.insertBefore(draggedRow, row.nextSibling);
                    }
                });
            });
        };

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            resetFeedback();

            if (!fileInput.files.length) {
                feedback.className = 'upload-feedback upload-feedback-error';
                feedback.textContent = 'Pilih file video terlebih dahulu.';
                return;
            }

            const formData = new FormData(form);
            const xhr = new XMLHttpRequest();

            submitButton.disabled = true;
            submitButton.textContent = 'Uploading...';
            progressWrap.classList.remove('d-none');
            progressBar.style.width = '0%';
            progressPercent.textContent = '0%';
            progressLabel.textContent = 'Mengupload video...';

            xhr.open(form.method, form.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Accept', 'application/json');

            xhr.upload.addEventListener('progress', (e) => {
                if (!e.lengthComputable) return;
                const percent = Math.min(100, Math.round((e.loaded / e.total) * 100));
                progressBar.style.width = `${percent}%`;
                progressPercent.textContent = `${percent}%`;
            });

            xhr.addEventListener('load', () => {
                if (xhr.status >= 200 && xhr.status < 400) {
                    const payload = JSON.parse(xhr.responseText || '{}');
                    progressBar.style.width = '100%';
                    progressPercent.textContent = '100%';
                    progressLabel.textContent = 'Upload selesai.';
                    updateVideoUI(payload);
                    form.reset();
                    submitButton.disabled = false;
                    submitButton.textContent = 'Upload';
                    setTimeout(() => {
                        progressWrap.classList.add('d-none');
                        progressBar.style.width = '0%';
                        progressPercent.textContent = '0%';
                        progressLabel.textContent = 'Mengupload video...';
                        bootstrap.Modal.getInstance(uploadModalElement)?.hide();
                    }, 450);
                    return;
                }

                submitButton.disabled = false;
                submitButton.textContent = 'Upload';
                progressLabel.textContent = 'Upload gagal.';

                let message = 'Upload gagal. Silakan coba lagi.';

                try {
                    const response = JSON.parse(xhr.responseText);

                    if (response?.message) {
                        message = response.message;
                    }

                    if (response?.errors?.video?.length) {
                        message = response.errors.video[0];
                    }
                } catch (error) {
                    if (xhr.responseText) {
                        message = `Upload gagal (${xhr.status}).`;
                    }
                }

                showError(message);
            });

            xhr.addEventListener('error', () => {
                submitButton.disabled = false;
                submitButton.textContent = 'Upload';
                progressLabel.textContent = 'Upload gagal.';
                showError('Koneksi terputus saat upload video.');
            });

            xhr.send(formData);
        });

        document.addEventListener('submit', async (event) => {
            const targetForm = event.target;

            if (!(targetForm instanceof HTMLFormElement)) return;

            if (targetForm.matches('[data-video-toggle-form], [data-video-edit-form]')) {
                event.preventDefault();

                try {
                    const payload = await sendFormRequest(targetForm);
                    updateVideoUI(payload);

                    if (targetForm.matches('[data-video-edit-form]')) {
                        bootstrap.Modal.getInstance(targetForm.closest('.modal'))?.hide();
                    }
                } catch (error) {
                    showError(error.message || 'Aksi video gagal diproses.');
                }
            }
        });

        document.addEventListener('ajax-confirmed-submit', async (event) => {
            const targetForm = event.target;

            if (!(targetForm instanceof HTMLFormElement) || !targetForm.matches('[data-video-delete-form]')) {
                return;
            }

            try {
                const payload = await sendFormRequest(targetForm);
                updateVideoUI(payload);
            } catch (error) {
                showError(error.message || 'Video gagal dihapus.');
            }
        });

        bindVideoDurations();
        bindSortableRows();
    });
</script>
@endpush

@endsection
