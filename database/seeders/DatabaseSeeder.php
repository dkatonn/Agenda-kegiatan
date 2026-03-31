<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@agenda.test'],
            ['nip' => '198501012010011001', 'name' => 'Admin Agenda', 'password' => bcrypt('password')]
        );

        foreach ([
            'background' => null,
            'running_text' => 'Selamat datang di TV Agenda. Silakan kelola pegawai, agenda, dan video melalui panel admin.',
            'video' => null,
        ] as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        foreach ([
            ['name' => 'Dimas Pratama', 'role' => 'Kepala Biro SDM', 'image_path' => null],
            ['name' => 'Rani Wulandari', 'role' => 'Analis Kepegawaian', 'image_path' => null],
            ['name' => 'Fajar Nugroho', 'role' => 'Staff Administrasi', 'image_path' => null],
        ] as $employee) {
            Employee::query()->firstOrCreate(
                ['name' => $employee['name']],
                $employee
            );
        }

        foreach ([
            [
                'date' => now()->toDateString(),
                'time' => '08:00',
                'name' => 'Rapat Koordinasi Mingguan',
                'location' => 'Ruang Rapat Utama',
                'disposition' => 'Kepala Biro',
            ],
            [
                'date' => now()->addDay()->toDateString(),
                'time' => '10:00',
                'name' => 'Monitoring Agenda Unit',
                'location' => 'Ruang Data dan Informasi',
                'disposition' => 'Sekretariat',
            ],
            [
                'date' => now()->addDays(2)->toDateString(),
                'time' => '13:30',
                'name' => 'Evaluasi Kinerja Bulanan',
                'location' => 'Aula Lantai 2',
                'disposition' => 'Tim SDM',
            ],
            [
                'date' => now()->addDays(3)->toDateString(),
                'time' => '09:15',
                'name' => 'Sinkronisasi Agenda Tata Usaha',
                'location' => 'Ruang Administrasi',
                'disposition' => 'Kasubbag TU',
            ],
            [
                'date' => now()->addDays(4)->toDateString(),
                'time' => '14:00',
                'name' => 'Verifikasi Data Kepegawaian',
                'location' => 'Ruang Arsip Digital',
                'disposition' => 'Analis Data',
            ],
            [
                'date' => now()->addDays(5)->toDateString(),
                'time' => '08:30',
                'name' => 'Pembahasan Penyusunan Jadwal Pelayanan Administrasi Kepegawaian untuk Kebutuhan Triwulan Kedua Internal',
                'location' => 'Ruang Pelayanan Administrasi Lantai Satu Gedung Utama Internal',
                'disposition' => 'Koordinator Tata Usaha dan Tim Pelayanan Administrasi Internal',
            ],
            [
                'date' => now()->addDays(6)->toDateString(),
                'time' => '09:45',
                'name' => 'Rapat Sinkronisasi Progres Digitalisasi Dokumen dan Penataan Arsip Aktif Unit Kerja Internal',
                'location' => 'Ruang Arsip Terpadu dan Area Verifikasi Dokumen Internal',
                'disposition' => 'Tim Arsiparis Internal dan Penanggung Jawab Dokumen',
            ],
            [
                'date' => now()->addDays(7)->toDateString(),
                'time' => '10:30',
                'name' => 'Evaluasi Pemetaan Kebutuhan Data Pegawai untuk Integrasi Dashboard Monitoring Kinerja Harian Internal',
                'location' => 'Command Center Data dan Informasi Kantor Pusat',
                'disposition' => 'Analis Sistem Informasi dan Tim Pengelola Dashboard',
            ],
            [
                'date' => now()->addDays(8)->toDateString(),
                'time' => '11:15',
                'name' => 'Koordinasi Persiapan Bahan Presentasi Capaian Layanan dan Target Pembenahan Administrasi Semester Ini',
                'location' => 'Ruang Rapat Kecil Bidang Administrasi dan Layanan Internal',
                'disposition' => 'Kasubbag TU bersama seluruh staf administrasi',
            ],
            [
                'date' => now()->addDays(9)->toDateString(),
                'time' => '13:00',
                'name' => 'Validasi Akhir Dataset Mutasi Pegawai untuk Kebutuhan Publikasi Rekapitulasi dan Monitoring Internal',
                'location' => 'Laboratorium Data Kepegawaian dan Ruang Sinkronisasi Sistem',
                'disposition' => 'Admin Data, Verifikator, dan Supervisor Pengolahan Informasi',
            ],
            [
                'date' => now()->addDays(10)->toDateString(),
                'time' => '14:20',
                'name' => 'Forum Tindak Lanjut Temuan Evaluasi Pelayanan dengan Penyesuaian Alur Kerja dan Penanggung Jawab',
                'location' => 'Aula Serbaguna Lantai Dua Gedung Administrasi',
                'disposition' => 'Pimpinan Unit, Sekretariat, dan Perwakilan Tim Pelaksana',
            ],
            [
                'date' => now()->addDays(11)->toDateString(),
                'time' => '15:10',
                'name' => 'Pemeriksaan Kesiapan Data Dukung untuk Rapat Koordinasi Besar serta Penyelarasan Agenda Mingguan',
                'location' => 'Ruang Data Strategis dan Area Persiapan Briefing Pimpinan',
                'disposition' => 'Tim Data dan Informasi bersama Sekretariat Pimpinan',
            ],
            [
                'date' => now()->addDays(12)->toDateString(),
                'time' => '08:10',
                'name' => 'Penyelarasan Agenda Pelayanan Kepegawaian dengan Target Kinerja Unit dan Jadwal Monitoring Lapangan',
                'location' => 'Ruang Koordinasi Administrasi Terpadu Gedung Pusat',
                'disposition' => 'Sekretariat Unit dan Tim Monitoring Pelayanan',
            ],
            [
                'date' => now()->addDays(13)->toDateString(),
                'time' => '10:40',
                'name' => 'Rapat Evaluasi Progres Pembaruan Data Pegawai dan Pemutakhiran Dokumen Pendukung Semester Berjalan',
                'location' => 'Command Center Kepegawaian dan Ruang Validasi Internal',
                'disposition' => 'Pengelola Data, Verifikator, dan Koordinator Administrasi',
            ],
            [
                'date' => now()->addDays(14)->toDateString(),
                'time' => '13:50',
                'name' => 'Koordinasi Final Persiapan Materi Paparan Pimpinan untuk Forum Pembahasan Agenda Prioritas Mingguan',
                'location' => 'Ruang Rapat Pimpinan Lantai Dua dan Area Persiapan Presentasi',
                'disposition' => 'Tim Sekretariat, Analis Data, dan Penanggung Jawab Materi',
            ],
        ] as $agenda) {
            $lookup = [
                'date' => $agenda['date'],
                'time' => $agenda['time'],
                'name' => $agenda['name'],
            ];

            $payload = $agenda;

            if (Schema::hasColumn('agendas', 'title')) {
                $lookup = ['title' => $agenda['name']];
                $payload['title'] = $agenda['name'];
            }

            if (Schema::hasColumn('agendas', 'agenda_date')) {
                $payload['agenda_date'] = $agenda['date'];
            }

            if (Schema::hasColumn('agendas', 'agenda_time')) {
                $payload['agenda_time'] = $agenda['time'];
            }

            if (Schema::hasColumn('agendas', 'description')) {
                $payload['description'] = $agenda['disposition'];
            }

            if (Schema::hasColumn('agendas', 'unit')) {
                $payload['unit'] = 'data';
            }

            if (Schema::hasColumn('agendas', 'is_active')) {
                $payload['is_active'] = true;
            }

            Agenda::query()->firstOrCreate($lookup, $payload);
        }
    }
}
