<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\Profile;
use App\Models\RunningText;
use App\Models\Video;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        Profile::query()->updateOrCreate(
            ['name' => 'Shaun Patrick Hendra'],
            [
                'position' => 'Magang Biro SDM',
                'photo_path' => null,
                'unit' => 'data',
                'is_active' => true,
                'display_order' => 1,
            ]
        );

        Video::query()->updateOrCreate(
            ['title' => 'Video Profil'],
            [
                'source_type' => 'upload',
                'source_path' => 'assets/videos/company-profile.mp4',
                'unit' => 'data',
                'is_active' => true,
                'display_order' => 1,
            ]
        );

        RunningText::query()->updateOrCreate(
            ['title' => 'Pengumuman Utama'],
            [
                'message' => 'Peringatan Hari Lahir Pancasila akan dilaksanakan di Lapangan Monas pukul 08:00 WIB.',
                'unit' => 'data',
                'priority' => 1,
                'is_active' => true,
            ]
        );

        $agendas = [
            ['title' => 'Koordinasi Internal', 'agenda_date' => '2026-03-10', 'agenda_time' => '08:15', 'location' => 'Ruang Rapat', 'disposition' => 'Kasubag TU', 'unit' => 'tu'],
            ['title' => 'Briefing Pegawai', 'agenda_date' => '2026-03-10', 'agenda_time' => '09:00', 'location' => 'Aula', 'disposition' => 'Staf TU', 'unit' => 'tu'],
            ['title' => 'Validasi Dokumen', 'agenda_date' => '2026-03-10', 'agenda_time' => '10:15', 'location' => 'Lt.2', 'disposition' => 'Admin TU', 'unit' => 'tu'],
            ['title' => 'Pemeriksaan Berkas', 'agenda_date' => '2026-03-11', 'agenda_time' => '08:30', 'location' => 'Ruang Arsip', 'disposition' => 'Arsiparis', 'unit' => 'tu'],
            ['title' => 'Sinkronisasi Data', 'agenda_date' => '2026-03-11', 'agenda_time' => '13:00', 'location' => 'Zoom', 'disposition' => 'Operator', 'unit' => 'tu'],
            ['title' => 'Evaluasi Mingguan', 'agenda_date' => '2026-03-12', 'agenda_time' => '09:30', 'location' => 'Aula', 'disposition' => 'Kasubag TU', 'unit' => 'tu'],
            ['title' => 'Rencana Program', 'agenda_date' => '2026-03-12', 'agenda_time' => '14:45', 'location' => 'Ruang Rapat', 'disposition' => 'Tim TU', 'unit' => 'tu'],
            ['title' => 'Monitoring Dashboard', 'agenda_date' => '2026-03-10', 'agenda_time' => '08:00', 'location' => 'Command Center', 'disposition' => 'Analis Data', 'unit' => 'data'],
            ['title' => 'Update Integrasi API', 'agenda_date' => '2026-03-10', 'agenda_time' => '10:00', 'location' => 'Ruang Server', 'disposition' => 'Programmer', 'unit' => 'data'],
            ['title' => 'Uji Validitas Dataset', 'agenda_date' => '2026-03-10', 'agenda_time' => '13:30', 'location' => 'Lab Data', 'disposition' => 'Data Engineer', 'unit' => 'data'],
            ['title' => 'Rapat Lintas Unit', 'agenda_date' => '2026-03-11', 'agenda_time' => '09:15', 'location' => 'Zoom', 'disposition' => 'Koordinator', 'unit' => 'data'],
            ['title' => 'Pemetaan KPI', 'agenda_date' => '2026-03-11', 'agenda_time' => '11:00', 'location' => 'Lt.3', 'disposition' => 'Analis Sistem', 'unit' => 'data'],
            ['title' => 'Review SLA Layanan', 'agenda_date' => '2026-03-12', 'agenda_time' => '15:00', 'location' => 'Ruang Rapat', 'disposition' => 'Supervisor', 'unit' => 'data'],
            ['title' => 'Pelaporan Mingguan', 'agenda_date' => '2026-03-12', 'agenda_time' => '16:15', 'location' => 'Command Center', 'disposition' => 'Tim Data', 'unit' => 'data'],
        ];

        foreach ($agendas as $agenda) {
            Agenda::query()->updateOrCreate(
                [
                    'title' => $agenda['title'],
                    'agenda_date' => $agenda['agenda_date'],
                    'location' => $agenda['location'],
                ],
                $agenda + ['is_active' => true]
            );
        }
    }
}
