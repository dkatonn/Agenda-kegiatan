<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $unitLabel }} - Panel Uji</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --bg:#eef3f9; --card:#fff; --text:#17314f; --muted:#5d7592; --line:#d5deeb; --blue:#0c4482; --danger:#c73a45; }
        * { box-sizing: border-box; }
        body { margin:0; font-family:"Poppins",sans-serif; background:var(--bg); color:var(--text); }
        .page { max-width:1500px; margin:0 auto; padding:24px; }
        .hero { display:flex; justify-content:space-between; align-items:end; gap:16px; margin-bottom:20px; }
        .hero h1 { margin:0; font-size:clamp(28px,3vw,48px); color:var(--blue); }
        .hero p { margin:6px 0 0; color:var(--muted); }
        .top-actions { display:flex; gap:12px; flex-wrap:wrap; align-items:center; justify-content:flex-end; }
        .link-chip, .logout-btn { text-decoration:none; color:var(--blue); background:#fff; border:1px solid var(--line); border-radius:999px; padding:10px 14px; font-weight:600; }
        .logout-btn { cursor:pointer; }
        .user-badge { padding:10px 14px; border-radius:999px; background:#dce9f7; color:var(--blue); font-weight:700; }
        .status { margin-bottom:14px; border-radius:12px; padding:12px 14px; background:#dce9f7; color:var(--blue); font-weight:600; }
        .status.error { background:rgba(199,58,69,.12); color:var(--danger); }
        .grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; }
        .card { background:rgba(255,255,255,.95); border:1px solid var(--line); border-radius:18px; padding:18px; box-shadow:0 14px 30px rgba(14,41,75,.08); }
        .card.full { grid-column:1 / -1; }
        .card h2 { margin:0 0 6px; color:var(--blue); font-size:22px; }
        .card p { margin:0 0 14px; color:var(--muted); font-size:14px; }
        form { display:grid; gap:12px; }
        .fields { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; }
        .fields.three { grid-template-columns:repeat(3,minmax(0,1fr)); }
        label { display:grid; gap:6px; font-size:13px; font-weight:600; color:var(--muted); }
        input, select, textarea, button { font:inherit; }
        input, select, textarea { width:100%; border:1px solid var(--line); border-radius:12px; padding:11px 12px; background:#fff; color:var(--text); }
        textarea { min-height:100px; resize:vertical; }
        .checkbox { display:flex; align-items:center; gap:8px; color:var(--muted); font-size:14px; }
        .checkbox input { width:auto; }
        .actions, .row-actions { display:flex; flex-wrap:wrap; gap:10px; }
        button { border:0; border-radius:12px; padding:11px 14px; cursor:pointer; font-weight:700; }
        .btn-primary { background:var(--blue); color:#fff; }
        .btn-secondary { background:#e9eef5; color:var(--text); }
        .btn-danger { background:rgba(199,58,69,.12); color:var(--danger); }
        .table-wrap { margin-top:16px; overflow:auto; border:1px solid var(--line); border-radius:14px; }
        table { width:100%; border-collapse:collapse; min-width:760px; background:#fff; }
        th, td { border-bottom:1px solid var(--line); padding:12px; text-align:left; vertical-align:top; font-size:14px; }
        th { background:#f5f8fc; color:var(--blue); }
        .pill { display:inline-flex; align-items:center; border-radius:999px; padding:4px 10px; background:#edf3fb; color:var(--blue); font-size:12px; font-weight:700; text-transform:uppercase; }
        .mono { font-family:Consolas,monospace; font-size:13px; }
        .hint { padding:12px 14px; border-radius:12px; background:#f7faff; color:#55708f; font-size:13px; }
        @media (max-width:1080px) {
            .grid { grid-template-columns:1fr; }
            .fields, .fields.three { grid-template-columns:1fr; }
            .hero { align-items:start; flex-direction:column; }
            .top-actions { justify-content:flex-start; }
        }
    </style>
</head>
<body>
    <div class="page">
        <section class="hero">
            <div>
                <h1>{{ $unitLabel }} Panel Uji</h1>
                <p>Halaman sementara untuk uji tambah, ubah, hapus data sebelum desain final jadi.</p>
            </div>
            <div class="top-actions">
                <span class="user-badge" id="sapaan-pengguna">Selamat datang, {{ $currentUser->nip }}</span>
                <a class="link-chip" href="{{ url('/') }}" target="_blank">Lihat Halaman TV</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout-btn" type="submit">Keluar</button>
                </form>
            </div>
        </section>

        <div id="status" class="status">Panel siap. Data sedang dimuat.</div>

        <section class="grid">
            <article class="card">
                <h2>Agenda {{ strtoupper($unit) }}</h2>
                <p>Agenda di halaman ini otomatis tersimpan untuk unit `{{ $unit }}`.</p>
                <form id="agenda-form">
                    <input type="hidden" name="id">
                    <div class="fields">
                        <label>Nama kegiatan<input type="text" name="title" required></label>
                        <label>Tanggal<input type="date" name="agenda_date" required></label>
                    </div>
                    <div class="fields">
                        <label>Jam<input type="time" name="agenda_time" required></label>
                        <label>Tempat<input type="text" name="location" required></label>
                    </div>
                    <label>Disposisi<input type="text" name="disposition"></label>
                    <div class="actions">
                        <button class="btn-primary" type="submit">Simpan agenda</button>
                    </div>
                </form>
                <div class="table-wrap"><table><thead><tr><th>Kegiatan</th><th>Tanggal dan jam</th><th>Tempat</th><th>Disposisi</th><th>Aksi</th></tr></thead><tbody id="agenda-table"></tbody></table></div>
            </article>

            <article class="card">
                <h2>Admin</h2>
                <p>Penambahan admin wajib isi NIP 18 digit, peran, disposisi, kata sandi, dan konfirmasi kata sandi.</p>
                <div class="hint">Disposisi dikosongkan dulu secara default. Saat ubah data admin, kata sandi boleh dikosongkan kalau tidak ingin diubah.</div>
                <form id="user-form">
                    <input type="hidden" name="id">
                    <div class="fields">
                        <label>NIP<input type="text" name="nip" inputmode="numeric" pattern="[0-9]{18}" maxlength="18" required></label>
                        <label>Peran<select name="role"><option value="admin">Admin</option><option value="superadmin">Superadmin</option></select></label>
                    </div>
                    <div class="fields">
                        <label>Disposisi<input type="text" name="disposition" placeholder="tu atau data"></label>
                        <div></div>
                    </div>
                    <div class="fields">
                        <label>Kata sandi<input type="password" name="password" minlength="8" placeholder="Minimal 8 karakter"></label>
                        <label>Konfirmasi kata sandi<input type="password" name="password_confirmation" minlength="8" placeholder="Harus sama persis"></label>
                    </div>
                    <div class="actions">
                        <button class="btn-primary" type="submit">Simpan admin</button>
                    </div>
                </form>
                <div class="table-wrap"><table><thead><tr><th>NIP</th><th>Peran</th><th>Disposisi</th><th>Email</th><th>Aksi</th></tr></thead><tbody id="user-table"></tbody></table></div>
            </article>

            <article class="card">
                <h2>Profil</h2>
                <p>Untuk uji kartu profil di halaman TV.</p>
                <form id="profile-form">
                    <input type="hidden" name="id">
                    <div class="fields">
                        <label>Nama<input type="text" name="name" required></label>
                        <label>Jabatan<input type="text" name="position" required></label>
                    </div>
                    <div class="fields">
                        <label>Foto<input type="file" name="photo_file" accept="image/*"></label>
                        <label>Urutan tampil<input type="number" name="display_order" min="0" value="0"></label>
                    </div>
                    <div class="actions">
                        <button class="btn-primary" type="submit">Simpan profil</button>
                    </div>
                </form>
                <div class="table-wrap"><table><thead><tr><th>Nama</th><th>Jabatan</th><th>Unit</th><th>Urutan</th><th>Aksi</th></tr></thead><tbody id="profile-table"></tbody></table></div>
            </article>

            <article class="card">
                <h2>Video</h2>
                <p>Kamu bisa isi tautan video atau unggah file video langsung dari komputer.</p>
                <form id="video-form">
                    <input type="hidden" name="id">
                    <div class="fields">
                        <label>Judul<input type="text" name="title" required></label>
                        <label>Tautan video<input type="url" name="source_path" placeholder="https://..."></label>
                    </div>
                    <div class="fields">
                        <label>File video<input type="file" name="source_file" accept="video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm"></label>
                        <label>Urutan tampil<input type="number" name="display_order" min="0" value="0"></label>
                    </div>
                    <div class="actions">
                        <button class="btn-primary" type="submit">Simpan video</button>
                    </div>
                </form>
                <div class="table-wrap"><table><thead><tr><th>Judul</th><th>Sumber video</th><th>Unit</th><th>Urutan</th><th>Aksi</th></tr></thead><tbody id="video-table"></tbody></table></div>
            </article>

            <article class="card">
                <h2>Teks berjalan</h2>
                <p>Dipakai untuk ticker pengumuman di bawah layar TV.</p>
                <form id="running-text-form">
                    <input type="hidden" name="id">
                    <label>Judul<input type="text" name="title" required></label>
                    <label>Pesan<textarea name="message" required></textarea></label>
                    <div class="actions">
                        <button class="btn-primary" type="submit">Simpan teks berjalan</button>
                    </div>
                </form>
                <div class="table-wrap"><table><thead><tr><th>Judul</th><th>Pesan</th><th>Unit</th><th>Prioritas</th><th>Aksi</th></tr></thead><tbody id="running-text-table"></tbody></table></div>
            </article>

            @if ($unit === 'data')
                <article class="card full">
                    <h2>Agenda TU Yang Terlihat Di Admin Data</h2>
                    <p>Bagian ini hanya untuk baca, supaya kamu bisa cek requirement bahwa admin Data dapat melihat agenda TU.</p>
                    <div class="table-wrap"><table><thead><tr><th>Kegiatan</th><th>Tanggal dan jam</th><th>Tempat</th><th>Disposisi</th></tr></thead><tbody id="agenda-tu-di-data-table"></tbody></table></div>
                </article>
            @endif
        </section>
    </div>

    <script>
        const unitSaatIni = @json($unit);
        const tokenCsrf = document.querySelector('meta[name="csrf-token"]').content;
        const kotakStatus = document.getElementById('status');
        const endpoint = {
            agenda: '/admin/api/agendas',
            profil: '/admin/api/profiles',
            video: '/admin/api/videos',
            teks: '/admin/api/running-texts',
            admin: '/admin/api/users',
        };
        const dataState = { agenda: [], agendaTu: [], profil: [], video: [], teks: [], admin: [] };
        const labelJenis = { agenda: 'agenda', user: 'admin', profile: 'profil', video: 'video', runningText: 'teks berjalan' };
        const konfigurasiForm = {
            agenda: {
                formId: 'agenda-form',
                endpoint: endpoint.agenda,
                stateKey: 'agenda',
                buatPayload(form) {
                    const data = new FormData(form);
                    data.set('unit', unitSaatIni);
                    data.set('is_active', '1');
                    data.delete('description');
                    return data;
                },
            },
            user: {
                formId: 'user-form',
                endpoint: endpoint.admin,
                stateKey: 'admin',
                buatPayload(form) {
                    return new FormData(form);
                },
            },
            profile: {
                formId: 'profile-form',
                endpoint: endpoint.profil,
                stateKey: 'profil',
                buatPayload(form) {
                    const data = new FormData(form);
                    data.set('unit', unitSaatIni);
                    data.set('is_active', '1');
                    return data;
                },
            },
            video: {
                formId: 'video-form',
                endpoint: endpoint.video,
                stateKey: 'video',
                buatPayload(form) {
                    const data = new FormData(form);
                    const adaFile = form.elements.namedItem('source_file').files.length > 0;
                    data.set('unit', unitSaatIni);
                    data.set('is_active', '1');
                    data.set('source_type', adaFile ? 'upload' : 'url');
                    return data;
                },
            },
            runningText: {
                formId: 'running-text-form',
                endpoint: endpoint.teks,
                stateKey: 'teks',
                buatPayload(form) {
                    const data = new FormData(form);
                    data.set('unit', unitSaatIni);
                    data.set('priority', '0');
                    data.set('is_active', '1');
                    data.delete('starts_at');
                    data.delete('ends_at');
                    return data;
                },
            },
        };

        function aturSapaan() {
            const target = document.getElementById('sapaan-pengguna');
            if (!target) return;
            const jamSekarang = new Date().getHours();
            let sapaan = 'Selamat malam';

            if (jamSekarang >= 4 && jamSekarang < 11) {
                sapaan = 'Selamat pagi';
            } else if (jamSekarang >= 11 && jamSekarang < 15) {
                sapaan = 'Selamat siang';
            } else if (jamSekarang >= 15 && jamSekarang < 18) {
                sapaan = 'Selamat sore';
            }

            target.textContent = `${sapaan}, {{ $currentUser->nip }}`;
        }

        function ubahStatus(pesan, error = false) {
            kotakStatus.textContent = pesan;
            kotakStatus.classList.toggle('error', error);
        }

        function formatTanggalJam(tanggal, jam = '') {
            if (!tanggal) return '-';
            const bagianTanggal = tanggal.slice(0, 10).split('-');
            const hasilTanggal = bagianTanggal.length === 3 ? `${bagianTanggal[2]}-${bagianTanggal[1]}-${bagianTanggal[0]}` : tanggal;
            const hasilJam = jam ? jam.slice(0, 5) : '00:00';
            return `${hasilTanggal} ${hasilJam}`;
        }

        function renderTabel(id, html, colspan, pesanKosong) {
            document.getElementById(id).innerHTML = html || `<tr><td colspan="${colspan}">${pesanKosong}</td></tr>`;
        }

        function requestJson(url) {
            return fetch(url, {
                headers: { 'Accept': 'application/json' },
            }).then(async (response) => {
                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(data.message || 'Gagal memuat data.');
                return data;
            });
        }

        function kirimForm(url, formData, id = '') {
            if (id) {
                formData.append('_method', 'PUT');
            }

            return fetch(id ? `${url}/${id}` : url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': tokenCsrf, 'Accept': 'application/json' },
                body: formData,
            }).then(async (response) => {
                if (response.status === 204) return null;
                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    const pesanValidasi = Object.values(data.errors || {}).flat().join(' ');
                    throw new Error(pesanValidasi || data.message || 'Gagal menyimpan data.');
                }
                return data;
            });
        }

        function hapusData(url) {
            return fetch(url, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': tokenCsrf, 'Accept': 'application/json' },
            }).then(async (response) => {
                if (response.status === 204) return null;
                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(data.message || 'Gagal menghapus data.');
                return data;
            });
        }

        function renderAksi(jenis, item) {
            return `<div class="row-actions"><button class="btn-secondary" type="button" onclick="editData('${jenis}', ${item.id})">Ubah</button><button class="btn-danger" type="button" onclick="hapusDataPanel('${jenis}', ${item.id})">Hapus</button></div>`;
        }

        function renderAgenda() {
            const html = dataState.agenda
                .filter((item) => item.unit === unitSaatIni)
                .map((item) => `<tr><td><strong>${item.title}</strong></td><td>${formatTanggalJam(item.agenda_date, item.agenda_time)}</td><td>${item.location}</td><td>${item.disposition ?? '-'}</td><td>${renderAksi('agenda', item)}</td></tr>`)
                .join('');
            renderTabel('agenda-table', html, 5, 'Belum ada agenda.');

            if (unitSaatIni === 'data') {
                const htmlTu = dataState.agendaTu
                    .map((item) => `<tr><td>${item.title}</td><td>${formatTanggalJam(item.agenda_date, item.agenda_time)}</td><td>${item.location}</td><td>${item.disposition ?? '-'}</td></tr>`)
                    .join('');
                renderTabel('agenda-tu-di-data-table', htmlTu, 4, 'Belum ada agenda TU.');
            }
        }

        function renderAdmin() {
            const html = dataState.admin
                .map((item) => `<tr><td class="mono">${item.nip ?? '-'}</td><td><span class="pill">${item.role}</span></td><td><span class="pill">${item.disposition ?? '-'}</span></td><td>${item.email}</td><td>${renderAksi('user', item)}</td></tr>`)
                .join('');
            renderTabel('user-table', html, 5, 'Belum ada admin.');
        }

        function renderProfil() {
            const html = dataState.profil
                .map((item) => `<tr><td>${item.name}</td><td>${item.position}</td><td><span class="pill">${item.unit}</span></td><td>${item.display_order}</td><td>${renderAksi('profile', item)}</td></tr>`)
                .join('');
            renderTabel('profile-table', html, 5, 'Belum ada profil.');
        }

        function renderVideo() {
            const html = dataState.video
                .map((item) => `<tr><td>${item.title}</td><td><span class="mono">${item.source_path}</span></td><td><span class="pill">${item.unit}</span></td><td>${item.display_order}</td><td>${renderAksi('video', item)}</td></tr>`)
                .join('');
            renderTabel('video-table', html, 5, 'Belum ada video.');
        }

        function renderTeks() {
            const html = dataState.teks
                .map((item) => `<tr><td>${item.title}</td><td>${item.message}</td><td><span class="pill">${item.unit}</span></td><td>${item.priority}</td><td>${renderAksi('runningText', item)}</td></tr>`)
                .join('');
            renderTabel('running-text-table', html, 5, 'Belum ada teks berjalan.');
        }

        function renderSemua() {
            renderAgenda();
            renderAdmin();
            renderProfil();
            renderVideo();
            renderTeks();
        }

        async function muatSemua() {
            try {
                ubahStatus('Memuat data panel admin...');
                const [agenda, agendaTu, admin, profil, video, teks] = await Promise.all([
                    requestJson(endpoint.agenda),
                    unitSaatIni === 'data' ? requestJson(`${endpoint.agenda}?lihat=tu`) : Promise.resolve([]),
                    requestJson(endpoint.admin),
                    requestJson(endpoint.profil),
                    requestJson(endpoint.video),
                    requestJson(endpoint.teks),
                ]);

                dataState.agenda = agenda;
                dataState.agendaTu = agendaTu;
                dataState.admin = admin;
                dataState.profil = profil;
                dataState.video = video;
                dataState.teks = teks;

                renderSemua();
                ubahStatus('Data berhasil dimuat.');
            } catch (error) {
                ubahStatus(error.message, true);
            }
        }

        function resetForm(formId) {
            const form = document.getElementById(formId);
            form.reset();

            const idField = form.querySelector('input[name="id"]');
            if (idField) idField.value = '';

            if (formId === 'user-form') {
                form.elements.namedItem('disposition').value = '';
            }

            if (formId === 'profile-form' || formId === 'video-form') {
                const urutan = form.elements.namedItem('display_order');
                if (urutan) urutan.value = '0';
            }
        }

        async function submitForm(jenis, event) {
            event.preventDefault();
            const config = konfigurasiForm[jenis];
            const form = document.getElementById(config.formId);
            const id = form.elements.namedItem('id').value;
            const payload = config.buatPayload(form);

            try {
                await kirimForm(config.endpoint, payload, id);
                resetForm(config.formId);
                await muatSemua();
                ubahStatus(`${labelJenis[jenis]} berhasil disimpan.`);
            } catch (error) {
                ubahStatus(error.message, true);
            }
        }

        function isiForm(formId, item) {
            const form = document.getElementById(formId);
            Object.entries(item).forEach(([key, value]) => {
                const field = form.elements.namedItem(key);
                if (!field) return;
                if (field.type === 'checkbox') {
                    field.checked = Boolean(value);
                    return;
                }
                if (field.type === 'datetime-local' && value) {
                    field.value = value.slice(0, 16);
                    return;
                }
                if (field.type === 'password' || field.type === 'file') return;
                field.value = value ?? '';
            });
            form.elements.namedItem('id').value = item.id;
        }

        window.editData = function (jenis, id) {
            const config = konfigurasiForm[jenis];
            const item = dataState[config.stateKey].find((entry) => entry.id === id);
            if (!item) {
                ubahStatus('Data tidak ditemukan.', true);
                return;
            }
            isiForm(config.formId, item);
            ubahStatus(`Mode ubah aktif untuk ${labelJenis[jenis]} ID ${id}.`);
            document.getElementById(config.formId).scrollIntoView({ behavior: 'smooth', block: 'start' });
        };

        window.hapusDataPanel = async function (jenis, id) {
            if (!confirm(`Hapus ${labelJenis[jenis]} ini?`)) return;
            const config = konfigurasiForm[jenis];
            try {
                await hapusData(`${config.endpoint}/${id}`);
                await muatSemua();
                ubahStatus(`${labelJenis[jenis]} berhasil dihapus.`);
            } catch (error) {
                ubahStatus(error.message, true);
            }
        };

        document.getElementById('agenda-form').addEventListener('submit', (event) => submitForm('agenda', event));
        document.getElementById('user-form').addEventListener('submit', (event) => submitForm('user', event));
        document.getElementById('profile-form').addEventListener('submit', (event) => submitForm('profile', event));
        document.getElementById('video-form').addEventListener('submit', (event) => submitForm('video', event));
        document.getElementById('running-text-form').addEventListener('submit', (event) => submitForm('runningText', event));
        aturSapaan();
        Object.values(konfigurasiForm).forEach((config) => resetForm(config.formId));
        muatSemua();
    </script>
</body>
</html>
