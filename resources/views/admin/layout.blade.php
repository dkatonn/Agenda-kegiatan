<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') - TV Agenda</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    {{-- tambahan kalau nanti butuh css per halaman --}}
    @stack('styles')
</head>

@php
    $sidebarAgendaCount = \App\Models\Agenda::count();
    $sidebarEmployeeCount = \App\Models\Employee::count();
    $sidebarSettings = \App\Models\Setting::pluck('value', 'key');
    $sidebarHasBackground = !empty($sidebarSettings->get('background'));
    $sidebarHasRunningText = !empty($sidebarSettings->get('running_text'));
    $adminTabBootstrapPending = session()->pull('admin_tab_bootstrap_pending', false);
    $adminIdleTimeoutMs = (int) config('session.lifetime', 10) * 60 * 1000;
    $currentAdmin = auth()->user();
    $currentAdminPhotoUrl = $currentAdmin && filled($currentAdmin->image_path)
        ? asset('storage/' . ltrim($currentAdmin->image_path, '/'))
        : null;
@endphp

<body
    data-admin-tab-bootstrap="{{ $adminTabBootstrapPending ? '1' : '0' }}"
    data-admin-idle-timeout-ms="{{ $adminIdleTimeoutMs }}"
    data-admin-login-url="{{ route('login') }}"
    data-admin-logout-url="{{ route('logout') }}"
    data-admin-tab-close-logout-url="{{ route('logout.tab-close') }}"
>
    <div class="admin-wrapper">

        <!-- SIDEBAR -->
        <aside class="sidebar">

            <div class="logo">
                <div class="logo-mark">
                    <i class="bi bi-broadcast-pin"></i>
                </div>
                <div class="logo-text">
                    <strong>TV Agenda</strong>
                    <span>Pusat Kendali</span>
                </div>
            </div>

            <div class="sidebar-caption">Kelola tampilan TV, agenda, pegawai, dan media dari satu panel.</div>

            <nav class="menu">

                <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.index') ? 'active' : '' }}">
                    <i class="bi bi-house"></i>
                    <span>Dasbor</span>
                </a>

                <a href="{{ route('admin.employee') }}" class="{{ request()->routeIs('admin.employee*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Pegawai</span>
                </a>

                <a href="{{ route('admin.video') }}" class="{{ request()->routeIs('admin.video*') ? 'active' : '' }}">
                    <i class="bi bi-film"></i>
                    <span>Video</span>
                </a>

                <a href="{{ route('admin.agenda') }}" class="{{ request()->routeIs('admin.agenda*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event"></i>
                    <span>Agenda</span>
                </a>

                <a href="{{ route('admin.running-text') }}" class="{{ request()->routeIs('admin.running-text*') ? 'active' : '' }}">
                    <i class="bi bi-chat-left-text"></i>
                    <span>Teks Berjalan</span>
                </a>

                <a href="{{ route('admin.background') }}" class="{{ request()->routeIs('admin.background*') ? 'active' : '' }}">
                    <i class="bi bi-image"></i>
                    <span>Latar Belakang</span>
                </a>

                <a href="{{ route('admin.user-settings') }}" class="{{ request()->routeIs('admin.user-settings*') || request()->routeIs('admin.password.*') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i>
                    <span>Pengaturan Admin</span>
                </a>

            </nav>

            <div class="sidebar-utility">
                <div class="sidebar-utility-label">Akses Cepat</div>
                <a href="{{ route('tv') }}" target="_blank" class="sidebar-utility-link">
                    <i class="bi bi-tv"></i>
                    <span>Buka Preview TV</span>
                </a>
                <div class="sidebar-utility-stats">
                    <div class="sidebar-utility-stat">
                        <span>Agenda</span>
                        <strong>{{ $sidebarAgendaCount }}</strong>
                    </div>
                    <div class="sidebar-utility-stat">
                        <span>Pegawai</span>
                        <strong>{{ $sidebarEmployeeCount }}</strong>
                    </div>
                    <div class="sidebar-utility-stat">
                        <span>Latar Belakang</span>
                        <strong>{{ $sidebarHasBackground ? 'Aktif' : 'Kosong' }}</strong>
                    </div>
                    <div class="sidebar-utility-stat">
                        <span>Teks</span>
                        <strong>{{ $sidebarHasRunningText ? 'Aktif' : 'Kosong' }}</strong>
                    </div>
                </div>
                <div class="sidebar-utility-note">
                    Ringkasan ini membantu cek cepat kondisi konten TV tanpa perlu pindah halaman.
                </div>
            </div>
        </aside>


        <!-- MAIN -->
        <div class="main">

            <!-- TOPBAR -->
            <header class="topbar d-flex justify-content-between align-items-center">
                <div class="topbar-copy">
                    <div class="topbar-eyebrow">Panel Admin</div>
                    <h5 class="mb-0">@yield('title')</h5>
                </div>

                <div class="topbar-actions">
                    <div class="dropdown">
                        <button class="btn topbar-user-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="topbar-user-avatar {{ $currentAdminPhotoUrl ? 'has-image' : '' }}">
                                @if($currentAdminPhotoUrl)
                                    <img src="{{ $currentAdminPhotoUrl }}" alt="{{ $currentAdmin?->name ?? 'Admin' }}">
                                @else
                                <i class="bi bi-person"></i>
                                @endif
                            </span>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end topbar-user-menu">
                            <li class="topbar-user-summary">
                                <span class="topbar-user-summary-avatar {{ $currentAdminPhotoUrl ? 'has-image' : '' }}">
                                    @if($currentAdminPhotoUrl)
                                        <img src="{{ $currentAdminPhotoUrl }}" alt="{{ $currentAdmin?->name ?? 'Admin' }}">
                                    @else
                                        <i class="bi bi-person"></i>
                                    @endif
                                </span>
                                <div class="topbar-user-summary-copy">
                                    <strong>{{ $currentAdmin?->name ?? 'Admin' }}</strong>
                                    <span>{{ $currentAdmin?->nip ?? 'Administrator' }}</span>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a href="{{ route('admin.password.edit') }}" class="dropdown-item">
                                    <i class="bi bi-key"></i>
                                    Ubah Kata Sandi
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item" data-confirm-submit="Yakin ingin keluar dari dasbor?" data-confirm-action-label="Ya, keluar">
                                        <i class="bi bi-box-arrow-right"></i>
                                        Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>


            <!-- CONTENT -->
            <div class="content container-fluid">

                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif

                @if($errors->any() && !View::hasSection('suppressGlobalErrors'))
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
                @endif

                @yield('content')

            </div>

        </div>

    </div>

    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content delete-confirm-modal">
                <div class="modal-body text-center">
                    <div class="delete-confirm-icon">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h5 class="delete-confirm-title">Konfirmasi Aksi</h5>
                    <p class="delete-confirm-message js-delete-confirm-message">
                        Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-danger-soft delete-confirm-submit js-delete-confirm-submit">Ya, hapus</button>
                    <button type="button" class="btn btn-light delete-confirm-cancel" data-bs-dismiss="modal">Tidak</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const body = document.body;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const tabBootstrapAllowed = body.dataset.adminTabBootstrap === '1';
            const idleTimeoutMs = Number(body.dataset.adminIdleTimeoutMs || 600000);
            const loginUrl = body.dataset.adminLoginUrl;
            const logoutUrl = body.dataset.adminLogoutUrl;
            const tabCloseLogoutUrl = body.dataset.adminTabCloseLogoutUrl;
            const adminTabStorageKey = 'admin.active.tab';
            let idleTimer = null;
            let busyActivityCount = 0;

            const sendLogoutRequest = (url) => {
                if (!url || !csrfToken) {
                    return;
                }

                const payload = new FormData();
                payload.append('_token', csrfToken);

                if (navigator.sendBeacon) {
                    navigator.sendBeacon(url, payload);
                    return;
                }

                fetch(url, {
                    method: 'POST',
                    body: payload,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                    keepalive: true,
                }).catch(() => {});
            };

            const redirectToLogin = (reason = '') => {
                const targetUrl = reason ? `${loginUrl}?reason=${encodeURIComponent(reason)}` : loginUrl;
                window.location.replace(targetUrl);
            };

            const existingTabSession = sessionStorage.getItem(adminTabStorageKey);

            if (!existingTabSession) {
                if (!tabBootstrapAllowed) {
                    sendLogoutRequest(tabCloseLogoutUrl);
                    redirectToLogin('tab_closed');
                    return;
                }

                sessionStorage.setItem(adminTabStorageKey, `${Date.now()}-${Math.random().toString(36).slice(2)}`);
            }

            const resetIdleTimer = () => {
                if (busyActivityCount > 0) {
                    return;
                }

                window.clearTimeout(idleTimer);
                idleTimer = window.setTimeout(() => {
                    sessionStorage.removeItem(adminTabStorageKey);
                    sendLogoutRequest(logoutUrl);
                    redirectToLogin('idle');
                }, idleTimeoutMs);
            };

            ['click', 'keydown', 'mousemove', 'scroll', 'touchstart'].forEach((eventName) => {
                window.addEventListener(eventName, resetIdleTimer, { passive: true });
            });

            window.addEventListener('focus', resetIdleTimer);
            resetIdleTimer();

            window.addEventListener('admin:busy-start', () => {
                busyActivityCount += 1;
                window.clearTimeout(idleTimer);
            });

            window.addEventListener('admin:busy-end', () => {
                busyActivityCount = Math.max(0, busyActivityCount - 1);
                resetIdleTimer();
            });

            document.querySelectorAll('[data-nip-input]').forEach((input) => {
                if (input.dataset.nipBound === 'true') {
                    return;
                }

                input.dataset.nipBound = 'true';
                input.addEventListener('input', () => {
                    input.value = input.value.replace(/\D/g, '').slice(0, 18);
                });
            });

            const updateInputCounter = (input) => {
                const limit = Number(input.dataset.charLimit || input.getAttribute('maxlength') || 0);
                const counterField = input.closest('.input-counter-field');
                const counter = counterField?.querySelector('[data-char-counter]') || input.parentElement?.nextElementSibling?.querySelector?.('[data-char-counter]');

                if (!limit || !counter) {
                    return;
                }

                if (input.value.length > limit) {
                    input.value = input.value.slice(0, limit);
                }

                counter.textContent = `${input.value.length}/${limit}`;
            };

            window.initAdminCharCounters = (scope = document) => {
                scope.querySelectorAll('[data-char-limit]').forEach((input) => {
                    updateInputCounter(input);

                    if (input.dataset.counterBound === 'true') {
                        return;
                    }

                    input.dataset.counterBound = 'true';
                    input.addEventListener('input', () => updateInputCounter(input));
                    input.addEventListener('paste', () => {
                        window.setTimeout(() => updateInputCounter(input), 0);
                    });
                });
            };

            window.initAdminCharCounters(document);

            const setEditFormEnabled = (form, enabled) => {
                form.querySelectorAll('input, textarea, select, button[type="submit"]').forEach((field) => {
                    if (field.type === 'hidden') {
                        return;
                    }

                    field.disabled = !enabled;
                });
            };

            const setEditLockStatus = (modal, message = '', tone = 'info') => {
                const status = modal.querySelector('.js-edit-lock-status');

                if (!status) {
                    return;
                }

                if (!message) {
                    status.className = 'alert alert-info d-none js-edit-lock-status';
                    status.textContent = '';
                    return;
                }

                status.className = `alert alert-${tone} js-edit-lock-status`;
                status.textContent = message;
            };

            const updateLastEditedMeta = (modal, lockPayload) => {
                const meta = modal.querySelector('[data-last-updated-meta]');

                if (!meta || !lockPayload) {
                    return;
                }

                const updatedBy = lockPayload.updated_by_name || 'sistem';
                const updatedAt = lockPayload.updated_at_label ? ` pada ${lockPayload.updated_at_label}` : '';
                meta.textContent = `Terakhir diubah oleh ${updatedBy}${updatedAt}`;
            };

            const syncVersionField = (form, lockPayload) => {
                const versionField = form.querySelector('input[name="updated_at_version"]');

                if (!versionField || !lockPayload?.updated_at) {
                    return;
                }

                versionField.value = lockPayload.updated_at;
            };

            const lockRequest = async (url, method = 'POST') => {
                const response = await fetch(url, {
                    method,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                    keepalive: method !== 'POST',
                });

                const payload = await response.json().catch(() => ({}));

                if (!response.ok) {
                    throw payload;
                }

                return payload;
            };

            document.addEventListener('shown.bs.modal', async (event) => {
                const modal = event.target;

                if (!(modal instanceof HTMLElement) || modal.dataset.editLockModal !== '') {
                    return;
                }

                const form = modal.querySelector('[data-edit-lock-form]');
                const lockEndpoint = form?.dataset.lockEndpoint;

                if (!form || !lockEndpoint) {
                    return;
                }

                modal.dataset.lockHeld = '0';
                setEditFormEnabled(form, false);
                setEditLockStatus(modal, 'Memeriksa status edit...', 'info');

                try {
                    const payload = await lockRequest(lockEndpoint, 'POST');
                    modal.dataset.lockHeld = payload?.lock?.is_mine ? '1' : '0';
                    setEditFormEnabled(form, true);
                    syncVersionField(form, payload.lock);
                    updateLastEditedMeta(modal, payload.lock);
                    setEditLockStatus(modal, payload.message || 'Lock edit aktif untuk Anda.', 'success');
                } catch (payload) {
                    setEditFormEnabled(form, false);
                    syncVersionField(form, payload?.lock);
                    updateLastEditedMeta(modal, payload?.lock);
                    setEditLockStatus(modal, payload?.message || 'Data sedang dikunci oleh admin lain.', 'warning');
                }
            });

            document.addEventListener('hidden.bs.modal', (event) => {
                const modal = event.target;

                if (!(modal instanceof HTMLElement) || modal.dataset.editLockModal !== '') {
                    return;
                }

                const form = modal.querySelector('[data-edit-lock-form]');
                const unlockEndpoint = form?.dataset.unlockEndpoint;

                if (form) {
                    setEditFormEnabled(form, true);
                }

                setEditLockStatus(modal);

                if (!form || !unlockEndpoint || modal.dataset.lockHeld !== '1') {
                    modal.dataset.lockHeld = '0';
                    return;
                }

                modal.dataset.lockHeld = '0';
                lockRequest(unlockEndpoint, 'DELETE').catch(() => {});
            });

            document.querySelectorAll('[data-server-table-form]').forEach((form) => {
                const pageSizeSelect = form.querySelector('.table-page-size');
                const searchInput = form.querySelector('.table-search-input');
                let searchDebounce = null;

                pageSizeSelect?.addEventListener('change', () => {
                    form.submit();
                });

                searchInput?.addEventListener('input', () => {
                    window.clearTimeout(searchDebounce);
                    searchDebounce = window.setTimeout(() => {
                        form.submit();
                    }, 350);
                });
            });

            document.querySelectorAll('form[action="{{ route('logout') }}"]').forEach((form) => {
                form.addEventListener('submit', () => {
                    sessionStorage.removeItem(adminTabStorageKey);
                });
            });

            document.querySelectorAll('.data-panel').forEach((panel) => {
                if (panel.dataset.serverTable === 'true') {
                    return;
                }

                const table = panel.querySelector('.js-admin-table');
                if (!table) return;

                const tbody = table.querySelector('tbody');
                if (!tbody) return;

                const pageSizeSelect = panel.querySelector('.table-page-size');
                const searchInput = panel.querySelector('.table-search-input');
                const info = panel.querySelector('.table-info');
                const prevButton = panel.querySelector('.table-prev');
                const nextButton = panel.querySelector('.table-next');
                const pageIndicator = panel.querySelector('.table-page-indicator');

                let currentPage = 1;
                const getRows = () => Array.from(tbody.querySelectorAll('tr')).filter((row) => row.children.length > 1);

                const getFilteredRows = () => {
                    const rows = getRows();
                    const keyword = (searchInput?.value || '').trim().toLowerCase();
                    if (!keyword) return rows;

                    return rows.filter((row) => row.innerText.toLowerCase().includes(keyword));
                };

                const renderTable = () => {
                    const pageSize = Number(pageSizeSelect?.value || 10);
                    const filteredRows = getFilteredRows();
                    const totalRows = filteredRows.length;
                    const totalPages = Math.max(1, Math.ceil(totalRows / pageSize));

                    if (currentPage > totalPages) {
                        currentPage = totalPages;
                    }

                    const startIndex = totalRows === 0 ? 0 : (currentPage - 1) * pageSize;
                    const endIndex = Math.min(startIndex + pageSize, totalRows);

                    getRows().forEach((row) => {
                        row.style.display = 'none';
                    });

                    filteredRows.slice(startIndex, endIndex).forEach((row) => {
                        row.style.display = '';
                    });

                    if (info) {
                        if (totalRows === 0) {
                            info.textContent = 'Menampilkan 0 sampai 0 dari 0 data';
                        } else {
                            info.textContent = `Menampilkan ${startIndex + 1} sampai ${endIndex} dari ${totalRows} data`;
                        }
                    }

                    if (pageIndicator) {
                        pageIndicator.textContent = `${currentPage}`;
                    }

                    if (prevButton) prevButton.disabled = currentPage === 1;
                    if (nextButton) nextButton.disabled = currentPage >= totalPages;
                };

                pageSizeSelect?.addEventListener('change', () => {
                    currentPage = 1;
                    renderTable();
                });

                searchInput?.addEventListener('input', () => {
                    currentPage = 1;
                    renderTable();
                });

                prevButton?.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage -= 1;
                        renderTable();
                    }
                });

                nextButton?.addEventListener('click', () => {
                    const pageSize = Number(pageSizeSelect?.value || 10);
                    const totalRows = getFilteredRows().length;
                    const totalPages = Math.max(1, Math.ceil(totalRows / pageSize));
                    if (currentPage < totalPages) {
                        currentPage += 1;
                        renderTable();
                    }
                });

                panel.addEventListener('admin-table:refresh', () => {
                    renderTable();
                });

                renderTable();
            });

            const confirmModalElement = document.getElementById('deleteConfirmModal');
            const confirmMessageElement = document.querySelector('.js-delete-confirm-message');
            const confirmSubmitButton = document.querySelector('.js-delete-confirm-submit');
            const confirmModal = confirmModalElement ? new bootstrap.Modal(confirmModalElement) : null;
            let pendingAction = null;

            const openDeleteConfirm = (message, onConfirm, confirmLabel = 'Ya, hapus') => {
                if (!confirmModal || !confirmMessageElement || !confirmSubmitButton) {
                    if (window.confirm(message)) {
                        onConfirm();
                    }
                    return;
                }

                pendingAction = onConfirm;
                confirmMessageElement.textContent = message;
                confirmSubmitButton.textContent = confirmLabel;
                confirmModal.show();
            };

            confirmSubmitButton?.addEventListener('click', () => {
                if (pendingAction) {
                    pendingAction();
                    pendingAction = null;
                }

                confirmModal?.hide();
            });

            confirmModalElement?.addEventListener('hidden.bs.modal', () => {
                pendingAction = null;
            });

            document.querySelectorAll('form.js-confirm-delete').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    event.preventDefault();

                    const message = form.dataset.confirmMessage || 'Apakah Anda yakin ingin menghapus data ini?';
                    const confirmLabel = form.dataset.confirmActionLabel || 'Ya, hapus';
                    openDeleteConfirm(message, () => {
                        if (form.dataset.ajaxSubmit === 'true') {
                            form.dispatchEvent(new CustomEvent('ajax-confirmed-submit', { bubbles: true }));
                            return;
                        }

                        form.submit();
                    }, confirmLabel);
                });
            });

            document.querySelectorAll('button[data-confirm-submit]').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();

                    const message = button.dataset.confirmSubmit || 'Apakah Anda yakin ingin melanjutkan aksi ini?';
                    const form = button.form;
                    const confirmLabel = button.dataset.confirmActionLabel || 'Ya, lanjutkan';

                    openDeleteConfirm(message, () => {
                        if (form) {
                            if (typeof form.requestSubmit === 'function') {
                                form.requestSubmit(button);
                            } else {
                                const hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = button.name;
                                hiddenInput.value = button.value;
                                hiddenInput.dataset.confirmTemp = 'true';
                                form.appendChild(hiddenInput);
                                form.submit();
                                hiddenInput.remove();
                            }
                        }
                    }, confirmLabel);
                });
            });
        });
    </script>

    {{-- tambahan kalau nanti butuh js per halaman --}}
    @stack('scripts')

</body>

</html>
